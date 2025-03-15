<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles

    <!-- Google Maps API Loader -->
    <gmpx-api-loader key="AIzaSyD4xIxoKPy81-hrL8IXLqhQoMmtQoXqVLY" solution-channel="GMP_GE_mapsandplacesautocomplete_v2"></gmpx-api-loader>

    <!-- Extended Component Library -->
    <script type="module" src="https://unpkg.com/@googlemaps/extended-component-library@0.6/dist/index.min.js"></script>

</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <div class="flex h-screen bg-gray-100">
            <!-- Sidebar -->
            <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-md transform transition-transform duration-300 lg:translate-x-0" id="sidebar">
                <div class="flex items-center justify-between p-4 border-b">
                    <div class="flex items-center">
                        <img class="h-8 w-auto" src="{{ Vite::asset('resources/images/logo.png') }}" alt="Logo">
                        <h1 class="ml-2 text-xl font-bold">Admin Panel</h1>
                    </div>
                    <button class="lg:hidden focus:outline-none" id="closeSidebar">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <nav class="px-4 pt-4">
                    <div class="space-y-1">
                        <a href="{{ route('admin.dashboard') }}" class="block py-2.5 px-4 rounded transition duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-500 text-white' : 'hover:bg-gray-100' }}">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                <span>Dashboard</span>
                            </div>
                        </a>

                        <a href="{{ route('admin.videos.index') }}" class="block py-2.5 px-4 rounded transition duration-200 {{ request()->routeIs('admin.videos.*') || request()->routeIs('admin.playlists.*') ? 'bg-blue-500 text-white' : 'hover:bg-gray-100' }}">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                <span>Vídeos e Playlists</span>
                            </div>
                        </a>

                        <a href="{{ route('admin.laboratories.index') }}" class="block py-2.5 px-4 rounded transition duration-200 {{ request()->routeIs('admin.laboratories.*') ? 'bg-blue-500 text-white' : 'hover:bg-gray-100' }}">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                </svg>
                                <span>Laboratórios</span>
                            </div>
                        </a>

                        <a href="{{ route('admin.events.index') }}" class="block py-2.5 px-4 rounded transition duration-200 {{ request()->routeIs('admin.events.*') ? 'bg-blue-500 text-white' : 'hover:bg-gray-100' }}">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>Eventos e Workshops</span>
                            </div>
                        </a>
                    </div>

                    <div class="mt-10 pt-6 border-t border-gray-100">
                        <a href="{{ route('portal') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-100">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                                </svg>
                                <span>Voltar ao Site</span>
                            </div>
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}"
                               onclick="event.preventDefault(); this.closest('form').submit();"
                               class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-100">
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    <span>Desconectar</span>
                                </div>
                            </a>
                        </form>
                    </div>
                </nav>
            </div>

            <!-- Mobile sidebar button -->
            <div class="fixed bottom-5 right-5 lg:hidden z-50">
                <button id="openSidebar" class="bg-blue-500 text-white p-3 rounded-full shadow-lg focus:outline-none">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="flex-1 ml-0 lg:ml-64 transition-all duration-300">
                <!-- Top navbar -->
                <div class="bg-white shadow-sm py-4 px-6 flex items-center justify-between">
                    <h2 class="text-xl font-semibold">@yield('page-title', 'Dashboard')</h2>
                    <div class="flex items-center">
                        <span class="text-sm mr-2">{{ Auth::user()->name }}</span>
                        <img class="h-8 w-8 rounded-full" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}">
                    </div>
                </div>

                <!-- Main content -->
                <main class="p-6">
                    @if(session('success'))
                        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    <script>
        // Mobile sidebar functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const openSidebarButton = document.getElementById('openSidebar');
            const closeSidebarButton = document.getElementById('closeSidebar');

            function isMobile() {
                return window.innerWidth < 1024;
            }

            // Set initial state based on screen size
            if (isMobile()) {
                sidebar.classList.add('-translate-x-full');
            } else {
                sidebar.classList.remove('-translate-x-full');
            }

            // Toggle sidebar on mobile
            if (openSidebarButton) {
                openSidebarButton.addEventListener('click', () => {
                    sidebar.classList.remove('-translate-x-full');
                });
            }

            if (closeSidebarButton) {
                closeSidebarButton.addEventListener('click', () => {
                    sidebar.classList.add('-translate-x-full');
                });
            }

            // Handle resize events
            window.addEventListener('resize', function() {
                if (isMobile()) {
                    if (!sidebar.classList.contains('-translate-x-full') && !document.activeElement?.closest('#sidebar')) {
                        sidebar.classList.add('-translate-x-full');
                    }
                } else {
                    sidebar.classList.remove('-translate-x-full');
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
