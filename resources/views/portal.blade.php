<!-- resources/views/portal.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Portal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <!-- Include post form for creating new posts -->
                    <x-post-portal-form :tags="$tags" :selectedTags="$selectedTags" />

                    <!-- Include post list for displaying posts -->
                    <x-post-portal-list :posts="$posts" :tags="$tags" :selectedTags="$selectedTags" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
