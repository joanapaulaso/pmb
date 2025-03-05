// resources/js/register-form.js

function initRegisterForm() {
    // Limpeza do dropdown de subcategorias
    Livewire.on('subcategoryAdded', () => {
        const select = document.getElementById('subcategory-select');
        if (select) {
            select.selectedIndex = 0;
        }
    });

    // Função para inicializar o autocomplete
    function initAutocomplete() {
        const input = document.getElementById('institution-address');
        if (input && window.google && window.google.maps && window.google.maps.places) {
            const autocomplete = new google.maps.places.Autocomplete(input, {
                types: ['address'],
                fields: ['formatted_address'],
            });

            autocomplete.addListener('place_changed', () => {
                const place = autocomplete.getPlace();
                if (place && place.formatted_address) {
                    Livewire.dispatch('setInstitutionAddress', { address: place.formatted_address });
                }
            });
        } else {
            console.warn('Google Maps Places API ou elemento #institution-address não encontrado.');
        }
    }

    // Hook para inicializar o autocomplete quando o elemento for atualizado
    Livewire.hook('element.updated', (el, component) => {
        if (el.id === 'institution-address') {
            initAutocomplete();
        }
    });

    // Tentar inicializar ao carregar, se o elemento já estiver presente
    if (document.getElementById('institution-address')) {
        initAutocomplete();
    }
}

// Carregar o Google Maps API dinamicamente
// function loadGoogleMaps() {
//     if (!window.google || !window.google.maps) {
//         const script = document.createElement('script');
//         script.src = 'https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places&loading=async';
//         script.async = true;
//         script.onload = () => {
//             console.log('Google Maps API carregado com sucesso.');
//             initRegisterForm();
//         };
//         script.onerror = () => console.error('Erro ao carregar Google Maps API.');
//         document.head.appendChild(script);
//     } else {
//         initRegisterForm();
//     }
// }

// // Iniciar o carregamento
// loadGoogleMaps();
