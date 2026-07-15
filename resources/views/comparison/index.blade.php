@extends('layouts.app')

@section('title', 'Perbandingan Negara')

@section('content')
<div class="space-y-8">

    {{-- ===== HEADER ===== --}}
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-5">
            <div>
                <h3 class="text-xl font-extrabold text-slate-800 flex items-center gap-2">
                    <span class="bg-violet-100 text-violet-600 w-9 h-9 rounded-xl flex items-center justify-center text-sm shrink-0">
                        <i class="fa-solid fa-arrow-right-arrow-left"></i>
                    </span>
                    Perbandingan Risiko Negara
                </h3>
                <p class="text-xs text-slate-500 mt-1.5 ml-11">Bandingkan dua negara secara berdampingan berdasarkan indikator risiko, cuaca, ekonomi, dan nilai tukar.</p>
            </div>

            {{-- Country Selectors --}}
            <form action="{{ route('comparison.index') }}" method="GET" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Negara Pertama</label>
                    <div class="relative">
                        <select name="country1" class="bg-slate-50 border border-slate-300 text-slate-800 text-sm font-semibold rounded-xl p-2.5 pr-8 appearance-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-violet-400 min-w-[180px]">
                            @foreach($countries as $c)
                                <option value="{{ $c->code }}" {{ strtoupper($countryCode1) === $c->code ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2.5 pointer-events-none text-slate-400"><i class="fa-solid fa-chevron-down text-xs"></i></div>
                    </div>
                </div>
                <div class="flex items-end pb-2.5">
                    <div class="bg-violet-100 text-violet-600 w-8 h-8 rounded-full flex items-center justify-center font-black text-sm">VS</div>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Negara Kedua</label>
                    <div class="relative">
                        <select name="country2" class="bg-slate-50 border border-slate-300 text-slate-800 text-sm font-semibold rounded-xl p-2.5 pr-8 appearance-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-violet-400 min-w-[180px]">
                            @foreach($countries as $c)
                                <option value="{{ $c->code }}" {{ strtoupper($countryCode2) === $c->code ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2.5 pointer-events-none text-slate-400"><i class="fa-solid fa-chevron-down text-xs"></i></div>
                    </div>
                </div>
                <button type="submit" class="bg-violet-600 hover:bg-violet-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2 shadow-md shadow-violet-200 self-end">
                    <i class="fa-solid fa-magnifying-glass-chart"></i>
                    Bandingkan
                </button>
            </form>
        </div>
    </div>

    {{-- ===== SIDE-BY-SIDE COMPARISON ===== --}}
    @php
        $c1 = $compareData['country1'] ?? null;
        $c2 = $compareData['country2'] ?? null;
    @endphp

    {{-- Peta Lokasi --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h4 class="font-bold text-slate-800 text-base">Peta Lokasi Negara</h4>
            <p class="text-xs text-slate-500 mt-0.5">Posisi geografis kedua negara yang dibandingkan.</p>
        </div>
        <div id="comparison-map" class="w-full h-[380px]"></div>
    </div>

    {{-- Risk Score Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach(['country1' => ['color' => 'violet', 'grad' => 'from-violet-500 to-purple-600'], 'country2' => ['color' => 'indigo', 'grad' => 'from-indigo-500 to-blue-600']] as $key => $style)
            @php $data = $compareData[$key] ?? null; $country = $data['model'] ?? null; @endphp
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                {{-- Country Header --}}
                <div class="bg-gradient-to-r {{ $style['grad'] }} p-6 text-white">
                    <div class="flex items-center gap-4">
                        @if($country?->flag)
                            <img src="{{ $country->flag }}" class="w-14 h-10 object-cover rounded-lg shadow-md border-2 border-white/30" alt="">
                        @endif
                        <div>
                            <h4 class="text-xl font-black leading-none">{{ $country?->name ?? 'Belum dipilih' }}</h4>
                            <p class="text-xs opacity-80 mt-1">{{ $country?->capital }} · {{ $country?->region }}</p>
                        </div>
                    </div>
                </div>

                {{-- Stats Grid --}}
                <div class="p-5 grid grid-cols-2 gap-4">
                    {{-- Risk Score --}}
                    @php $risk = $data['risk'] ?? null; @endphp
                    <div class="col-span-2 flex items-center justify-between bg-slate-50 rounded-xl p-4 border border-slate-100">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Skor Risiko</p>
                            <p class="text-3xl font-black text-slate-800 mt-0.5">{{ $risk?->total_score ?? 'N/A' }}</p>
                        </div>
                        @if($risk)
                        <span class="px-4 py-2 rounded-xl font-black text-sm
                            {{ $risk->risk_level === 'High'   ? 'bg-rose-100 text-rose-700' : '' }}
                            {{ $risk->risk_level === 'Medium' ? 'bg-amber-100 text-amber-700' : '' }}
                            {{ $risk->risk_level === 'Low'    ? 'bg-emerald-100 text-emerald-700' : '' }}
                        ">
                            {{ $risk->risk_level === 'High' ? 'Tinggi' : ($risk->risk_level === 'Medium' ? 'Sedang' : 'Rendah') }}
                        </span>
                        @else
                            <span class="text-xs text-slate-400 font-semibold">Belum dihitung</span>
                        @endif
                    </div>

                    {{-- Weather --}}
                    @php $w = $data['weather'] ?? null; @endphp
                    <div class="bg-sky-50 rounded-xl p-3.5 border border-sky-100">
                        <p class="text-[10px] font-bold text-sky-500 uppercase tracking-wider">Suhu</p>
                        <p class="text-2xl font-black text-sky-700 mt-1">{{ $w?->temperature ? $w->temperature.'°C' : 'N/A' }}</p>
                        <p class="text-xs text-sky-500 mt-0.5 font-semibold">{{ $w?->weather_condition ?? '-' }}</p>
                    </div>
                    <div class="bg-blue-50 rounded-xl p-3.5 border border-blue-100">
                        <p class="text-[10px] font-bold text-blue-500 uppercase tracking-wider">Angin</p>
                        <p class="text-2xl font-black text-blue-700 mt-1">{{ $w?->wind_speed ? $w->wind_speed.' km/h' : 'N/A' }}</p>
                        <p class="text-xs text-blue-500 mt-0.5 font-semibold">Kecepatan Angin</p>
                    </div>

                    {{-- Economy --}}
                    @php $gdp = $data['gdp'] ?? null; $inf = $data['inflation'] ?? null; $rate = $data['rate'] ?? null; @endphp
                    <div class="bg-emerald-50 rounded-xl p-3.5 border border-emerald-100">
                        <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-wider">PDB ({{ $gdp?->year ?? '-' }})</p>
                        <p class="text-base font-black text-emerald-700 mt-1">
                            {{ $gdp?->gdp_value ? '$'.number_format($gdp->gdp_value / 1e9, 1).'T' : 'N/A' }}
                        </p>
                        <p class="text-xs text-emerald-500 mt-0.5 font-semibold">Nilai PDB</p>
                    </div>
                    <div class="bg-amber-50 rounded-xl p-3.5 border border-amber-100">
                        <p class="text-[10px] font-bold text-amber-500 uppercase tracking-wider">Inflasi</p>
                        <p class="text-2xl font-black text-amber-700 mt-1">{{ $inf?->inflation_rate ? number_format($inf->inflation_rate, 1).'%' : 'N/A' }}</p>
                        <p class="text-xs text-amber-500 mt-0.5 font-semibold">Laju Inflasi</p>
                    </div>
                    <div class="col-span-2 bg-purple-50 rounded-xl p-3.5 border border-purple-100 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-bold text-purple-500 uppercase tracking-wider">Nilai Tukar</p>
                            <p class="text-xl font-black text-purple-700 mt-1">
                                {{ $rate ? '1 USD = '.number_format($rate->exchange_rate, 2).' '.$rate->target_currency : 'N/A' }}
                            </p>
                        </div>
                        <div class="bg-purple-100 text-purple-600 w-10 h-10 rounded-xl flex items-center justify-center text-lg">
                            <i class="fa-solid fa-money-bill-transfer"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Radar / Bar Chart Visual Comparison --}}
    @if($c1 && $c2 && ($c1['risk'] ?? null) && ($c2['risk'] ?? null))
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h4 class="font-bold text-slate-800 text-base mb-5">Grafik Perbandingan Skor Risiko</h4>
        <div class="w-full h-[280px]">
            <canvas id="compare-chart"></canvas>
        </div>
    </div>
    @endif

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ===== MAP =====
    const map = L.map('comparison-map').setView([20, 15], 2);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '© OpenStreetMap contributors, © CARTO',
        subdomains: 'abcd', maxZoom: 19
    }).addTo(map);

    const markers = [];
    @if(isset($compareData['country1']['model']) && $compareData['country1']['model']->latitude)
    (function() {
        const c = @json(['lat' => $compareData['country1']['model']->latitude, 'lng' => $compareData['country1']['model']->longitude, 'name' => $compareData['country1']['model']->name, 'code' => $compareData['country1']['model']->code, 'color' => '#7c3aed']);
        const m = L.circleMarker([c.lat, c.lng], { radius: 14, fillColor: c.color, color: '#fff', weight: 3, fillOpacity: 0.9 })
            .addTo(map)
            .bindPopup(`<b style="color:${c.color}">${c.name}</b><br><small>Negara Pertama</small>`);
        markers.push([c.lat, c.lng]);
    })();
    @endif

    @if(isset($compareData['country2']['model']) && $compareData['country2']['model']->latitude)
    (function() {
        const c = @json(['lat' => $compareData['country2']['model']->latitude, 'lng' => $compareData['country2']['model']->longitude, 'name' => $compareData['country2']['model']->name, 'code' => $compareData['country2']['model']->code, 'color' => '#2563eb']);
        const m = L.circleMarker([c.lat, c.lng], { radius: 14, fillColor: c.color, color: '#fff', weight: 3, fillOpacity: 0.9 })
            .addTo(map)
            .bindPopup(`<b style="color:${c.color}">${c.name}</b><br><small>Negara Kedua</small>`);
        markers.push([c.lat, c.lng]);
    })();
    @endif

    if (markers.length === 2) {
        map.fitBounds(markers, { padding: [60, 60] });
    } else if (markers.length === 1) {
        map.setView(markers[0], 4);
    }

    // ===== BAR CHART =====
    @if(isset($compareData['country1']['risk']) && isset($compareData['country2']['risk']))
    const ctx = document.getElementById('compare-chart');
    if (ctx) {
        const r1 = @json($compareData['country1']['risk']);
        const r2 = @json($compareData['country2']['risk']);
        const name1 = @json($compareData['country1']['model']->name);
        const name2 = @json($compareData['country2']['model']->name);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Cuaca', 'Inflasi', 'Nilai Tukar', 'Berita', 'Total Risiko'],
                datasets: [
                    {
                        label: name1,
                        data: [
                            r1.weather_score ?? 0,
                            r1.inflation_score ?? 0,
                            r1.currency_score ?? 0,
                            r1.news_score ?? 0,
                            r1.total_score ?? 0,
                        ],
                        backgroundColor: 'rgba(124, 58, 237, 0.7)',
                        borderColor: '#7c3aed',
                        borderWidth: 2,
                        borderRadius: 8,
                    },
                    {
                        label: name2,
                        data: [
                            r2.weather_score ?? 0,
                            r2.inflation_score ?? 0,
                            r2.currency_score ?? 0,
                            r2.news_score ?? 0,
                            r2.total_score ?? 0,
                        ],
                        backgroundColor: 'rgba(37, 99, 235, 0.7)',
                        borderColor: '#2563eb',
                        borderWidth: 2,
                        borderRadius: 8,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { font: { weight: 'bold', size: 12 } } }
                },
                scales: {
                    y: { beginAtZero: true, max: 100, grid: { borderDash: [5,5] } },
                    x: { grid: { display: false } }
                }
            }
        });
    }
    @endif
});
</script>
@endsection
