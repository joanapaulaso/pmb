@extends('admin.layouts.admin-layout')

@section('page-title', 'Editar Laboratório')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Editar Laboratório</h2>
        </div>

        <form action="{{ route('admin.laboratories.update', $laboratory) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <!-- Nome -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
                <input type="text" name="name" id="name" value="{{ old('name', $laboratory->name) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Instituição e Estado -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="institution_id" class="block text-sm font-medium text-gray-700">Instituição</label>
                    <select name="institution_id" id="institution_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        @foreach(\App\Models\Institution::all() as $institution)
                            <option value="{{ $institution->id }}" {{ old('institution_id', $laboratory->institution_id) == $institution->id ? 'selected' : '' }}>{{ $institution->name }}</option>
                        @endforeach
                    </select>
                    @error('institution_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="state_id" class="block text-sm font-medium text-gray-700">Estado</label>
                    <select name="state_id" id="state_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        @foreach(\App\Models\State::all() as $state)
                            <option value="{{ $state->id }}" {{ old('state_id', $laboratory->state_id) == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                        @endforeach
                    </select>
                    @error('state_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Descrição -->
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Descrição</label>
                @if($laboratory->team && $laboratory->team->description)
                    <span class="block text-sm text-gray-600 mb-2">
                        <strong>Descrição atual:</strong> {{ $laboratory->team->description }}
                    </span>
                @endif
                <textarea name="description" id="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $laboratory->team->description ?? $laboratory->description) }}</textarea>
                @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Website -->
            <div class="mb-4">
                <label for="website" class="block text-sm font-medium text-gray-700">Website</label>
                <input type="url" name="website" id="website" value="{{ old('website', $laboratory->team->website ?? $laboratory->website) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @error('website') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Endereço com Place Picker -->
            <div class="mb-4">
                <label for="address" class="block text-sm font-medium text-gray-700">Endereço</label>
                @if($laboratory->team && $laboratory->team->address)
                    <span class="block text-sm text-gray-600 mb-2">
                        <strong>Endereço atual:</strong> {{ $laboratory->team->address }}
                    </span>
                @elseif($laboratory->address)
                    <span class="block text-sm text-gray-600 mb-2">
                        <strong>Endereço atual:</strong> {{ $laboratory->address }}
                    </span>
                @endif
                <input type="hidden" id="hidden-address-input" name="address" value="{{ old('address', $laboratory->team->address ?? $laboratory->address) }}">
                <gmpx-place-picker
                    id="address"
                    placeholder="Digite o endereço do laboratório..."
                    class="mt-1 block w-full border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                ></gmpx-place-picker>
                @error('address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Mapa -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Localização no Mapa</label>
                <div id="map-container" class="w-full h-64 mt-2 bg-gray-100 rounded-lg shadow overflow-hidden">
                    <gmp-map
                        id="edit-map"
                        center="{{ $laboratory->team->latitude ?? $laboratory->lat ?? -16.3141633 }},{{ $laboratory->team->longitude ?? $laboratory->lng ?? -52.6125466 }}"
                        zoom="15"
                        map-id="75d0118fd3ecfce6"
                        class="w-full h-full"
                    >
                        <gmp-advanced-marker id="map-marker" draggable></gmp-advanced-marker>
                    </gmp-map>
                </div>
                <input type="hidden" name="lat" id="lat" value="{{ old('lat', $laboratory->team->latitude ?? $laboratory->lat) }}">
                <input type="hidden" name="lng" id="lng" value="{{ old('lng', $laboratory->team->longitude ?? $laboratory->lng) }}">
            </div>

            <!-- Campos adicionais -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="building" class="block text-sm font-medium text-gray-700">Prédio</label>
                    <input type="text" name="building" id="building" value="{{ old('building', $laboratory->team->building ?? $laboratory->building) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('building') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="floor" class="block text-sm font-medium text-gray-700">Andar</label>
                    <input type="text" name="floor" id="floor" value="{{ old('floor', $laboratory->team->floor ?? $laboratory->floor) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('floor') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="room" class="block text-sm font-medium text-gray-700">Sala</label>
                    <input type="text" name="room" id="room" value="{{ old('room', $laboratory->team->room ?? $laboratory->room) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('room') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="department" class="block text-sm font-medium text-gray-700">Departamento</label>
                    <input type="text" name="department" id="department" value="{{ old('department', $laboratory->team->department ?? $laboratory->department) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('department') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="campus" class="block text-sm font-medium text-gray-700">Campus</label>
                <input type="text" name="campus" id="campus" value="{{ old('campus', $laboratory->team->campus ?? $laboratory->campus) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @error('campus') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Telefone</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $laboratory->team->phone ?? $laboratory->phone) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="contact_email" class="block text-sm font-medium text-gray-700">Email de Contato</label>
                    <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email', $laboratory->team->contact_email ?? $laboratory->contact_email) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('contact_email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="working_hours" class="block text-sm font-medium text-gray-700">Horário de Funcionamento</label>
                <input type="text" name="working_hours" id="working_hours" value="{{ old('working_hours', $laboratory->team->working_hours ?? $laboratory->working_hours) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @error('working_hours') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="has_accessibility" id="has_accessibility" value="1" {{ old('has_accessibility', $laboratory->team->has_accessibility ?? $laboratory->has_accessibility) ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">Possui Acessibilidade</span>
                </label>
                @error('has_accessibility') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Logo -->
            <div class="mb-4">
                <label for="logo" class="block text-sm font-medium text-gray-700">Logo</label>
                <input type="file" name="logo" id="logo" class="mt-1 block w-full">
                @if($laboratory->team && $laboratory->team->logo)
                    <img src="{{ Storage::url($laboratory->team->logo) }}" alt="Logo" class="mt-2 h-20">
                @elseif($laboratory->logo)
                    <img src="{{ Storage::url($laboratory->logo) }}" alt="Logo" class="mt-2 h-20">
                @endif
                @error('logo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Botões -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.laboratories.index') }}" class="px-4 py-2 text-sm text-gray-700 bg-gray-200 rounded-md">Cancelar</a>
                <button type="submit" class="px-4 py-2 text-sm text-white bg-indigo-600 rounded-md">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script>
    async function initMapAndPlacePicker() {
        await customElements.whenDefined('gmp-map');

        const map = document.querySelector('#edit-map');
        const marker = document.querySelector('#map-marker');
        const placePicker = document.querySelector('#address');
        const hiddenAddressInput = document.querySelector('#hidden-address-input');
        const latInput = document.querySelector('#lat');
        const lngInput = document.querySelector('#lng');
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

        // Adicionar ouvinte para arrastar o marcador
        marker.addEventListener('dragend', (event) => {
            const position = marker.position;
            let latitude, longitude;

            if (typeof position.lat === 'function') {
                latitude = position.lat();
                longitude = position.lng();
            } else {
                latitude = position.lat;
                longitude = position.lng;
            }

            latInput.value = latitude;
            lngInput.value = longitude;

            geocodePosition({ lat: latitude, lng: longitude });
        });

        // Função para geocodificar a posição e atualizar o endereço
        function geocodePosition(pos) {
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({ location: pos }, (results, status) => {
                if (status === google.maps.GeocoderStatus.OK && results[0]) {
                    const address = results[0].formatted_address;
                    hiddenAddressInput.value = address;
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

        // Evento de mudança no Place Picker
        placePicker.addEventListener('gmpx-placechange', () => {
            const place = placePicker.value;

            if (!place || !place.location) {
                console.warn('Localização não disponível');
                infowindow.close();
                marker.position = null;
                return;
            }

            let latitude, longitude;
            if (typeof place.location.lat === 'function') {
                latitude = place.location.lat();
                longitude = place.location.lng();
            } else {
                latitude = place.location.lat;
                longitude = place.location.lng;
            }

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

            infowindow.setContent(
                `<strong>${place.displayName || 'Local selecionado'}</strong><br>
                 <span>${place.formattedAddress || ''}</span><br>
                 <small>Lat: ${latitude.toFixed(6)}, Lng: ${longitude.toFixed(6)}</small>`
            );
            infowindow.open(map.innerMap, marker);
        });

        // Inicializar com valores existentes, se houver
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
        console.log('Inicializando mapa e place picker');
        initMapAndPlacePicker();
    });
</script>
@endsection
