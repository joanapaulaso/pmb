<div x-data="{ showAddress: @entangle('showNewInstitution').defer, showAdditionalInfo: false }">
    <x-form-address-section submit="updateInstitutionAddress">
        <x-slot name="title">
            {{ __('Endereço do Laboratório') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Atualize o endereço e informações adicionais do laboratório.') }}
        </x-slot>

        <x-slot name="form">
            <div class="col-span-6 sm:col-span-4">
                <!-- Seção de Endereço Principal com Google Maps -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="place-picker-container p-4 bg-white rounded-lg shadow">
                        <input
                            type="hidden"
                            id="hidden-address-input"
                            wire:model="institution_address"
                        />

                        <!-- Campos ocultos para coordenadas -->
                        <input type="hidden" id="hidden-latitude-input" wire:model="latitude">
                        <input type="hidden" id="hidden-longitude-input" wire:model="longitude">

                        <gmpx-place-picker
                            id="institution-address"
                            placeholder="{{ __('Digite o endereço do laboratório...') }}"
                            class="w-full border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            @if(!auth()->user()->can('updateAddress', $team)) readonly @endif
                        ></gmpx-place-picker>

                        @if ($errors->has('institution_address'))
                            <x-input-error for="institution_address" class="mt-2" />
                        @endif

                        <!-- Mostrar coordenadas atuais -->
                        @if($latitude && $longitude)
                        <p class="mt-2 text-xs text-gray-500">
                            Coordenadas: {{ $latitude }}, {{ $longitude }}
                        </p>
                        @endif
                    </div>

                    <div wire:ignore class="map-container h-96 bg-gray-100 rounded-lg shadow overflow-hidden">
                        <gmp-map
                            id="map"
                            center="-16.3141633,-52.6125466"
                            zoom="4"
                            map-id="75d0118fd3ecfce6"
                            class="w-full h-full"
                        >
                            <gmp-advanced-marker id="map-marker" draggable></gmp-advanced-marker>
                        </gmp-map>
                    </div>
                </div>

                @if($institution_address)
                    <p class="text-sm text-gray-600 mb-4">
                        <strong>Endereço atual:</strong> {{ $institution_address }}
                    </p>
                @endif

                <!-- Botão para exibir/ocultar informações adicionais -->
                <div class="py-3">
                    <button
                        type="button"
                        class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center"
                        x-on:click="showAdditionalInfo = !showAdditionalInfo"
                    >
                        <span x-show="!showAdditionalInfo">&#9654; Mostrar informações adicionais</span>
                        <span x-show="showAdditionalInfo">&#9660; Ocultar informações adicionais</span>
                    </button>
                </div>

                <!-- Informações Adicionais de Localização -->
                <div x-show="showAdditionalInfo" x-transition class="bg-gray-50 p-5 rounded-lg border border-gray-200 mt-2">
                    <!-- Localização Física -->
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Localização Física') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <x-label for="building" value="{{ __('Prédio/Bloco') }}" />
                            <x-input id="building" type="text" class="mt-1 block w-full" wire:model="building" />
                            @if ($errors->has('building'))
                                <x-input-error for="building" class="mt-2" />
                            @endif
                        </div>

                        <div>
                            <x-label for="floor" value="{{ __('Andar') }}" />
                            <x-input id="floor" type="text" class="mt-1 block w-full" wire:model="floor" />
                            @if ($errors->has('floor'))
                                <x-input-error for="floor" class="mt-2" />
                            @endif
                        </div>

                        <div>
                            <x-label for="room" value="{{ __('Sala') }}" />
                            <x-input id="room" type="text" class="mt-1 block w-full" wire:model="room" />
                            @if ($errors->has('room'))
                                <x-input-error for="room" class="mt-2" />
                            @endif
                        </div>
                    </div>

                    <!-- Organização -->
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Organização') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <x-label for="department" value="{{ __('Departamento/Setor') }}" />
                            <x-input id="department" type="text" class="mt-1 block w-full" wire:model="department" />
                            @if ($errors->has('department'))
                                <x-input-error for="department" class="mt-2" />
                            @endif
                        </div>

                        <div>
                            <x-label for="campus" value="{{ __('Campus') }}" />
                            <x-input id="campus" type="text" class="mt-1 block w-full" wire:model="campus" />
                            @if ($errors->has('campus'))
                                <x-input-error for="campus" class="mt-2" />
                            @endif
                        </div>
                    </div>

                    <!-- Informações de Contato -->
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Informações de Contato') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <x-label for="phone" value="{{ __('Telefone') }}" />
                            <x-input id="phone" type="text" class="mt-1 block w-full" wire:model="phone" />
                            @if ($errors->has('phone'))
                                <x-input-error for="phone" class="mt-2" />
                            @endif
                        </div>

                        <div>
                            <x-label for="contact_email" value="{{ __('Email de Contato') }}" />
                            <x-input id="contact_email" type="email" class="mt-1 block w-full" wire:model="contact_email" />
                            @if ($errors->has('contact_email'))
                                <x-input-error for="contact_email" class="mt-2" />
                            @endif
                        </div>

                        <div>
                            <x-label for="contact_person" value="{{ __('Pessoa de Contato') }}" />
                            <x-input id="contact_person" type="text" class="mt-1 block w-full" wire:model="contact_person" />
                            @if ($errors->has('contact_person'))
                                <x-input-error for="contact_person" class="mt-2" />
                            @endif
                        </div>
                    </div>

                    <!-- Informações Complementares -->
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Informações Complementares') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <x-label for="complement" value="{{ __('Complemento') }}" />
                            <x-input id="complement" type="text" class="mt-1 block w-full" wire:model="complement" />
                            @if ($errors->has('complement'))
                                <x-input-error for="complement" class="mt-2" />
                            @endif
                        </div>

                        <div>
                            <x-label for="reference_point" value="{{ __('Ponto de Referência') }}" />
                            <x-input id="reference_point" type="text" class="mt-1 block w-full" wire:model="reference_point" />
                            @if ($errors->has('reference_point'))
                                <x-input-error for="reference_point" class="mt-2" />
                            @endif
                        </div>

                        <div>
                            <x-label for="postal_code" value="{{ __('CEP') }}" />
                            <x-input id="postal_code" type="text" class="mt-1 block w-full" wire:model="postal_code" />
                            @if ($errors->has('postal_code'))
                                <x-input-error for="postal_code" class="mt-2" />
                            @endif
                        </div>
                    </div>

                    <!-- Informações Operacionais -->
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Informações Operacionais') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <x-label for="working_hours" value="{{ __('Horário de Funcionamento') }}" />
                            <x-input id="working_hours" type="text" class="mt-1 block w-full" wire:model="working_hours" placeholder="Ex: Segunda a Sexta, 8h às 18h" />
                            @if ($errors->has('working_hours'))
                                <x-input-error for="working_hours" class="mt-2" />
                            @endif
                        </div>

                        <div>
                            <x-label for="website" value="{{ __('Site') }}" />
                            <x-input id="website" type="url" class="mt-1 block w-full" wire:model="website" placeholder="https://example.com" />
                            @if ($errors->has('website'))
                                <x-input-error for="website" class="mt-2" />
                            @endif
                        </div>
                    </div>

                    <!-- Acessibilidade -->
                    <div class="flex items-center mb-6">
                        <input id="has_accessibility" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" wire:model="has_accessibility">
                        <label for="has_accessibility" class="ml-2 text-sm text-gray-600">{{ __('O local possui acessibilidade para pessoas com deficiência') }}</label>
                        @if ($errors->has('has_accessibility'))
                            <x-input-error for="has_accessibility" class="mt-2" />
                        @endif
                    </div>

                    <!-- Observações -->
                    <div>
                        <x-label for="address_notes" value="{{ __('Observações sobre o endereço/localização') }}" />
                        <textarea id="address_notes" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3" wire:model="address_notes" placeholder="Informações adicionais sobre como encontrar o laboratório, detalhes de acesso, etc."></textarea>
                        @if ($errors->has('address_notes'))
                            <x-input-error for="address_notes" class="mt-2" />
                        @endif
                    </div>
                </div>
            </div>
        </x-slot>

        <x-slot name="actions">
            @can('updateAddress', $team)
                <x-button wire:loading.attr="disabled" wire:target="updateInstitutionAddress">
                    {{ __('Salvar') }}
                </x-button>
            @else
                <p class="text-sm text-gray-500">Apenas o coordenador pode atualizar as informações de endereço.</p>
            @endcan
        </x-slot>
    </x-form-address-section>

    <script>
        async function initMapAndPlacePicker() {
            await customElements.whenDefined('gmp-map');

            const map = document.querySelector('#map');
            const marker = document.querySelector('#map-marker');
            const placePicker = document.querySelector('#institution-address');
            const hiddenAddressInput = document.querySelector('#hidden-address-input');
            const hiddenLatInput = document.querySelector('#hidden-latitude-input');
            const hiddenLngInput = document.querySelector('#hidden-longitude-input');
            const infowindow = new google.maps.InfoWindow();

            if (!map || !marker || !placePicker) {
                console.warn('Elementos do mapa ou place picker não encontrados.');
                return;
            }

            map.innerMap.setOptions({
                mapTypeControl: true,
                streetViewControl: true
            });

            // Adicionar um ouvinte para o evento de arrastar o marcador
            marker.addEventListener('dragend', (event) => {
                const position = marker.position;
                let latitude, longitude;

                try {
                    // Verificar se position.lat é uma função ou propriedade
                    if (typeof position.lat === 'function') {
                        latitude = position.lat();
                        longitude = position.lng();
                    } else {
                        latitude = position.lat;
                        longitude = position.lng;
                    }

                    console.log('Marcador arrastado para:', { lat: latitude, lng: longitude });

                    // Atualizar os inputs escondidos
                    if (hiddenLatInput && hiddenLngInput) {
                        hiddenLatInput.value = latitude;
                        hiddenLngInput.value = longitude;

                        // Disparar eventos para atualizar o modelo Livewire
                        hiddenLatInput.dispatchEvent(new Event('input', { bubbles: true }));
                        hiddenLngInput.dispatchEvent(new Event('input', { bubbles: true }));

                        // Notificar o Livewire diretamente
                        if (window.Livewire) {
                            window.Livewire.dispatch('setCoordinatesFromMap', {
                                lat: latitude,
                                lng: longitude
                            });
                        }
                    }

                    // Atualizar o infowindow
                    geocodePosition({ lat: latitude, lng: longitude });
                } catch (error) {
                    console.error('Erro ao processar posição do marcador:', error);
                    console.log('Estrutura do objeto position:', position);
                }
            });

            // Função para geocodificar uma posição e atualizar o infowindow
            function geocodePosition(pos) {
                const geocoder = new google.maps.Geocoder();
                geocoder.geocode({
                    location: pos
                }, function(results, status) {
                    if (status === google.maps.GeocoderStatus.OK && results[0]) {
                        const address = results[0].formatted_address;

                        // Atualizar infowindow
                        infowindow.setContent(
                            `<strong>Localização selecionada</strong><br>
                             <span>${address}</span><br>
                             <small>Lat: ${pos.lat.toFixed(6)}, Lng: ${pos.lng.toFixed(6)}</small>`
                        );
                        infowindow.open(map.innerMap, marker);

                        // Atualizar também o input de endereço (opcional)
                        if (hiddenAddressInput) {
                            hiddenAddressInput.value = address;
                            hiddenAddressInput.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                    } else {
                        infowindow.setContent(
                            `<strong>Localização selecionada</strong><br>
                             <span>Endereço não disponível</span><br>
                             <small>Lat: ${pos.lat.toFixed(6)}, Lng: ${pos.lng.toFixed(6)}</small>`
                        );
                        infowindow.open(map.innerMap, marker);
                    }
                });
            }

            placePicker.addEventListener('gmpx-placechange', () => {
                const place = placePicker.value;

                console.log('Dados completos do place:', place);

                if (!place || !place.location) {
                    console.warn('Localização não disponível');
                    infowindow.close();
                    marker.position = null;
                    return;
                }

                // Obter as coordenadas de forma segura
                let latitude, longitude;

                try {
                    // Verificar o tipo de objeto da localização
                    if (typeof place.location.lat === 'function') {
                        // Caso a latitude e longitude sejam funções (como no Google Maps API v3)
                        latitude = place.location.lat();
                        longitude = place.location.lng();
                    } else {
                        // Caso seja um objeto com propriedades numéricas
                        latitude = place.location.lat;
                        longitude = place.location.lng;
                    }

                    console.log('Coordenadas extraídas:', { lat: latitude, lng: longitude });

                    // Atualizar o mapa
                    if (place.viewport) {
                        map.innerMap.fitBounds(place.viewport);
                    } else {
                        map.center = place.location;
                        map.zoom = 17;
                    }

                    marker.position = place.location;

                    // Atualizar os inputs ocultos de coordenadas
                    if (hiddenLatInput && hiddenLngInput) {
                        hiddenLatInput.value = latitude;
                        hiddenLngInput.value = longitude;

                        // Disparar eventos para atualizar o modelo Livewire
                        hiddenLatInput.dispatchEvent(new Event('input', { bubbles: true }));
                        hiddenLngInput.dispatchEvent(new Event('input', { bubbles: true }));
                    }

                    // Mostrar informações no mapa
                    infowindow.setContent(
                        `<strong>${place.displayName || 'Local selecionado'}</strong><br>
                        <span>${place.formattedAddress || ''}</span><br>
                        <small>Lat: ${latitude.toFixed(6)}, Lng: ${longitude.toFixed(6)}</small>`
                    );
                    infowindow.open(map.innerMap, marker);

                    // Atualizar o input hidden com o endereço formatado
                    if (hiddenAddressInput && place.formattedAddress) {
                        hiddenAddressInput.value = place.formattedAddress;
                        hiddenAddressInput.dispatchEvent(new Event('input', { bubbles: true }));

                        console.log('Endereço atualizado:', place.formattedAddress);
                    }

                    // Notificar o Livewire sobre a mudança
                    if (window.Livewire) {
                        window.Livewire.dispatch('setInstitutionAddressFromPlacePicker', {
                            address: place.formattedAddress,
                            latitude: latitude,
                            longitude: longitude
                        });
                        console.log('Evento Livewire disparado com objeto:', {
                            address: place.formattedAddress,
                            latitude: latitude,
                            longitude: longitude
                        });
                    }
                } catch (error) {
                    console.error('Erro ao extrair coordenadas:', error);
                    console.log('Estrutura do objeto place.location:', place.location);
                }
            });

            // Se já existe um endereço e coordenadas, inicializar o mapa
            const currentLat = '{{ $latitude }}';
            const currentLng = '{{ $longitude }}';
            const currentAddress = '{{ $institution_address }}';

            if (currentLat && currentLng && currentLat != '0' && currentLng != '0') {
                // Se temos coordenadas, usá-las diretamente
                const location = {
                    lat: parseFloat(currentLat),
                    lng: parseFloat(currentLng)
                };

                map.center = location;
                map.zoom = 17;
                marker.position = location;

                infowindow.setContent(
                    `<strong>Localização atual</strong><br>
                     <span>${currentAddress || 'Endereço não disponível'}</span><br>
                     <small>Lat: ${location.lat.toFixed(6)}, Lng: ${location.lng.toFixed(6)}</small>`
                );
                infowindow.open(map.innerMap, marker);
            } else if (currentAddress && currentAddress.trim() !== '') {
                // Se temos apenas o endereço, geocodificar
                const geocoder = new google.maps.Geocoder();
                geocoder.geocode({ 'address': currentAddress }, function(results, status) {
                    if (status === google.maps.GeocoderStatus.OK && results[0]) {
                        const location = results[0].geometry.location;

                        map.center = location;
                        map.zoom = 17;
                        marker.position = location;

                        // Atualizar os inputs ocultos de coordenadas
                        if (hiddenLatInput && hiddenLngInput) {
                            hiddenLatInput.value = location.lat();
                            hiddenLngInput.value = location.lng();

                            // Disparar eventos para atualizar o modelo Livewire
                            hiddenLatInput.dispatchEvent(new Event('input', { bubbles: true }));
                            hiddenLngInput.dispatchEvent(new Event('input', { bubbles: true }));

                            // Notificar o Livewire sobre as coordenadas
                            if (window.Livewire) {
                                window.Livewire.dispatch('setCoordinatesFromMap', {
                                    lat: location.lat(),
                                    lng: location.lng()
                                });
                            }
                        }

                        infowindow.setContent(
                            `<strong>Endereço atual</strong><br>
                             <span>${currentAddress}</span><br>
                             <small>Lat: ${location.lat().toFixed(6)}, Lng: ${location.lng().toFixed(6)}</small>`
                        );
                        infowindow.open(map.innerMap, marker);
                    }
                });
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            console.log('Inicializando mapa e place picker');
            initMapAndPlacePicker();
        });

            // Script para debug do botão salvar
    document.addEventListener('DOMContentLoaded', function() {
        const saveButton = document.querySelector('button[wire\\:target="updateInstitutionAddress"]');
        if (saveButton) {
            console.log('Botão salvar encontrado, adicionando listener');
            saveButton.addEventListener('click', function() {
                console.log('Botão salvar clicado!');
                console.log('Valores dos inputs no momento do clique:');
                console.log('Endereço:', document.querySelector('#hidden-address-input')?.value);
                console.log('Latitude:', document.querySelector('#hidden-latitude-input')?.value);
                console.log('Longitude:', document.querySelector('#hidden-longitude-input')?.value);
            });
        } else {
            console.warn('Botão salvar não encontrado!');
        }

        // Monitorar as mudanças nos inputs ocultos
        const hiddenLatInput = document.querySelector('#hidden-latitude-input');
        const hiddenLngInput = document.querySelector('#hidden-longitude-input');

        if (hiddenLatInput) {
            const originalSetAttribute = hiddenLatInput.setAttribute;
            hiddenLatInput.setAttribute = function(name, value) {
                console.log(`Latitude input attribute ${name} sendo definido para`, value);
                return originalSetAttribute.call(this, name, value);
            };

            const inputHandler = function(e) {
                console.log('Latitude input alterado para:', e.target.value);
            };
            hiddenLatInput.addEventListener('input', inputHandler);
        }

        if (hiddenLngInput) {
            const originalSetAttribute = hiddenLngInput.setAttribute;
            hiddenLngInput.setAttribute = function(name, value) {
                console.log(`Longitude input attribute ${name} sendo definido para`, value);
                return originalSetAttribute.call(this, name, value);
            };

            const inputHandler = function(e) {
                console.log('Longitude input alterado para:', e.target.value);
            };
            hiddenLngInput.addEventListener('input', inputHandler);
        }
    });
    </script>

    <style>
        .place-picker-container, .map-container {
            @apply p-4 bg-white rounded-lg shadow;
        }
        .map-container {
            @apply h-96 overflow-hidden;
        }
    </style>
</div>
