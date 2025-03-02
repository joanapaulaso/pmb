<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

    </head>
    <body class="bg-white text-black">

        {{-- Nav --}}
        <nav class="bg-white shadow-md" x-data="{ open: false }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center">
                        <img class="h-12 w-auto" src="{{ Vite::asset('resources/images/logo.png') }}" alt="Logo">
                        <div class="hidden sm:flex ml-10 space-x-4">
                            <a href="#" class="text-gray-900 hover:bg-[#2A8A9D] hover:text-white px-3 py-2 rounded-md text-sm font-medium">SOBRE</a>
                            <a href="#" class="text-gray-900 hover:bg-[#2A8A9D] hover:text-white px-3 py-2 rounded-md text-sm font-medium">FÓRUM</a>
                            <a href="#" class="text-gray-900 hover:bg-[#2A8A9D] hover:text-white px-3 py-2 rounded-md text-sm font-medium">FAQ</a>
                            <a href="#" class="text-gray-900 hover:bg-[#2A8A9D] hover:text-white px-3 py-2 rounded-md text-sm font-medium">EVENTOS</a>
                            <a href="#" class="text-gray-900 hover:bg-[#2A8A9D] hover:text-white px-3 py-2 rounded-md text-sm font-medium">CONTATO</a>
                        </div>
                    </div>
                    <div class="sm:hidden flex items-center">
                        <button @click="open = !open" type="button" class="text-gray-900 inline-flex items-center justify-center p-2 rounded-md hover:text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" aria-controls="mobile-menu" aria-expanded="false">
                            <span class="sr-only">Open main menu</span>
                            <svg :class="{ 'block': !open, 'hidden': open }" class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg :class="{ 'hidden': !open, 'block': open }" class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="hidden sm:flex items-center">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="bg-[#2A8A9D] text-white px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="bg-[#2A8A9D] text-white px-3 py-2 rounded-md text-sm font-medium">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-[#2A8A9D] text-white px-3 py-2 rounded-md text-sm font-medium ml-2">Register</a>
                        @endif
                    @endauth
                    </div>
                </div>
            </div>

            <!-- Mobile menu, show/hide based on menu state. -->
            <div :class="{ 'block': open, 'hidden': !open }" class="md:hidden" id="mobile-menu">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                    <a href="#" class="text-gray-900 hover:bg-gray-700 hover:text-white block px-3 py-2 rounded-md text-base font-medium">SOBRE</a>
                    <a href="#" class="text-gray-900 hover:bg-gray-700 hover:text-white block px-3 py-2 rounded-md text-base font-medium">FÓRUM</a>
                    <a href="#" class="text-gray-900 hover:bg-gray-700 hover:text-white block px-3 py-2 rounded-md text-base font-medium">FAQ</a>
                    <a href="#" class="text-gray-900 hover:bg-gray-700 hover:text-white block px-3 py-2 rounded-md text-base font-medium">EVENTOS</a>
                    <a href="#" class="text-gray-900 hover:bg-gray-700 hover:text-white block px-3 py-2 rounded-md text-base font-medium">CONTATO</a>
                    @auth
                        <a href="{{ url('/dashboard') }}" class="bg-[#2A8A9D] text-white px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="bg-[#2A8A9D] text-white px-3 py-2 rounded-md text-sm font-medium">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-[#2A8A9D] text-white px-3 py-2 rounded-md text-sm font-medium ml-2">Register</a>
                        @endif
                    @endauth
                </div>
            </div>
        </nav>

        {{-- Hero --}}
        <div class="hero-section bg-white relative overflow-hidden min-h-screen">
            <video class="absolute inset-0 w-full h-full object-cover" autoplay loop muted playsinline>
                <source src="{{ Vite::asset('resources/images/hero_bg_00_animation.mp4') }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <div class="relative z-10 max-w-7xl mx-auto h-full flex flex-col justify-center py-16 px-4 sm:py-24 sm:px-6 lg:px-8">
                <div class="lg:grid lg:grid-cols-2 lg:gap-8 lg:items-center">
                    <div>
                        <h1 class="text-4xl font-bold text-gray-200 sm:text-5xl lg:text-6xl">
                            Conectando as Mentes da Metabolômica no Brasil
                        </h1>
                        <p class="mt-4 max-w-2xl text-lg text-gray-200">
                            Junte-se a nós para fazer parte da comunidade que molda o futuro da metabolômica.
                        </p>
                        <a href="{{ route('register') }}" class="mt-8 inline-flex items-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                            REGISTRE-SE
                        </a>
                    </div>
                    <div class="mt-10 lg:mt-0 lg:col-start-2 lg:relative">
                        <div class="relative mx-auto w-full lg:max-w-md">
                            <img class="w-full rounded-full shadow-lg" src="{{ Vite::asset('resources/images/hero_main_image.png') }}" alt="Hero Image">
                        </div>
                    </div>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 w-full">
                <img class="w-full" src="{{ Vite::asset('resources/images/hero_detail_bottom.png') }}" alt="Hero Detail Bottom">
            </div>
        </div>

        {{-- Institutuinal Video Section --}}
        <div class="relative bg-white py-16 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto lg:grid lg:grid-cols-2 lg:gap-8 lg:items-center">
                <div class="text-center lg:text-left">
                    <p class="text-xl font-semibold text-black">
                        Com a palavra, Vinícius Verri, fundador do Portal Metabolômica Brasil.
                    </p>
                    <div class="mt-4 flex justify-center lg:justify-start">
                        <img class="" src="{{ Vite::asset('resources/images/mass_spec_detail_00.png') }}" alt="Mass Spec Detail">
                    </div>
                </div>
                <div class="mt-10 lg:mt-0 flex justify-center">
                    <iframe class="w-full max-w-md rounded-lg shadow-lg" width="560" height="315" src="https://www.youtube.com/embed/5wK_bwHyAKA?si=ngMXQWdguVYpSBOo" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
            </div>
        </div>

        {{-- Carousel --}}
        <div x-data="carousel()" x-init="start()" class="relative bg-white py-16 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto overflow-hidden relative">
                <!-- Carousel Slides -->
                <div class="relative w-full h-64 overflow-hidden">
                    <template x-for="(slide, index) in slides" :key="index">
                        <div
                            x-show="activeSlide === index"
                            x-transition:enter="transition transform ease-out duration-500"
                            x-transition:enter-start="translate-x-full"
                            x-transition:enter-end="translate-x-0"
                            x-transition:leave="transition transform ease-in duration-500"
                            x-transition:leave-start="translate-x-0"
                            x-transition:leave-end="translate-x-full"
                            class="absolute inset-0">
                            <img class="w-full h-full object-cover" :src="slide.image" alt="Carousel Image">
                            <div class="absolute bottom-0 left-0 w-full bg-gray-200 p-4">
                                <p class="text-black"><strong> Novo centro de espectrometria de massas:</strong> Universidade Federal do Estado do Rio de Janeiro (UNIRIO) investe em repaginação de laboratório multiusuário, abrindo espaço e dando oportunidade para pesquisadores em todo o Brasil. <em>Saiba mais...</em></p>
                            </div>
                        </div>
                    </template>
                </div>
                <!-- Left Button -->
                <div class="absolute inset-y-0 left-0 flex items-center z-20">
                    <button @click="previous" class="bg-gray-800 text-white p-2 rounded-full hover:bg-gray-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                </div>
                <!-- Right Button -->
                <div class="absolute inset-y-0 right-0 flex items-center z-20">
                    <button @click="next" class="bg-gray-800 text-white p-2 rounded-full hover:bg-gray-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <script>
            function carousel() {
                return {
                    activeSlide: 0,
                    slides: [
                        { image: "{{ Vite::asset('resources/images/carousel_00.png') }}" },
                        { image: "{{ Vite::asset('resources/images/carousel_01.png') }}" },
                        { image: "{{ Vite::asset('resources/images/carousel_02.png') }}" },
                        { image: "{{ Vite::asset('resources/images/carousel_03.png') }}" }
                    ],
                    start() {
                        this.interval = setInterval(() => {
                            this.next();
                        }, 5000);
                    },
                    next() {
                        this.activeSlide = (this.activeSlide + 1) % this.slides.length;
                    },
                    previous() {
                        this.activeSlide = (this.activeSlide - 1 + this.slides.length) % this.slides.length;
                    }
                }
            }
        </script>

        <footer class="bg-white py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="lg:flex lg:justify-between">
                    <div class="flex flex-wrap justify-between w-full lg:w-2/3">
                        <div class="px-4 py-2 w-1/2 md:w-1/5">
                            <h3 class="text-sm font-bold mb-2">SOBRE</h3>
                            <ul class="text-sm space-y-1">
                                <li><a href="#">SOBRE</a></li>
                                <li><a href="#">SOBRE</a></li>
                                <li><a href="#">SOBRE</a></li>
                                <li><a href="#">SOBRE</a></li>
                            </ul>
                        </div>
                        <div class="px-4 py-2 w-1/2 md:w-1/5">
                            <h3 class="text-sm font-bold mb-2">FÓRUM</h3>
                            <ul class="text-sm space-y-1">
                                <li><a href="#">FÓRUM</a></li>
                                <li><a href="#">FÓRUM</a></li>
                                <li><a href="#">FÓRUM</a></li>
                                <li><a href="#">FÓRUM</a></li>
                            </ul>
                        </div>
                        <div class="px-4 py-2 w-1/2 md:w-1/5">
                            <h3 class="text-sm font-bold mb-2">FAQ</h3>
                            <ul class="text-sm space-y-1">
                                <li><a href="#">FAQ</a></li>
                                <li><a href="#">FAQ</a></li>
                                <li><a href="#">FAQ</a></li>
                                <li><a href="#">FAQ</a></li>
                            </ul>
                        </div>
                        <div class="px-4 py-2 w-1/2 md:w-1/5">
                            <h3 class="text-sm font-bold mb-2">EVENTOS</h3>
                            <ul class="text-sm space-y-1">
                                <li><a href="#">EVENTOS</a></li>
                                <li><a href="#">EVENTOS</a></li>
                                <li><a href="#">EVENTOS</a></li>
                                <li><a href="#">EVENTOS</a></li>
                            </ul>
                        </div>
                        <div class="px-4 py-2 w-1/2 md:w-1/5">
                            <h3 class="text-sm font-bold mb-2">CONTATO</h3>
                            <ul class="text-sm space-y-1">
                                <li><a href="#">CONTATO</a></li>
                                <li><a href="#">CONTATO</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="mt-8 lg:mt-0 flex flex-col items-center lg:items-start lg:w-1/3">
                        <img class="h-16 w-auto mb-4" src="{{ Vite::asset('resources/images/logo.png') }}" alt="Logo">
                        <div class="flex space-x-4 mb-4">
                            <a href="#"><img class="h-6 w-6" src="{{ Vite::asset('resources/images/icon_twitter.png') }}" alt="Twitter"></a>
                            <a href="#"><img class="h-6 w-6" src="{{ Vite::asset('resources/images/icon_insta.png') }}" alt="Instagram"></a>
                            <a href="#"><img class="h-6 w-6" src="{{ Vite::asset('resources/images/icon_linkedin.png') }}" alt="LinkedIn"></a>
                            <a href="#"><img class="h-6 w-6" src="{{ Vite::asset('resources/images/icon_fb.png') }}" alt="LinkedIn"></a>
                        </div>
                        <p class="text-sm text-gray-500">Copyright 2024 © Todos os direitos reservados.</p>
                    </div>
                </div>
            </div>
        </footer>




    </body>
</html>
