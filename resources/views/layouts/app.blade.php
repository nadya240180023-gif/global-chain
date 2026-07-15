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
            background: linear-gradient(135deg, #a78bfa, #6366f1);
            color: #ffffff !important;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.2);
        }
        .sidebar-link-active i {
            color: #ffffff !important;
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
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="bg-[#f3f7f9] text-slate-800 h-full flex flex-col antialiased">

<div class="flex h-full w-full overflow-hidden p-4 gap-6">
    <!-- Sidebar -->
    <aside class="w-72 bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.02)] flex flex-col shrink-0 overflow-hidden">
        <!-- Logo -->
        <div class="p-6 border-b border-slate-100 flex items-center gap-3">
            <div class="bg-gradient-to-tr from-violet-500 to-indigo-600 text-white p-2.5 rounded-xl shadow-md shadow-indigo-500/10 flex items-center justify-center shrink-0 w-11 h-11">
                <i class="fa-solid fa-globe text-xl"></i>
            </div>
            <div>
                <h1 class="text-lg font-extrabold tracking-wider text-slate-800 leading-none">GSC RISK</h1>
                <span class="text-[10px] text-slate-400 font-semibold tracking-wider uppercase block mt-1">Intelijen Risiko</span>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 overflow-y-auto py-4 space-y-1 px-4">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all {{ Request::routeIs('dashboard') ? 'sidebar-link-active' : '' }}">
                <i class="fa-solid fa-chart-pie text-lg text-slate-400"></i>
                Dasbor Utama
            </a>

            <a href="{{ route('countries.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all {{ Request::routeIs('countries.*') ? 'sidebar-link-active' : '' }}">
                <i class="fa-solid fa-flag text-lg text-slate-400"></i>
                Data Negara
            </a>

            <a href="{{ route('countries.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all {{ Request::routeIs('countries.index') ? 'sidebar-link-active' : '' }}">
                <i class="fa-solid fa-triangle-exclamation text-lg text-slate-400"></i>
                Pemantauan Risiko
            </a>

            <a href="{{ route('weather.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all {{ Request::routeIs('weather.index') ? 'sidebar-link-active' : '' }}">
                <i class="fa-solid fa-cloud-sun text-lg text-slate-400"></i>
                Pemantauan Cuaca
            </a>

            <a href="{{ route('currency.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all {{ Request::routeIs('currency.index') ? 'sidebar-link-active' : '' }}">
                <i class="fa-solid fa-money-bill-transfer text-lg text-slate-400"></i>
                Nilai Tukar Mata Uang
            </a>

            <a href="{{ route('news.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all {{ Request::routeIs('news.index') ? 'sidebar-link-active' : '' }}">
                <i class="fa-solid fa-newspaper text-lg text-slate-400"></i>
                Intelijen Berita
            </a>

            <a href="{{ route('ports.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all {{ Request::routeIs('ports.index') ? 'sidebar-link-active' : '' }}">
                <i class="fa-solid fa-anchor text-lg text-slate-400"></i>
                Dasbor Pelabuhan
            </a>

            <a href="{{ route('comparison.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all {{ Request::routeIs('comparison.index') ? 'sidebar-link-active' : '' }}">
                <i class="fa-solid fa-arrow-right-arrow-left text-lg text-slate-400"></i>
                Perbandingan Negara
            </a>

            <a href="{{ route('watchlist.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all {{ Request::routeIs('watchlist.index') ? 'sidebar-link-active' : '' }}">
                <i class="fa-solid fa-bookmark text-lg text-slate-400"></i>
                Daftar Pantauan
            </a>

            <a href="{{ route('suppliers.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all {{ Request::routeIs('suppliers.*') ? 'sidebar-link-active' : '' }}">
                <i class="fa-solid fa-file-invoice text-lg text-slate-400"></i>
                Laporan Supplier
            </a>

            <a href="{{ route('admin.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all {{ Request::routeIs('admin.index') ? 'sidebar-link-active' : '' }}">
                <i class="fa-solid fa-user-gear text-lg text-slate-400"></i>
                Panel Admin
            </a>

            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all {{ Request::routeIs('profile.*') ? 'sidebar-link-active' : '' }}">
                <i class="fa-solid fa-gear text-lg text-slate-400"></i>
                Pengaturan
            </a>
        </nav>

        <!-- Sidebar Footer / Profile -->
        <div class="p-4 border-t border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3 overflow-hidden">
                <div class="bg-gradient-to-tr from-violet-100 to-indigo-100 text-indigo-700 font-bold p-2.5 rounded-2xl shrink-0 w-11 h-11 flex items-center justify-center uppercase shadow-inner">
                    {{ substr(Auth::user()->name, 0, 2) }}
                </div>
                <div class="truncate">
                    <p class="text-sm font-bold text-slate-800 leading-tight truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-slate-400 font-medium truncate mt-0.5">{{ Auth::user()->email }}</p>
                </div>
            </div>
            
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="text-slate-400 hover:text-rose-600 transition-colors cursor-pointer p-2 rounded-xl" title="Keluar">
                    <i class="fa-solid fa-right-from-bracket text-lg"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Render Top Navbar only for non-dashboard pages -->
        @if(!Request::routeIs('dashboard'))
            <header class="bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.02)] mb-6 h-16 flex items-center justify-between px-8 shrink-0">
                <div class="flex items-center gap-4">
                    <h2 class="text-lg font-bold text-slate-800">
                        @yield('title', 'Global Risk Control')
                    </h2>
                </div>
                
                <div class="flex items-center gap-6">
                    <!-- Sinkronisasi Global -->
                    <a href="{{ route('countries.sync') }}" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 px-4 py-2 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
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
                <div class="mb-6 bg-emerald-50 border border-emerald-100 p-4 rounded-2xl shadow-sm flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                    <p class="text-sm font-bold text-emerald-800">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-rose-50 border border-rose-100 p-4 rounded-2xl shadow-sm flex items-center gap-3">
                    <i class="fa-solid fa-circle-xmark text-rose-600 text-lg"></i>
                    <p class="text-sm font-bold text-rose-800">{{ session('error') }}</p>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

</body>
</html>