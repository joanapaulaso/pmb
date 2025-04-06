<nav x-data="{ open: false }" class="bg-white border-b border-gray-200">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center space-x-6">
                <!-- Logo -->
                <a href="{{ route('welcome') }}" class="flex items-center">
                    <img class="h-8 sm:h-10 lg:h-12 min-h-[40px] min-w-[120px] object-contain aspect-auto" src="{{ Vite::asset('resources/images/logo.png') }}" alt="PMB Logo">
                </a>

                <!-- Navigation Links -->
                <div class="hidden space-x-6 sm:-my-px sm:ms-8 sm:flex items-center">
                    <x-nav-link href="{{ route('portal') }}" :active="request()->routeIs('portal')" class="text-gray-700 hover:text-gray-900 transition-colors">
                        {{ __('Portal') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" class="text-gray-700 hover:text-gray-900 transition-colors">
                        {{ __('Comunidade') }}
                    </x-nav-link>

                    <!-- Recursos Dropdown -->
                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger">
                            <span class="inline-flex rounded">
                                <button type="button" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition-colors">
                                    {{ __('Recursos') }}
                                    <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                            </span>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link href="{{ route('events.index') }}" :active="request()->routeIs('events.index')" class="text-gray-700 hover:bg-gray-100 transition-colors">
                                {{ __('Eventos') }}
                            </x-dropdown-link>
                            <x-dropdown-link href="{{ route('videos.index') }}" :active="request()->routeIs('videos.index')" class="text-gray-700 hover:bg-gray-100 transition-colors">
                                {{ __('Vídeos') }}
                            </x-dropdown-link>
                            <x-dropdown-link href="{{ route('labs.map') }}" :active="request()->routeIs('labs.map')" class="text-gray-700 hover:bg-gray-100 transition-colors">
                                {{ __('Mapa de Laboratórios') }}
                            </x-dropdown-link>
                            <x-dropdown-link href="{{ route('membros') }}" :active="request()->routeIs('membros')" class="text-gray-700 hover:bg-gray-100 transition-colors">
                                {{ __('Membros') }}
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Teams Dropdown -->
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures() && Auth::check())
                    <div class="ms-3 relative">
                        @if(Auth::user()->allTeams()->count() > 0)
                            <x-dropdown align="right" width="60">
                                <x-slot name="trigger">
                                    <span class="inline-flex rounded">
                                        <button type="button" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition-colors">
                                            {{ Auth::user()->currentTeam ? Auth::user()->currentTeam->name : __('Team Space') }}
                                            <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                            </svg>
                                        </button>
                                    </span>
                                </x-slot>

                                <x-slot name="content">
                                    <div class="w-60">
                                        <!-- Team Management -->
                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            {{ __('Manage Team') }}
                                        </div>

                                        @if(Auth::user()->currentTeam)
                                            <!-- Team Settings -->
                                            <x-dropdown-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}" class="text-gray-700 hover:bg-gray-100 transition-colors">
                                                {{ __('Meu Laboratório') }}
                                            </x-dropdown-link>
                                        @endif

                                        @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                            <x-dropdown-link href="{{ route('teams.create') }}" class="text-gray-700 hover:bg-gray-100 transition-colors">
                                                {{ __('Create New Team') }}
                                            </x-dropdown-link>
                                        @endcan

                                        <!-- Team Switcher -->
                                        @if (Auth::user()->allTeams()->count() > 1)
                                            <div class="border-t border-gray-200"></div>
                                            <div class="block px-4 py-2 text-xs text-gray-400">
                                                {{ __('Switch Teams') }}
                                            </div>
                                            @foreach (Auth::user()->allTeams() as $team)
                                                <x-switchable-team :team="$team" />
                                            @endforeach
                                        @endif
                                    </div>
                                </x-slot>
                            </x-dropdown>
                        @else
                            @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                <a href="{{ route('teams.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded shadow font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 focus:bg-blue-600 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                    {{ __('Create Team') }}
                                </a>
                            @endcan
                        @endif
                    </div>
                @endif

                <!-- Settings Dropdown -->
                @if (Auth::check())
                <div class="ms-3 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <button class="flex text-sm border-2 border-gray-200 rounded-full focus:outline-none focus:border-gray-300 transition-colors">
                                    <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                </button>
                            @else
                                <span class="inline-flex rounded">
                                    <button type="button" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition-colors">
                                        {{ Auth::user()->name }}
                                        <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </span>
                            @endif
                        </x-slot>

                        <x-slot name="content">
                            <!-- Account Management -->
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Manage Account') }}
                            </div>

                            <x-dropdown-link href="{{ route('profile.show') }}" class="text-gray-700 hover:bg-gray-100 transition-colors">
                                {{ __('Perfil') }}
                            </x-dropdown-link>

                            @if (Auth::user()->isAdmin())
                                <x-dropdown-link href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:bg-gray-100 transition-colors">
                                    {{ __('Painel Admin') }}
                                </x-dropdown-link>
                            @endif

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-dropdown-link href="{{ route('api-tokens.index') }}" class="text-gray-700 hover:bg-gray-100 transition-colors">
                                    {{ __('API Tokens') }}
                                </x-dropdown-link>
                            @endif

                            <div class="border-t border-gray-200"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf
                                <x-dropdown-link href="{{ route('logout') }}"
                                                 @click.prevent="$root.submit();" class="text-gray-700 hover:bg-gray-100 transition-colors">
                                    {{ __('Desconectar') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
                @else
                <div class="ms-3 relative flex items-center gap-2">
                    <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-white border-1 border-gray-300 rounded shadow font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        {{ __('Log in') }}
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded shadow font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 focus:bg-blue-600 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        {{ __('Register') }}
                    </a>
                </div>
                @endif
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition-colors">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1 bg-stone-50">
            <x-responsive-nav-link href="{{ route('portal') }}" :active="request()->routeIs('portal')" class="text-gray-700 hover:bg-gray-100 transition-colors">
                {{ __('Portal') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" class="text-gray-700 hover:bg-gray-100 transition-colors">
                {{ __('Comunidade') }}
            </x-responsive-nav-link>
            <!-- Recursos no Menu Mobile -->
            <div class="border-t border-gray-200"></div>
            <div class="block px-4 py-2 text-xs text-gray-400">
                {{ __('Recursos') }}
            </div>
            <x-responsive-nav-link href="{{ route('events.index') }}" :active="request()->routeIs('events.index')" class="text-gray-700 hover:bg-gray-100 transition-colors">
                {{ __('Eventos') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('videos.index') }}" :active="request()->routeIs('videos.index')" class="text-gray-700 hover:bg-gray-100 transition-colors">
                {{ __('Vídeos') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('labs.map') }}" :active="request()->routeIs('labs.map')" class="text-gray-700 hover:bg-gray-100 transition-colors">
                {{ __('Mapa de Laboratórios') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('membros') }}" :active="request()->routeIs('membros')" class="text-gray-700 hover:bg-gray-100 transition-colors">
                {{ __('Membros') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        @if (Auth::check())
        <div class="pt-4 pb-1 border-t border-gray-200 bg-stone-50">
            <div class="flex items-center px-4">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <div class="shrink-0 me-3">
                        <img class="h-10 w-10 rounded-full object-cover border-2 border-gray-200" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                    </div>
                @endif
                <div>
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')" class="text-gray-700 hover:bg-gray-100 transition-colors">
                    {{ __('Perfil') }}
                </x-responsive-nav-link>

                @if (Auth::user()->isAdmin())
                    <x-responsive-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')" class="text-gray-700 hover:bg-gray-100 transition-colors">
                        {{ __('Painel Admin') }}
                    </x-responsive-nav-link>
                @endif

                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <x-responsive-nav-link href="{{ route('api-tokens.index') }}" :active="request()->routeIs('api-tokens.index')" class="text-gray-700 hover:bg-gray-100 transition-colors">
                        {{ __('API Tokens') }}
                    </x-responsive-nav-link>
                @endif

                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf
                    <x-responsive-nav-link href="{{ route('logout') }}"
                                           @click.prevent="$root.submit();" class="text-gray-700 hover:bg-gray-100 transition-colors">
                        {{ __('Desconectar') }}
                    </x-responsive-nav-link>
                </form>

                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="border-t border-gray-200"></div>
                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Manage Team') }}
                    </div>

                    @if(Auth::user()->allTeams()->count() > 0)
                        @if(Auth::user()->currentTeam)
                            <x-responsive-nav-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}" :active="request()->routeIs('teams.show')" class="text-gray-700 hover:bg-gray-100 transition-colors">
                                {{ __('Meu Laboratório') }}
                            </x-responsive-nav-link>
                        @endif

                        @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                            <x-responsive-nav-link href="{{ route('teams.create') }}" :active="request()->routeIs('teams.create')" class="text-gray-700 hover:bg-gray-100 transition-colors">
                                {{ __('Create New Team') }}
                            </x-responsive-nav-link>
                        @endcan

                        @if (Auth::user()->allTeams()->count() > 1)
                            <div class="border-t border-gray-200"></div>
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Switch Teams') }}
                            </div>
                            @foreach (Auth::user()->allTeams() as $team)
                                <x-switchable-team :team="$team" component="responsive-nav-link" class="text-gray-700 hover:bg-gray-100 transition-colors" />
                            @endforeach
                        @endif
                    @else
                        @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                            <x-responsive-nav-link href="{{ route('teams.create') }}" class="text-gray-700 hover:bg-gray-100 transition-colors">
                                {{ __('Create Team') }}
                            </x-responsive-nav-link>
                        @endcan
                    @endif
                @endif
            </div>
        </div>
        @else
        <div class="pt-4 pb-1 border-t border-gray-200 bg-stone-50">
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link href="{{ route('login') }}" class="text-gray-700 hover:bg-gray-100 transition-colors">
                    {{ __('Log in') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('register') }}" class="text-gray-700 hover:bg-gray-100 transition-colors">
                    {{ __('Register') }}
                </x-responsive-nav-link>
            </div>
        </div>
        @endif
    </div>
</nav>