<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'GSC Risk') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body {
                font-family: 'Outfit', sans-serif;
            }
            .glass-panel {
                background: rgba(255, 255, 255, 0.7);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border: 1px solid rgba(255, 255, 255, 0.8);
                box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
            }
            .animate-blob {
                animation: blob 7s infinite;
            }
            .animation-delay-2000 {
                animation-delay: 2s;
            }
            .animation-delay-4000 {
                animation-delay: 4s;
            }
            @keyframes blob {
                0% { transform: translate(0px, 0px) scale(1); }
                33% { transform: translate(30px, -50px) scale(1.1); }
                66% { transform: translate(-20px, 20px) scale(0.9); }
                100% { transform: translate(0px, 0px) scale(1); }
            }
        </style>
    </head>
    <body class="h-full antialiased bg-slate-50 text-slate-800 overflow-hidden">
        
        <!-- Animated Background Effects -->
        <div class="fixed inset-0 z-0">
            <div class="absolute inset-0 bg-[linear-gradient(to_right,#e2e8f0_1px,transparent_1px),linear-gradient(to_bottom,#e2e8f0_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_80%_50%_at_50%_0%,#000_70%,transparent_100%)] opacity-50"></div>
            <div class="absolute top-0 -left-4 w-96 h-96 bg-blue-400 rounded-full mix-blend-multiply filter blur-[128px] opacity-30 animate-blob"></div>
            <div class="absolute top-0 -right-4 w-96 h-96 bg-indigo-400 rounded-full mix-blend-multiply filter blur-[128px] opacity-30 animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-32 left-1/2 w-96 h-96 bg-cyan-400 rounded-full mix-blend-multiply filter blur-[128px] opacity-30 animate-blob animation-delay-4000"></div>
        </div>

        <div class="min-h-screen flex flex-col lg:flex-row relative z-10">
            
            <!-- Left Side: GSC Risk Intelligence Info -->
            <div class="hidden lg:flex lg:w-6/12 xl:w-7/12 flex-col justify-center px-12 xl:px-24">
                
                <!-- Header Logo & Title -->
                <div class="flex items-center space-x-3 mb-10">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/30 border border-blue-400/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold tracking-wider text-slate-800">GSC RISK</span>
                </div>

                <h2 class="text-5xl xl:text-6xl font-extrabold tracking-tight leading-tight text-slate-800 mb-6">
                    Intelijen Rantai Pasok Global <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-500 to-indigo-500">Waktu Nyata</span>
                </h2>
                <p class="text-slate-600 text-lg xl:text-xl mb-12 leading-relaxed max-w-2xl">
                    Pantau kepadatan pelabuhan, ancaman cuaca, perubahan nilai tukar, dan berita geopolitik dalam satu dasbor pintar terpadu.
                </p>

                <!-- Widgets Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 max-w-2xl">
                    
                    <div class="p-5 glass-panel rounded-2xl flex items-start space-x-4 hover:bg-slate-800/40 transition-colors duration-300 group cursor-default">
                        <div class="p-3 bg-amber-500/10 rounded-xl text-amber-400 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-slate-800">Tingkat Risiko Cuaca</h4>
                            <p class="text-xs text-slate-500 mt-1">Pelabuhan Hamburg: <span class="text-amber-500 font-medium">Siaga Tinggi</span></p>
                        </div>
                    </div>

                    <div class="p-5 glass-panel rounded-2xl flex items-start space-x-4 hover:bg-white/40 transition-colors duration-300 group cursor-default">
                        <div class="p-3 bg-emerald-500/10 rounded-xl text-emerald-500 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-slate-800">Dampak Mata Uang</h4>
                            <p class="text-xs text-slate-500 mt-1">USD/EUR: <span class="text-emerald-500 font-medium">+1.24% hari ini</span></p>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Right Side: Login / Auth Card -->
            <div class="flex-1 flex flex-col justify-center items-center py-12 px-6 sm:px-12 lg:w-6/12 xl:w-5/12">
                <!-- Mobile Logo -->
                <div class="lg:hidden mx-auto mb-8 flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/30 border border-blue-400/20">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold tracking-wider text-slate-800">GSC RISK</span>
                </div>

                <div class="w-full max-w-md glass-panel p-8 sm:p-10 rounded-3xl shadow-2xl relative">
                    {{ $slot }}
                </div>
                
                <div class="mt-8 text-xs text-slate-500 text-center">
                    &copy; {{ date('Y') }} GSC Risk Inc. Hak Cipta Dilindungi.<br>
                    <a href="#" class="hover:text-slate-300 transition-colors">Kebijakan Privasi</a> &bull; <a href="#" class="hover:text-slate-300 transition-colors">Syarat & Ketentuan</a>
                </div>
            </div>

        </div>
    </body>
</html>

