@extends('layouts.app')

@section('title', 'Pemantauan Cuaca Global')

@section('content')
<div class="space-y-8">

    <!-- Top Grid: Select Country & Current Weather Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Selector and General Info -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-slate-800 text-base mb-2">Cuaca Tingkat Negara</h3>
                <p class="text-xs text-slate-500 leading-relaxed mb-6">Pilih negara untuk memantau detail historis cuaca. Cuaca ekstrem dapat memicu risiko keterlambatan logistik.</p>
                
                <form action="{{ route('weather.index') }}" method="GET" class="space-y-4">
                    <div>
                        <label for="country-select" class="text-xs font-bold text-slate-500 block mb-2">Pilih Negara:</label>
                        <div class="relative">
                            <select id="country-select" name="country" onchange="this.form.submit()" class="bg-slate-50 border border-slate-300 text-slate-800 text-sm font-semibold rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 pr-8 appearance-none cursor-pointer">
                                @foreach($countries as $c)
                                    <option value="{{ $c->code }}" {{ ($selectedCountry && $selectedCountry->code === $c->code) ? 'selected' : '' }}>
                                        {{ $c->name }} ({{ $c->code }})
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
                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center gap-3">
                    <img src="{{ $selectedCountry->flag }}" class="w-12 h-8 object-cover rounded shadow-sm border border-slate-200" alt="">
                    <div>
                        <h4 class="font-bold text-slate-800 leading-none">{{ $selectedCountry->name }}</h4>
                        <span class="text-[10px] text-slate-400 font-semibold mt-1 block">Ibukota: {{ $selectedCountry->capital }}</span>
                    </div>
                </div>
            @endif
        </div>

        <!-- Weather Stats Cards -->
        <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="border-b border-slate-100 pb-3 mb-4 flex items-center justify-between">
                <h3 class="font-bold text-slate-800 text-sm">Status Cuaca Terakhir</h3>
                <span class="text-xs bg-sky-50 text-sky-700 font-bold px-2.5 py-1 rounded-full">Data Terbaru</span>
            </div>

            @php
                $latestWeather = $weatherHistory->first();
            @endphp

            @if(!$latestWeather)
                <div class="text-center py-12 text-slate-400 flex-1 flex flex-col items-center justify-center">
                    <i class="fa-solid fa-cloud-bolt text-5xl mb-2 text-slate-300"></i>
                    <p class="font-semibold text-slate-600 text-sm">Data Cuaca Kosong</p>
                    <p class="text-xs mt-0.5">Silakan lakukan sinkronisasi detail negara untuk memuat cuaca terbaru.</p>
                </div>
            @else
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 py-4">
                    <!-- Temperature -->
                    <div class="text-center">
                        <span class="text-slate-400 font-bold text-xs uppercase tracking-wider block">Temperatur</span>
                        <span class="text-3xl font-black text-slate-800 mt-2 block">{{ $latestWeather->temperature }}°C</span>
                        <span class="text-[10px] text-slate-400 font-medium block mt-1">Suhu Udara</span>
                    </div>
                    <!-- Rainfall -->
                    <div class="text-center">
                        <span class="text-slate-400 font-bold text-xs uppercase tracking-wider block">Curah Hujan</span>
                        <span class="text-3xl font-black text-slate-800 mt-2 block">{{ $latestWeather->rainfall }} mm</span>
                        <span class="text-[10px] text-slate-400 font-medium block mt-1">Presipitasi</span>
                    </div>
                    <!-- Wind speed -->
                    <div class="text-center">
                        <span class="text-slate-400 font-bold text-xs uppercase tracking-wider block">Kecepatan Angin</span>
                        <span class="text-3xl font-black text-slate-800 mt-2 block">{{ $latestWeather->wind_speed }} km/h</span>
                        <span class="text-[10px] text-slate-400 font-medium block mt-1">Kekuatan Angin</span>
                    </div>
                    <!-- Condition -->
                    <div class="text-center">
                        <span class="text-slate-400 font-bold text-xs uppercase tracking-wider block">Kondisi</span>
                        <span class="text-base font-black text-purple-600 mt-3.5 block truncate">{{ $latestWeather->weather_condition }}</span>
                        <span class="text-[10px] text-slate-400 font-medium block mt-1">Status Langit</span>
                    </div>
                </div>
                <div class="text-[10px] text-slate-400 font-semibold border-t border-slate-100 pt-3">
                    Terakhir Dicatat: {{ \Carbon\Carbon::parse($latestWeather->recorded_at)->format('d M Y, H:i') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Weather Interactive Map Section -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <h3 class="font-bold text-slate-800 text-lg">Peta Pemantauan Cuaca Global</h3>
            <p class="text-xs text-slate-500 mt-0.5">Penanda peta diwarnai berdasarkan status cuaca: <span class="text-emerald-600 font-bold">Hijau (Normal)</span>, <span class="text-amber-500 font-bold font-semibold">Oranye (Hujan/Gerimis)</span>, dan <span class="text-rose-600 font-bold">Merah (Cuaca Ekstrem/Badai/Angin Kencang)</span>.</p>
        </div>
        <div id="weather-map" class="w-full h-[450px]"></div>
    </div>

    <!-- History Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <h3 class="font-bold text-slate-800 text-lg">Log Historis Cuaca</h3>
            <p class="text-xs text-slate-500 mt-0.5">Daftar rekaman cuaca historis untuk negara terpilih.</p>
        </div>
        <div class="overflow-x-auto">
            @if($weatherHistory->isEmpty())
                <div class="text-center py-12 text-slate-400">
                    <p class="font-semibold text-slate-600">Tidak ada log cuaca historis.</p>
                </div>
            @else
                <table class="table-auto w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-400 font-bold text-xs uppercase border-b border-slate-200">
                            <th class="p-4 pl-6">Waktu Pencatatan</th>
                            <th class="p-4 text-center">Temperatur (°C)</th>
                            <th class="p-4 text-center">Curah Hujan (mm)</th>
                            <th class="p-4 text-center">Kecepatan Angin (km/h)</th>
                            <th class="p-4 text-center">Kelembapan (%)</th>
                            <th class="p-4 pr-6 text-center font-bold">Kondisi Cuaca</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-150 text-sm text-slate-700">
                        @foreach($weatherHistory as $history)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="p-4 pl-6 font-semibold">{{ \Carbon\Carbon::parse($history->recorded_at)->format('d M Y, H:i') }}</td>
                                <td class="p-4 text-center font-bold text-slate-800">{{ $history->temperature ?? 'N/A' }}</td>
                                <td class="p-4 text-center font-bold text-slate-600">{{ $history->rainfall ?? 'N/A' }}</td>
                                <td class="p-4 text-center font-bold text-slate-600">{{ $history->wind_speed ?? 'N/A' }}</td>
                                <td class="p-4 text-center font-bold text-slate-500">{{ $history->humidity ?? 'N/A' }}</td>
                                <td class="p-4 pr-6 text-center font-black">
                                    <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold
                                        {{ $history->weather_condition === 'Thunderstorm' || ($history->wind_speed > 30) ? 'bg-rose-100 text-rose-800' : '' }}
                                        {{ $history->weather_condition === 'Rainy' || $history->weather_condition === 'Light Drizzle' ? 'bg-amber-100 text-amber-800' : '' }}
                                        {{ $history->weather_condition === 'Clear Sky' || $history->weather_condition === 'Partly Cloudy' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    ">
                                        {{ $history->weather_condition }}
                                    </span>
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
        // Initialize Leaflet Map
        const map = L.map('weather-map').setView([20, 0], 2);

        // Add OSM tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Marker data
        const countriesWeather = @json($allCountriesWeather);

        countriesWeather.forEach(c => {
            if (c.latitude && c.longitude) {
                // Determine color
                let color = '#10b981'; // Green (Clear)
                
                if (c.condition === 'Thunderstorm' || c.wind_speed > 30 || c.rainfall > 15) {
                    color = '#ef4444'; // Red (Extreme)
                } else if (c.condition === 'Rainy' || c.condition === 'Light Drizzle' || c.rainfall > 0) {
                    color = '#f59e0b'; // Orange/Yellow (Rainy)
                }

                const marker = L.circleMarker([c.latitude, c.longitude], {
                    radius: 9,
                    fillColor: color,
                    color: '#ffffff',
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.8
                }).addTo(map);

                // Add Popup
                const popupContent = `
                    <div class="font-sans" style="min-width: 150px;">
                        <h4 class="font-bold text-slate-800 text-sm mb-1">${c.name} (${c.code})</h4>
                        <p class="text-xs text-slate-600 mb-1"><b>Cuaca:</b> ${c.condition}</p>
                        <p class="text-xs text-slate-600 mb-1"><b>Suhu:</b> ${c.temperature !== null ? c.temperature + '°C' : 'N/A'}</p>
                        <p class="text-xs text-slate-600 mb-1"><b>Hujan:</b> ${c.rainfall !== null ? c.rainfall + ' mm' : 'N/A'}</p>
                        <p class="text-xs text-slate-600 mb-2"><b>Angin:</b> ${c.wind_speed !== null ? c.wind_speed + ' km/h' : 'N/A'}</p>
                        <div class="border-t border-slate-200 pt-1">
                            <a href="/dashboard?country=${c.code}" target="_parent" class="text-purple-600 hover:text-purple-800 text-xs font-bold flex items-center gap-1">
                                <i class="fa-solid fa-square-poll-vertical"></i> Dashboard Risiko
                            </a>
                        </div>
                    </div>
                `;
                marker.bindPopup(popupContent);
            }
        });
    });
</script>
@endsection
