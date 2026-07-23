@extends('layouts.app')

@section('title', 'Data Master Negara')

@section('content')

@php
    $regions    = $countries->groupBy('region')->map->count();
@endphp

<div class="space-y-8">

    {{-- ══ PAGE HEADER ══ --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-800 flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/20">
                    <i class="fa-solid fa-flag text-slate-800 text-base"></i>
                </span>
                Data Master Negara
            </h1>
            <p class="text-sm text-slate-400 font-medium mt-2 ml-[52px]">
                Mengelola basis data 
                <span class="text-blue-600 font-bold">{{ $countries->count() }}</span> negara &amp;
                <span class="text-blue-600 font-bold">{{ $regions->count() }}</span> regional
            </p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            @if(session('success'))
                <span class="text-sm font-semibold text-emerald-700 bg-emerald-50 px-4 py-2 rounded-xl border border-emerald-100 flex items-center gap-2 max-w-sm">
                    <i class="fa-solid fa-circle-check shrink-0"></i>
                    <span class="truncate">{{ session('success') }}</span>
                </span>
            @endif
            @if(session('error'))
                <span class="text-sm font-semibold text-rose-700 bg-rose-50 px-4 py-2 rounded-xl border border-rose-100 flex items-center gap-2">
                    <i class="fa-solid fa-circle-xmark shrink-0"></i>
                    {{ session('error') }}
                </span>
            @endif
            <a href="{{ route('countries.sync') }}"
               class="bg-gradient-to-r from-blue-600 to-blue-600 hover:from-blue-700 hover:to-blue-700
                      text-slate-800 px-5 py-2.5 rounded-2xl text-sm font-bold shadow-lg shadow-blue-500/25
                      hover:shadow-blue-500/40 transition-all flex items-center gap-2">
                <i class="fa-solid fa-arrows-spin"></i>
                Sync from API
            </a>
        </div>
    </div>



    {{-- ══ MAIN TABLE CARD ══ --}}
    <div class="bg-slate-50 rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgba(0,0,0,0.03)] overflow-hidden">

        {{-- Toolbar --}}
        <div class="p-5 border-b border-slate-100 flex flex-wrap items-center gap-3">

            {{-- Search --}}
            <div class="relative flex-1 min-w-[220px] max-w-sm">
                <input id="search-country" type="text" placeholder="Search country or code..."
                       class="w-full bg-slate-100 border border-slate-100 rounded-xl py-3 pl-10 pr-4
                              text-sm font-medium text-slate-700 placeholder:text-slate-400
                              focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </div>
            </div>

            {{-- Region --}}
            <select id="filter-region"
                    class="bg-slate-100 border border-slate-100 rounded-xl py-3 px-4
                           text-sm font-semibold text-slate-700
                           focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all cursor-pointer">
                <option value="">🌐 All Regions</option>
                @foreach($regions->keys()->sort() as $region)
                    <option value="{{ $region }}">{{ $region }} ({{ $regions[$region] }})</option>
                @endforeach
            </select>



            <span id="row-count" class="text-sm font-bold text-slate-400 ml-auto">
                {{ $countries->count() }} countries
            </span>
        </div>

        {{-- Table --}}
        @if($countries->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                    <i class="fa-solid fa-earth-asia text-slate-600 text-4xl"></i>
                </div>
                <p class="font-black text-slate-600 text-xl">No Countries Found</p>
                <p class="text-sm text-slate-400 mt-2 max-w-xs">Click "Sync from API" above to load the global country database.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-800 text-left">
                            <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-slate-400 w-16">Flag</th>
                            <th class="px-4 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Country</th>
                            <th class="px-4 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Region</th>
                            <th class="px-4 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Capital</th>
                            <th class="px-4 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Currency</th>
                            <th class="px-4 py-4 text-xs font-bold uppercase tracking-wider text-slate-400 text-right">GDP (Nominal)</th>
                            <th class="px-4 py-4 text-xs font-bold uppercase tracking-wider text-slate-400 text-right">Population</th>
                            <th class="px-4 py-4 text-xs font-bold uppercase tracking-wider text-slate-400 text-center">Risk Score</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($countries as $country)
                            @php
                                $score = $country->riskScores->first();
                                $level = $score?->risk_level ?? null;

                                $rc = match($country->region) {
                                    'Asia'     => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'Europe'   => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'Americas' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'Africa'   => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'Oceania'  => 'bg-sky-50 text-sky-700 border-sky-200',
                                    default    => 'bg-slate-100 text-slate-600 border-slate-200',
                                };

                                $pop = $country->population;
                                $popLabel = $pop >= 1e9
                                    ? number_format($pop / 1e9, 2) . ' B'
                                    : ($pop >= 1e6
                                        ? number_format($pop / 1e6, 1) . ' M'
                                        : number_format($pop));
                                $popPct = min(round($pop / 1500000000 * 100), 100);

                                $gdpRecord = $country->gdpData->first();
                                $gdpVal = $gdpRecord?->gdp_value;
                                $gdpLabel = 'N/A';
                                if ($gdpVal) {
                                    $gdpLabel = $gdpVal >= 1e12
                                        ? '$' . number_format($gdpVal / 1e12, 2) . ' T'
                                        : ($gdpVal >= 1e9
                                            ? '$' . number_format($gdpVal / 1e9, 2) . ' B'
                                            : '$' . number_format($gdpVal));
                                }

                                $riskCfg = match($level) {
                                    'High'   => ['badge' => 'bg-rose-50 text-rose-700 border-rose-200',    'bar' => 'from-rose-400 to-rose-600',    'dot' => 'bg-rose-500'],
                                    'Medium' => ['badge' => 'bg-amber-50 text-amber-700 border-amber-200',  'bar' => 'from-amber-400 to-amber-500',   'dot' => 'bg-amber-400'],
                                    'Low'    => ['badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'bar' => 'from-emerald-400 to-emerald-600', 'dot' => 'bg-emerald-500'],
                                    default  => ['badge' => 'bg-slate-100 text-slate-400 border-slate-200', 'bar' => 'from-slate-300 to-slate-400',   'dot' => 'bg-slate-400'],
                                };
                            @endphp
                            <tr class="hover:bg-slate-100/70 transition-colors duration-150 group"
                                data-name="{{ strtolower($country->name) }} {{ strtolower($country->code) }}"
                                data-region="{{ $country->region }}"
                                data-risk="{{ $level ?? '' }}">

                                {{-- Flag --}}
                                <td class="px-5 py-4">
                                    <img src="{{ $country->flag }}" alt="{{ $country->name }}"
                                         class="w-12 h-8 object-cover rounded-lg shadow-sm border border-slate-200
                                                group-hover:shadow-md group-hover:scale-105 transition-all duration-200">
                                </td>

                                {{-- Country --}}
                                <td class="px-4 py-4 min-w-[170px]">
                                    <div class="font-bold text-base text-slate-800 group-hover:text-blue-700 transition-colors">
                                        {{ $country->name }}
                                    </div>
                                    <div class="text-xs font-bold text-slate-400 mt-0.5 tracking-widest">{{ $country->code }}</div>
                                </td>

                                {{-- Region --}}
                                <td class="px-4 py-4 min-w-[140px]">
                                    <span class="inline-block px-3 py-1.5 rounded-lg border text-xs font-bold uppercase tracking-wide {{ $rc }}">
                                        {{ $country->region }}
                                    </span>
                                    @if($country->subregion)
                                        <div class="text-xs text-slate-400 mt-1.5">{{ $country->subregion }}</div>
                                    @endif
                                </td>

                                {{-- Capital --}}
                                <td class="px-4 py-4 min-w-[120px]">
                                    <div class="text-sm font-semibold text-slate-700 flex items-center gap-1.5">
                                        <i class="fa-solid fa-location-dot text-slate-600 text-xs"></i>
                                        {{ $country->capital ?? 'N/A' }}
                                    </div>
                                </td>

                                {{-- Currency --}}
                                <td class="px-4 py-4 min-w-[100px]">
                                    <div class="text-sm font-bold text-slate-800">{{ $country->currency_code }}</div>
                                    <div class="text-xs text-slate-400 mt-0.5 truncate max-w-[100px]" title="{{ $country->currency }}">
                                        {{ $country->currency }}
                                    </div>
                                </td>

                                {{-- GDP --}}
                                <td class="px-4 py-4 text-right min-w-[120px]">
                                    <div class="text-sm font-extrabold text-slate-800">{{ $gdpLabel }}</div>
                                    @if($gdpRecord)
                                        <div class="text-[10px] text-slate-400 font-bold mt-1">Tahun {{ $gdpRecord->year }}</div>
                                    @endif
                                </td>

                                {{-- Population --}}
                                <td class="px-4 py-4 text-right min-w-[120px]">
                                    <div class="text-sm font-bold text-slate-800">{{ $popLabel }}</div>
                                    <div class="w-full bg-slate-100 rounded-full h-2 mt-2 overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-blue-400 to-blue-500 rounded-full"
                                             style="width:{{ $popPct }}%"></div>
                                    </div>
                                </td>

                                {{-- Risk Score --}}
                                <td class="px-4 py-4 text-center">
                                    @if($score)
                                        <a href="{{ route('countries.sync_single', $country->id) }}" 
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border text-xs font-bold {{ $riskCfg['badge'] }} hover:scale-105 transition-all shadow-sm"
                                           title="Klik untuk Perbarui Data & Kalkulasi Ulang Risiko">
                                            <span class="w-2 h-2 rounded-full {{ $riskCfg['dot'] }}"></span>
                                            {{ $score->total_score }} &bull; {{ $level }}
                                        </a>
                                    @else
                                        <a href="{{ route('countries.sync_single', $country->id) }}" 
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200
                                                        bg-slate-100 text-slate-400 hover:text-slate-600 hover:border-slate-300 hover:bg-slate-50 text-xs font-semibold hover:scale-105 transition-all shadow-sm"
                                           title="Klik untuk Kalkulasi Risiko Negara Ini">
                                            <i class="fa-regular fa-clock"></i> Pending
                                        </a>
                                    @endif
                                </td>


                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-100/50 flex items-center justify-between">
                <span class="text-sm font-medium text-slate-400">
                    Showing <span id="footer-count" class="text-slate-700 font-bold">{{ $countries->count() }}</span> countries
                </span>
                <span class="text-sm font-medium text-slate-400">
                    Click <i class="fa-solid fa-rotate text-blue-500"></i> to sync &amp; recalculate risk score
                </span>
            </div>
        @endif
    </div>
</div>

<script>
    const allRows  = Array.from(document.querySelectorAll('tbody tr'));
    const searchEl = document.getElementById('search-country');
    const regionEl = document.getElementById('filter-region');
    const countEl  = document.getElementById('row-count');
    const footerEl = document.getElementById('footer-count');

    function applyFilters() {
        const q      = searchEl ? searchEl.value.trim().toLowerCase() : '';
        const region = regionEl ? regionEl.value : '';
        let visible  = 0;

        allRows.forEach(row => {
            const name      = row.dataset.name   || '';
            const rowRegion = row.dataset.region || '';

            const ok = (!q      || name.includes(q))
                    && (!region || rowRegion === region);

            row.style.display = ok ? '' : 'none';
            if (ok) visible++;
        });

        const label = visible + ' countr' + (visible === 1 ? 'y' : 'ies');
        if (countEl)  countEl.textContent  = label;
        if (footerEl) footerEl.textContent = visible;
    }

    if (searchEl) searchEl.addEventListener('input',   applyFilters);
    if (regionEl) regionEl.addEventListener('change',  applyFilters);
</script>

@endsection