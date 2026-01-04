<div>
    <x-form-section submit="save">
        <x-slot name="title">
            {{ __('Informações Legadas') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Pesquisadores, técnicas analíticas e linhas de pesquisa importadas do mapa antigo.') }}
        </x-slot>

        <x-slot name="form">
            <div class="col-span-6">
                <x-label for="researchers" value="{{ __('Pesquisadores') }}" />
                <textarea id="researchers" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" wire:model.defer="researchers"></textarea>
                <x-input-error for="researchers" class="mt-2" />
            </div>

            <div class="col-span-6">
                <x-label for="analytical_techniques" value="{{ __('Técnicas Analíticas') }}" />
                <textarea id="analytical_techniques" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" wire:model.defer="analytical_techniques"></textarea>
                <x-input-error for="analytical_techniques" class="mt-2" />
            </div>

            <div class="col-span-6">
                <x-label for="research_lines" value="{{ __('Linhas de Pesquisa') }}" />
                <textarea id="research_lines" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" wire:model.defer="research_lines"></textarea>
                <x-input-error for="research_lines" class="mt-2" />
            </div>
        </x-slot>

        <x-slot name="actions">
            <x-action-message class="me-3" on="success">
                {{ __('Salvo.') }}
            </x-action-message>
            <x-button>
                {{ __('Salvar') }}
            </x-button>
        </x-slot>
    </x-form-section>
</div>
