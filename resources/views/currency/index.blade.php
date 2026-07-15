@extends('layouts.app')

@section('title', 'Analisis Dampak Mata Uang')

@section('content')
<div class="space-y-8">

    <!-- Top Grid: Selection & Calculator -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Selector and Currency Info -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-slate-800 text-base mb-2">Nilai Tukar Valuta Asing</h3>
                <p class="text-xs text-slate-500 leading-relaxed mb-6">Pilih negara untuk memantau nilai mata uang domestiknya terhadap USD. Fluktuasi tajam menandakan ketidakstabilan ekonomi.</p>
                
                <form action="{{ route('currency.index') }}" method="GET" class="space-y-4">
                    <div>
                        <label for="country-select" class="text-xs font-bold text-slate-500 block mb-2">Pilih Negara / Mata Uang:</label>
                        <div class="relative">
                            <select id="country-select" name="country" onchange="this.form.submit()" class="bg-slate-50 border border-slate-300 text-slate-800 text-sm font-semibold rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 pr-8 appearance-none cursor-pointer">
                                @foreach($countries as $c)
                                    <option value="{{ $c->code }}" {{ ($selectedCountry && $selectedCountry->code === $c->code) ? 'selected' : '' }}>
                                        {{ $c->name }} ({{ $c->currency_code }} - {{ $c->currency }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            @if($selectedCountry)
                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Nilai Kurs Saat Ini</span>
                        <span class="text-2xl font-black text-slate-800 leading-none mt-1 block">
                            1 USD = {{ $latestRate ? number_format($latestRate->exchange_rate, 4) : 'N/A' }} {{ $selectedCountry->currency_code }}
                        </span>
                    </div>
                    <div class="bg-emerald-50 text-emerald-700 font-bold p-3 rounded-lg text-xl flex items-center justify-center">
                        <i class="fa-solid fa-sack-dollar"></i>
                    </div>
                </div>
            @endif
        </div>

        <!-- Dynamic Currency Calculator -->
        <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="border-b border-slate-100 pb-3 mb-4">
                <h3 class="font-bold text-slate-800 text-sm">Kalkulator Konversi Impor</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 py-2 items-center">
                <!-- USD Input -->
                <div>
                    <label for="calc-usd" class="text-xs font-bold text-slate-400 block mb-1">Jumlah USD (US$)</label>
                    <div class="relative rounded-lg shadow-sm">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="text-slate-500 text-sm font-bold">$</span>
                        </div>
                        <input type="number" id="calc-usd" class="block w-full rounded-lg border-slate-300 pl-7 pr-3 py-2.5 focus:border-purple-500 focus:ring-purple-500 text-sm font-bold text-slate-800" placeholder="0.00" value="100">
                    </div>
                </div>

                <!-- Target Currency Output -->
                <div>
                    <label for="calc-target" class="text-xs font-bold text-slate-400 block mb-1">
                        Konversi ({{ $selectedCountry ? $selectedCountry->currency_code : 'Target' }})
                    </label>
                    <div class="relative rounded-lg shadow-sm">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="text-slate-500 text-sm font-bold">{{ $selectedCountry ? $selectedCountry->currency_code : '' }}</span>
                        </div>
                        <input type="text" id="calc-target" class="block w-full rounded-lg border-slate-350 bg-slate-50 pl-14 pr-3 py-2.5 text-sm font-black text-purple-700" readonly>
                    </div>
                </div>
            </div>
            
            <div class="text-[10px] text-slate-400 font-semibold border-t border-slate-100 pt-3">
                Kalkulasi real-time berdasarkan kurs terbaru yang tersimpan.
            </div>
        </div>
    </div>

    <!-- Chart Line History -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <div class="border-b border-slate-250 pb-3 mb-6">
            <h3 class="font-bold text-slate-800 text-lg">Tren Pergerakan Kurs (30 Catatan Terakhir)</h3>
            <p class="text-xs text-slate-500 mt-0.5">Menunjukkan riwayat nilai tukar mata uang domestik per 1 USD.</p>
        </div>
        <div class="w-full h-[320px]">
            <canvas id="rate-history-chart"></canvas>
        </div>
    </div>

    <!-- History Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <h3 class="font-bold text-slate-800 text-lg">Log Riwayat Kurs</h3>
            <p class="text-xs text-slate-500 mt-0.5">Log data nilai tukar mata uang asing yang tercatat dalam sistem.</p>
        </div>
        <div class="overflow-x-auto">
            @if($rateHistory->isEmpty())
                <div class="text-center py-12 text-slate-400">
                    <p class="font-semibold text-slate-600">Tidak ada riwayat nilai kurs yang tersimpan.</p>
                </div>
            @else
                <table class="table-auto w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-400 font-bold text-xs uppercase border-b border-slate-200">
                            <th class="p-4 pl-6">Waktu Sinkronisasi</th>
                            <th class="p-4">Mata Uang Acuan</th>
                            <th class="p-4">Mata Uang Tujuan</th>
                            <th class="p-4 pr-6 text-right font-bold">Nilai Tukar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-150 text-sm text-slate-700">
                        @foreach($rateHistory->reverse() as $rate)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="p-4 pl-6 font-semibold">{{ \Carbon\Carbon::parse($rate->recorded_at)->format('d M Y, H:i') }}</td>
                                <td class="p-4 font-bold text-slate-500">{{ $rate->base_currency }}</td>
                                <td class="p-4 font-bold text-slate-800">{{ $rate->target_currency }}</td>
                                <td class="p-4 pr-6 text-right font-black text-indigo-600">{{ number_format($rate->exchange_rate, 4) }}</td>
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

        function updateConversion() {
            const val = parseFloat(usdInput.value) || 0;
            targetInput.value = (val * rate).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 4 });
        }

        usdInput.addEventListener('input', updateConversion);
        updateConversion(); // run once on load

        // Chart.js Configuration
        const chartHistory = @json($rateHistory);
        if (chartHistory.length > 0) {
            const ctx = document.getElementById('rate-history-chart').getContext('2d');
            
            const labels = chartHistory.map(item => {
                const date = new Date(item.recorded_at);
                return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' });
            });
            const data = chartHistory.map(item => item.exchange_rate);
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Nilai Tukar (1 USD)',
                        data: data,
                        borderColor: '#7c3aed',
                        backgroundColor: 'rgba(124, 58, 237, 0.05)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.3,
                        pointBackgroundColor: '#7c3aed',
                        pointBorderColor: '#ffffff',
                        pointRadius: 4
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
                                borderDash: [5, 5]
                            },
                            ticks: {
                                font: {
                                    weight: 'bold'
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                maxTicksLimit: 8
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection
