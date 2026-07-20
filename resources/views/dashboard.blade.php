@extends('layouts.app')

@section('content')

<style>
/* ── Risk-marker pulse animation ── */
@keyframes riskPulse {
    0%   { transform: scale(0.7); opacity: 1; }
    70%  { transform: scale(2.6); opacity: 0; }
    100% { transform: scale(0.7); opacity: 0; }
}

/* ── Leaflet popup re-style ── */
.leaflet-popup-content-wrapper {
    border-radius: 18px !important;
    box-shadow: 0 12px 40px rgba(0,0,0,0.13) !important;
    border: 1px solid rgba(226,232,240,0.9) !important;
    padding: 0 !important;
    overflow: hidden;
}
.leaflet-popup-content {
    margin: 14px 16px !important;
}
.leaflet-popup-tip-container { margin-top: -1px; }
.leaflet-popup-close-button {
    color: #94a3b8 !important;
    font-size: 18px !important;
    top: 8px !important;
    right: 10px !important;
}

/* ── Port tooltip ── */
.port-tooltip.leaflet-tooltip {
    background: #f0f4f8 !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 9px !important;
    padding: 6px 12px !important;
    box-shadow: 0 4px 16px rgba(0,0,0,0.09) !important;
    font-family: 'Outfit', sans-serif !important;
    font-size: 13px !important;
    font-weight: 700 !important;
    color: #334155 !important;
}
.port-tooltip.leaflet-tooltip::before { display: none !important; }

