<div x-data="{ showAddress: @entangle('showNewInstitution').defer }">
    <x-form-address-section submit="updateInstitutionAddress">
        <x-slot name="title">
            {{ __('Endereço do Laboratório') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Atualize o endereço do laboratório.') }}
        </x-slot>

        <x-slot name="form">
            <div class="col-span-6 sm:col-span-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="place-picker-container p-4 bg-white rounded-lg shadow">
                        <input
                            type="hidden"
                            id="hidden-address-input"
                            wire:model="institution_address"
                        />

                        <gmpx-place-picker
                            id="institution-address"
                            placeholder="{{ __('Digite o endereço do laboratório...') }}"
                            class="w-full border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            @if(!auth()->user()->can('updateAddress', $team)) readonly @endif
                        ></gmpx-place-picker>

                        @if ($errors->has('institution_address'))
                            <x-input-error for="institution_address" class="mt-2" />
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
                            <gmp-advanced-marker id="map-marker"></gmp-advanced-marker>
                        </gmp-map>
                    </div>
                </div>

                @if($institution_address)
                    <p class="mt-4 text-sm text-gray-600">
                        Endereço atual: {{ $institution_address }}
                    </p>
                @endif
            </div>
        </x-slot>

        <x-slot name="actions">
            @can('updateAddress', $team)
                <x-button wire:loading.attr="disabled" wire:target="updateInstitutionAddress">
                    {{ __('Salvar') }}
                </x-button>
            @else
                <p class="text-sm text-gray-500">Apenas o coordenador pode atualizar o endereço.</p>
            @endcan
        </x-slot>
    </x-form-address-section>

    <script>
        async function initMapAndPlacePicker() {
            await customElements.whenDefined('gmp-map');

            const map = document.querySelector('#map');
            const marker = document.querySelector('#map-marker');
            const placePicker = document.querySelector('#institution-address');
            const hiddenInput = document.querySelector('#hidden-address-input');
            const infowindow = new google.maps.InfoWindow();

            if (!map || !marker || !placePicker) {
                console.warn('Elementos do mapa ou place picker não encontrados.');
                return;
            }

            map.innerMap.setOptions({
                mapTypeControl: false
            });

            placePicker.addEventListener('gmpx-placechange', () => {
                const place = placePicker.value;

                console.log('Dados completos do place:', place);

                if (!place || !place.location) {
                    console.warn('Localização não disponível');
                    infowindow.close();
                    marker.position = null;
                    return;
                }

                // Atualizar o mapa
                if (place.viewport) {
                    map.innerMap.fitBounds(place.viewport);
                } else {
                    map.center = place.location;
                    map.zoom = 17;
                }

                marker.position = place.location;

                // Mostrar informações no mapa
                infowindow.setContent(
                    `<strong>${place.displayName || 'Local selecionado'}</strong><br>
                     <span>${place.formattedAddress || ''}</span>`
                );
                infowindow.open(map.innerMap, marker);

                // Atualizar o input hidden com o endereço formatado
                if (hiddenInput && place.formattedAddress) {
                    hiddenInput.value = place.formattedAddress;
                    hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));

                    console.log('Endereço atualizado:', place.formattedAddress);
                }

                // CORREÇÃO: Passar um objeto em vez de uma string diretamente
                if (window.Livewire) {
                    window.Livewire.dispatch('setInstitutionAddressFromPlacePicker', {
                        address: place.formattedAddress
                    });
                    console.log('Evento Livewire disparado com objeto:', { address: place.formattedAddress });
                }
            });

            // Se já existe um endereço, tentar inicializar o mapa
            const currentAddress = '{{ $institution_address }}';
            if (currentAddress && currentAddress.trim() !== '') {
                const geocoder = new google.maps.Geocoder();
                geocoder.geocode({ 'address': currentAddress }, function(results, status) {
                    if (status === google.maps.GeocoderStatus.OK && results[0]) {
                        const location = results[0].geometry.location;
                        map.center = location;
                        map.zoom = 17;
                        marker.position = location;

                        infowindow.setContent(`<strong>Endereço atual</strong><br><span>${currentAddress}</span>`);
                        infowindow.open(map.innerMap, marker);
                    }
                });
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            console.log('Inicializando mapa e place picker');
            initMapAndPlacePicker();
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
