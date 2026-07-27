@extends('layouts.app')

@section('title', 'Kalkulator & Estimasi Rute Pengiriman')

@section('content')
<div class="space-y-8">
    {{-- ===== HEADER & DESCRIPTION ===== --}}
    <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/10 rounded-full blur-2xl"></div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-10">
            <div>
                <h3 class="text-2xl font-extrabold text-slate-800 flex items-center gap-3">
                    <span class="bg-blue-100 text-blue-600 w-10 h-10 rounded-xl flex items-center justify-center text-base shrink-0">
                        <i class="fa-solid fa-route"></i>
                    </span>
                    Kalkulator & Estimasi Rute Pengiriman
                </h3>
                <p class="text-sm text-slate-400 mt-2 ml-[52px]">
                    Hitung jarak, estimasi waktu transit, serta analisis faktor risiko (cuaca, geopolitik, dan kepadatan) yang memengaruhi rute pelayaran antar pelabuhan global.
                </p>
            </div>
        </div>
    </div>

    {{-- ===== CONFIGURATION FORM & RESULTS ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Form Card --}}
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm h-fit">
            <h4 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-sliders text-blue-500"></i>
                Parameter Rute
            </h4>

            <form action="{{ route('shipping.estimator') }}" method="GET" class="space-y-5">
                <div>
                    <label for="origin" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pelabuhan Asal</label>
                    <div class="relative">
                        <select name="origin" id="origin" required class="w-full pl-10 pr-4 py-3 border border-slate-200 bg-slate-50 rounded-xl text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-400 appearance-none cursor-pointer">
                            <option value="">Pilih Pelabuhan Asal</option>
                            @foreach($allPorts as $p)
                                <option value="{{ $p['code'] }}" {{ request('origin') == $p['code'] ? 'selected' : '' }}>
                                    {{ $p['name'] }} ({{ $p['code'] }}) - {{ $p['country_name'] }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="fa-solid fa-ship text-slate-400"></i>
                        </div>
                        <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                            <i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="destination" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pelabuhan Tujuan</label>
                    <div class="relative">
                        <select name="destination" id="destination" required class="w-full pl-10 pr-4 py-3 border border-slate-200 bg-slate-50 rounded-xl text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-400 appearance-none cursor-pointer">
                            <option value="">Pilih Pelabuhan Tujuan</option>
                            @foreach($allPorts as $p)
                                <option value="{{ $p['code'] }}" {{ request('destination') == $p['code'] ? 'selected' : '' }}>
                                    {{ $p['name'] }} ({{ $p['code'] }}) - {{ $p['country_name'] }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="fa-solid fa-anchor text-slate-400"></i>
                        </div>
                        <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                            <i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="speed" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kecepatan Kapal Rata-rata</label>
                    <div class="relative flex items-center">
                        <input 
                            type="number" 
                            name="speed" 
                            id="speed" 
                            min="5" 
                            max="40" 
                            step="0.5" 
                            value="{{ $speed }}" 
                            required 
                            class="w-full pl-10 pr-16 py-3 border border-slate-200 bg-slate-50 rounded-xl text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-400"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="fa-solid fa-gauge-high text-slate-400"></i>
                        </div>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <span class="text-xs font-bold text-slate-400">knot</span>
                        </div>
                    </div>
                    <span class="text-[10px] text-slate-400 mt-1.5 block">1 knot ≈ 1.85 km/jam. Rata-rata kapal kargo: 18-24 knot.</span>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3.5 px-6 rounded-xl text-sm font-bold transition-all flex items-center justify-center gap-2 shadow-lg shadow-blue-200">
                    <i class="fa-solid fa-calculator"></i>
                    Kalkulasi Estimasi
                </button>

                @if(request('origin'))
                    <a href="{{ route('shipping.estimator') }}" class="w-full border border-slate-200 text-slate-600 py-3 px-6 rounded-xl text-sm font-bold hover:bg-slate-50 transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-rotate-left"></i>
                        Reset Rute
                    </a>
                @endif
            </form>
        </div>

        {{-- Map & Details Column --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Map Card --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
                <div class="bg-slate-900 border-b border-slate-800 px-5 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-map-location-dot text-blue-400 text-lg"></i>
                        <div>
                            <p class="text-sm font-bold text-slate-200">Visualisasi Rute Pelayaran</p>
                            <p class="text-[10px] text-slate-500 font-medium">Garis lintasan imajiner (orthodromic) antar pelabuhan</p>
                        </div>
                    </div>
                </div>
                <div class="relative h-[320px] w-full" id="map-container">
                    <div id="estimator-map" class="absolute inset-0 h-full w-full bg-slate-100 z-10"></div>
                </div>
            </div>

            {{-- Calculation Results --}}
            @if($result)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Total Duration --}}
                    <div class="bg-gradient-to-br from-slate-900 to-slate-950 p-5 rounded-2xl border border-slate-800 shadow-lg text-white">
                        <span class="text-[10px] font-bold text-blue-400 uppercase tracking-widest block">Total Estimasi Transit</span>
                        <h3 class="text-2xl font-black mt-2 text-white">
                            {{ $result['days_only'] }}<span class="text-sm font-bold text-slate-400"> H </span>{{ $result['hours_only'] }}<span class="text-sm font-bold text-slate-400"> Jam</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-2 font-medium">
                            Total: {{ number_format($result['total_hours'], 1) }} jam perjalanan
                        </p>
                    </div>

                    {{-- Distance --}}
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Jarak Rute Pelayaran</span>
                        <h3 class="text-2xl font-black mt-2 text-slate-800">
                            {{ number_format($result['distance_nm']) }} <span class="text-sm font-bold text-slate-400">NM</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-2 font-medium">
                            Setara {{ number_format($result['distance_km']) }} km
                        </p>
                    </div>

                    {{-- Breakdowns --}}
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm font-semibold">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Transit Base vs Delay</span>
                        <div class="mt-2 text-sm text-slate-800 space-y-1">
                            <p>Base: <span class="font-extrabold text-blue-600">{{ $result['base_days_only'] }}H {{ $result['base_hours_only'] }}J</span></p>
                            <p>Delay: <span class="font-extrabold text-amber-600">+{{ $result['delay_days_only'] }}H {{ $result['delay_hours_only'] }}J</span></p>
                        </div>
                        <div class="w-full bg-slate-100 h-1.5 rounded-full mt-2 overflow-hidden flex">
                            @php
                                $total = $result['total_days'] > 0 ? $result['total_days'] : 1;
                                $basePct = ($result['base_days'] / $total) * 100;
                                $delayPct = ($result['delay_days'] / $total) * 100;
                            @endphp
                            <div class="bg-blue-500 h-full" style="width: {{ $basePct }}%"></div>
                            <div class="bg-amber-500 h-full" style="width: {{ $delayPct }}%"></div>
                        </div>
                    </div>
                </div>

                {{-- Detailed breakdown analysis --}}
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-6">
                    <h4 class="text-lg font-bold text-slate-800 flex items-center gap-2 border-b border-slate-100 pb-4">
                        <i class="fa-solid fa-triangle-exclamation text-amber-500"></i>
                        Rincian Faktor Risiko & Keterlambatan (Delay)
                    </h4>

                    <div class="grid grid-cols-1 gap-6">
                        {{-- 1. Weather Category Card --}}
                        <div class="border border-slate-100 rounded-2xl p-5 bg-gradient-to-r from-slate-50 to-white shadow-sm flex flex-col md:flex-row justify-between gap-4">
                            <div class="space-y-3 flex-1">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                                        <i class="fa-solid fa-cloud-sun-rain text-base"></i>
                                    </div>
                                    <div>
                                        <h5 class="text-sm font-bold text-slate-800">Faktor Cuaca & Kelautan</h5>
                                        <p class="text-[11px] text-slate-400 font-medium">Berdasarkan kondisi iklim/cuaca terkini di area pelabuhan asal & tujuan</p>
                                    </div>
                                </div>
                                <div class="pl-12 space-y-1.5">
                                    @foreach($result['weather_reasons'] as $reason)
                                        <p class="text-xs text-slate-600 leading-relaxed flex items-start gap-1.5 font-medium">
                                            <span class="text-sky-400 mt-1">&#8226;</span>
                                            {{ $reason }}
                                        </p>
                                    @endforeach
                                </div>
                            </div>
                            <div class="shrink-0 flex flex-col items-start md:items-end justify-center">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Delay Cuaca</span>
                                <p class="text-2xl font-black mt-1 @if($result['weather_delay_days'] > 0) text-sky-600 @else text-slate-400 @endif">
                                    +{{ $result['weather_delay_days'] }} <span class="text-xs font-bold">Hari</span>
                                </p>
                            </div>
                        </div>

                        {{-- 2. Geopolitical Category Card --}}
                        <div class="border border-slate-100 rounded-2xl p-5 bg-gradient-to-r from-slate-50 to-white shadow-sm flex flex-col md:flex-row justify-between gap-4">
                            <div class="space-y-3 flex-1">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                                        <i class="fa-solid fa-shield-halved text-base"></i>
                                    </div>
                                    <div>
                                        <h5 class="text-sm font-bold text-slate-800">Risiko Negara & Geopolitik</h5>
                                        <p class="text-[11px] text-slate-400 font-medium">Pengaruh stabilitas ekonomi, kepabeanan, dan keamanan wilayah</p>
                                    </div>
                                </div>
                                <div class="pl-12 space-y-1.5">
                                    @foreach($result['risk_reasons'] as $reason)
                                        <p class="text-xs text-slate-600 leading-relaxed flex items-start gap-1.5 font-medium">
                                            <span class="text-rose-400 mt-1">&#8226;</span>
                                            {{ $reason }}
                                        </p>
                                    @endforeach
                                </div>
                            </div>
                            <div class="shrink-0 flex flex-col items-start md:items-end justify-center">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Delay Risiko</span>
                                <p class="text-2xl font-black mt-1 @if($result['risk_delay_days'] > 0) text-rose-600 @else text-slate-400 @endif">
                                    +{{ $result['risk_delay_days'] }} <span class="text-xs font-bold">Hari</span>
                                </p>
                            </div>
                        </div>

                        {{-- 3. Port Congestion Category Card --}}
                        <div class="border border-slate-100 rounded-2xl p-5 bg-gradient-to-r from-slate-50 to-white shadow-sm flex flex-col md:flex-row justify-between gap-4">
                            <div class="space-y-3 flex-1">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                                        <i class="fa-solid fa-anchor-circle-exclamation text-base"></i>
                                    </div>
                                    <div>
                                        <h5 class="text-sm font-bold text-slate-800">Kepadatan Pelabuhan & Bongkar Muat</h5>
                                        <p class="text-[11px] text-slate-400 font-medium">Estimasi antrean sandar kapal kontainer di pelabuhan tujuan</p>
                                    </div>
                                </div>
                                <div class="pl-12 space-y-1.5">
                                    @foreach($result['congestion_reasons'] as $reason)
                                        <p class="text-xs text-slate-600 leading-relaxed flex items-start gap-1.5 font-medium">
                                            <span class="text-amber-400 mt-1">&#8226;</span>
                                            {{ $reason }}
                                        </p>
                                    @endforeach
                                </div>
                            </div>
                            <div class="shrink-0 flex flex-col items-start md:items-end justify-center">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Delay Antrean</span>
                                <p class="text-2xl font-black mt-1 @if($result['congestion_delay_days'] > 0.5) text-amber-600 @else text-slate-400 @endif">
                                    +{{ $result['congestion_delay_days'] }} <span class="text-xs font-bold">Hari</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Context stats for weather/risk --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-slate-100">
                        {{-- Weather Status --}}
                        <div class="space-y-3">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block">Status Cuaca Terakhir</span>
                            <div class="space-y-2">
                                @foreach(['Asal' => $originPort, 'Tujuan' => $destPort] as $label => $port)
                                    @php
                                        $w = $result['weather_info'][$label] ?? null;
                                    @endphp
                                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                                        <div class="flex items-center gap-2.5">
                                            <span class="text-xs font-bold text-slate-500 bg-slate-200 px-2 py-0.5 rounded">{{ $label }}</span>
                                            <p class="text-xs font-bold text-slate-700 truncate max-w-[120px]">{{ $port['name'] }}</p>
                                        </div>
                                        @if($w)
                                            <span class="text-xs font-bold text-slate-600">
                                                {{ $w->weather_condition }} ({{ $w->temperature }}°C, Wind: {{ $w->wind_speed }}km/h)
                                            </span>
                                        @else
                                            <span class="text-xs font-bold text-slate-400">Tidak ada data cuaca</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Risk Status --}}
                        <div class="space-y-3">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block">Status Risiko Geopolitik</span>
                            <div class="space-y-2">
                                @foreach(['Asal' => $originPort, 'Tujuan' => $destPort] as $label => $port)
                                    @php
                                        $r = $result['risk_info'][$label] ?? null;
                                    @endphp
                                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                                        <div class="flex items-center gap-2.5">
                                            <span class="text-xs font-bold text-slate-500 bg-slate-200 px-2 py-0.5 rounded">{{ $label }}</span>
                                            <p class="text-xs font-bold text-slate-700 truncate max-w-[120px]">{{ $port['name'] }}</p>
                                        </div>
                                        @if($r)
                                            <span class="px-2 py-1 rounded-lg text-[10px] font-extrabold uppercase
                                                @if($r->risk_level === 'Tinggi') bg-rose-100 text-rose-700
                                                @elseif($r->risk_level === 'Sedang') bg-amber-100 text-amber-700
                                                @else bg-emerald-100 text-emerald-700 @endif">
                                                Skor: {{ $r->total_score }} ({{ $r->risk_level }})
                                            </span>
                                        @else
                                            <span class="text-xs font-bold text-slate-400">Tidak ada data risiko</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white p-12 rounded-2xl border border-slate-200 shadow-sm text-center flex flex-col items-center justify-center gap-3">
                    <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-2xl">
                        <i class="fa-solid fa-circle-question"></i>
                    </div>
                    <h5 class="text-base font-bold text-slate-700 mt-2">Kalkulator Rute Siap Digunakan</h5>
                    <p class="text-xs text-slate-400 max-w-sm mx-auto">Silakan pilih pelabuhan asal, tujuan, dan input estimasi kecepatan kapal untuk melihat detail estimasi waktu transit dan peta rute pelayaran.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize Leaflet Map
        const map = L.map('estimator-map', {
            zoomControl: true,
            scrollWheelZoom: true
        }).setView([15, 100], 2);

        // Dark/Atlas theme tile layer matching premium vibes
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);

        // Marker Icons
        const originIcon = L.divIcon({
            className: 'custom-div-icon',
            html: `<div style="background-color: #3b82f6; width: 14px; height: 14px; border: 3px solid #ffffff; border-radius: 50%; box-shadow: 0 0 10px rgba(59,130,246,0.8);"></div>`,
            iconSize: [14, 14],
            iconAnchor: [7, 7]
        });

        const destIcon = L.divIcon({
            className: 'custom-div-icon',
            html: `<div style="background-color: #ef4444; width: 14px; height: 14px; border: 3px solid #ffffff; border-radius: 50%; box-shadow: 0 0 10px rgba(239,68,68,0.8);"></div>`,
            iconSize: [14, 14],
            iconAnchor: [7, 7]
        });

        const intermediateIcon = L.divIcon({
            className: 'custom-div-icon',
            html: `<div style="background-color: #94a3b8; width: 8px; height: 8px; border: 1.5px solid #ffffff; border-radius: 50%;"></div>`,
            iconSize: [8, 8],
            iconAnchor: [4, 4]
        });

        // Add markers for all ports in background (low opacity) to make map interactive
        const allPorts = @json($allPorts);
        const originCode = "{{ request('origin') }}";
        const destCode = "{{ request('destination') }}";

        let originLatLng = null;
        let destLatLng = null;

        allPorts.forEach(port => {
            const isOrigin = port.code === originCode;
            const isDest = port.code === destCode;
            
            if (isOrigin) {
                originLatLng = [port.latitude, port.longitude];
                L.marker(originLatLng, { icon: originIcon })
                 .bindPopup(`<b>Asal: ${port.name} (${port.code})</b><br>${port.country_name}`)
                 .addTo(map)
                 .openPopup();
            } else if (isDest) {
                destLatLng = [port.latitude, port.longitude];
                L.marker(destLatLng, { icon: destIcon })
                 .bindPopup(`<b>Tujuan: ${port.name} (${port.code})</b><br>${port.country_name}`)
                 .addTo(map);
            } else {
                // Show other ports as small dots
                L.marker([port.latitude, port.longitude], { icon: intermediateIcon })
                 .bindPopup(`<b>${port.name} (${port.code})</b><br>${port.country_name}`)
                 .addTo(map);
            }
        });

        // Draw Polyline Route if both selected
        if (originLatLng && destLatLng) {
            // Draw simple geodesic/straight line path
            const pathPoints = [originLatLng, destLatLng];
            const polyline = L.polyline(pathPoints, {
                color: '#6366f1',
                weight: 4,
                dashArray: '5, 8',
                opacity: 0.8
            }).addTo(map);

            // Zoom map to fit line
            map.fitBounds(polyline.getBounds(), { padding: [50, 50] });
        }
    });
</script>
@endsection
