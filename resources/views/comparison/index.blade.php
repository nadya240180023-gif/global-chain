@extends('layouts.app')

@section('title', 'Mesin Perbandingan Negara')

@section('content')
<div class="space-y-8">

    <!-- Country Selector Form -->
    <form id="compare-form" action="{{ route('comparison.index') }}" method="GET" class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-[0_8px_30px_rgba(0,0,0,0.03)] flex flex-wrap items-end gap-5 relative">
        <!-- Visual Loading Overlay -->
        <div id="loading-overlay" class="absolute inset-0 bg-white/80 backdrop-blur-[2px] rounded-3xl flex items-center justify-center gap-3 opacity-0 pointer-events-none transition-opacity duration-200 z-50">
            <i class="fa-solid fa-arrows-spin fa-spin text-2xl text-indigo-600"></i>
            <span class="text-sm font-extrabold text-slate-700">Menganalisis Rantai Pasok Real-time...</span>
        </div>

        <div class="flex-1 min-w-[240px]">
            <label class="text-xs font-extrabold text-slate-400 uppercase tracking-wider block mb-2">Negara Pertama (Kiri):</label>
            <div class="relative">
                <select name="country1" onchange="showLoadingAndSubmit()" class="bg-slate-50 border border-slate-200 text-slate-700 text-sm font-bold rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full p-3.5 pr-10 appearance-none cursor-pointer transition-all">
                    @foreach($countries as $c)
                        <option value="{{ $c->code }}" {{ $countryCode1 === $c->code ? 'selected' : '' }}>{{ $c->name }} ({{ $c->code }})</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i class="fa-solid fa-chevron-down text-xs"></i></div>
            </div>
        </div>
        <div class="shrink-0 text-slate-300 pb-3 hidden md:block">
            <div class="w-10 h-10 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center shadow-sm">
                <i class="fa-solid fa-arrow-right-arrow-left text-sm text-slate-400"></i>
            </div>
        </div>
        <div class="flex-1 min-w-[240px]">
            <label class="text-xs font-extrabold text-slate-400 uppercase tracking-wider block mb-2">Negara Kedua (Kanan):</label>
            <div class="relative">
                <select name="country2" onchange="showLoadingAndSubmit()" class="bg-slate-50 border border-slate-200 text-slate-700 text-sm font-bold rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full p-3.5 pr-10 appearance-none cursor-pointer transition-all">
                    @foreach($countries as $c)
                        <option value="{{ $c->code }}" {{ $countryCode2 === $c->code ? 'selected' : '' }}>{{ $c->name }} ({{ $c->code }})</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i class="fa-solid fa-chevron-down text-xs"></i></div>
            </div>
        </div>
        <button type="submit" onclick="document.getElementById('loading-overlay').classList.remove('opacity-0', 'pointer-events-none')" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-slate-800 font-extrabold px-6 py-3.5 rounded-2xl text-sm shadow-md shadow-indigo-500/10 transition-all flex items-center gap-2 cursor-pointer h-[50px]">
            <i class="fa-solid fa-magnifying-glass-chart"></i>
            Bandingkan
        </button>
    </form>

    @if($compareData['country1'] && $compareData['country2'])
        @php
            $c1 = $compareData['country1'];
            $c2 = $compareData['country2'];
        @endphp

        <!-- Side-by-Side Risk Meters -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @foreach(['country1' => $c1, 'country2' => $c2] as $key => $data)
                @php
                    $riskLevel = $data['risk']?->risk_level ?? 'Low';
                    $score = $data['risk']?->total_score ?? 0;
                    $rc = $riskLevel === 'High' ? 'rose' : ($riskLevel === 'Medium' ? 'amber' : 'emerald');
                    
                    $bgClass = $riskLevel === 'High' ? 'bg-rose-50/60 border-rose-100' : ($riskLevel === 'Medium' ? 'bg-amber-50/60 border-amber-100' : 'bg-emerald-50/60 border-emerald-100');
                    $textClass = $riskLevel === 'High' ? 'text-rose-600' : ($riskLevel === 'Medium' ? 'text-amber-600' : 'text-emerald-600');
                    $borderClass = $riskLevel === 'High' ? 'border-rose-400' : ($riskLevel === 'Medium' ? 'border-amber-400' : 'border-emerald-400');
                @endphp
                <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-[0_8px_30px_rgba(0,0,0,0.03)] hover:shadow-[0_8px_30px_rgba(0,0,0,0.06)] transition-all duration-300">
                    <div class="flex items-center gap-4 mb-6">
                        <img src="{{ $data['model']->flag }}" class="w-14 h-9 object-cover rounded-xl shadow-sm border border-slate-200/80" alt="">
                        <div>
                            <h4 class="font-extrabold text-slate-800 text-lg leading-tight">{{ $data['model']->name }}</h4>
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ $data['model']->region }} &bull; {{ $data['model']->capital }}</span>
                        </div>
                    </div>

                    <!-- Risk Score Display -->
                    <div class="flex items-center gap-4 p-4 rounded-2xl border mb-6 {{ $bgClass }}">
                        <div class="w-16 h-16 rounded-full border-4 flex items-center justify-center bg-white shrink-0 shadow-sm {{ $borderClass }}">
                            <span class="text-xl font-black {{ $textClass }}">{{ $score }}</span>
                        </div>
                        <div>
                            <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Skor Risiko Total</p>
                            <p class="text-lg font-black leading-none mt-1 {{ $textClass }}">RISIKO {{ $riskLevel === 'High' ? 'TINGGI' : ($riskLevel === 'Medium' ? 'MENENGAH' : 'RENDAH') }}</p>
                        </div>
                    </div>

                    <!-- Sub-Score Bars -->
                    <div class="space-y-3">
                        @php $subScores = [
                            ['label'=>'Cuaca',   'val'=> $data['risk']?->weather_score   ?? 0, 'icon' => 'fa-cloud-sun'],
                            ['label'=>'Inflasi', 'val'=> $data['risk']?->inflation_score ?? 0, 'icon' => 'fa-chart-line'],
                            ['label'=>'Kurs',    'val'=> $data['risk']?->currency_score  ?? 0, 'icon' => 'fa-coins'],
                            ['label'=>'Berita',  'val'=> $data['risk']?->news_score      ?? 0, 'icon' => 'fa-newspaper'],
                        ]; @endphp
                        @foreach($subScores as $s)
                        <div class="flex items-center gap-3">
                            <span class="w-20 font-bold text-xs text-slate-500 flex items-center gap-1.5 shrink-0">
                                <i class="fa-solid {{ $s['icon'] }} w-4 text-slate-400 text-center"></i>
                                {{ $s['label'] }}
                            </span>
                            <div class="flex-1 bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-550 {{ $s['val'] >= 70 ? 'bg-gradient-to-r from-rose-400 to-rose-600' : ($s['val'] >= 35 ? 'bg-gradient-to-r from-amber-400 to-amber-500' : 'bg-gradient-to-r from-emerald-400 to-emerald-600') }}" style="width: {{ $s['val'] }}%"></div>
                            </div>
                            <span class="w-8 text-right font-extrabold text-xs text-slate-600">{{ $s['val'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Metrics Comparison Grid -->
        <div class="bg-white rounded-3xl border border-slate-200/60 shadow-[0_8px_30px_rgba(0,0,0,0.03)] overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <i class="fa-solid fa-list-check"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800 text-base">Perbandingan Indikator Ekonomi</h3>
                    <p class="text-xs text-slate-400 font-semibold">Tabel data metrik logistik &amp; makroekonomi live</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="table-auto w-full border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-800/40 text-left border-b border-slate-100">
                            <th class="p-4 pl-6 text-xs font-extrabold uppercase tracking-wider text-slate-500 w-1/3">Indikator</th>
                            <th class="p-4 text-center text-indigo-600 font-black text-xs uppercase tracking-wider">{{ $c1['model']->name }}</th>
                            <th class="p-4 text-center text-blue-600 font-black text-xs uppercase tracking-wider">{{ $c2['model']->name }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php
                            $rows = [
                                ['label'=>'Mata Uang',           'v1'=> $c1['model']->currency ?? 'N/A',             'v2'=> $c2['model']->currency ?? 'N/A', 'icon' => 'fa-money-bill-wave'],
                                ['label'=>'Kode Mata Uang',      'v1'=> $c1['model']->currency_code ?? 'N/A',        'v2'=> $c2['model']->currency_code ?? 'N/A', 'icon' => 'fa-barcode'],
                                ['label'=>'Jumlah Penduduk',     'v1'=> number_format($c1['model']->population),      'v2'=> number_format($c2['model']->population), 'icon' => 'fa-users'],
                                ['label'=>'PDB Terbaru',         'v1'=> $c1['gdp'] ? '$'.number_format($c1['gdp']->gdp_value / 1e12, 3).' T' : 'N/A', 'v2'=> $c2['gdp'] ? '$'.number_format($c2['gdp']->gdp_value / 1e12, 3).' T' : 'N/A', 'icon' => 'fa-vault'],
                                ['label'=>'Inflasi Terbaru',     'v1'=> $c1['inflation'] ? $c1['inflation']->inflation_rate.'%' : 'N/A', 'v2'=> $c2['inflation'] ? $c2['inflation']->inflation_rate.'%' : 'N/A', 'icon' => 'fa-percent'],
                                ['label'=>'Kurs (1 USD)',        'v1'=> $c1['rate'] ? number_format($c1['rate']->exchange_rate, 2).' '.$c1['model']->currency_code : 'N/A', 'v2'=> $c2['rate'] ? number_format($c2['rate']->exchange_rate, 2).' '.$c2['model']->currency_code : 'N/A', 'icon' => 'fa-scale-balanced'],
                                ['label'=>'Suhu Saat Ini',       'v1'=> $c1['weather'] ? $c1['weather']->temperature.'°C' : 'N/A', 'v2'=> $c2['weather'] ? $c2['weather']->temperature.'°C' : 'N/A', 'icon' => 'fa-temperature-high'],
                                ['label'=>'Kondisi Cuaca',       'v1'=> $c1['weather']?->weather_condition ?? 'N/A',  'v2'=> $c2['weather']?->weather_condition ?? 'N/A', 'icon' => 'fa-cloud-sun-rain'],
                                ['label'=>'Kec. Angin',          'v1'=> $c1['weather'] ? $c1['weather']->wind_speed.' km/h' : 'N/A', 'v2'=> $c2['weather'] ? $c2['weather']->wind_speed.' km/h' : 'N/A', 'icon' => 'fa-wind'],
                            ];
                        @endphp
                        @foreach($rows as $row)
                        <tr class="hover:bg-slate-50 transition-colors duration-150">
                            <td class="p-4 pl-6 font-bold text-xs text-slate-500 flex items-center gap-2">
                                <i class="fa-solid {{ $row['icon'] }} w-4 text-slate-400"></i>
                                {{ $row['label'] }}
                            </td>
                            <td class="p-4 text-center font-extrabold text-sm text-slate-700">{{ $row['v1'] }}</td>
                            <td class="p-4 text-center font-extrabold text-sm text-slate-700">{{ $row['v2'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>



        <!-- Chart.js GDP Trend Comparison -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-[0_8px_30px_rgba(0,0,0,0.03)]">
                <div class="flex items-center gap-2 mb-4">
                    <i class="fa-solid fa-chart-area text-indigo-500"></i>
                    <h4 class="font-extrabold text-slate-800 text-sm">Tren PDB Historis</h4>
                </div>
                <div class="h-56"><canvas id="gdp-compare-chart"></canvas></div>
            </div>
            <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-[0_8px_30px_rgba(0,0,0,0.03)]">
                <div class="flex items-center gap-2 mb-4">
                    <i class="fa-solid fa-chart-line text-purple-500"></i>
                    <h4 class="font-extrabold text-slate-800 text-sm">Tren Inflasi Historis (%)</h4>
                </div>
                <div class="h-56"><canvas id="inflation-compare-chart"></canvas></div>
            </div>
        </div>

        {{-- AI Supply Chain Advisor Section --}}
        <div class="bg-gradient-to-tr from-indigo-900/95 to-slate-900/95 text-white rounded-3xl p-6 shadow-xl border border-slate-800 relative overflow-hidden">
            <!-- Decorative Glow -->
            <div class="absolute -right-16 -top-16 w-48 h-48 bg-indigo-500/10 rounded-full blur-3xl"></div>
            <div class="absolute -left-16 -bottom-16 w-48 h-48 bg-purple-500/10 rounded-full blur-3xl"></div>

            <div class="flex items-center gap-3 mb-6 relative z-10">
                <span class="w-10 h-10 rounded-2xl bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 flex items-center justify-center shadow-inner">
                    <i class="fa-solid fa-brain text-lg"></i>
                </span>
                <div>
                    <h3 class="font-black text-white text-base">Asisten Keputusan AI (Supply Chain)</h3>
                    <p class="text-xs text-slate-400 font-medium">Analisis mitigasi risiko &amp; rute distribusi optimal</p>
                </div>
            </div>

            @php
                $s1 = $c1['risk']?->total_score ?? 35;
                $s2 = $c2['risk']?->total_score ?? 35;
                $diff = abs($s1 - $s2);
                $better = $s1 < $s2 ? $c1['model'] : $c2['model'];
                $worse = $s1 >= $s2 ? $c1['model'] : $c2['model'];
                $betterScore = min($s1, $s2);
                $worseScore = max($s1, $s2);
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                {{-- Analisis --}}
                <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-5 border border-white/10 flex flex-col justify-between">
                    <div>
                        <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest block mb-2">Simpulan AI</span>
                        <p class="text-sm font-bold text-slate-200 leading-relaxed">
                            Berdasarkan data risiko real-time, <strong class="text-indigo-400 font-extrabold">{{ $better->name }}</strong> memiliki tingkat stabilitas rantai pasok yang **lebih unggul** dibandingkan dengan <strong class="text-slate-400 font-extrabold">{{ $worse->name }}</strong>.
                        </p>
                        <p class="text-xs text-slate-400 mt-3 leading-relaxed">
                            Selisih skor risiko adalah <strong class="text-slate-200">{{ $diff }} poin</strong>. {{ $better->name }} (Skor: {{ $betterScore }}/100) menawarkan rute logistik dengan probabilitas gangguan cuaca, gejolak finansial, atau sentimen geopolitik yang lebih rendah daripada {{ $worse->name }} (Skor: {{ $worseScore }}/100).
                        </p>
                    </div>
                    <div class="mt-5 pt-4 border-t border-white/10">
                        <div class="text-[9px] text-slate-500 font-bold uppercase tracking-wider">Rekomendasi Utama Rute Pasokan</div>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[10px] font-black text-emerald-400 bg-emerald-500/10 px-3 py-1 rounded-lg border border-emerald-500/20 uppercase tracking-wide">{{ $better->name }} (Rute Utama)</span>
                        </div>
                    </div>
                </div>

                {{-- Rekomendasi Taktis --}}
                <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-5 border border-white/10">
                    <span class="text-[10px] font-black text-purple-400 uppercase tracking-widest block mb-2">Panduan Mitigasi AI</span>
                    <ul class="space-y-3.5 text-xs text-slate-300 font-medium">
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-circle-nodes text-indigo-400 mt-0.5 shrink-0"></i>
                            <span><strong>Diversifikasi Alokasi:</strong> Alokasikan porsi pasokan utama (e.g. 70%) ke {{ $better->name }} dan simpan {{ $worse->name }} sebagai rute alternatif (30%) untuk mitigasi risiko ketergantungan.</span>
                        </li>
                        @if($c1['inflation'] && $c2['inflation'] && abs(($c1['inflation']->inflation_rate ?? 0) - ($c2['inflation']->inflation_rate ?? 0)) > 2)
                            <li class="flex items-start gap-2.5">
                                <i class="fa-solid fa-coins text-indigo-400 mt-0.5 shrink-0"></i>
                                <span><strong>Proteksi Margin (Inflasi):</strong> Perbedaan tingkat inflasi yang cukup jauh menyarankan perlunya negosiasi kontrak jangka panjang berbasis mata uang USD untuk transaksi dengan negara berinflasi lebih tinggi guna mengunci harga beli.</span>
                            </li>
                        @endif
                        @if($c1['weather'] && $c2['weather'] && (abs(($c1['weather']->rainfall ?? 0) - ($c2['weather']->rainfall ?? 0)) > 10 || abs(($c1['weather']->wind_speed ?? 0) - ($c2['weather']->wind_speed ?? 0)) > 20))
                            <li class="flex items-start gap-2.5">
                                <i class="fa-solid fa-cloud-bolt text-indigo-400 mt-0.5 shrink-0"></i>
                                <span><strong>Pemberitahuan Cuaca Logistik:</strong> Salah satu negara mengalami cuaca/angin basah yang berpotensi menunda bongkar muat kapal laut. Disarankan menggunakan asuransi keterlambatan barang khusus pengiriman lewat pelabuhan tersebut.</span>
                            </li>
                        @endif
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-shield-halved text-indigo-400 mt-0.5 shrink-0"></i>
                            <span><strong>Langkah Lanjutan:</strong> Monitor halaman detail masing-masing negara secara rutin di sistem kami untuk mengantisipasi perubahan skor risiko instan.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
function showLoadingAndSubmit() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) {
        overlay.classList.remove('opacity-0', 'pointer-events-none');
    }
    document.getElementById('compare-form').submit();
}

document.addEventListener('DOMContentLoaded', function() {
    @if($compareData['country1'] && $compareData['country2'])
    const c1GdpHistory = @json($compareData['country1']['gdp_history']);
    const c2GdpHistory = @json($compareData['country2']['gdp_history']);
    const c1InfHistory = @json($compareData['country1']['inflation_history']);
    const c2InfHistory = @json($compareData['country2']['inflation_history']);

    // GDP Chart
    const gdpCtx = document.getElementById('gdp-compare-chart').getContext('2d');
    new Chart(gdpCtx, {
        type: 'line',
        data: {
            labels: c1GdpHistory.map(d => d.year),
            datasets: [
                {
                    label: '{{ $c1["model"]->name }}',
                    data: c1GdpHistory.map(d => (d.gdp_value / 1e12).toFixed(4)),
                    borderColor: '#7c3aed',
                    backgroundColor: 'rgba(124,58,237,0.05)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3
                },
                {
                    label: '{{ $c2["model"]->name }}',
                    data: c2GdpHistory.map(d => (d.gdp_value / 1e12).toFixed(4)),
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79,70,229,0.05)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3
                }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { font: { size: 12, weight: 'bold' } } } },
            scales: {
                y: { grid: { borderDash: [4,4] }, ticks: { font: { weight: 'bold', size: 11 } } },
                x: { grid: { display: false }, ticks: { font: { size: 11 } } }
            }
        }
    });

    // Inflation Chart
    const infCtx = document.getElementById('inflation-compare-chart').getContext('2d');
    new Chart(infCtx, {
        type: 'line',
        data: {
            labels: c1InfHistory.map(d => d.year),
            datasets: [
                {
                    label: '{{ $c1["model"]->name }}',
                    data: c1InfHistory.map(d => d.inflation_rate),
                    borderColor: '#7c3aed',
                    backgroundColor: 'rgba(124,58,237,0.05)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3
                },
                {
                    label: '{{ $c2["model"]->name }}',
                    data: c2InfHistory.map(d => d.inflation_rate),
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79,70,229,0.05)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3
                }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { font: { size: 12, weight: 'bold' } } } },
            scales: {
                y: { grid: { borderDash: [4,4] }, ticks: { font: { weight: 'bold', size: 11 } } },
                x: { grid: { display: false }, ticks: { font: { size: 11 } } }
            }
        }
    });
    @endif
});
</script>
@endsection
