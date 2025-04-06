<div>
    <div id="map-container" class="w-full h-64 mt-2 bg-stone-50 rounded border-1 border-gray-200 shadow-xs overflow-hidden">
        <gmp-map
            id="map-{{ $componentId }}"
            center="{{ $lat }},{{ $lng }}"
            zoom="15"
            map-id="75d0118fd3ecfce6"
            class="w-full h-full"
        >
            <gmp-advanced-marker id="marker-{{ $componentId }}" draggable></gmp-advanced-marker>
        </gmp-map>
    </div>
    <input type="hidden" wire:model="lat" id="lat-{{ $componentId }}">
    <input type="hidden" wire:model="lng" id="lng-{{ $componentId }}">
    <input type="hidden" wire:model="address" id="hidden-address-{{ $componentId }}">
</div>

<script>
    async function initMapAndPlacePicker(componentId) {
        await customElements.whenDefined('gmp-map');

        const map = document.querySelector('#map-' + componentId);
        const marker = document.querySelector('#marker-' + componentId);
        const placePicker = document.querySelector('#address');
        const hiddenAddressInput = document.querySelector('#hidden-address-' + componentId);
        const latInput = document.querySelector('#lat-' + componentId);
        const lngInput = document.querySelector('#lng-' + componentId);
        const infowindow = new google.maps.InfoWindow();

        if (!map || !marker || !placePicker) {
            console.warn('Elementos do mapa ou place picker não encontrados.');
            return;
        }

        map.innerMap.setOptions({
            mapTypeControl: true,
            streetViewControl: true,
            fullscreenControl: true,
            zoomControl: true
        });

        marker.addEventListener('dragend', (event) => {
            const position = marker.position;
            let latitude = typeof position.lat === 'function' ? position.lat() : position.lat;
            let longitude = typeof position.lng === 'function' ? position.lng() : position.lng;

            latInput.value = latitude;
            lngInput.value = longitude;
            window.Livewire.find(componentId).call('updateCoordinates', latitude, longitude, hiddenAddressInput.value);
            geocodePosition({ lat: latitude, lng: longitude });
        });

        function geocodePosition(pos) {
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({ location: pos }, (results, status) => {
                if (status === google.maps.GeocoderStatus.OK && results[0]) {
                    const address = results[0].formatted_address;
                    hiddenAddressInput.value = address;
                    window.Livewire.find(componentId).call('updateCoordinates', pos.lat, pos.lng, address);
                    infowindow.setContent(
                        `<strong>Localização selecionada</strong><br>
                         <span>${address}</span><br>
                         <small>Lat: ${pos.lat.toFixed(6)}, Lng: ${pos.lng.toFixed(6)}</small>`
                    );
                } else {
                    infowindow.setContent(
                        `<strong>Localização selecionada</strong><br>
                         <span>Endereço não disponível</span><br>
                         <small>Lat: ${pos.lat.toFixed(6)}, Lng: ${pos.lng.toFixed(6)}</small>`
                    );
                }
                infowindow.open(map.innerMap, marker);
            });
        }

        placePicker.addEventListener('gmpx-placechange', () => {
            const place = placePicker.value;

            if (!place || !place.location) {
                console.warn('Localização não disponível');
                infowindow.close();
                marker.position = null;
                return;
            }

            let latitude = typeof place.location.lat === 'function' ? place.location.lat() : place.location.lat;
            let longitude = typeof place.location.lng === 'function' ? place.location.lng() : place.location.lng;

            if (place.viewport) {
                map.innerMap.fitBounds(place.viewport);
            } else {
                map.center = place.location;
                map.zoom = 15;
            }

            marker.position = place.location;
            latInput.value = latitude;
            lngInput.value = longitude;
            hiddenAddressInput.value = place.formattedAddress || '';
            window.Livewire.find(componentId).call('updateCoordinates', latitude, longitude, place.formattedAddress || '');

            infowindow.setContent(
                `<strong>${place.displayName || 'Local selecionado'}</strong><br>
                 <span>${place.formattedAddress || ''}</span><br>
                 <small>Lat: ${latitude.toFixed(6)}, Lng: ${longitude.toFixed(6)}</small>`
            );
            infowindow.open(map.innerMap, marker);
        });

        const currentLat = parseFloat(latInput.value);
        const currentLng = parseFloat(lngInput.value);
        const currentAddress = hiddenAddressInput.value;

        if (currentLat && currentLng) {
            const location = { lat: currentLat, lng: currentLng };
            map.center = location;
            map.zoom = 15;
            marker.position = location;

            infowindow.setContent(
                `<strong>Localização atual</strong><br>
                 <span>${currentAddress || 'Endereço não disponível'}</span><br>
                 <small>Lat: ${currentLat.toFixed(6)}, Lng: ${currentLng.toFixed(6)}</small>`
            );
            infowindow.open(map.innerMap, marker);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        initMapAndPlacePicker('{{ $componentId }}');
    });
</script>