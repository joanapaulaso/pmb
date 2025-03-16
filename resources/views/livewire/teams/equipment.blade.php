<div>
    <x-form-address-section submit="saveEquipments">
        <x-slot name="title">
            {{ __('Equipamentos do Laboratório') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Adicione ou edite informações sobre os equipamentos do laboratório.') }}
        </x-slot>

        <x-slot name="form">
            <div class="col-span-6">
                @foreach($equipments as $index => $equipment)
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200" wire:key="equipment-{{ $index }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Modelo -->
                            <div>
                                <x-label for="equipments.{{ $index }}.model" value="{{ __('Modelo') }}" />
                                <x-input
                                    id="equipments.{{ $index }}.model"
                                    type="text"
                                    class="mt-1 block w-full"
                                    wire:model.defer="equipments.{{ $index }}.model"
                                />
                                @error("equipments.{$index}.model")
                                    <x-input-error for="equipments.{$index}.model" class="mt-2" />
                                @enderror
                            </div>

                            <!-- Marca -->
                            <div>
                                <x-label for="equipments.{{ $index }}.brand" value="{{ __('Marca') }}" />
                                <x-input
                                    id="equipments.{{ $index }}.brand"
                                    type="text"
                                    class="mt-1 block w-full"
                                    wire:model.defer="equipments.{{ $index }}.brand"
                                />
                                @error("equipments.{$index}.brand")
                                    <x-input-error for="equipments.{$index}.brand" class="mt-2" />
                                @enderror
                            </div>

                            <!-- Responsável Técnico -->
                            <div>
                                <x-label for="equipments.{{ $index }}.technical_responsible" value="{{ __('Responsável Técnico') }}" />
                                <x-input
                                    id="equipments.{{ $index }}.technical_responsible"
                                    type="text"
                                    class="mt-1 block w-full"
                                    wire:model.defer="equipments.{{ $index }}.technical_responsible"
                                />
                                @error("equipments.{$index}.technical_responsible")
                                    <x-input-error for="equipments.{$index}.technical_responsible" class="mt-2" />
                                @enderror
                            </div>

                            <!-- Foto -->
                            <div>
                                <x-label for="equipments.{{ $index }}.photo" value="{{ __('Foto do Equipamento') }}" />
                                <input
                                    id="equipments.{{ $index }}.photo"
                                    type="file"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                    wire:model="equipments.{{ $index }}.photo"
                                />
                                @if($equipment['photo_path'])
                                    <div class="mt-2">
                                        <img src="{{ Storage::url($equipment['photo_path']) }}" alt="{{ $equipment['model'] }}" class="h-20 w-auto rounded">
                                    </div>
                                @endif
                                @error("equipments.{$index}.photo")
                                    <x-input-error for="equipments.{$index}.photo" class="mt-2" />
                                @enderror
                            </div>
                        </div>

                        <!-- Disponibilidade -->
                        <div class="mt-4">
                            <p class="text-sm font-medium text-gray-700">{{ __('Disponível para:') }}</p>
                            <div class="flex flex-col space-y-2 mt-2">
                                <!-- Prestação de Serviços -->
                                <div class="flex items-center">
                                    <input
                                        id="equipments.{{ $index }}.available_for_services"
                                        type="checkbox"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        wire:model.defer="equipments.{{ $index }}.available_for_services"
                                    >
                                    <label for="equipments.{{ $index }}.available_for_services" class="ml-2 text-sm text-gray-600">
                                        {{ __('Prestação de serviços') }}
                                    </label>
                                </div>

                                <!-- Colaboração em Projeto/Convênio -->
                                <div class="flex items-center">
                                    <input
                                        id="equipments.{{ $index }}.available_for_collaboration"
                                        type="checkbox"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        wire:model.defer="equipments.{{ $index }}.available_for_collaboration"
                                    >
                                    <label for="equipments.{{ $index }}.available_for_collaboration" class="ml-2 text-sm text-gray-600">
                                        {{ __('Colaboração em projeto/convênio') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Botão para Remover Equipamento -->
                        @if(count($equipments) > 1)
                            <button
                                type="button"
                                class="mt-4 text-red-600 hover:text-red-800 text-sm font-medium"
                                wire:click="removeEquipment({{ $index }})"
                            >
                                {{ __('Remover Equipamento') }}
                            </button>
                        @endif
                    </div>
                @endforeach

                <!-- Botão para Adicionar Novo Equipamento -->
                <button
                    type="button"
                    class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center"
                    wire:click="addEquipment"
                >
                    {{ __('Adicionar Novo Equipamento') }}
                </button>
            </div>
        </x-slot>

        <x-slot name="actions">
            @can('updateEquipment', $team)
                <x-button wire:loading.attr="disabled" wire:target="saveEquipments">
                    {{ __('Salvar') }}
                </x-button>
            @else
                <p class="text-sm text-gray-500">Apenas o coordenador pode atualizar os equipamentos.</p>
            @endcan
        </x-slot>
    </x-form-address-section>
</div>
