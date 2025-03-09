import './bootstrap';
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

// Importar o CSS do Quill diretamente no JavaScript
import 'quill/dist/quill.snow.css';

import Quill from 'quill';

const mediaFiles = import.meta.glob('../images/*.{png,jpg,jpeg,gif,svg,mp4}', { eager: true });

window.appMedia = mediaFiles;
window.Alpine = Alpine;

Livewire.start();

// Garantir que Quill esteja disponível globalmente
window.Quill = Quill;

// Implementação de upload de imagens para o Quill
class ImageUploader {
    constructor(quill, options) {
        this.quill = quill;
        this.options = options || {};
        this.range = null;

        // Configurações padrão
        if (!this.options.url) {
            this.options.url = '/upload-image';
            console.log('URL para upload de imagens não fornecida, usando padrão:', this.options.url);
        }

        if (!this.options.csrfToken) {
            this.options.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            console.log('Token CSRF encontrado:', this.options.csrfToken ? 'Sim' : 'Não');
        }

        // Adiciona o botão de upload no toolbar
        const toolbar = this.quill.getModule('toolbar');
        if (toolbar) {
            // Adiciona manipulador para o botão de imagem
            toolbar.addHandler('image', this.selectLocalImage.bind(this));
            console.log('Handler de imagem adicionado ao toolbar');
        } else {
            console.warn('Não foi possível encontrar o módulo toolbar');
        }

        // Cria um input de arquivo oculto
        this.fileInput = document.createElement('input');
        this.fileInput.setAttribute('type', 'file');
        this.fileInput.setAttribute('accept', 'image/*');
        this.fileInput.setAttribute('style', 'display:none');
        document.body.appendChild(this.fileInput);

        this.fileInput.addEventListener('change', this.fileChanged.bind(this));
        console.log('Input de arquivo criado e configurado');
    }

    selectLocalImage() {
        console.log('Selecionando imagem local');
        this.range = this.quill.getSelection() || { index: 0, length: 0 };
        this.fileInput.click();
    }

    fileChanged() {
        console.log('Arquivo selecionado, processando...');
        const file = this.fileInput.files[0];
        if (!file) {
            console.warn('Nenhum arquivo selecionado');
            return;
        }

        // Verifica o tipo de arquivo
        if (!file.type.match(/^image\/(jpg|jpeg|png|gif|webp)$/)) {
            alert('Apenas imagens são permitidas (JPG, PNG, GIF, WEBP)');
            this.fileInput.value = '';
            console.warn('Tipo de arquivo não suportado:', file.type);
            return;
        }

        // Verificar tamanho do arquivo (limite de 5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('A imagem deve ter no máximo 5MB');
            this.fileInput.value = '';
            console.warn('Arquivo muito grande:', file.size);
            return;
        }

