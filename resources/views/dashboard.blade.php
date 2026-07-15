@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-6 text-slate-800 pb-12">
    <!-- Header & Search Bar Row -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-800">Global Dashboard</h1>
            <p class="text-xs text-slate-400 font-semibold mt-1">Overview of global supply chain risk</p>
        </div>

        <div class="flex items-center gap-4 w-full md:w-auto">
            <!-- Search Bar -->
            <div class="relative w-full md:w-80">
                <input type="text" placeholder="Search country, port, news..." class="w-full bg-white border border-slate-100 rounded-2xl py-2.5 pl-10 pr-4 text-xs font-semibold text-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 shadow-[0_4px_20px_rgba(0,0,0,0.01)] transition-all">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </div>
            </div>

            <!-- Notifications -->
            <button class="relative bg-white border border-slate-100 p-3 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.01)] hover:bg-slate-50 transition-all text-slate-500 cursor-pointer w-11 h-11 flex items-center justify-center shrink-0">
                <i class="fa-regular fa-bell text-sm"></i>
                <span class="absolute top-2 right-2.5 bg-rose-500 border-2 border-white w-2.5 h-2.5 rounded-full"></span>
            </button>

            <!-- Profile Icon -->
            <div class="bg-gradient-to-tr from-violet-500 to-indigo-600 text-white rounded-2xl w-11 h-11 flex items-center justify-center font-bold text-sm shadow-md shadow-indigo-500/10 cursor-pointer shrink-0">
                {{ substr(Auth::user()->name, 0, 2) }}
            </div>
        </div>
    </div>

    <!-- Metrics Row (5 cards) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <!-- Global Risk Score (Avg) -->
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-[0_8px_30px_rgba(0,0,0,0.01)] flex flex-col justify-between h-36">
            <span class="text-[11px] text-slate-400 font-extrabold uppercase tracking-wider block">Global Risk Score (Avg)</span>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="text-3xl font-black text-slate-800">{{ $globalRiskAvg }}</span>
                <span class="text-xs text-slate-400 font-bold">/100</span>
            </div>
            <div class="flex items-center justify-between mt-3">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[10px] font-extrabold tracking-wide uppercase
                    {{ $globalRiskAvg >= 70 ? 'bg-rose-50 text-rose-600' : ($globalRiskAvg >= 35 ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600') }}
                ">
                    {{ $globalRiskAvg >= 70 ? 'High Risk' : ($globalRiskAvg >= 35 ? 'Medium Risk' : 'Low Risk') }}
                </span>
                <!-- Sparkline Placeholder SVG -->
                <svg class="w-16 h-8 text-indigo-500/60" viewBox="0 0 100 30" fill="none">
                    <path d="M0,25 Q15,5 30,20 T60,10 T90,15 T100,5" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                </svg>
            </div>
        </div>

        <!-- High-Risk Countries -->
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-[0_8px_30px_rgba(0,0,0,0.01)] flex flex-col justify-between h-36">
            <span class="text-[11px] text-slate-400 font-extrabold uppercase tracking-wider block">High-Risk Countries</span>
            <div class="text-3xl font-black text-slate-800 mt-2">{{ $highRiskCount }}</div>
            <div class="flex items-center gap-1.5 text-[10px] font-bold text-rose-500 mt-3">
                <i class="fa-solid fa-arrow-trend-up"></i>
                <span>+10%</span>
                <span class="text-slate-400 font-semibold ml-0.5">from last week</span>
            </div>
        </div>

        <!-- Medium Risk Countries -->
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-[0_8px_30px_rgba(0,0,0,0.01)] flex flex-col justify-between h-36">
            <span class="text-[11px] text-slate-400 font-extrabold uppercase tracking-wider block">Medium Risk Countries</span>
            <div class="text-3xl font-black text-slate-800 mt-2">{{ $mediumRiskCount }}</div>
            <div class="flex items-center gap-1.5 text-[10px] font-bold text-amber-500 mt-3">
                <i class="fa-solid fa-arrow-trend-down"></i>
                <span>-5%</span>
                <span class="text-slate-400 font-semibold ml-0.5">from last week</span>
            </div>
        </div>

        <!-- Low Risk Countries -->
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-[0_8px_30px_rgba(0,0,0,0.01)] flex flex-col justify-between h-36">
            <span class="text-[11px] text-slate-400 font-extrabold uppercase tracking-wider block">Low Risk Countries</span>
            <div class="text-3xl font-black text-slate-800 mt-2">{{ $lowRiskCount }}</div>
            <div class="flex items-center gap-1.5 text-[10px] font-bold text-emerald-500 mt-3">
                <i class="fa-solid fa-arrow-trend-up"></i>
                <span>+3%</span>
                <span class="text-slate-400 font-semibold ml-0.5">from last week</span>
            </div>
        </div>

        <!-- Monitored Countries -->
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-[0_8px_30px_rgba(0,0,0,0.01)] flex flex-col justify-between h-36">
            <span class="text-[11px] text-slate-400 font-extrabold uppercase tracking-wider block">Monitored Countries</span>
            <div class="text-3xl font-black text-slate-800 mt-2">{{ $monitoredCount }}</div>
            <div class="text-[10px] text-slate-400 font-bold mt-3">
                Total active countries
            </div>
        </div>
    </div>

    <!-- Main Workspace Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Area (Columns 1 & 2) -->
        <div class="lg:col-span-2 flex flex-col gap-6">
            <!-- Risk Map Card -->
            <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgba(0,0,0,0.01)] overflow-hidden flex flex-col">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-800">Risk Map</h2>
                        <p class="text-[10px] text-slate-400 font-bold mt-0.5">Visual risk assessment of active logistics nodes</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="flex items-center gap-1.5 text-[10px] font-bold text-slate-500"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Low</span>
                        <span class="flex items-center gap-1.5 text-[10px] font-bold text-slate-500"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Medium</span>
                        <span class="flex items-center gap-1.5 text-[10px] font-bold text-slate-500"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> High</span>
                    </div>
                </div>
                <!-- Leaflet World Map Container -->
                <div id="risk-map" class="h-80 w-full bg-slate-50 z-0"></div>
            </div>

            <!-- Top 5 Countries and Global Indicators Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Top 5 Countries Card -->
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_8px_30px_rgba(0,0,0,0.01)] flex flex-col justify-between min-h-[300px]">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-800 mb-4">Top 5 Highest Risk Countries</h3>
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
                                        <span class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wide
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
                        <a href="{{ route('countries.index') }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-black transition-colors flex items-center justify-center gap-1">
                            View All Countries <i class="fa-solid fa-chevron-right text-[10px]"></i>
                        </a>
                    </div>
                </div>

                <!-- Global Indicators Card -->
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_8px_30px_rgba(0,0,0,0.01)] flex flex-col min-h-[300px]">
                    <h3 class="text-base font-extrabold text-slate-800 mb-4">Global Indicators</h3>
                    <div class="grid grid-cols-2 gap-4 flex-1">
                        <!-- GDP -->
                        <div class="bg-slate-50/50 border border-slate-100 rounded-2xl p-3.5 flex flex-col justify-between">
                            <span class="text-[9px] text-slate-400 font-extrabold uppercase tracking-wide block">Global GDP (Total)</span>
                            <div class="text-base font-black text-slate-800 mt-2">${{ number_format($globalGdpValue / 1e12, 1) }} T</div>
                            <span class="text-[9px] font-bold text-emerald-500 flex items-center gap-1 mt-1">
                                <i class="fa-solid fa-caret-up"></i> +2.8%
                            </span>
                        </div>
                        <!-- Inflation -->
                        <div class="bg-slate-50/50 border border-slate-100 rounded-2xl p-3.5 flex flex-col justify-between">
                            <span class="text-[9px] text-slate-400 font-extrabold uppercase tracking-wide block">Global Inflation (Avg)</span>
                            <div class="text-base font-black text-slate-800 mt-2">{{ number_format($globalInflation, 1) }}%</div>
                            <span class="text-[9px] font-bold text-rose-500 flex items-center gap-1 mt-1">
                                <i class="fa-solid fa-caret-down"></i> -0.6%
                            </span>
                        </div>
                        <!-- Currency -->
                        <div class="bg-slate-50/50 border border-slate-100 rounded-2xl p-3.5 flex flex-col justify-between">
                            <span class="text-[9px] text-slate-400 font-extrabold uppercase tracking-wide block">Global Currency Index</span>
                            <div class="text-base font-black text-slate-800 mt-2">{{ number_format($globalCurrencyRate, 1) }}</div>
                            <span class="text-[9px] font-bold text-emerald-500 flex items-center gap-1 mt-1">
                                <i class="fa-solid fa-caret-up"></i> +1.2%
                            </span>
                        </div>
                        <!-- Trade -->
                        <div class="bg-slate-50/50 border border-slate-100 rounded-2xl p-3.5 flex flex-col justify-between">
                            <span class="text-[9px] text-slate-400 font-extrabold uppercase tracking-wide block">Global Trade (Total)</span>
                            <div class="text-base font-black text-slate-800 mt-2">{{ $globalTrade }} Shipments</div>
                            <span class="text-[9px] font-bold text-emerald-500 flex items-center gap-1 mt-1">
                                <i class="fa-solid fa-caret-up"></i> +3.1%
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Weather Alert Card -->
            @if($extremeWeather)
                <div class="bg-rose-50 border border-rose-100 rounded-3xl p-5 shadow-[0_8px_30px_rgba(244,63,94,0.02)] flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="bg-rose-500 text-white p-3 rounded-2xl shadow-md shadow-rose-500/20 flex items-center justify-center shrink-0 w-11 h-11 animate-pulse">
                            <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-black text-rose-900">Extreme Weather Detected</h4>
                            <p class="text-[11px] text-rose-700 font-bold mt-1">
                                Heavy rains / wind warning in {{ $extremeWeather->country->name }}: Temperature {{ $extremeWeather->temperature }}°C, Rainfall {{ $extremeWeather->rainfall }}mm, Wind speed {{ $extremeWeather->wind_speed }} km/h
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('weather.index') }}" class="bg-white hover:bg-slate-50 text-rose-600 font-black text-[10px] px-4 py-2 rounded-xl border border-rose-100 transition-all shrink-0">
                        View Details
                    </a>
                </div>
            @endif
        </div>

        <!-- Right Sidebar Area (Column 3) -->
        <div class="flex flex-col gap-6">
            <!-- Currency (USD/IDR) Card -->
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_8px_30px_rgba(0,0,0,0.01)] flex flex-col">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] text-slate-400 font-extrabold uppercase tracking-wider block">Currency (USD/IDR)</span>
                    <span class="text-[10px] text-emerald-500 font-extrabold flex items-center gap-1">
                        <i class="fa-solid fa-caret-up"></i> +0.40%
                    </span>
                </div>
                <div class="text-2xl font-black text-slate-800">16,415.00 <span class="text-xs text-slate-400 font-bold ml-0.5">IDR</span></div>
                
                <!-- Chart.js Graph Canvas -->
                <div class="h-28 mt-4 w-full">
                    <canvas id="currency-chart" class="w-full h-full"></canvas>
                </div>
            </div>

            <!-- Country Quick View Card -->
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_8px_30px_rgba(0,0,0,0.01)] flex flex-col justify-between min-h-[340px]">
                <div>
                    <h3 class="text-base font-extrabold text-slate-800 mb-4">Country Quick View</h3>
                    
                    <!-- Country Dropdown Form -->
                    <form action="{{ route('dashboard') }}" method="GET" id="quickview-form" class="mb-4">
                        <div class="relative">
                            <select name="country" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-100 text-slate-700 text-xs font-bold rounded-2xl p-3 pr-8 appearance-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                                @foreach($countries as $c)
                                    <option value="{{ $c->code }}" {{ $selectedCountry && $selectedCountry->code === $c->code ? 'selected' : '' }}>
                                        {{ $c->name }} ({{ $c->code }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-400">
                                <i class="fa-solid fa-chevron-down text-[10px]"></i>
                            </div>
                        </div>
                    </form>

                    <!-- Country Information Details -->
                    @if($selectedCountry)
                        <div class="space-y-3">
                            <div class="flex items-center justify-between text-xs font-semibold py-1">
                                <span class="text-slate-400">GDP (Nominal)</span>
                                <span class="text-slate-700 font-extrabold">
                                    ${{ $selectedCountry->gdpData()->orderBy('year', 'desc')->first() ? number_format($selectedCountry->gdpData()->orderBy('year', 'desc')->first()->gdp_value / 1e9, 2) . ' B' : 'N/A' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-xs font-semibold py-1">
                                <span class="text-slate-400">Inflation Rate</span>
                                <span class="text-slate-700 font-extrabold">
                                    {{ $selectedCountry->inflationData()->orderBy('year', 'desc')->first() ? number_format($selectedCountry->inflationData()->orderBy('year', 'desc')->first()->inflation_rate, 2) . '%' : 'N/A' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-xs font-semibold py-1">
                                <span class="text-slate-400">Population</span>
                                <span class="text-slate-700 font-extrabold">
                                    {{ number_format($selectedCountry->population / 1e6, 1) }} Million
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-xs font-semibold py-1">
                                <span class="text-slate-400">Currency</span>
                                <span class="text-slate-700 font-extrabold">
                                    {{ $selectedCountry->currency_code }} ({{ $selectedCountry->currency }})
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-xs font-semibold py-1">
                                <span class="text-slate-400">Risk Score</span>
                                <span class="px-2.5 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wide
                                    {{ $latestScore?->risk_level === 'High' ? 'bg-rose-50 text-rose-600' : ($latestScore?->risk_level === 'Medium' ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600') }}
                                ">
                                    {{ $latestScore ? $latestScore->total_score : 20 }} ({{ $latestScore ? $latestScore->risk_level : 'Low' }} Risk)
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-xs font-semibold py-1">
                                <span class="text-slate-400">Weather (Now)</span>
                                <span class="text-slate-700 font-extrabold flex items-center gap-1.5">
                                    {{ $selectedCountry->weatherData()->orderBy('recorded_at', 'desc')->first() ? $selectedCountry->weatherData()->orderBy('recorded_at', 'desc')->first()->temperature . '°C' : 'N/A' }}
                                    <span class="text-[10px] text-slate-400 font-bold">
                                        ({{ $selectedCountry->weatherData()->orderBy('recorded_at', 'desc')->first()?->weather_condition ?? 'N/A' }})
                                    </span>
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Port Location Map Card -->
            <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgba(0,0,0,0.01)] overflow-hidden flex flex-col min-h-[380px]">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-base font-extrabold text-slate-800">Port Location Map</h3>
                    <p class="text-[10px] text-slate-400 font-bold mt-0.5">Geographical monitoring of active logistics ports</p>
                </div>
                <div id="port-map" class="h-44 w-full bg-slate-50 z-0"></div>
                
                <!-- Dynamic Clicked Port Details Panel -->
                <div class="p-5 bg-slate-50 border-t border-slate-100 flex-1 flex flex-col justify-between">
                    <div>
                        <h4 id="panel-port-name" class="text-xs font-black text-slate-800">Port of Singapore</h4>
                        <div class="grid grid-cols-2 gap-3 mt-3">
                            <div>
                                <span class="text-[9px] text-slate-400 font-bold block">Country</span>
                                <span id="panel-port-country" class="text-xs font-extrabold text-slate-600 block mt-0.5">Singapore</span>
                            </div>
                            <div>
                                <span class="text-[9px] text-slate-400 font-bold block">Region</span>
                                <span id="panel-port-region" class="text-xs font-extrabold text-slate-600 block mt-0.5">Southeast Asia</span>
                            </div>
                            <div>
                                <span class="text-[9px] text-slate-400 font-bold block">Type</span>
                                <span id="panel-port-type" class="text-xs font-extrabold text-slate-600 block mt-0.5">Seaport</span>
                            </div>
                            <div>
                                <span class="text-[9px] text-slate-400 font-bold block">Status</span>
                                <span id="panel-port-status" class="text-xs font-extrabold text-emerald-600 block mt-0.5">Active</span>
                            </div>
                        </div>
                    </div>
                    <div class="pt-3 border-t border-slate-200/50 text-right">
                        <a href="{{ route('ports.index') }}" class="text-indigo-600 hover:text-indigo-800 text-[10px] font-black transition-colors flex items-center justify-end gap-1">
                            View details <i class="fa-solid fa-chevron-right text-[8px]"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent News Card -->
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_8px_30px_rgba(0,0,0,0.01)] flex flex-col min-h-[300px] justify-between">
                <div>
                    <h3 class="text-base font-extrabold text-slate-800 mb-4">Recent News</h3>
                    <div class="space-y-4">
                        @foreach($recentNews as $news)
                            <div class="flex flex-col gap-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-500 text-[9px] font-extrabold uppercase">
                                        {{ $news->country->name }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase
                                        {{ $news->sentiment === 'Negative' ? 'bg-rose-50 text-rose-600' : ($news->sentiment === 'Positive' ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-500') }}
                                    ">
                                        {{ $news->sentiment }}
                                    </span>
                                    <span class="text-[9px] text-slate-400 font-bold ml-auto">
                                        {{ \Carbon\Carbon::parse($news->published_at)->diffForHumans() }}
                                    </span>
                                </div>
                                <p class="text-xs font-bold text-slate-700 hover:text-indigo-600 transition-colors leading-relaxed">
                                    {{ $news->title }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="pt-4 border-t border-slate-100 text-center">
                    <a href="{{ route('news.index') }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-black transition-colors flex items-center justify-center gap-1">
                        View All News <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- 1. Risk Map (World Map circles colored by risk level) ---
        const riskMap = L.map('risk-map').setView([20, 0], 2);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            maxZoom: 18,
            attribution: '© OpenStreetMap contributors © CARTO'
        }).addTo(riskMap);

        const countriesScores = @json($latestScores);
        countriesScores.forEach(item => {
            if (item.country && item.country.latitude && item.country.longitude) {
                let color = '#10b981'; // Green (Low Risk)
                if (item.risk_level === 'High') {
                    color = '#f43f5e'; // Red (High Risk)
                } else if (item.risk_level === 'Medium') {
                    color = '#f59e0b'; // Orange/Yellow (Medium Risk)
                }

                const marker = L.circleMarker([item.country.latitude, item.country.longitude], {
                    radius: 8,
                    fillColor: color,
                    color: '#ffffff',
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.8
                }).addTo(riskMap);

                marker.bindPopup(`
                    <div class="font-sans" style="min-width: 140px;">
                        <h4 class="font-bold text-slate-800 text-xs mb-1">${item.country.name}</h4>
                        <div class="border-t border-slate-100 pt-1 mt-1 text-[10px] text-slate-500">
                            <b>Skor Risiko:</b> <span class="font-extrabold text-slate-700">${item.total_score}</span><br>
                            <b>Level Risiko:</b> <span class="font-extrabold text-slate-700">${item.risk_level}</span>
                        </div>
                    </div>
                `);
            }
        });

        // --- 2. Port Map (Pins of ports with details logic) ---
        const portMap = L.map('port-map').setView([20, 0], 1);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            maxZoom: 18,
            attribution: '© OpenStreetMap contributors'
        }).addTo(portMap);

        const allPorts = @json($allPorts);
        allPorts.forEach(port => {
            if (port.latitude && port.longitude) {
                // Indigo/purple circle marker for seaport pin
                const marker = L.circleMarker([port.latitude, port.longitude], {
                    radius: 6,
                    fillColor: '#6366f1',
                    color: '#ffffff',
                    weight: 1.5,
                    opacity: 1,
                    fillOpacity: 0.9
                }).addTo(portMap);

                marker.on('click', function() {
                    document.getElementById('panel-port-name').innerText = port.name;
                    document.getElementById('panel-port-country').innerText = port.country ? port.country.name : 'Unknown';
                    document.getElementById('panel-port-region').innerText = port.country ? port.country.region : 'Unknown';
                    document.getElementById('panel-port-type').innerText = 'Seaport';
                    document.getElementById('panel-port-status').innerText = 'Active';
                });

                marker.bindTooltip(port.name, {
                    permanent: false,
                    direction: 'top'
                });
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
                    borderColor: '#a78bfa',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#6366f1',
                    pointHoverRadius: 5,
                    pointRadius: 2,
                    tension: 0.45,
                    fill: true,
                    backgroundColor: function(context) {
                        const chart = context.chart;
                        const {ctx, chartArea} = chart;
                        if (!chartArea) return null;
                        const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                        gradient.addColorStop(0, 'rgba(167, 139, 250, 0.2)');
                        gradient.addColorStop(1, 'rgba(99, 102, 241, 0.0)');
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
                            font: { size: 9, family: 'Outfit' },
                            color: '#94a3b8'
                        }
                    },
                    y: {
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            font: { size: 9, family: 'Outfit' },
                            color: '#94a3b8'
                        }
                    }
                }
            }
        });
    });
</script>
@endsection