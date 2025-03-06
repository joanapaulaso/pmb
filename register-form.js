// resources/js/register-form.js

function initRegisterForm() {
    function initAutocomplete() {
        const input = document.getElementById('institution-address');
        if (input && window.google && window.google.maps && window.google.maps.places) {
            console.log('Inicializando autocomplete para #institution-address:', input);

            const autocomplete = new google.maps.places.Autocomplete(input, {
                // Remova ou ajuste 'types' para permitir mais tipos de lugares
                fields: ['formatted_address', 'geometry', 'name'],
                componentRestrictions: { country: 'br' }
            });

            autocomplete.addListener('place_changed', () => {
                const place = autocomplete.getPlace();
                console.log('Detalhes do lugar:', place);
                if (place && place.formatted_address) {
                    Livewire.dispatch('setInstitutionAddress', { address: place.formatted_address });
                    console.log('Endereço/Lugar selecionado:', {
                        formatted_address: place.formatted_address,
                        name: place.name,
                        place_id: place.place_id
                    });
                } else {
                    console.warn('Nenhum lugar selecionado ou erro no autocomplete.');
                }
            });
        } else {
            console.warn('Google Maps Places API ou elemento #institution-address não encontrado.');
        }
    }

    // Função para verificar periodicamente o elemento
    function checkForElement() {
        const input = document.getElementById('institution-address');
        if (input) {
            initAutocomplete();
            observer.disconnect(); // Parar de observar após encontrar o elemento
            clearInterval(checkInterval); // Parar o intervalo após encontrar o elemento
        }
    }

    // MutationObserver para monitorar mudanças no DOM (focar no container do Livewire)
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.addedNodes.length) {
                checkForElement();
            }
        });
    });

    // Iniciar a observação do DOM, focando no container do Livewire, se possível
    const livewireContainer = document.querySelector('div[id^="lw-"]'); // Selecionar container do Livewire
    if (livewireContainer) {
        observer.observe(livewireContainer, { childList: true, subtree: true });
    } else {
        observer.observe(document.body, { childList: true, subtree: true }); // Fallback para o body
    }

    // Verificar periodicamente o elemento com intervalo ajustado
    let checkInterval = setInterval(checkForElement, 250); // Aumentar para 250ms para evitar sobrecarga

    // Limpeza do observer e intervalo quando a página for descarregada
    window.addEventListener('unload', () => {
        observer.disconnect();
        clearInterval(checkInterval);
    });

    // Tentar inicializar imediatamente, se o elemento já existir
    if (document.getElementById('institution-address')) {
        initAutocomplete();
    }

    // Observar atualizações do Livewire para reinicializar o autocomplete
    Livewire.hook('element.updated', (el, component) => {
        if (el.id === 'institution-address') {
            if (window.google && window.google.maps && window.google.maps.places) {
                google.maps.event.clearInstanceListeners(el);
            }
            initAutocomplete();
        }
    });

    // Ouvinte para o evento institutionAddressUpdated
    Livewire.on('institutionAddressUpdated', (data) => {
        console.log('Endereço atualizado no Livewire:', data.address);
    });
}

function loadGoogleMaps() {
    if (!window.google || !window.google.maps) {
        const script = document.createElement('script');
        script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyD4xIxoKPy81-hrL8IXLqhQoMmtQoXqVLY&libraries=places&loading=async';
        script.async = true;
        script.onload = () => {
            console.log('Google Maps API carregado com sucesso.');
            initRegisterForm();
        };
        script.onerror = () => console.error('Erro ao carregar Google Maps API.');
        document.head.appendChild(script);
    } else {
        initRegisterForm();
    }
}

console.log('DOM verificado para #institution-address:', document.getElementById('institution-address'));
console.log('Google Maps Places API disponível:', window.google && window.google.maps && window.google.maps.places);
console.log('Container Livewire encontrado:', document.querySelector('div[id^="lw-"]'));
console.log('Elemento #institution-address encontrado:', document.getElementById('institution-address'));

loadGoogleMaps();
