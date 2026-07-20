@extends('layouts.app')

@section('title', 'Analisis Dampak Mata Uang')

@section('content')

@php
    $avgRate = $rateHistory->avg('exchange_rate');
    $maxRate = $rateHistory->max('exchange_rate');
    $minRate = $rateHistory->min('exchange_rate');
    
    // Calculate currency volatility percentage
    $volatility = 0;
    if ($minRate > 0) {
        $volatility = (($maxRate - $minRate) / $minRate) * 100;
    }
    
    $volatilityStatus = 'Low Volatility';
    $volatilityClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
    $volatilityDesc = 'Nilai tukar sangat stabil, risiko rantai pasok rendah.';
    
    if ($volatility > 5) {
        $volatilityStatus = 'High Volatility';
        $volatilityClass = 'bg-rose-50 text-rose-700 border-rose-200';
        $volatilityDesc = 'Fluktuasi kurs tajam, awasi risiko biaya impor.';
    } elseif ($volatility > 2) {
        $volatilityStatus = 'Moderate Volatility';
        $volatilityClass = 'bg-amber-50 text-amber-700 border-amber-200';
        $volatilityDesc = 'Volatilitas sedang, awasi perubahan kontrak impor.';
    }
@endphp

<div class="space-y-8">

    {{-- ══ PAGE HEADER ══ --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-800 flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/20">
                    <i class="fa-solid fa-money-bill-transfer text-slate-800 text-base"></i>
                </span>
                Currency Impact Analytics
            </h1>
            <p class="text-sm text-slate-400 font-medium mt-2 ml-[52px]">
                Monitoring exchange rate volatility and financial impacts on global logistics
            </p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            @if($selectedCountry)
                <a href="{{ route('countries.sync_single', $selectedCountry->id) }}"
                   class="bg-gradient-to-r from-blue-600 to-blue-600 hover:from-blue-700 hover:to-blue-700
                          text-slate-800 px-5 py-2.5 rounded-2xl text-sm font-bold shadow-lg shadow-blue-500/25
                          hover:shadow-blue-500/40 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-arrows-spin"></i>
                    Sync Exchange Rates
                </a>
            @endif
        </div>
    </div>

    {{-- ══ CURRENCY METRICS CARDS ══ --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        
        {{-- Current Rate --}}
        <div class="bg-slate-50 rounded-3xl p-5 border border-slate-100 shadow-sm flex flex-col justify-between min-h-[130px]">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wide text-slate-400">Kurs Saat Ini (1 USD)</span>
                <span class="w-8 h-8 bg-blue-50 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-sack-dollar text-blue-500 text-sm"></i>
                </span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-slate-800 truncate">
                    {{ $latestRate ? number_format($latestRate->exchange_rate, 2) : 'N/A' }} 
                    <span class="text-xs font-bold text-slate-400">{{ $selectedCountry?->currency_code }}</span>
                </div>
                <div class="text-xs text-slate-400 font-semibold mt-1">
                    Nilai tukar terhadap Dolar AS
                </div>
            </div>
        </div>

        {{-- Volatility Status --}}
        <div class="bg-slate-50 rounded-3xl p-5 border border-slate-100 shadow-sm flex flex-col justify-between min-h-[130px]">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wide text-slate-400">Tingkat Volatilitas</span>
                <span class="w-8 h-8 bg-purple-50 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-chart-line text-purple-500 text-sm"></i>
                </span>
            </div>
            <div class="mt-2">
                <span class="inline-block px-3 py-1 rounded-lg border text-xs font-bold {{ $volatilityClass }}">
                    {{ $volatilityStatus }} ({{ number_format($volatility, 2) }}%)
                </span>
                <div class="text-xs text-slate-400 font-semibold mt-1.5 leading-snug">
                    {{ $volatilityDesc }}
                </div>
            </div>
        </div>

        {{-- Highest Rate (30 days) --}}
        <div class="bg-slate-50 rounded-3xl p-5 border border-slate-100 shadow-sm flex flex-col justify-between min-h-[130px]">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wide text-slate-400">Kurs Tertinggi (30 Hari)</span>
                <span class="w-8 h-8 bg-emerald-50 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-arrow-trend-up text-emerald-500 text-sm"></i>
                </span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-slate-800 truncate">
                    {{ $maxRate ? number_format($maxRate, 2) : 'N/A' }} 
                    <span class="text-xs font-bold text-slate-400">{{ $selectedCountry?->currency_code }}</span>
                </div>
                <div class="text-xs text-slate-400 font-semibold mt-1">
                    Nilai tertinggi tercatat dalam sebulan
                </div>
            </div>
        </div>

        {{-- Lowest Rate (30 days) --}}
        <div class="bg-slate-50 rounded-3xl p-5 border border-slate-100 shadow-sm flex flex-col justify-between min-h-[130px]">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wide text-slate-400">Kurs Terendah (30 Hari)</span>
                <span class="w-8 h-8 bg-rose-50 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-arrow-trend-down text-rose-500 text-sm"></i>
                </span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-slate-800 truncate">
                    {{ $minRate ? number_format($minRate, 2) : 'N/A' }} 
                    <span class="text-xs font-bold text-slate-400">{{ $selectedCountry?->currency_code }}</span>
                </div>
                <div class="text-xs text-slate-400 font-semibold mt-1">
                    Nilai terendah tercatat dalam sebulan
                </div>
            </div>
        </div>

    </div>

    {{-- ══ MAIN WORKSPACE GRID ══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Left Column: Selector & Bidirectional Calculator --}}
        <div class="lg:col-span-1 flex flex-col gap-8">
            
            {{-- Selector Card --}}
            <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100 shadow-sm">
                <h3 class="text-base font-extrabold text-slate-800 mb-2">Pilih Negara / Valuta</h3>
                <p class="text-xs text-slate-400 font-semibold leading-relaxed mb-4">Pilih negara untuk memantau fluktuasi nilai mata uang domestiknya terhadap USD.</p>
                
                <form action="{{ route('currency.index') }}" method="GET" class="space-y-4">
                    <div>
                        <div class="relative">
                            <select id="country-select" name="country" onchange="this.form.submit()" class="bg-slate-100 border border-slate-200 text-slate-800 text-sm font-semibold rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 pr-8 appearance-none cursor-pointer transition-all">
                                @foreach($countries as $c)
                                    <option value="{{ $c->code }}" {{ ($selectedCountry && $selectedCountry->code === $c->code) ? 'selected' : '' }}>
                                        {{ $c->name }} ({{ $c->currency_code }} - {{ $c->currency }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-400">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>
                </form>

                @if($selectedCountry)
                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center gap-3">
                        <img src="{{ $selectedCountry->flag }}" class="w-12 h-8 object-cover rounded-lg shadow-sm border border-slate-200" alt="">
                        <div>
                            <h4 class="font-extrabold text-slate-800 leading-none text-sm">{{ $selectedCountry->name }}</h4>
                            <span class="text-xs text-slate-400 font-semibold mt-1.5 block">Simbol Kurs: {{ $selectedCountry->currency_code }}</span>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Bidirectional Calculator --}}
            <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100 shadow-sm flex-1 flex flex-col justify-between">
                <div>
                    <div class="border-b border-slate-100 pb-3 mb-4">
                        <h3 class="text-base font-extrabold text-slate-800">Kalkulator Konversi Dua Arah</h3>
                        <p class="text-xs text-slate-400 font-semibold mt-1">Konversi mata uang target ke USD atau sebaliknya secara real-time.</p>
                    </div>

                    <div class="space-y-4 py-2">
                        <!-- USD Input -->
                        <div>
                            <label for="calc-usd" class="text-xs font-bold text-slate-400 block mb-1.5">Jumlah Dolar (USD)</label>
                            <div class="relative rounded-xl shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-slate-400 text-sm font-bold">$</span>
                                </div>
                                <input type="number" id="calc-usd" class="block w-full rounded-xl border border-slate-200 pl-7 pr-3 py-2.5 focus:border-blue-500 focus:ring-blue-500 text-sm font-bold text-slate-800" placeholder="0.00" value="100">
                            </div>
                        </div>

                        <!-- Intercept Swap Icon -->
                        <div class="flex items-center justify-center text-slate-400">
                            <i class="fa-solid fa-arrow-up-down text-base"></i>
                        </div>

                        <!-- Target Currency Input -->
                        <div>
                            <label for="calc-target" class="text-xs font-bold text-slate-400 block mb-1.5">
                                Jumlah ({{ $selectedCountry ? $selectedCountry->currency_code : 'Target' }})
                            </label>
                            <div class="relative rounded-xl shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-slate-400 text-sm font-bold">{{ $selectedCountry ? $selectedCountry->currency_code : '' }}</span>
                                </div>
                                <input type="number" id="calc-target" class="block w-full rounded-xl border border-slate-200 pl-14 pr-3 py-2.5 focus:border-blue-500 focus:ring-blue-500 text-sm font-bold text-slate-800" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-xs text-slate-400 font-semibold border-t border-slate-100 pt-3 mt-4">
                    Kalkulasi berdasarkan kurs terkahir (1 USD = {{ $latestRate ? number_format($latestRate->exchange_rate, 4) : 'N/A' }} {{ $selectedCountry?->currency_code }}).
                </div>
            </div>

        </div>

        {{-- Right Column: Trend Chart (Takes 2/3 width) --}}
        <div class="lg:col-span-2 bg-slate-50 p-6 rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgba(0,0,0,0.01)] flex flex-col justify-between">
            <div class="border-b border-slate-100 pb-3 mb-4">
                <h3 class="text-base font-extrabold text-slate-800">Tren Pergerakan Kurs (30 Hari Terakhir)</h3>
                <p class="text-xs text-slate-400 font-semibold mt-1">Grafik historis nilai tukar mata uang domestik per 1 Dolar AS.</p>
            </div>
            <div class="w-full h-[320px]">
                <canvas id="rate-history-chart"></canvas>
            </div>
        </div>

    </div>

    {{-- ══ LOG HISTORIS TABLE CARD ══ --}}
    <div class="bg-slate-50 rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgba(0,0,0,0.03)] overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-extrabold text-slate-800">Log Riwayat Kurs</h3>
                <p class="text-xs text-slate-400 font-semibold mt-1">Log data nilai tukar mata uang asing yang tercatat dalam sistem.</p>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            @if($rateHistory->isEmpty())
                <div class="text-center py-20">
                    <div class="w-16 h-16 bg-slate-100 rounded-3xl flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-money-bill-transfer text-slate-600 text-3xl"></i>
                    </div>
                    <p class="font-extrabold text-slate-600 text-sm">Tidak ada riwayat nilai kurs yang tersimpan.</p>
                </div>
            @else
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-800 text-left">
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Waktu Sinkronisasi</th>
                            <th class="px-4 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Mata Uang Acuan</th>
                            <th class="px-4 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Mata Uang Tujuan</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400 text-right">Nilai Kurs Akhir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($rateHistory->reverse() as $rate)
                            <tr class="hover:bg-slate-100/70 transition-colors duration-150">
                                <td class="px-6 py-4 font-semibold text-slate-700 text-sm">
                                    {{ \Carbon\Carbon::parse($rate->recorded_at)->format('d M Y, H:i') }}
                                </td>
                                <td class="px-4 py-4 font-bold text-slate-400 text-sm">
                                    {{ $rate->base_currency }} (Dolar AS)
                                </td>
                                <td class="px-4 py-4 font-bold text-slate-800 text-sm">
                                    {{ $rate->target_currency }} ({{ $selectedCountry?->currency }})
                                </td>
                                <td class="px-6 py-4 text-right font-black text-blue-600 text-sm">
                                    {{ number_format($rate->exchange_rate, 4) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const rate = {{ $latestRate ? $latestRate->exchange_rate : 1.0 }};
        const usdInput = document.getElementById('calc-usd');
        const targetInput = document.getElementById('calc-target');

        // Bidirectional Conversion logic
        function convertUsdToTarget() {
            const usdVal = parseFloat(usdInput.value) || 0;
            targetInput.value = (usdVal * rate).toFixed(2);
        }

        function convertTargetToUsd() {
            const targetVal = parseFloat(targetInput.value) || 0;
            usdInput.value = (targetVal / rate).toFixed(2);
        }

        usdInput.addEventListener('input', convertUsdToTarget);
        targetInput.addEventListener('input', convertTargetToUsd);

        // Run once on load
        convertUsdToTarget();

        // Chart.js Configuration
        const chartHistory = @json($rateHistory);
        if (chartHistory.length > 0) {
            const ctx = document.getElementById('rate-history-chart').getContext('2d');
            
            const labels = chartHistory.map(item => {
                const date = new Date(item.recorded_at);
                return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
            });
            const data = chartHistory.map(item => item.exchange_rate);
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Nilai Tukar (1 USD)',
                        data: data,
                        borderColor: '#3b82f6',
                        backgroundColor: function(context) {
                            const chart = context.chart;
                            const {ctx, chartArea} = chart;
                            if (!chartArea) return null;
                            const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                            gradient.addColorStop(0, 'rgba(59, 130, 246,0.2)');
                            gradient.addColorStop(1, 'rgba(59, 130, 246,0.0)');
                            return gradient;
                        },
                        borderWidth: 3,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#f0f4f8',
                        pointBorderColor: '#3b82f6',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            grid: {
                                borderDash: [5, 5],
                                color: '#f1f5f9'
                            },
                            ticks: {
                                font: {
                                    size: 11,
                                    family: 'Outfit',
                                    weight: 'bold'
                                },
                                color: '#64748b'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                maxTicksLimit: 10,
                                font: {
                                    size: 11,
                                    family: 'Outfit'
                                },
                                color: '#64748b'
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection
