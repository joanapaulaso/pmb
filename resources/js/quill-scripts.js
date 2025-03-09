
// Adicione este código ao seu arquivo JavaScript principal ou crie um arquivo específico

// Implementação de upload de imagens para o Quill
class ImageUploader {
    constructor(quill, options) {
        this.quill = quill;
        this.options = options;
        this.range = null;

        // Configurações padrão
        if (!this.options.url) {
            console.error('URL para upload de imagens não fornecida');
            return;
        }

        if (!this.options.csrfToken) {
            this.options.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        }

        // Adiciona o botão de upload no toolbar
        const toolbar = this.quill.getModule('toolbar');
        if (toolbar) {
            // Adiciona manipulador para o botão de imagem
            toolbar.addHandler('image', this.selectLocalImage.bind(this));
        }

        // Cria um input de arquivo oculto
        this.fileInput = document.createElement('input');
        this.fileInput.setAttribute('type', 'file');
        this.fileInput.setAttribute('accept', 'image/*');
        this.fileInput.setAttribute('style', 'display:none');
        document.body.appendChild(this.fileInput);

        this.fileInput.addEventListener('change', this.fileChanged.bind(this));
    }

    selectLocalImage() {
        this.range = this.quill.getSelection();
        this.fileInput.click();
    }

    fileChanged() {
        const file = this.fileInput.files[0];
        if (!file) return;

        // Verifica o tipo de arquivo
        if (!file.type.match(/^image\/(jpg|jpeg|png|gif|webp)$/)) {
            alert('Apenas imagens são permitidas (JPG, PNG, GIF, WEBP)');
            this.fileInput.value = '';
            return;
        }

        // Verificar tamanho do arquivo (limite de 5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('A imagem deve ter no máximo 5MB');
            this.fileInput.value = '';
            return;
        }

        this.uploadFile(file);
    }

    uploadFile(file) {
        const formData = new FormData();
        formData.append('image', file);

        const quill = this.quill;
        const range = this.range;

        // Exibir indicador de carregamento no editor
        const loadingIndex = range.index;
        quill.insertText(loadingIndex, 'Carregando imagem...', 'bold', true);
        quill.setSelection(loadingIndex + 19);

        fetch(this.options.url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': this.options.csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro ao fazer upload da imagem');
                }
                return response.json();
            })
            .then(result => {
                // Remove o texto de carregamento
                quill.deleteText(loadingIndex, 19);

                // Insere a imagem com classes para responsividade
                quill.insertEmbed(
                    loadingIndex,
                    'image',
                    {
                        src: result.url,
                        alt: result.alt || 'Imagem publicada',
                        class: 'quill-image responsive-image'
                    },
                    'user'
                );

                // Move o cursor após a imagem
                quill.setSelection(loadingIndex + 1);

                // Limpa o input de arquivo
                this.fileInput.value = '';
            })
            .catch(error => {
                console.error('Erro no upload:', error);

                // Remove o texto de carregamento
                quill.deleteText(loadingIndex, 19);

                // Mostra mensagem de erro
                quill.insertText(loadingIndex, 'Erro ao carregar imagem. Tente novamente.', 'bold', true);
                setTimeout(() => {
                    quill.deleteText(loadingIndex, 35);
                }, 3000);

                // Limpa o input de arquivo
                this.fileInput.value = '';
            });
    }
}

// Registra o módulo no Quill
Quill.register('modules/imageUploader', ImageUploader);

// Função para inicializar o editor Quill com suporte a upload de imagens
function initQuillWithImageUpload(elementId, toolbarId, options = {}) {
    // Configuração padrão para o editor
    const defaultOptions = {
        modules: {
            toolbar: toolbarId ? {
                container: toolbarId
            } : [
                [{ 'font': [] }, { 'size': [] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'script': 'super' }, { 'script': 'sub' }],
                [{ 'header': '1' }, { 'header': '2' }, 'blockquote', 'code-block'],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }, { 'indent': '-1' }, { 'indent': '+1' }],
                [{ 'direction': 'rtl' }, { 'align': [] }],
                ['link', 'image', 'video', 'formula'],
                ['clean']
            ],
            imageUploader: {
                url: '/upload-image',
                csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        },
        placeholder: 'Escreva aqui...',
        theme: 'snow'
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

    // Inicializa o editor Quill com as opções
    return new Quill(elementId, mergedOptions);
}

// Exporta para uso global
window.initQuillWithImageUpload = initQuillWithImageUpload;