        this.uploadFile(file);
    }

    uploadFile(file) {
        console.log('Iniciando upload do arquivo:', file.name);
        const formData = new FormData();
        formData.append('image', file);

        const quill = this.quill;
        const range = this.range;

        // Exibir indicador de carregamento no editor
        const loadingIndex = range.index;
        quill.insertText(loadingIndex, 'Carregando imagem...', { 'italic': true, 'color': '#3b82f6' });
        quill.setSelection(loadingIndex + 19);
        console.log('Texto de carregamento inserido na posição:', loadingIndex);

        // Log dos cabeçalhos para depuração
        console.log('URL para upload:', this.options.url);
        console.log('CSRF Token:', this.options.csrfToken);

        fetch(this.options.url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': this.options.csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        })
            .then(response => {
                console.log('Resposta recebida:', response.status);
                if (!response.ok) {
                    return response.text().then(text => {
                        console.error('Erro na resposta:', text);
                        throw new Error('Erro ao fazer upload da imagem: ' + response.status);
                    });
                }
                return response.json();
            })
            .then(result => {
                console.log('Upload bem-sucedido:', result);

                // Remove o texto de carregamento
                quill.deleteText(loadingIndex, 19);

                if (!result.url) {
                    throw new Error('URL da imagem não fornecida na resposta');
                }

                // Conteúdo antes da inserção
                console.log('Conteúdo antes da inserção:', quill.getContents());

                // Inserir a imagem apenas (sem texto adicional)
                quill.insertEmbed(loadingIndex, 'image', result.url);

                // Adicione a classe responsiva diretamente
                setTimeout(() => {
                    const images = quill.root.querySelectorAll('img');
                    images.forEach(img => {
                        if (!img.classList.contains('responsive-image')) {
                            img.classList.add('responsive-image');
                        }

                        // Verificar se existe um nó de texto vazio após a imagem
                        const parent = img.parentNode;
                        if (parent && parent.childNodes) {
                            for (let i = 0; i < parent.childNodes.length; i++) {
                                if (parent.childNodes[i] === img && i + 1 < parent.childNodes.length) {
                                    const nextNode = parent.childNodes[i + 1];
                                    // Se for um nó de texto com apenas um ponto, removê-lo
                                    if (nextNode.nodeType === Node.TEXT_NODE && nextNode.textContent.trim() === '.') {
                                        parent.removeChild(nextNode);
                                    }
                                }
                            }
                        }
                    });
                }, 100);

                // Move o cursor após a imagem
                quill.setSelection(loadingIndex + 1);

                // Limpa o input de arquivo
                this.fileInput.value = '';

                // Conteúdo após a inserção
                console.log('Conteúdo após a inserção:', quill.getContents());
            })
            .catch(error => {
                console.error('Erro no upload:', error);

                // Remove o texto de carregamento
                quill.deleteText(loadingIndex, 19);

                // Mostra mensagem de erro
                quill.insertText(loadingIndex, 'Erro ao carregar imagem. Tente novamente.', { 'color': 'red', 'bold': true });
                setTimeout(() => {
                    quill.deleteText(loadingIndex, 35);
                }, 3000);

                // Limpa o input de arquivo
                this.fileInput.value = '';
            });
    }
}

// Registra o módulo no Quill de forma explícita
Quill.register('modules/imageUploader', ImageUploader);

// Agora a função deve funcionar corretamente
window.initQuillWithImageUpload = function (elementId, toolbarId, options = {}) {
    console.log('Inicializando Quill com suporte a upload de imagens:', elementId);

    // Verificar se o módulo foi registrado corretamente
    if (!Quill.imports['modules/imageUploader']) {
        console.warn('Módulo imageUploader não encontrado, registrando novamente');
        Quill.register('modules/imageUploader', ImageUploader);
    }

    // Configuração padrão para o editor
    const defaultOptions = {
        modules: {
            toolbar: toolbarId ? {
                container: toolbarId
            } : [
                [{ 'font': [] }, { 'size': [] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                [{ 'align': [] }],
                ['link', 'image'],
                ['clean']
            ],
            imageUploader: {
                url: '/upload-image',
                csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        },
        theme: 'snow',
        placeholder: 'Escreva aqui...'
    };

    // Combina as configurações padrão com as opções fornecidas
    const mergedOptions = { ...defaultOptions, ...options };
    if (options.modules) {
        mergedOptions.modules = { ...defaultOptions.modules, ...options.modules };
        if (options.modules.toolbar) {
            mergedOptions.modules.toolbar = options.modules.toolbar;
        }
        if (options.modules.imageUploader) {
            mergedOptions.modules.imageUploader = {
                ...defaultOptions.modules.imageUploader,
                ...options.modules.imageUploader
            };
        }
    }

    console.log('Opções do Quill:', mergedOptions);

    // Verificar se o elemento existe
    const element = typeof elementId === 'string' ? document.querySelector(elementId) : elementId;
    if (!element) {
        console.error('Elemento não encontrado:', elementId);
        return null;
    }

    try {
        // Inicializa o editor Quill com as opções
        return new Quill(element, mergedOptions);
    } catch (error) {
        console.error('Erro ao inicializar o Quill:', error);
        return null;
    }
};
