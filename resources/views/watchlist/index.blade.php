@extends('layouts.app')

@section('title', 'Daftar Pantauan Negara (Watchlist)')

@section('content')
<div class="space-y-8">

    {{-- ══ PAGE HEADER ══ --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-800 flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/20">
                    <i class="fa-solid fa-bookmark text-slate-800 text-base"></i>
                </span>
                Country Watchlist
            </h1>
            <p class="text-sm text-slate-400 font-medium mt-2 ml-[52px]">
                Monitor key risk score fluctuations and economic indicators for selected countries
            </p>
        </div>
    </div>

    {{-- ══ QUICK ADD SELECTOR ROW ══ --}}
    @if($availableCountries->isNotEmpty())
        <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100 shadow-sm">
            <h3 class="text-base font-extrabold text-slate-800 mb-1">Tambah Negara Pantauan Cepat</h3>
            <p class="text-xs text-slate-400 font-semibold mb-4">Tambahkan negara baru secara langsung ke dalam daftar pantauan Anda tanpa keluar halaman.</p>
            
            <form action="" method="POST" id="quick-add-form" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                @csrf
                <div class="relative flex-1">
                    <select id="quick-country-select" class="bg-slate-100 border border-slate-200 text-slate-800 text-sm font-bold rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 pr-8 appearance-none cursor-pointer transition-all">
                        <option value="" disabled selected>Pilih negara untuk ditambahkan...</option>
                        @foreach($availableCountries as $ac)
                            <option value="{{ $ac->id }}">{{ $ac->name }} ({{ $ac->code }})</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-400">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>
                </div>
                <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-600 hover:from-blue-700 hover:to-blue-700 text-slate-800 px-6 py-3 rounded-xl text-sm font-bold shadow-lg shadow-blue-500/20 hover:shadow-blue-500/35 transition-all flex items-center justify-center gap-2 cursor-pointer whitespace-nowrap">
                    <i class="fa-solid fa-plus"></i> Tambah ke Pantauan
                </button>
            </form>
        </div>
    @endif

    {{-- ══ WATCHLIST CONTENT ══ --}}
    @if($watchlistCountries->isEmpty())
        {{-- EMPTY STATE WITH DROPDOWN --}}
        <div class="bg-slate-50 p-12 md:p-20 rounded-3xl border border-slate-100 shadow-sm text-center">
            <div class="w-20 h-20 bg-blue-50 rounded-3xl flex items-center justify-center mx-auto mb-8">
                <i class="fa-regular fa-bookmark text-blue-500 text-4xl"></i>
            </div>
            <h3 class="text-xl font-black text-slate-800 mb-2">Daftar Pantauan Masih Kosong</h3>
            <p class="text-slate-400 text-sm font-semibold max-w-md mx-auto leading-relaxed">
                Anda belum memilih negara mana pun untuk dipantau. Tambahkan negara pertama Anda menggunakan kolom cepat di atas, atau kembali ke Dashboard utama.
            </p>
            
            <div class="mt-8 flex justify-center gap-4 flex-wrap">
                <a href="{{ route('dashboard') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-3 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
                    <i class="fa-solid fa-house"></i> Ke Dashboard
                </a>
            </div>
        </div>
    @else
        {{-- LIST GRID --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($watchlistCountries as $item)
                @php
                    $riskLevel = $item['risk_level'] ?? 'Unknown';
                    $riskScore = $item['risk_score'] ?? null;
                    
                    $riskColor = match($riskLevel) {
                        'High'   => [
                            'bg' => 'bg-rose-50/50', 
                            'border' => 'border-rose-100', 
                            'text' => 'text-rose-700', 
                            'badge' => 'bg-rose-100 text-rose-800 border-rose-200', 
                            'bar' => 'bg-rose-500'
                        ],
                        'Medium' => [
                            'bg' => 'bg-amber-50/50', 
                            'border' => 'border-amber-100', 
                            'text' => 'text-amber-700', 
                            'badge' => 'bg-amber-100 text-amber-800 border-amber-200', 
                            'bar' => 'bg-amber-500'
                        ],
                        'Low'    => [
                            'bg' => 'bg-emerald-50/50', 
                            'border' => 'border-emerald-100', 
                            'text' => 'text-emerald-700', 
                            'badge' => 'bg-emerald-100 text-emerald-800 border-emerald-200', 
                            'bar' => 'bg-emerald-500'
                        ],
                        default  => [
                            'bg' => 'bg-slate-100/50', 
                            'border' => 'border-slate-100', 
                            'text' => 'text-slate-600', 
                            'badge' => 'bg-slate-100 text-slate-700 border-slate-200', 
                            'bar' => 'bg-slate-400'
                        ],
                    };
                @endphp

                <div class="bg-slate-50 rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgba(0,0,0,0.015)] hover:shadow-[0_8px_30px_rgba(0,0,0,0.04)] transition-all duration-300 overflow-hidden flex flex-col justify-between min-h-[220px]">
                    <!-- Top Risk Level Bar -->
                    <div class="h-2 w-full {{ $riskColor['bar'] }}"></div>

                    <div class="p-6 flex-1 flex flex-col justify-between">
                        <!-- Country Header -->
                        <div class="flex items-center gap-4">
                            @if($item['flag'])
                                <img src="{{ $item['flag'] }}" class="w-16 h-11 object-cover rounded-xl shadow-sm border border-slate-150" alt="{{ $item['name'] }}">
                            @else
                                <div class="w-16 h-11 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 font-bold text-sm">
                                    {{ $item['code'] }}
                                </div>
                            @endif
                            <div>
                                <h4 class="font-extrabold text-slate-800 text-base leading-snug">{{ $item['name'] }}</h4>
                                <p class="text-xs text-slate-400 font-semibold mt-1">Ibukota: {{ $item['capital'] ?? 'N/A' }} · {{ $item['currency_code'] }}</p>
                            </div>
                        </div>

                        <!-- Risk Score Card (ENLARGED FONTS) -->
                        <div class="flex items-center justify-between my-5 p-4 rounded-2xl {{ $riskColor['bg'] }} border {{ $riskColor['border'] }}">
                            <div>
                                <span class="text-xs font-bold {{ $riskColor['text'] }} uppercase tracking-wider block">Skor Risiko</span>
                                <span class="text-2xl font-black {{ $riskColor['text'] }} leading-none mt-1 block">
                                    {{ $riskScore ?? '—' }}
                                </span>
                            </div>
                            <span class="px-3.5 py-1.5 rounded-xl text-xs font-black {{ $riskColor['badge'] }} flex items-center gap-1.5 border">
                                <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                                {{ $riskLevel }}
                            </span>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-3">
                            <a href="{{ route('dashboard', ['country' => $item['code']]) }}" class="flex-1 bg-blue-50 hover:bg-blue-100 text-blue-600 text-xs font-bold py-3 rounded-xl text-center transition-all flex items-center justify-center gap-2">
                                <i class="fa-solid fa-chart-pie text-sm"></i>
                                Buka Dashboard
                            </a>
                            <form action="{{ route('watchlist.toggle', $item['id']) }}" method="POST">
                                @csrf
                                <button type="submit" class="p-2.5 text-rose-500 hover:bg-rose-50 hover:text-rose-700 rounded-xl transition-all cursor-pointer border border-transparent hover:border-rose-100" title="Hapus dari Watchlist">
                                    <i class="fa-solid fa-bookmark-slash text-base"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ══ SUMMARY STATS ROW ══ --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pt-2">
            @php
                $highCount = collect($watchlistCountries)->where('risk_level', 'High')->count();
                $medCount  = collect($watchlistCountries)->where('risk_level', 'Medium')->count();
                $lowCount  = collect($watchlistCountries)->where('risk_level', 'Low')->count();
            @endphp
            <div class="bg-rose-50/50 border border-rose-100 p-5 rounded-3xl flex items-center gap-4">
                <span class="w-12 h-12 bg-rose-100 rounded-2xl flex items-center justify-center text-rose-600 text-xl font-black">{{ $highCount }}</span>
                <div>
                    <p class="text-sm font-bold text-rose-800">Risiko Tinggi</p>
                    <p class="text-xs text-slate-400 font-semibold mt-0.5">Memerlukan perhatian khusus</p>
                </div>
            </div>
            <div class="bg-amber-50/50 border border-amber-100 p-5 rounded-3xl flex items-center gap-4">
                <span class="w-12 h-12 bg-amber-100 rounded-2xl flex items-center justify-center text-amber-600 text-xl font-black">{{ $medCount }}</span>
                <div>
                    <p class="text-sm font-bold text-amber-800">Risiko Menengah</p>
                    <p class="text-xs text-slate-400 font-semibold mt-0.5">Awasi indikator berkala</p>
                </div>
            </div>
            <div class="bg-emerald-50/50 border border-emerald-100 p-5 rounded-3xl flex items-center gap-4">
                <span class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600 text-xl font-black">{{ $lowCount }}</span>
                <div>
                    <p class="text-sm font-bold text-emerald-800">Risiko Rendah</p>
                    <p class="text-xs text-slate-400 font-semibold mt-0.5">Operasional aman / stabil</p>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const quickForm = document.getElementById('quick-add-form');
        const selectEl = document.getElementById('quick-country-select');
        
        if (quickForm && selectEl) {
            quickForm.addEventListener('submit', function(e) {
                const countryId = selectEl.value;
                if (!countryId) {
                    e.preventDefault();
                    alert('Silakan pilih negara terlebih dahulu!');
                    return;
                }
                
                // Form action mapped to dynamically toggle the selected country id
                quickForm.action = `/watchlist/toggle/${countryId}`;
            });
        }
    });
</script>
@endsection
