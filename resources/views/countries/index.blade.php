@extends('layouts.app')

@section('title', 'Country Risk Database')

@section('content')

@php
    $highRisk   = $countries->filter(fn($c) => optional($c->riskScores->first())->risk_level === 'High')->count();
    $mediumRisk = $countries->filter(fn($c) => optional($c->riskScores->first())->risk_level === 'Medium')->count();
    $lowRisk    = $countries->filter(fn($c) => optional($c->riskScores->first())->risk_level === 'Low')->count();
    $unassessed = $countries->filter(fn($c) => is_null($c->riskScores->first()))->count();
    $regions    = $countries->groupBy('region')->map->count();
@endphp

<div class="space-y-8">

    {{-- ══ PAGE HEADER ══ --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-800 flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/20">
                    <i class="fa-solid fa-earth-asia text-slate-800 text-base"></i>
                </span>
                Country Risk Database
            </h1>
            <p class="text-sm text-slate-400 font-medium mt-2 ml-[52px]">
                Monitoring supply chain risk across
                <span class="text-blue-600 font-bold">{{ $countries->count() }}</span> countries &amp;
                <span class="text-blue-600 font-bold">{{ $regions->count() }}</span> regions
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

    {{-- ══ STATS CARDS ══ --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">

        {{-- Total Countries --}}
        <div class="lg:col-span-2 bg-gradient-to-br from-blue-500 via-blue-600 to-blue-700 rounded-3xl p-6 text-slate-800 shadow-xl shadow-blue-500/20 flex flex-col justify-between min-h-[130px] relative overflow-hidden">
            <div class="absolute -right-6 -top-6 w-28 h-28 bg-slate-50/10 rounded-full"></div>
            <div class="absolute -right-2 -bottom-8 w-20 h-20 bg-slate-50/5 rounded-full"></div>
            <span class="text-xs font-bold uppercase tracking-wider text-blue-200 relative z-10">Total Countries</span>
            <div class="relative z-10">
                <div class="text-5xl font-black mt-1">{{ $countries->count() }}</div>
                <div class="flex gap-2 mt-2 flex-wrap">
                    @foreach($regions->sortDesc() as $reg => $cnt)
                        <span class="text-xs bg-slate-50/20 text-slate-800 font-semibold px-2.5 py-0.5 rounded-full">{{ $reg }}: {{ $cnt }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- High Risk --}}
        <div class="bg-slate-50 rounded-3xl p-5 border border-slate-100 shadow-sm flex flex-col justify-between min-h-[130px]">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wide text-slate-400">High Risk</span>
                <span class="w-8 h-8 bg-rose-50 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-triangle-exclamation text-rose-500 text-sm"></i>
                </span>
            </div>
            <div>
                <div class="text-4xl font-black text-rose-600">{{ $highRisk }}</div>
                <div class="flex items-center gap-2 mt-2">
                    <div class="flex-1 bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-rose-400 to-rose-600 rounded-full"
                             style="width:{{ $countries->count() > 0 ? round($highRisk/$countries->count()*100) : 0 }}%"></div>
                    </div>
                    <span class="text-xs font-bold text-rose-500">{{ $countries->count() > 0 ? round($highRisk/$countries->count()*100) : 0 }}%</span>
                </div>
            </div>
        </div>

        {{-- Medium Risk --}}
        <div class="bg-slate-50 rounded-3xl p-5 border border-slate-100 shadow-sm flex flex-col justify-between min-h-[130px]">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wide text-slate-400">Medium Risk</span>
                <span class="w-8 h-8 bg-amber-50 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-circle-exclamation text-amber-500 text-sm"></i>
                </span>
            </div>
            <div>
                <div class="text-4xl font-black text-amber-500">{{ $mediumRisk }}</div>
                <div class="flex items-center gap-2 mt-2">
                    <div class="flex-1 bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-amber-400 to-amber-500 rounded-full"
                             style="width:{{ $countries->count() > 0 ? round($mediumRisk/$countries->count()*100) : 0 }}%"></div>
                    </div>
                    <span class="text-xs font-bold text-amber-500">{{ $countries->count() > 0 ? round($mediumRisk/$countries->count()*100) : 0 }}%</span>
                </div>
            </div>
        </div>

        {{-- Low Risk --}}
        <div class="bg-slate-50 rounded-3xl p-5 border border-slate-100 shadow-sm flex flex-col justify-between min-h-[130px]">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wide text-slate-400">Low Risk</span>
                <span class="w-8 h-8 bg-emerald-50 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
                </span>
            </div>
            <div>
                <div class="text-4xl font-black text-emerald-600">{{ $lowRisk }}</div>
                <div class="flex items-center gap-2 mt-2">
                    <div class="flex-1 bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-emerald-400 to-emerald-600 rounded-full"
                             style="width:{{ $countries->count() > 0 ? round($lowRisk/$countries->count()*100) : 0 }}%"></div>
                    </div>
                    <span class="text-xs font-bold text-emerald-600">{{ $countries->count() > 0 ? round($lowRisk/$countries->count()*100) : 0 }}%</span>
                </div>
            </div>
        </div>

        {{-- Not Assessed --}}
        <div class="bg-slate-50 rounded-3xl p-5 border border-slate-100 shadow-sm flex flex-col justify-between min-h-[130px]">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wide text-slate-400">Not Assessed</span>
                <span class="w-8 h-8 bg-slate-100 rounded-xl flex items-center justify-center">
                    <i class="fa-regular fa-clock text-slate-400 text-sm"></i>
                </span>
            </div>
            <div>
                <div class="text-4xl font-black text-slate-400">{{ $unassessed }}</div>
                <div class="text-xs font-medium text-slate-400 mt-2">Awaiting sync</div>
            </div>
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

            {{-- Risk --}}
            <select id="filter-risk"
                    class="bg-slate-100 border border-slate-100 rounded-xl py-3 px-4
                           text-sm font-semibold text-slate-700
                           focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all cursor-pointer">
                <option value="">⚪ All Risk Levels</option>
                <option value="High">🔴 High Risk</option>
                <option value="Medium">🟡 Medium Risk</option>
                <option value="Low">🟢 Low Risk</option>
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
                            <th class="px-4 py-4 text-xs font-bold uppercase tracking-wider text-slate-400 text-right">Population</th>
                            <th class="px-4 py-4 text-xs font-bold uppercase tracking-wider text-slate-400 text-center min-w-[180px]">Risk Score</th>
                            <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-slate-400 text-center">Actions</th>
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
                                    <a href="{{ route('dashboard', ['country' => $country->code]) }}" class="block group/link">
                                        <div class="font-bold text-base text-slate-800 group-hover/link:text-blue-700 transition-colors">
                                            {{ $country->name }}
                                        </div>
                                        <div class="text-xs font-bold text-slate-400 mt-0.5 tracking-widest">{{ $country->code }}</div>
                                    </a>
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

                                {{-- Population --}}
                                <td class="px-4 py-4 text-right min-w-[120px]">
                                    <div class="text-sm font-bold text-slate-800">{{ $popLabel }}</div>
                                    <div class="w-full bg-slate-100 rounded-full h-2 mt-2 overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-blue-400 to-blue-500 rounded-full"
                                             style="width:{{ $popPct }}%"></div>
                                    </div>
                                </td>

                                {{-- Risk Score --}}
                                <td class="px-5 py-4 text-center">
                                    @if($score)
                                        <a href="{{ route('countries.sync_single', $country->id) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border text-xs font-bold {{ $riskCfg['badge'] }} hover:scale-105 transition-all shadow-sm"
                                           title="Klik untuk Perbarui Data & Kalkulasi Ulang Risiko">
                                            <span class="w-2 h-2 rounded-full {{ $riskCfg['dot'] }}"></span>
                                            {{ $score->total_score }} &bull; {{ $level }}
                                        </a>
                                        <div class="w-28 bg-slate-100 rounded-full h-2 mx-auto mt-2 overflow-hidden">
                                            <div class="h-full bg-gradient-to-r {{ $riskCfg['bar'] }} rounded-full"
                                                 style="width:{{ min($score->total_score, 100) }}%"></div>
                                        </div>
                                    @else
                                        <a href="{{ route('countries.sync_single', $country->id) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200
                                                        bg-slate-100 text-slate-400 hover:text-slate-600 hover:border-slate-300 hover:bg-slate-50 text-xs font-semibold hover:scale-105 transition-all shadow-sm"
                                           title="Klik untuk Kalkulasi Risiko Negara Ini">
                                            <i class="fa-regular fa-clock"></i>
                                            Pending
                                        </a>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="px-5 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('dashboard', ['country' => $country->code]) }}"
                                           class="flex items-center justify-center w-9 h-9 bg-blue-50 hover:bg-blue-100
                                                  text-blue-600 rounded-xl transition-all hover:scale-110"
                                           title="View Dashboard">
                                            <i class="fa-solid fa-eye text-sm"></i>
                                        </a>
                                        <a href="{{ route('countries.sync_single', $country->id) }}"
                                           class="flex items-center justify-center w-9 h-9 bg-blue-50 hover:bg-blue-100
                                                  text-blue-600 rounded-xl transition-all hover:scale-110"
                                           title="Sync & Recalculate Risk">
                                            <i class="fa-solid fa-rotate text-sm"></i>
                                        </a>
                                    </div>
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
    const riskEl   = document.getElementById('filter-risk');
    const countEl  = document.getElementById('row-count');
    const footerEl = document.getElementById('footer-count');

    function applyFilters() {
        const q      = searchEl ? searchEl.value.trim().toLowerCase() : '';
        const region = regionEl ? regionEl.value : '';
        const risk   = riskEl   ? riskEl.value   : '';
        let visible  = 0;

        allRows.forEach(row => {
            const name      = row.dataset.name   || '';
            const rowRegion = row.dataset.region || '';
            const rowRisk   = row.dataset.risk   || '';

            const ok = (!q      || name.includes(q))
                    && (!region || rowRegion === region)
                    && (!risk   || rowRisk   === risk);

            row.style.display = ok ? '' : 'none';
            if (ok) visible++;
        });

        const label = visible + ' countr' + (visible === 1 ? 'y' : 'ies');
        if (countEl)  countEl.textContent  = label;
        if (footerEl) footerEl.textContent = visible;
    }

    if (searchEl) searchEl.addEventListener('input',   applyFilters);
    if (regionEl) regionEl.addEventListener('change',  applyFilters);
    if (riskEl)   riskEl.addEventListener('change',    applyFilters);
</script>

@endsection