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
            .glow-glow {
                box-shadow: 0 0 50px -10px rgba(99, 102, 241, 0.15);
            }
        </style>
    </head>
    <body class="h-full antialiased bg-slate-950 text-slate-100">
        <div class="min-h-screen flex flex-col lg:flex-row">
            
            <!-- Left Side: GSC Risk Intelligence Info (Desktop only) -->
            <div class="hidden lg:flex lg:w-7/12 bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 relative overflow-hidden flex-col justify-between p-12 border-r border-slate-800">
                <!-- Background Grid and Glows -->
                <div class="absolute inset-0 bg-[linear-gradient(to_right,#0f172a_1px,transparent_1px),linear-gradient(to_bottom,#0f172a_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)] opacity-60"></div>
                <div class="absolute -top-40 -left-40 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl"></div>

                <!-- Header Logo & Title -->
                <div class="relative z-10 flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 to-violet-600 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <span class="text-xl font-bold tracking-wider bg-clip-text text-transparent bg-gradient-to-r from-white via-slate-100 to-indigo-200">GSC RISK</span>
                </div>

                <!-- Core App Presentation / Dynamic Widgets -->
                <div class="relative z-10 my-auto max-w-xl">
                    <h2 class="text-4xl font-extrabold tracking-tight leading-tight text-white mb-4">
                        Real-time Global Supply Chain <span class="text-indigo-400">Risk Intelligence</span>
                    </h2>
                    <p class="text-slate-400 text-lg mb-8 leading-relaxed">
                        Monitor port congestions, localized weather threats, currency shifts, and geopolitical news all in a unified smart dashboard.
                    </p>

                    <!-- Real Supply Chain Widgets Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <!-- Widget 1: Weather Risk -->
                        <div class="p-4 bg-slate-900/60 backdrop-blur-md rounded-2xl border border-slate-800 shadow-md flex items-start space-x-3">
                            <div class="p-2.5 bg-amber-500/10 rounded-xl text-amber-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-slate-200">Weather Risk Level</h4>
                                <p class="text-xs text-slate-400 mt-0.5">Port of Hamburg: <span class="text-amber-400 font-medium">High Alert</span></p>
                            </div>
                        </div>

                        <!-- Widget 2: Currency Trend -->
                        <div class="p-4 bg-slate-900/60 backdrop-blur-md rounded-2xl border border-slate-800 shadow-md flex items-start space-x-3">
                            <div class="p-2.5 bg-emerald-500/10 rounded-xl text-emerald-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-slate-200">Currency Impact</h4>
                                <p class="text-xs text-slate-400 mt-0.5">USD/EUR: <span class="text-emerald-400 font-medium">+1.24% today</span></p>
                            </div>
                        </div>

                        <!-- Widget 3: Live Ports Monitor -->
                        <div class="p-4 bg-slate-900/60 backdrop-blur-md rounded-2xl border border-slate-800 shadow-md flex items-start space-x-3">
                            <div class="p-2.5 bg-blue-500/10 rounded-xl text-blue-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-slate-200">Monitored Ports</h4>
                                <p class="text-xs text-slate-400 mt-0.5"><span class="text-blue-400 font-medium">8 Global Hubs</span> active</p>
                            </div>
                        </div>

                        <!-- Widget 4: System Sentinel -->
                        <div class="p-4 bg-slate-900/60 backdrop-blur-md rounded-2xl border border-slate-800 shadow-md flex items-start space-x-3">
                            <div class="p-2.5 bg-indigo-500/10 rounded-xl text-indigo-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-slate-200">Risk Assessment</h4>
                                <p class="text-xs text-slate-400 mt-0.5">Lexicon Engine: <span class="text-indigo-400 font-medium">99.8% accurate</span></p>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Footer -->
                <div class="relative z-10 flex justify-between items-center text-xs text-slate-500">
                    <span>&copy; {{ date('Y') }} GSC Risk Inc. All rights reserved.</span>
                    <div class="flex space-x-4">
                        <a href="#" class="hover:text-slate-300 transition-colors">Privacy Policy</a>
                        <a href="#" class="hover:text-slate-300 transition-colors">Terms of Service</a>
                    </div>
                </div>
            </div>

            <!-- Right Side: Login / Auth Card -->
            <div class="flex-1 flex flex-col justify-center py-12 px-6 sm:px-12 lg:flex-none lg:w-5/12 bg-slate-950 relative overflow-hidden">
                <!-- Mobile Logo -->
                <div class="lg:hidden mx-auto mb-8 flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 to-violet-600 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold tracking-wider text-white">GSC RISK</span>
                </div>

                <!-- Form container -->
                <div class="mx-auto w-full max-w-sm lg:w-96">
                    {{ $slot }}
                </div>
            </div>

        </div>
    </body>
</html>

