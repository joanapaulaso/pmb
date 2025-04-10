@props(['submit'])

<div {{ $attributes->merge(['class' => 'grid grid-cols-1 gap-4 sm:gap-6 md:grid-cols-3']) }}>
    <x-section-title>
        <x-slot name="title">{{ $title }}</x-slot>
        <x-slot name="description">{{ $description }}</x-slot>
    </x-section-title>

    <div class="mt-4 sm:mt-5 md:mt-0 md:col-span-2">
        <form wire:submit="{{ $submit }}">
            <div class="px-4 py-5 bg-white sm:p-6 shadow {{ isset($actions) ? 'sm:rounded-tl-md sm:rounded-tr-md' : 'sm:rounded-md' }}">
                <div class="grid grid-cols-1 gap-4">
                    {{ $form }}
                </div>
            </div>

            @if (isset($actions))
                <div class="flex flex-col sm:flex-row items-center justify-end px-4 py-3 bg-gray-50 text-end sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md space-y-3 sm:space-y-0 sm:space-x-3">
                    {{ $actions }}
                </div>
            @endif
        </form>
    </div>
</div>