/* ── Zoom controls ── */
.leaflet-control-zoom {
    border: none !important;
    box-shadow: 0 4px 16px rgba(0,0,0,0.10) !important;
    border-radius: 12px !important;
    overflow: hidden;
}
.leaflet-control-zoom a {
    background: #f0f4f8 !important;
    color: #475569 !important;
    border: none !important;
    font-family: 'Outfit', sans-serif !important;
    font-weight: 700 !important;
    width: 30px !important;
    height: 30px !important;
    line-height: 29px !important;
}
.leaflet-control-zoom a:hover { background: #f8fafc !important; color: #3b82f6 !important; }
</style>

<div class="flex flex-col gap-8 text-slate-800 pb-12">
    <!-- Header & Search Bar Row -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-blue-700 to-indigo-700">Dasbor Global</h1>
            <p class="text-sm text-slate-400 font-semibold mt-1">Ringkasan risiko rantai pasok global</p>
        </div>

        <div class="flex items-center gap-4 w-full md:w-auto">
            <!-- Search Bar -->
            <div class="relative w-full md:w-80">
                <input type="text" placeholder="Cari negara, pelabuhan, berita..." class="w-full bg-white/60 border border-white/80 backdrop-blur-md rounded-2xl py-2.5 pl-10 pr-4 text-sm font-semibold text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 shadow-[0_4px_20px_rgba(0,0,0,0.01)] transition-all">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </div>
            </div>

            <!-- Notifications -->
            <button class="relative bg-white/60 border border-white/80 p-3 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.04)] hover:bg-white/90 backdrop-blur-md transition-all text-slate-400 cursor-pointer w-11 h-11 flex items-center justify-center shrink-0">
                <i class="fa-regular fa-bell text-sm"></i>
                <span class="absolute top-2 right-2.5 bg-rose-500 border-2 border-white w-2.5 h-2.5 rounded-full"></span>
            </button>

            <!-- Profile Icon Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" @click.away="open = false" class="bg-gradient-to-tr from-blue-500 to-blue-700 text-white rounded-2xl w-11 h-11 flex items-center justify-center font-bold text-sm shadow-md shadow-blue-500/10 cursor-pointer shrink-0 focus:outline-none">
                    {{ substr(Auth::user()->name, 0, 2) }}
                </button>
                
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50"
                     style="display: none;">
                    <div class="px-4 py-2 border-b border-slate-100">
                        <p class="text-xs font-black text-slate-800 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-slate-400 font-semibold truncate mt-0.5">{{ Auth::user()->email }}</p>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                        <i class="fa-solid fa-user-gear text-slate-400"></i> Pengaturan Akun
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="block w-full">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-rose-600 hover:bg-rose-50 transition-colors text-left">
                            <i class="fa-solid fa-right-from-bracket"></i> Keluar (Logout)
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Metrics Row (5 cards) -->
    <!-- Metrics Row (5 cards) - Uniform Elegant Design (Custom Stats for Admin) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <!-- Global Risk Score (Avg) -->
        <div class="bg-white rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:-translate-y-1 hover:shadow-[0_12px_40px_rgb(0,0,0,0.08)] transition-all duration-300 cursor-pointer flex flex-col justify-between h-36 border border-slate-200/60 group">
            <span class="text-[11px] text-slate-500 font-extrabold uppercase tracking-widest block">Skor Risiko Global (Rata-rata)</span>
            <div class="flex items-baseline gap-1.5 mt-2">
                <span class="text-4xl font-black text-slate-800">{{ $globalRiskAvg }}</span>
                <span class="text-sm text-slate-400 font-bold">/100</span>
            </div>
            <div class="flex items-center justify-between mt-auto pt-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[10px] font-extrabold tracking-widest uppercase bg-slate-50 text-slate-600 border border-slate-100 group-hover:bg-blue-50 group-hover:text-blue-600 group-hover:border-blue-100 transition-colors">
                    {{ $globalRiskAvg >= 70 ? 'Risiko Tinggi' : ($globalRiskAvg >= 35 ? 'Risiko Menengah' : 'Risiko Rendah') }}
                </span>
                <!-- Sparkline Placeholder SVG -->
                <svg class="w-12 h-6 text-slate-300 group-hover:text-blue-400 transition-colors" viewBox="0 0 100 30" fill="none">
                    <path d="M0,25 Q15,5 30,20 T60,10 T90,15 T100,5" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round"/>
                </svg>
            </div>
        </div>

        @if(Auth::user()->email === 'admin@gsc.com')
            <!-- Total Pengguna (Admin) -->
            <div class="bg-white rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:-translate-y-1 hover:shadow-[0_12px_40px_rgb(0,0,0,0.08)] transition-all duration-300 cursor-pointer flex flex-col justify-between h-36 border border-slate-200/60 group">
                <span class="text-[11px] text-slate-500 font-extrabold uppercase tracking-widest block">Total Pengguna Terdaftar</span>
                <div class="text-4xl font-black text-slate-800 mt-2">{{ $totalUsers }}</div>
                <div class="flex items-center gap-1.5 text-[11px] font-bold mt-auto pt-2 text-indigo-600">
                    <i class="fa-solid fa-users"></i>
                    <span>Kelola Pengguna Sistem</span>
                </div>
            </div>

            <!-- Total Artikel Analisis (Admin) -->
            <div class="bg-white rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:-translate-y-1 hover:shadow-[0_12px_40px_rgb(0,0,0,0.08)] transition-all duration-300 cursor-pointer flex flex-col justify-between h-36 border border-slate-200/60 group">
                <span class="text-[11px] text-slate-500 font-extrabold uppercase tracking-widest block">Total Artikel Analisis</span>
                <div class="text-4xl font-black text-slate-800 mt-2">{{ $totalArticles }}</div>
                <div class="flex items-center gap-1.5 text-[11px] font-bold mt-auto pt-2 text-indigo-600">
                    <i class="fa-solid fa-newspaper"></i>
                    <span>Kelola Publikasi Artikel</span>
                </div>
            </div>

            <!-- Total Pelabuhan Terdaftar (Admin) -->
            <div class="bg-white rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:-translate-y-1 hover:shadow-[0_12px_40px_rgb(0,0,0,0.08)] transition-all duration-300 cursor-pointer flex flex-col justify-between h-36 border border-slate-200/60 group">
                <span class="text-[11px] text-slate-500 font-extrabold uppercase tracking-widest block">Total Dataset Pelabuhan</span>
                <div class="text-4xl font-black text-slate-800 mt-2">{{ $totalPorts }}</div>
                <div class="flex items-center gap-1.5 text-[11px] font-bold mt-auto pt-2 text-indigo-600">
                    <i class="fa-solid fa-anchor"></i>
                    <span>Kelola Geodataset Pelabuhan</span>
                </div>
            </div>
        @else
            <!-- High-Risk Countries -->
            <div class="bg-white rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:-translate-y-1 hover:shadow-[0_12px_40px_rgb(0,0,0,0.08)] transition-all duration-300 cursor-pointer flex flex-col justify-between h-36 border border-slate-200/60 group">
                <span class="text-[11px] text-slate-500 font-extrabold uppercase tracking-widest block">Negara Risiko Tinggi</span>
                <div class="text-4xl font-black text-slate-800 mt-2">{{ $highRiskCount }}</div>
                <div class="flex items-center gap-1.5 text-[11px] font-bold mt-auto pt-2">
                    <div class="flex items-center gap-1 px-2 py-1 rounded-md bg-rose-50 text-rose-500 border border-rose-100">
                        <i class="fa-solid fa-arrow-trend-up text-[10px]"></i>
                        <span>+10%</span>
                    </div>
                    <span class="text-slate-400 font-medium">dari minggu lalu</span>
                </div>
            </div>

            <!-- Medium Risk Countries -->
            <div class="bg-white rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:-translate-y-1 hover:shadow-[0_12px_40px_rgb(0,0,0,0.08)] transition-all duration-300 cursor-pointer flex flex-col justify-between h-36 border border-slate-200/60 group">
                <span class="text-[11px] text-slate-500 font-extrabold uppercase tracking-widest block">Negara Risiko Menengah</span>
                <div class="text-4xl font-black text-slate-800 mt-2">{{ $mediumRiskCount }}</div>
                <div class="flex items-center gap-1.5 text-[11px] font-bold mt-auto pt-2">
                    <div class="flex items-center gap-1 px-2 py-1 rounded-md bg-emerald-50 text-emerald-500 border border-emerald-100">
                        <i class="fa-solid fa-arrow-trend-down text-[10px]"></i>
                        <span>-5%</span>
                    </div>
                    <span class="text-slate-400 font-medium">dari minggu lalu</span>
                </div>
            </div>

            <!-- Low Risk Countries -->
            <div class="bg-white rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:-translate-y-1 hover:shadow-[0_12px_40px_rgb(0,0,0,0.08)] transition-all duration-300 cursor-pointer flex flex-col justify-between h-36 border border-slate-200/60 group">
                <span class="text-[11px] text-slate-500 font-extrabold uppercase tracking-widest block">Negara Risiko Rendah</span>
                <div class="text-4xl font-black text-slate-800 mt-2">{{ $lowRiskCount }}</div>
                <div class="flex items-center gap-1.5 text-[11px] font-bold mt-auto pt-2">
                    <div class="flex items-center gap-1 px-2 py-1 rounded-md bg-rose-50 text-rose-500 border border-rose-100">
                        <i class="fa-solid fa-arrow-trend-up text-[10px]"></i>
                        <span>+3%</span>
                    </div>
                    <span class="text-slate-400 font-medium">dari minggu lalu</span>
                </div>
            </div>
        @endif

        <!-- Monitored Countries -->
        <div class="bg-white rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:-translate-y-1 hover:shadow-[0_12px_40px_rgb(0,0,0,0.08)] transition-all duration-300 cursor-pointer flex flex-col justify-between h-36 border border-slate-200/60 group">
            <span class="text-[11px] text-slate-500 font-extrabold uppercase tracking-widest block">Negara Terpantau</span>
            <div class="text-4xl font-black text-slate-800 mt-2">{{ $monitoredCount }}</div>
            <div class="flex items-center gap-1.5 text-[11px] font-bold mt-auto pt-2">
                <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-blue-50 text-blue-600 border border-blue-100">
                    <i class="fa-solid fa-globe"></i> Node Aktif
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════════ SECTION 1: MAPS (2-COLUMNS) ══════════════════ -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Risk Map Card -->
        <div class="bg-slate-100 rounded-3xl border border-white/60 shadow-[0_8px_30px_rgba(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgba(0,0,0,0.08)] transition-all duration-300 overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-2.5">
                        <h2 class="text-base font-extrabold text-slate-800">Peta Risiko Global</h2>
                        <span class="flex items-center gap-1.5 bg-emerald-50 text-emerald-600 text-xs font-black px-2.5 py-1 rounded-full uppercase tracking-wide">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse inline-block"></span>
                            Live
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 font-semibold mt-0.5">Penilaian risiko visual dari simpul logistik aktif</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="flex items-center gap-1.5 text-xs font-bold text-slate-400"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Rendah</span>
                    <span class="flex items-center gap-1.5 text-xs font-bold text-slate-400"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Menengah</span>
                    <span class="flex items-center gap-1.5 text-xs font-bold text-slate-400"><span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse"></span> Tinggi</span>
                </div>
            </div>
            <!-- Leaflet World Map Container with Floating Overlays -->
            <div class="relative">
                <div id="risk-map" class="h-[440px] w-full bg-slate-100"></div>
                <!-- Floating glassmorphism stat chips -->
                <div class="absolute bottom-3 left-3 z-[400] flex flex-wrap gap-2 pointer-events-none">
                    <div class="bg-slate-100/90 backdrop-blur-sm rounded-xl px-3 py-1.5 shadow-lg border border-white/80 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse shrink-0"></span>
                        <span class="text-xs font-black text-slate-700">{{ $highRiskCount }} Risiko Tinggi</span>
                    </div>
                    <div class="bg-slate-100/90 backdrop-blur-sm rounded-xl px-3 py-1.5 shadow-lg border border-white/80 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                        <span class="text-xs font-black text-slate-700">{{ $mediumRiskCount }} Menengah</span>
                    </div>
                    <div class="bg-slate-100/90 backdrop-blur-sm rounded-xl px-3 py-1.5 shadow-lg border border-white/80 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                        <span class="text-xs font-black text-slate-700">{{ $lowRiskCount }} Rendah</span>
                    </div>
                </div>
                <!-- Floating total monitored chip -->
                <div class="absolute top-3 right-3 z-[400] pointer-events-none">
                    <div class="bg-slate-100/90 backdrop-blur-sm rounded-xl px-3 py-1.5 shadow-lg border border-white/80 flex items-center gap-1.5">
                        <i class="fa-solid fa-earth-asia text-xs text-blue-500"></i>
                        <span class="text-xs font-black text-slate-700">{{ $monitoredCount }} Negara</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Port Location Map Card (ENLARGED & RE-LAYOUT) -->
        <div class="bg-slate-100 rounded-3xl border border-white/60 shadow-[0_8px_30px_rgba(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgba(0,0,0,0.08)] transition-all duration-300 overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-extrabold text-slate-800">Peta Lokasi Pelabuhan</h3>
                    <p class="text-xs text-slate-400 font-semibold mt-0.5">Pemantauan geografis pelabuhan logistik aktif</p>
                </div>
                <span class="flex items-center gap-1.5 bg-blue-50 text-blue-600 text-xs font-black px-2.5 py-1 rounded-full uppercase tracking-wide">
                    <i class="fa-solid fa-anchor text-xs"></i>
                    {{ count($allPorts) }} Pelabuhan
                </span>
            </div>
            <div class="relative">
                <div id="port-map" class="h-[300px] w-full bg-slate-100"></div>
                <!-- Port map overlay -->
                <div class="absolute top-2 right-2 z-[400] pointer-events-none">
                    <div class="bg-slate-100/90 backdrop-blur-sm rounded-xl px-3 py-1.5 shadow-lg border border-white/80 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse shrink-0"></span>
                        <span class="text-xs font-black text-slate-700">Klik pelabuhan untuk detail</span>
                    </div>
                </div>
            </div>
            
            <!-- Horizontal Dynamic Clicked Port Details Panel -->
            <div class="p-5 bg-slate-100/70 border-t border-slate-100 flex flex-col justify-between gap-3 h-[140px]">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-2">
                    <div>
                        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Pelabuhan Terpilih</span>
                        <h4 id="panel-port-name" class="text-sm font-black text-slate-800 mt-0.5">Port of Singapore</h4>
                    </div>
                    <a href="{{ route('ports.index') }}" class="text-blue-600 hover:text-blue-800 text-xs font-black transition-colors flex items-center gap-1 shrink-0">
                        Lihat Semua Pelabuhan <i class="fa-solid fa-chevron-right text-xs"></i>
                    </a>
                </div>
                <div class="grid grid-cols-4 gap-4 border-t border-slate-200/50 pt-3">
                    <div>
                        <span class="text-xs text-slate-400 font-semibold block">Negara</span>
                        <span id="panel-port-country" class="text-sm font-extrabold text-slate-700 block mt-0.5">Singapore</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 font-semibold block">Wilayah</span>
                        <span id="panel-port-region" class="text-sm font-extrabold text-slate-700 block mt-0.5">Southeast Asia</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 font-semibold block">Tipe</span>
                        <span id="panel-port-type" class="text-sm font-extrabold text-slate-700 block mt-0.5">Seaport</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 font-semibold block">Status</span>
                        <span id="panel-port-status" class="text-sm font-extrabold text-emerald-600 block mt-0.5">Active</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ══════════════════ SECTION 2: INTELLIGENCE (3-COLUMNS) ══════════════════ -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Top 5 Countries Card -->
        <div class="bg-slate-100 rounded-3xl p-6 border border-white/60 shadow-[0_8px_30px_rgba(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgba(0,0,0,0.08)] transition-all duration-300 flex flex-col justify-between min-h-[340px]">
            <div>
                <h3 class="text-base font-extrabold text-slate-800 mb-4">Top 5 Negara Risiko Tertinggi</h3>
                <div class="divide-y divide-slate-100">
                    @foreach($top5Countries->values() as $index => $tc)
                        <div class="flex items-center justify-between py-3.5">
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-bold text-slate-400">{{ $index + 1 }}.</span>
                                @if($tc->country->flag)
                                    <img src="{{ $tc->country->flag }}" class="w-6 h-4.5 object-cover rounded shadow-sm border border-slate-100" alt="">
                                @endif
                                <span class="text-xs font-bold text-slate-700">{{ $tc->country->name }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-black {{ $tc->risk_level === 'High' ? 'text-rose-600' : ($tc->risk_level === 'Medium' ? 'text-amber-600' : 'text-emerald-600') }}">
                                    {{ $tc->total_score }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded-lg text-xs font-black uppercase tracking-wide
                                    {{ $tc->risk_level === 'High' ? 'bg-rose-50 text-rose-600' : ($tc->risk_level === 'Medium' ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600') }}
                                ">
                                    {{ $tc->risk_level }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="pt-4 border-t border-slate-100 text-center">
                <a href="{{ route('countries.index') }}" class="text-blue-600 hover:text-blue-800 text-xs font-black transition-colors flex items-center justify-center gap-1">
                    Lihat Semua Negara <i class="fa-solid fa-chevron-right text-xs"></i>
                </a>
            </div>
        </div>

        <!-- Global Indicators Card -->
        <div class="bg-slate-100 rounded-3xl p-6 border border-white/60 shadow-[0_8px_30px_rgba(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgba(0,0,0,0.08)] transition-all duration-300 flex flex-col justify-between min-h-[340px]">
            <h3 class="text-base font-extrabold text-slate-800 mb-4">Indikator Global</h3>
            <div class="grid grid-cols-2 gap-4 flex-1">
                <!-- GDP -->
                <div class="bg-slate-100/50 border border-slate-100 rounded-2xl p-3.5 flex flex-col justify-between">
                    <span class="text-xs text-slate-400 font-extrabold uppercase tracking-wide block">PDB Global (Total)</span>
                    <div class="text-base font-black text-slate-800 mt-2">${{ number_format($globalGdpValue / 1e12, 1) }} T</div>
                    <span class="text-xs font-bold text-emerald-500 flex items-center gap-1 mt-1">
                        <i class="fa-solid fa-caret-up"></i> +2.8%
                    </span>
                </div>
                <!-- Inflation -->
                <div class="bg-slate-100/50 border border-slate-100 rounded-2xl p-3.5 flex flex-col justify-between">
                    <span class="text-xs text-slate-400 font-extrabold uppercase tracking-wide block">Inflasi Global (Rata-rata)</span>
                    <div class="text-base font-black text-slate-800 mt-2">{{ number_format($globalInflation, 1) }}%</div>
                    <span class="text-xs font-bold text-rose-500 flex items-center gap-1 mt-1">
                        <i class="fa-solid fa-caret-down"></i> -0.6%
                    </span>
                </div>
                <!-- Currency -->
                <div class="bg-slate-100/50 border border-slate-100 rounded-2xl p-3.5 flex flex-col justify-between">
                    <span class="text-xs text-slate-400 font-extrabold uppercase tracking-wide block">Indeks Mata Uang Global</span>
                    <div class="text-base font-black text-slate-800 mt-2">{{ number_format($globalCurrencyRate, 1) }}</div>
                    <span class="text-xs font-bold text-emerald-500 flex items-center gap-1 mt-1">
                        <i class="fa-solid fa-caret-up"></i> +1.2%
                    </span>
                </div>
                <!-- Trade -->
                <div class="bg-slate-100/50 border border-slate-100 rounded-2xl p-3.5 flex flex-col justify-between">
                    <span class="text-xs text-slate-400 font-extrabold uppercase tracking-wide block">Perdagangan Global (Total)</span>
                    <div class="text-base font-black text-slate-800 mt-2">{{ $globalTrade }} Pengiriman</div>
                    <span class="text-xs font-bold text-emerald-500 flex items-center gap-1 mt-1">
                        <i class="fa-solid fa-caret-up"></i> +3.1%
                    </span>
                </div>
            </div>
        </div>

        <!-- Currency & Quick View Combined Column -->
        <div class="flex flex-col gap-8">
            <!-- Currency (USD/IDR) Card -->
            <div class="bg-slate-100 rounded-3xl p-6 border border-white/60 shadow-[0_8px_30px_rgba(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgba(0,0,0,0.08)] transition-all duration-300 flex flex-col">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-slate-400 font-extrabold uppercase tracking-wider block">Mata Uang (USD/IDR)</span>
                    <span class="text-xs text-emerald-500 font-extrabold flex items-center gap-1">
                        <i class="fa-solid fa-caret-up"></i> +0.40%
                    </span>
                </div>
                <div class="text-xl font-black text-slate-800">16,415.00 <span class="text-xs text-slate-400 font-bold ml-0.5">IDR</span></div>
                
                <!-- Chart.js Graph Canvas -->
                <div class="h-20 mt-3 w-full">
                    <canvas id="currency-chart" class="w-full h-full"></canvas>
                </div>
            </div>

            <!-- Country Quick View Card -->
            <div class="bg-slate-100 rounded-3xl p-6 border border-white/60 shadow-[0_8px_30px_rgba(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgba(0,0,0,0.08)] transition-all duration-300 flex flex-col justify-between">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-800 mb-2">Tinjauan Cepat Negara</h3>
                    
                    <!-- Country Dropdown Form -->
                    <form action="{{ route('dashboard') }}" method="GET" id="quickview-form" class="mb-2">
                        <div class="relative">
                            <select name="country" onchange="this.form.submit()" class="w-full bg-slate-100 border border-slate-100 text-slate-700 text-xs font-bold rounded-xl p-2.5 pr-8 appearance-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                @foreach($countries as $c)
                                    <option value="{{ $c->code }}" {{ $selectedCountry && $selectedCountry->code === $c->code ? 'selected' : '' }}>
                                        {{ $c->name }} ({{ $c->code }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-400">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </form>

                    <!-- Country Information Details -->
                    @if($selectedCountry)
                        <div class="space-y-1">
                            <div class="flex items-center justify-between text-xs font-semibold py-0.5">
                                <span class="text-slate-400">PDB (Nominal)</span>
                                <span class="text-slate-700 font-extrabold">
                                    ${{ $selectedCountry->gdpData()->orderBy('year', 'desc')->first() ? number_format($selectedCountry->gdpData()->orderBy('year', 'desc')->first()->gdp_value / 1e9, 2) . ' B' : 'N/A' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-xs font-semibold py-0.5">
                                <span class="text-slate-400">Tingkat Inflasi</span>
                                <span class="text-slate-700 font-extrabold">
                                    {{ $selectedCountry->inflationData()->orderBy('year', 'desc')->first() ? number_format($selectedCountry->inflationData()->orderBy('year', 'desc')->first()->inflation_rate, 2) . '%' : 'N/A' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-xs font-semibold py-0.5">
                                <span class="text-slate-400">Skor Risiko</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wide
                                    {{ $latestScore?->risk_level === 'High' ? 'bg-rose-50 text-rose-600' : ($latestScore?->risk_level === 'Medium' ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600') }}
                                ">
                                    {{ $latestScore ? $latestScore->total_score : 20 }} ({{ $latestScore ? $latestScore->risk_level : 'Low' }})
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    <!-- ══════════════════ SECTION 3: RECENT NEWS (FULL-WIDTH) ══════════════════ -->
    <div class="bg-slate-100 rounded-3xl p-6 border border-white/60 shadow-[0_8px_30px_rgba(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgba(0,0,0,0.08)] transition-all duration-300">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center">
                    <i class="fa-solid fa-newspaper text-blue-500 text-sm"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-800">Berita Terbaru</h3>
                    <p class="text-xs text-slate-400 font-semibold">Intelijen berita rantai pasok global terkini</p>
                </div>
                <span class="flex items-center gap-1.5 bg-emerald-50 text-emerald-600 text-xs font-black px-2.5 py-1 rounded-full uppercase tracking-wide">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse inline-block"></span>
                    Live
                </span>
            </div>
            <a href="{{ route('news.index') }}" class="text-blue-600 hover:text-blue-800 text-xs font-black transition-colors flex items-center gap-1">
                Lihat Semua Berita <i class="fa-solid fa-chevron-right text-xs"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($recentNews->take(3) as $news)
                <div class="flex flex-col gap-2.5 p-4 rounded-2xl bg-white border border-slate-100 hover:-translate-y-0.5 hover:shadow-md transition-all duration-200">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-lg bg-slate-50 text-slate-600 text-xs font-bold uppercase border border-slate-100">
                            {{ $news->country->name }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold uppercase
                            {{ $news->sentiment === 'Negative' ? 'bg-rose-50 text-rose-600 border border-rose-100' : ($news->sentiment === 'Positive' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-slate-50 text-slate-400 border border-slate-100') }}
                        ">
                            {{ $news->sentiment }}
                        </span>
                        <span class="text-xs text-slate-400 font-bold ml-auto">
                            {{ \Carbon\Carbon::parse($news->published_at)->diffForHumans() }}
                        </span>
                    </div>
                    @if($news->url && $news->url !== '#')
                        <a href="{{ $news->url }}" target="_blank" class="group block flex-1">
                            <p class="text-sm font-semibold text-slate-700 group-hover:text-blue-600 transition-colors leading-relaxed line-clamp-3">
                                {{ $news->title }}
                            </p>
                        </a>
                    @else
                        <p class="text-sm font-semibold text-slate-700 leading-relaxed line-clamp-3 flex-1">
                            {{ $news->title }}
                        </p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- ══════════════════ SECTION 4: WEATHER ALERT ══════════════════ -->
    <div>
        @if($extremeWeather)
            <div class="bg-rose-50 border border-rose-100 rounded-3xl p-6 shadow-[0_8px_30px_rgba(244,63,94,0.02)] flex flex-col sm:flex-row items-start sm:items-center gap-6">
                <div class="flex items-start gap-4 flex-1">
                    <div class="bg-rose-500 text-white p-3 rounded-2xl shadow-md shadow-rose-500/20 flex items-center justify-center shrink-0 w-12 h-12 animate-pulse">
                        <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-rose-900">Peringatan Cuaca Ekstrem</h4>
                        <p class="text-xs text-rose-700 font-semibold mt-1.5 leading-relaxed">
                            Peringatan hujan lebat / angin di {{ $extremeWeather->country->name }}: Suhu {{ $extremeWeather->temperature }}°C, Hujan {{ $extremeWeather->rainfall }}mm, Angin {{ $extremeWeather->wind_speed }} km/j
                        </p>
                    </div>
                </div>
                <a href="{{ route('weather.index') }}" class="bg-white hover:bg-rose-100 text-rose-600 font-black text-xs px-5 py-2.5 rounded-xl border border-rose-200 transition-all shrink-0">
                    Lihat Detail Cuaca
                </a>
            </div>
        @else
            <div class="bg-emerald-50 border border-emerald-100 rounded-3xl p-6 flex flex-col sm:flex-row items-start sm:items-center gap-6">
                <div class="flex items-start gap-4 flex-1">
                    <div class="bg-emerald-500 text-white p-3 rounded-2xl flex items-center justify-center shrink-0 w-12 h-12">
                        <i class="fa-solid fa-circle-check text-xl"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-emerald-900">Prakiraan Cuaca Normal</h4>
                        <p class="text-xs text-emerald-700 font-semibold mt-1.5 leading-relaxed">
                            Tidak ada kondisi cuaca ekstrem yang terdeteksi di simpul logistik. Rute pelabuhan beroperasi normal.
                        </p>
                    </div>
                </div>
                <a href="{{ route('weather.index') }}" class="bg-white hover:bg-emerald-100 text-emerald-600 font-black text-xs px-5 py-2.5 rounded-xl border border-emerald-200 transition-all shrink-0">
                    Lihat Peta Cuaca
                </a>
            </div>
        @endif
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        // ══════════════════════════════════════════════════════════
        // 1.  GLOBAL RISK MAP  — animated pulsing markers
        // ══════════════════════════════════════════════════════════
        const riskMap = L.map('risk-map', {
            zoomControl: false,
            attributionControl: false,
            scrollWheelZoom: true, // Enable zoom with mouse scroll
            dragging: true,        // Explicitly enable map dragging
            tap: true,
            touchZoom: true,
            worldCopyJump: true,
            minZoom: 1.5,
        }).setView([20, 0], 2);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            maxZoom: 18,
        }).addTo(riskMap);

        L.control.zoom({ position: 'bottomright' }).addTo(riskMap);
        L.control.attribution({ prefix: '<span style="font-size:9px;color:#94a3b8;">© CARTO · OpenStreetMap</span>' }).addTo(riskMap);

        // Custom animated divIcon factory
        const createRiskMarker = (level, score) => {
            const cfg = {
                High:   { fill: '#f43f5e', glow: 'rgba(244,63,94,0.4)',   r: 15, speed: '1.6s' },
                Medium: { fill: '#f59e0b', glow: 'rgba(245,158,11,0.35)', r: 11, speed: '2.4s' },
                Low:    { fill: '#10b981', glow: 'rgba(16,185,129,0.3)',  r: 8,  speed: null   },
            };
            const c = cfg[level] || cfg.Low;
            const outer = c.r + 14;
            const pulseStyle = c.speed
                ? `animation:riskPulse ${c.speed} ease-out infinite;`
                : '';
            return L.divIcon({
                html: `
                    <div style="position:relative;width:${outer}px;height:${outer}px;">
                        <div style="position:absolute;inset:0;border-radius:50%;background:${c.glow};${pulseStyle}"></div>
                        <div style="
                            position:absolute;top:50%;left:50%;
                            width:${c.r}px;height:${c.r}px;
                            border-radius:50%;
                            background:${c.fill};
                            border:2.5px solid #f0f4f8;
                            box-shadow:0 3px 14px ${c.glow};
                            transform:translate(-50%,-50%);
                        "></div>
                    </div>`,
                className: '',
                iconSize:   [outer, outer],
                iconAnchor: [outer / 2, outer / 2],
            });
        };

        const countriesScores = @json($latestScores);
        countriesScores.forEach(item => {
            if (item.country && item.country.latitude && item.country.longitude) {
                const marker = L.marker(
                    [item.country.latitude, item.country.longitude],
                    { icon: createRiskMarker(item.risk_level, item.total_score) }
                ).addTo(riskMap);

                const lc = item.risk_level === 'High' ? '#f43f5e' : item.risk_level === 'Medium' ? '#f59e0b' : '#10b981';
                const lb = item.risk_level === 'High' ? '#e0f2fe1f2' : item.risk_level === 'Medium' ? '#e0f2febeb' : '#f0fdf4';
                const barW = Math.min(Math.round(item.total_score), 100);

                marker.bindPopup(`
                    <div style="font-family:'Outfit',sans-serif;min-width:175px;">
                        <div style="font-weight:900;font-size:15px;color:#f0f4f8;letter-spacing:-0.01em;padding-bottom:8px;border-bottom:1px solid #1e293b;">
                            ${item.country.name}
                        </div>
                        <div style="margin-top:10px;">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:5px;">
                                <span style="font-size:12px;font-weight:700;color:#94a3b8;">Skor Risiko</span>
                                <span style="font-weight:900;font-size:18px;color:${lc};">${item.total_score}</span>
                            </div>
                            <div style="width:100%;background:#1e293b;border-radius:99px;height:5px;overflow:hidden;">
                                <div style="width:${barW}%;background:${lc};height:100%;border-radius:99px;"></div>
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:10px;">
                            <span style="font-size:12px;font-weight:700;color:#94a3b8;">Tingkat</span>
                            <span style="background:${lb};color:${lc};font-weight:800;font-size:11px;padding:4px 12px;border-radius:8px;text-transform:uppercase;letter-spacing:0.05em;">Risiko ${item.risk_level === 'High' ? 'Tinggi' : (item.risk_level === 'Medium' ? 'Menengah' : 'Rendah')}</span>
                        </div>
                    </div>
                `, { maxWidth: 220 });
            }
        });

        // ══════════════════════════════════════════════════════════
        // 2.  PORT MAP  — custom blue markers with pulse
        // ══════════════════════════════════════════════════════════
        const portMap = L.map('port-map', {
            zoomControl: false,
            attributionControl: false,
            scrollWheelZoom: true, // Enable zoom with mouse scroll
            dragging: true,        // Explicitly enable map dragging
            tap: true,
            touchZoom: true,
            worldCopyJump: true,
        }).setView([20, 0], 1);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            maxZoom: 18,
        }).addTo(portMap);

        L.control.zoom({ position: 'bottomright' }).addTo(portMap);

        const portIcon = L.divIcon({
            html: `
                <div style="position:relative;width:20px;height:20px;">
                    <div style="position:absolute;inset:0;border-radius:50%;background:rgba(59,130,246,0.25);animation:riskPulse 2.8s ease-out infinite;"></div>
                    <div style="
                        position:absolute;top:50%;left:50%;
                        width:10px;height:10px;
                        border-radius:50%;
                        background:#3b82f6;
                        border:2.5px solid #f0f4f8;
                        box-shadow:0 2px 10px rgba(59,130,246,0.55);
                        transform:translate(-50%,-50%);
                    "></div>
                </div>`,
            className: '',
            iconSize:   [20, 20],
            iconAnchor: [10, 10],
        });

        const allPorts = @json($allPorts);
        allPorts.forEach(port => {
            if (port.latitude && port.longitude) {
                const marker = L.marker([port.latitude, port.longitude], { icon: portIcon }).addTo(portMap);

                marker.on('click', function() {
                    document.getElementById('panel-port-name').innerText    = port.name;
                    document.getElementById('panel-port-country').innerText = port.country ? port.country.name : 'Tidak Diketahui';
                    document.getElementById('panel-port-region').innerText  = port.country ? port.country.region : 'Tidak Diketahui';
                    document.getElementById('panel-port-type').innerText    = 'Pelabuhan Laut';
                    document.getElementById('panel-port-status').innerText  = 'Aktif';
                });

                marker.bindTooltip(
                    `<span style="font-family:'Outfit',sans-serif;font-size:11px;font-weight:700;">${port.name}</span>`,
                    { permanent: false, direction: 'top', className: 'port-tooltip' }
                );
            }
        });

        // --- 3. Currency USD/IDR History Line Chart ---
        const historyData = @json($currencyHistory);
        const ctx = document.getElementById('currency-chart').getContext('2d');
        
        // Generate time labels based on points count (simulating hours/days)
        const labels = Array.from({length: historyData.length}, (_, i) => `${i*4}:00`);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'USD/IDR',
                    data: historyData,
                    borderColor: '#3b82f6',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#e0f2fefff',
                    pointBorderColor: '#2563eb',
                    pointHoverRadius: 5,
                    pointRadius: 2,
                    tension: 0.45,
                    fill: true,
                    backgroundColor: function(context) {
                        const chart = context.chart;
                        const {ctx, chartArea} = chart;
                        if (!chartArea) return null;
                        const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
                        gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');
                        return gradient;
                    }
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 11, family: 'Outfit' },
                            color: '#64748b'
                        }
                    },
                    y: {
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            font: { size: 11, family: 'Outfit' },
                            color: '#64748b'
                        }
                    }
                }
            }
        });
    });
</script>
@endsection