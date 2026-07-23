<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GSC Risk Intelligence</title>

    <!-- Tailwind CSS (via Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- FontAwesome for Premium Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Outfit Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Leaflet.js for Interactive Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Chart.js for High-Fidelity Data Visualization -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
        .sidebar-link-active {
            background: rgba(255, 255, 255, 0.1);
            border-left: 3px solid #60a5fa;
            color: #ffffff !important;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .sidebar-link-active i {
            color: #60a5fa !important;
        }
        /* Custom scrollbar for premium feel */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #1e293b;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #334155;
        }
        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.04);
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
        .leaflet-container {
            touch-action: none !important;
            z-index: 1 !important;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 h-full flex flex-col antialiased overflow-x-auto overflow-y-hidden">

<!-- Animated Background Effects -->
<div class="fixed inset-0 z-0 pointer-events-none">
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#e2e8f0_1px,transparent_1px),linear-gradient(to_bottom,#e2e8f0_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_80%_50%_at_50%_0%,#000_70%,transparent_100%)] opacity-50"></div>
    <div class="absolute top-0 -left-4 w-96 h-96 bg-blue-400 rounded-full mix-blend-multiply filter blur-[128px] opacity-30 animate-blob"></div>
    <div class="absolute top-0 -right-4 w-96 h-96 bg-indigo-400 rounded-full mix-blend-multiply filter blur-[128px] opacity-30 animate-blob animation-delay-2000"></div>
    <div class="absolute -bottom-32 left-1/2 w-96 h-96 bg-cyan-400 rounded-full mix-blend-multiply filter blur-[128px] opacity-30 animate-blob animation-delay-4000"></div>
</div>

<div class="flex h-full min-w-[1024px] lg:w-full overflow-hidden relative z-10 bg-slate-50">
    <!-- Elegant Dark Sidebar -->
    <aside class="w-72 bg-slate-900 flex flex-col shrink-0 overflow-hidden shadow-[4px_0_24px_rgba(0,0,0,0.15)] border-r border-slate-700/50 relative z-20">
        <!-- Subtle glow effect inside sidebar -->
        <div class="absolute top-0 inset-x-0 h-32 bg-gradient-to-b from-blue-500/10 to-transparent pointer-events-none"></div>

        <!-- Logo -->
        <div class="p-7 border-b border-slate-800/80 flex items-center gap-4 relative z-10">
            <div class="bg-gradient-to-tr from-blue-500 to-indigo-600 text-white p-2 rounded-2xl shadow-lg shadow-blue-500/20 flex items-center justify-center shrink-0 w-12 h-12">
                <i class="fa-solid fa-earth-americas text-2xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-extrabold tracking-wide text-white leading-none">GSC RISK</h1>
                <span class="text-[10px] text-blue-400 font-bold tracking-[0.2em] uppercase block mt-1.5">Intelijen Risiko</span>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 overflow-y-auto py-6 space-y-1.5 px-4 relative z-10 custom-scrollbar">
            
            <div class="px-4 mb-2 mt-2">
                <span class="text-[10px] font-bold text-slate-500 tracking-widest uppercase">Utama</span>
            </div>
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-semibold text-slate-400 hover:bg-slate-800/80 hover:text-slate-200 transition-all {{ Request::routeIs('dashboard') ? 'sidebar-link-active' : '' }}">
                <div class="w-7 h-7 rounded-lg bg-slate-800/50 flex items-center justify-center shrink-0 border border-slate-700/50">
                    <i class="fa-solid fa-chart-pie text-blue-400 text-sm"></i>
                </div>
                Dasbor Utama
            </a>

            @if(Auth::user()->email !== 'admin@gsc.com')
                <div class="px-4 mb-2 mt-6">
                    <span class="text-[10px] font-bold text-slate-500 tracking-widest uppercase">Pemantauan Global</span>
                </div>
                <a href="{{ route('countries.data') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-semibold text-slate-400 hover:bg-slate-800/80 hover:text-slate-200 transition-all {{ Request::routeIs('countries.data') ? 'sidebar-link-active' : '' }}">
                    <div class="w-7 h-7 rounded-lg bg-slate-800/50 flex items-center justify-center shrink-0 border border-slate-700/50">
                        <i class="fa-solid fa-flag text-emerald-400 text-sm"></i>
                    </div>
                    Data Negara
                </a>
                <a href="{{ route('countries.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-semibold text-slate-400 hover:bg-slate-800/80 hover:text-slate-200 transition-all {{ Request::routeIs('countries.index') ? 'sidebar-link-active' : '' }}">
                    <div class="w-7 h-7 rounded-lg bg-slate-800/50 flex items-center justify-center shrink-0 border border-slate-700/50">
                        <i class="fa-solid fa-triangle-exclamation text-rose-400 text-sm"></i>
                    </div>
                    Pemantauan Risiko
                </a>
                <a href="{{ route('weather.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-semibold text-slate-400 hover:bg-slate-800/80 hover:text-slate-200 transition-all {{ Request::routeIs('weather.index') ? 'sidebar-link-active' : '' }}">
                    <div class="w-7 h-7 rounded-lg bg-slate-800/50 flex items-center justify-center shrink-0 border border-slate-700/50">
                        <i class="fa-solid fa-cloud-sun text-sky-400 text-sm"></i>
                    </div>
                    Pemantauan Cuaca
                </a>
                <a href="{{ route('currency.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-semibold text-slate-400 hover:bg-slate-800/80 hover:text-slate-200 transition-all {{ Request::routeIs('currency.index') ? 'sidebar-link-active' : '' }}">
                    <div class="w-7 h-7 rounded-lg bg-slate-800/50 flex items-center justify-center shrink-0 border border-slate-700/50">
                        <i class="fa-solid fa-money-bill-transfer text-emerald-400 text-sm"></i>
                    </div>
                    Nilai Tukar
                </a>
                <a href="{{ route('news.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-semibold text-slate-400 hover:bg-slate-800/80 hover:text-slate-200 transition-all {{ Request::routeIs('news.index') ? 'sidebar-link-active' : '' }}">
                    <div class="w-7 h-7 rounded-lg bg-slate-800/50 flex items-center justify-center shrink-0 border border-slate-700/50">
                        <i class="fa-solid fa-newspaper text-purple-400 text-sm"></i>
                    </div>
                    Intelijen Berita
                </a>
                <a href="{{ route('ports.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-semibold text-slate-400 hover:bg-slate-800/80 hover:text-slate-200 transition-all {{ Request::routeIs('ports.index') ? 'sidebar-link-active' : '' }}">
                    <div class="w-7 h-7 rounded-lg bg-slate-800/50 flex items-center justify-center shrink-0 border border-slate-700/50">
                        <i class="fa-solid fa-anchor text-cyan-400 text-sm"></i>
                    </div>
                    Dasbor Pelabuhan
                </a>
                <a href="{{ route('ports.world_map') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-semibold text-slate-400 hover:bg-slate-800/80 hover:text-slate-200 transition-all {{ Request::routeIs('ports.world_map') ? 'sidebar-link-active' : '' }}">
                    <div class="w-7 h-7 rounded-lg bg-slate-800/50 flex items-center justify-center shrink-0 border border-slate-700/50">
                        <i class="fa-solid fa-earth-americas text-indigo-400 text-sm"></i>
                    </div>
                    Peta Dunia
                </a>

                <div class="px-4 mb-2 mt-6">
                    <span class="text-[10px] font-bold text-slate-500 tracking-widest uppercase">Analitik</span>
                </div>
                <a href="{{ route('comparison.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-semibold text-slate-400 hover:bg-slate-800/80 hover:text-slate-200 transition-all {{ Request::routeIs('comparison.index') ? 'sidebar-link-active' : '' }}">
                    <div class="w-7 h-7 rounded-lg bg-slate-800/50 flex items-center justify-center shrink-0 border border-slate-700/50">
                        <i class="fa-solid fa-arrow-right-arrow-left text-orange-400 text-sm"></i>
                    </div>
                    Perbandingan Negara
                </a>
                <a href="{{ route('watchlist.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-semibold text-slate-400 hover:bg-slate-800/80 hover:text-slate-200 transition-all {{ Request::routeIs('watchlist.index') ? 'sidebar-link-active' : '' }}">
                    <div class="w-7 h-7 rounded-lg bg-slate-800/50 flex items-center justify-center shrink-0 border border-slate-700/50">
                        <i class="fa-solid fa-bookmark text-pink-400 text-sm"></i>
                    </div>
                    Daftar Pantauan
                </a>
                <a href="{{ route('suppliers.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-semibold text-slate-400 hover:bg-slate-800/80 hover:text-slate-200 transition-all {{ Request::routeIs('suppliers.*') ? 'sidebar-link-active' : '' }}">
                    <div class="w-7 h-7 rounded-lg bg-slate-800/50 flex items-center justify-center shrink-0 border border-slate-700/50">
                        <i class="fa-solid fa-file-invoice text-amber-400 text-sm"></i>
                    </div>
                    Laporan Supplier
                </a>
            @endif

            @if(Auth::user()->email === 'admin@gsc.com')
                <div class="px-4 mb-2 mt-6">
                    <span class="text-[10px] font-bold text-slate-500 tracking-widest uppercase">Kepengurusan</span>
                </div>
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-semibold text-slate-400 hover:bg-slate-800/80 hover:text-slate-200 transition-all {{ Request::routeIs('admin.users.index') ? 'sidebar-link-active' : '' }}">
                    <div class="w-7 h-7 rounded-lg bg-slate-800/50 flex items-center justify-center shrink-0 border border-slate-700/50">
                        <i class="fa-solid fa-users text-indigo-400 text-sm"></i>
                    </div>
                    Kelola User
                </a>
                <a href="{{ route('admin.ports.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-semibold text-slate-400 hover:bg-slate-800/80 hover:text-slate-200 transition-all {{ Request::routeIs('admin.ports.index') ? 'sidebar-link-active' : '' }}">
                    <div class="w-7 h-7 rounded-lg bg-slate-800/50 flex items-center justify-center shrink-0 border border-slate-700/50">
                        <i class="fa-solid fa-anchor text-indigo-400 text-sm"></i>
                    </div>
                    Kelola Pelabuhan
                </a>
                <a href="{{ route('admin.articles.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-semibold text-slate-400 hover:bg-slate-800/80 hover:text-slate-200 transition-all {{ Request::routeIs('admin.articles.index') ? 'sidebar-link-active' : '' }}">
                    <div class="w-7 h-7 rounded-lg bg-slate-800/50 flex items-center justify-center shrink-0 border border-slate-700/50">
                        <i class="fa-solid fa-newspaper text-indigo-400 text-sm"></i>
                    </div>
                    Kelola Artikel
                </a>
                <a href="{{ route('admin.words.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-semibold text-slate-400 hover:bg-slate-800/80 hover:text-slate-200 transition-all {{ Request::routeIs('admin.words.index') ? 'sidebar-link-active' : '' }}">
                    <div class="w-7 h-7 rounded-lg bg-slate-800/50 flex items-center justify-center shrink-0 border border-slate-700/50">
                        <i class="fa-solid fa-book-atlas text-indigo-400 text-sm"></i>
                    </div>
                    Kelola Kamus
                </a>
            @endif
            <div class="px-4 mb-2 mt-6">
                <span class="text-[10px] font-bold text-slate-500 tracking-widest uppercase">Akun</span>
            </div>
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-semibold text-slate-400 hover:bg-slate-800/80 hover:text-slate-200 transition-all {{ Request::routeIs('profile.*') ? 'sidebar-link-active' : '' }}">
                <div class="w-7 h-7 rounded-lg bg-slate-800/50 flex items-center justify-center shrink-0 border border-slate-700/50">
                    <i class="fa-solid fa-gear text-slate-400 text-sm"></i>
                </div>
                Pengaturan Akun
            </a>
        </nav>

        <!-- Sidebar Footer / Profile -->
        <div class="p-5 border-t border-slate-800 flex items-center justify-between bg-slate-900/80 relative z-10">
            <div class="flex items-center gap-3 overflow-hidden">
                <div class="bg-gradient-to-tr from-slate-700 to-slate-800 border border-slate-600/50 text-white font-bold p-2.5 rounded-xl shrink-0 w-10 h-10 flex items-center justify-center uppercase shadow-sm">
                    {{ substr(Auth::user()->name, 0, 2) }}
                </div>
                <div class="truncate">
                    <p class="text-sm font-bold text-slate-200 leading-tight truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-slate-500 font-medium truncate mt-0.5">{{ Auth::user()->email }}</p>
                </div>
            </div>
            
            <form id="logout-form" method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="button" onclick="document.getElementById('logout-form').submit();" class="text-slate-400 hover:text-rose-400 transition-colors cursor-pointer p-2 rounded-lg bg-slate-800 hover:bg-slate-700 border border-slate-700 flex items-center justify-center" title="Keluar">
                    <i class="fa-solid fa-right-from-bracket text-xs"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col overflow-hidden p-6">
        <!-- Render Top Navbar only for non-dashboard pages -->
        @if(!Request::routeIs('dashboard'))
            <header class="glass-panel rounded-3xl mb-8 h-16 flex items-center justify-between px-8 shrink-0">
                <div class="flex items-center gap-4">
                    <h2 class="text-lg font-bold text-slate-800">
                        @yield('title', 'Kendali Risiko Global')
                    </h2>
                </div>
                
                <div class="flex items-center gap-8">
                    <!-- Sinkronisasi Global -->
                    <a href="{{ route('countries.sync') }}" class="bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-600 px-4 py-2 rounded-xl text-sm font-bold transition-all flex items-center gap-2 shadow-sm">
                        <i class="fa-solid fa-rotate"></i>
                        Sinkronisasi API Global
                    </a>
                </div>
            </header>
        @endif

        <!-- Scrollable content wrapper -->
        <main class="flex-1 overflow-y-auto pr-1">
            <!-- Messages / Alerts -->
            @if(session('success'))
                <div class="mb-8 bg-emerald-900/40 border border-emerald-500/30 p-4 rounded-2xl shadow-sm flex items-center gap-3 backdrop-blur-md">
                    <i class="fa-solid fa-circle-check text-emerald-400 text-lg"></i>
                    <p class="text-sm font-bold text-emerald-100">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-8 bg-rose-900/40 border border-rose-500/30 p-4 rounded-2xl shadow-sm flex items-center gap-3 backdrop-blur-md">
                    <i class="fa-solid fa-circle-xmark text-rose-400 text-lg"></i>
                    <p class="text-sm font-bold text-rose-100">{{ session('error') }}</p>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

</body>
</html>