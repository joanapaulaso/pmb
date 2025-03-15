<div>
    <x-form-address-section submit="updateDescription">
        <x-slot name="title">
            {{ __('Descrição') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Atualize a descrição do laboratório.') }}
        </x-slot>

        <x-slot name="form">
            <div class="col-span-6">
                <!-- Descrição Atual -->
                @if($description)
                    <p class="text-sm text-gray-600 mb-4">
                        <strong>Descrição atual:</strong> {{ $description }}
                    </p>
                @endif

                <!-- Campo de Descrição -->
                <div>
                    <x-label for="description" value="{{ __('Descrição') }}" />
                    <textarea
                        id="description"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        rows="5"
                        wire:model.defer="description"
                        placeholder="{{ __('Digite a descrição do laboratório...') }}"
                    ></textarea>
                    @error('description') <x-input-error for="description" class="mt-2" /> @enderror
                </div>
            </div>
        </x-slot>

        <x-slot name="actions">
            @can('updateDescription', $team)
                <x-button wire:loading.attr="disabled" wire:target="updateDescription">
                    {{ __('Salvar') }}
                </x-button>
            @else
                <p class="text-sm text-gray-500">Apenas o coordenador pode atualizar a descrição.</p>
            @endcan
        </x-slot>
    </x-form-address-section>
</div>
