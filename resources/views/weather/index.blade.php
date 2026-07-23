@extends('layouts.app')

@section('title', 'Pemantauan Cuaca Global')

@section('content')

@php
    $validWeathers = collect($allCountriesWeather)->filter(fn($w) => !is_null($w['temperature']));
    $latestWeather = $weatherHistory->first();

    // Mock risk score for UI
    $riskScore = 20;
    $riskStatus = 'Tingkat Sedang';
@endphp

<style>
    /* Add custom animations and styles for Leaflet */
    @keyframes weatherPulse {
        0%   { transform: scale(0.7); opacity: 1; }
        70%  { transform: scale(2.6); opacity: 0; }
        100% { transform: scale(0.7); opacity: 0; }
    }
    .leaflet-popup-content-wrapper {
        border-radius: 18px !important;
        box-shadow: 0 12px 40px rgba(0,0,0,0.13) !important;
        border: 1px solid rgba(226,232,240,0.9) !important;
        padding: 0 !important;
        overflow: hidden;
    }
    .leaflet-popup-content { margin: 14px 16px !important; }
    .leaflet-popup-tip-container { margin-top: -1px; }
    .leaflet-popup-close-button { display: none; }
    
    .gold-accent {
        position: relative;
        background: #ffffff;
        border-radius: 1.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        border: 1px solid #f8fafc;
    }
    .gold-accent::before {
        content: '';
        position: absolute;
        left: 0;
        top: 15%;
        bottom: 15%;
        width: 5px;
        background: linear-gradient(to bottom, #d4af37, #f3e5ab);
        border-radius: 0 4px 4px 0;
        z-index: 10;
    }

    .weather-card {
        padding: 1.5rem 1rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        height: 260px;
        overflow: hidden;
    }

    /* Custom scrollbar for forecast */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9; 
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1; 
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8; 
    }
</style>

<div class="space-y-6 max-w-[1400px] mx-auto">

    {{-- HEADER SECTION --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
        <div class="flex items-center gap-4">
            <i class="fa-solid fa-cloud-sun text-blue-600 text-4xl mt-1"></i>
            <div>
                <h1 class="text-[28px] font-black text-slate-800 tracking-tight">Pemantauan Cuaca Global</h1>
                <p class="text-sm text-slate-500 font-medium">Data cuaca real-time dan pelacakan kondisi lingkungan global di seluruh dunia.</p>
            </div>
        </div>

        @if($selectedCountry)
        <div class="flex items-center gap-4 bg-white border border-slate-100 rounded-[2rem] p-2 pr-6 shadow-[0_4px_12px_rgba(0,0,0,0.02)]">
            <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-slate-700 bg-slate-50 border border-slate-100">
                {{ $selectedCountry->code }}
            </div>
            <div>
                <h3 class="font-extrabold text-slate-800 leading-none text-sm">{{ $selectedCountry->name }}</h3>
                <p class="text-xs text-slate-500 mt-1 font-medium">Ibu Kota: {{ $selectedCountry->capital ?? 'N/A' }}</p>
            </div>
        </div>
        @endif
    </div>

    {{-- SEARCH BAR SECTION --}}
    <div class="gold-accent p-4 relative" id="search-container">
        <form action="{{ route('weather.index') }}" method="GET" id="weather-search-form" class="w-full">
            <div class="relative w-full">
                <i class="fa-solid fa-earth-asia absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 z-20"></i>
                
                {{-- Search Input --}}
                <input type="text" 
                       id="weather-search-input"
                       placeholder="Ketik nama negara untuk mencari..."
                       value="{{ $selectedCountry ? $selectedCountry->name : '' }}"
                       class="w-full bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-2xl py-3.5 pl-11 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all cursor-text placeholder-slate-400 z-10"
                       autocomplete="off">
                
                {{-- Hidden input with ID --}}
                <input type="hidden" name="country" id="hidden-country-code" value="{{ $selectedCountry ? $selectedCountry->code : '' }}">
                
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 z-20">
                    <i class="fa-solid fa-chevron-down text-sm"></i>
                </div>

                {{-- Custom Dropdown List --}}
                <div id="weather-dropdown-list" 
                     class="absolute left-0 right-0 mt-2 max-h-60 overflow-y-auto bg-white border border-slate-200 rounded-2xl shadow-xl z-50 py-2 custom-scrollbar hidden">
                    @foreach($countries as $c)
                        <div class="dropdown-item px-4 py-2.5 hover:bg-slate-50 text-slate-700 font-bold text-sm cursor-pointer transition-colors flex items-center justify-between"
                             data-name="{{ strtolower($c->name) }}"
                             data-code="{{ $c->code }}"
                             data-fullname="{{ $c->name }}">
                            <span>{{ $c->name }}</span>
                            <span class="text-xs text-slate-400 font-normal">{{ $c->code }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const searchInput = document.getElementById('weather-search-input');
            const dropdownList = document.getElementById('weather-dropdown-list');
            const hiddenCode = document.getElementById('hidden-country-code');
            const searchForm = document.getElementById('weather-search-form');
            const items = dropdownList.querySelectorAll('.dropdown-item');

            if (!searchInput || !dropdownList) return;

            // Show dropdown on focus
            searchInput.addEventListener('focus', function() {
                dropdownList.classList.remove('hidden');
            });

            // Filter items on input
            searchInput.addEventListener('input', function() {
                const term = searchInput.value.toLowerCase();
                items.forEach(item => {
                    const name = item.getAttribute('data-name');
                    if (name.includes(term)) {
                        item.classList.remove('hidden');
                        item.style.display = 'flex';
                    } else {
                        item.classList.add('hidden');
                        item.style.display = 'none';
                    }
                });
            });

            // Handle selection
            items.forEach(item => {
                item.addEventListener('mousedown', function(e) {
                    const fullname = item.getAttribute('data-fullname');
                    const code = item.getAttribute('data-code');
                    
                    searchInput.value = fullname;
                    hiddenCode.value = code;
                    
                    dropdownList.classList.add('hidden');
                    searchForm.submit();
                });
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                const container = document.getElementById('search-container');
                if (container && !container.contains(e.target)) {
                    dropdownList.classList.add('hidden');
                }
            });

            // Handle enter key
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const term = searchInput.value.toLowerCase();
                    // Find first visible item
                    let matchedItem = null;
                    for (let i = 0; i < items.length; i++) {
                        if (!items[i].classList.contains('hidden')) {
                            matchedItem = items[i];
                            break;
                        }
                    }
                    if (matchedItem) {
                        searchInput.value = matchedItem.getAttribute('data-fullname');
                        hiddenCode.value = matchedItem.getAttribute('data-code');
                        searchForm.submit();
                    }
                }
            });
        });
    </script>

    {{-- 6 VERTICAL CARDS --}}
    @php
        $temp = $latestWeather ? $latestWeather->temperature : '29.5';
        $humidity = $latestWeather ? $latestWeather->humidity : '68';
        $wind = $latestWeather ? $latestWeather->wind_speed : '34.4';
        $rain = $latestWeather ? $latestWeather->rainfall : '0.1';
        
        $isStorm = $latestWeather && ($latestWeather->weather_condition === 'Thunderstorm' || $wind > 30 || $rain > 15);
        $stormStatus = $isStorm ? 'BAHAYA' : 'AMAN';
        $stormDesc = $isStorm ? 'Ada Badai' : 'Badai Nihil';
    @endphp
    
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
        <!-- Card 1 -->
        <div class="gold-accent weather-card">
            <span class="text-xs font-black tracking-widest text-slate-600 mb-6 uppercase">Suhu Udara</span>
            <div class="text-[40px] font-black text-rose-500 mb-1 leading-none flex items-start">
                {{ $temp }}<span class="text-2xl mt-1">°C</span>
            </div>
            <span class="text-sm text-slate-500 font-medium mt-4">Aktif</span>
        </div>
        <!-- Card 2 -->
        <div class="gold-accent weather-card relative">
            <span class="text-xs font-black tracking-widest text-slate-600 mb-6 uppercase">Kelembapan</span>
            <div class="text-[40px] font-black text-blue-600 mb-1 leading-none">{{ $humidity }}<span class="text-3xl">%</span></div>
            <span class="text-sm text-slate-500 font-medium mt-4">Relatif</span>
            <div class="absolute right-3 bottom-1/3 opacity-40">
                <i class="fa-solid fa-droplet text-blue-400 text-3xl"></i>
            </div>
        </div>
        <!-- Card 3 -->
        <div class="gold-accent weather-card relative">
            <span class="text-xs font-black tracking-widest text-slate-600 mb-6 uppercase">Kec. Angin</span>
            <div class="text-[40px] font-black text-cyan-500 mb-1 leading-none">{{ $wind }}</div>
            <span class="text-lg font-bold text-slate-400 mb-4">km/h</span>
            <span class="text-sm text-rose-500 font-bold">Kencang</span>
            <div class="absolute right-3 bottom-1/3 opacity-40">
                <i class="fa-solid fa-wind text-cyan-400 text-3xl"></i>
            </div>
        </div>
        <!-- Card 4 -->
        <div class="gold-accent weather-card relative">
            <span class="text-xs font-black tracking-widest text-slate-600 mb-6 uppercase">Hujan</span>
            <div class="text-[40px] font-black text-blue-600 mb-1 leading-none">{{ $rain }}</div>
            <span class="text-lg font-bold text-slate-400 mb-4">mm</span>
            <span class="text-sm text-blue-600 font-bold flex items-center gap-1">
                Hujan Aktif
            </span>
            <div class="absolute right-3 bottom-1/3 opacity-40">
                <i class="fa-solid fa-cloud-showers-water text-blue-400 text-3xl"></i>
            </div>
        </div>
        <!-- Card 5 -->
        <div class="gold-accent weather-card relative">
            <span class="text-xs font-black tracking-widest text-slate-600 mb-6 uppercase">Status Badai</span>
            <div class="text-2xl font-black text-emerald-600 mb-6 leading-none mt-2">{{ $stormStatus }}</div>
            <span class="text-sm text-emerald-600 font-bold flex items-center gap-1">
                {{ $stormDesc }}
            </span>
            <div class="absolute right-3 bottom-1/3 opacity-40">
                <i class="fa-solid fa-cloud-bolt text-indigo-400 text-3xl"></i>
            </div>
            <div class="absolute left-4 bottom-1/3 opacity-40">
                <i class="fa-solid fa-shield-halved text-emerald-400 text-xl"></i>
            </div>
        </div>
        <!-- Card 6 -->
        <div class="gold-accent weather-card relative">
            <span class="text-xs font-black tracking-widest text-slate-600 mb-4 uppercase">Risiko Cuaca</span>
            <div class="flex items-baseline justify-center gap-1 mb-6">
                <span class="text-[40px] font-black text-[#8b5a2b] leading-none">20</span>
                <span class="text-3xl font-black text-slate-300">/</span>
                <span class="text-3xl font-black text-[#8b5a2b]">30</span>
            </div>
            <span class="text-sm text-[#8b5a2b] font-bold">Tingkat Sedang</span>
            <div class="absolute right-3 bottom-1/3 opacity-40">
                <i class="fa-solid fa-shield-exclamation text-[#d4af37] text-3xl"></i>
            </div>
        </div>
    </div>

    {{-- MAP AND FORECAST SECTION --}}
    <div class="grid grid-cols-1 lg:grid-cols-[1.5fr_1fr] gap-6">
        
        {{-- Map --}}
        <div class="gold-accent overflow-hidden flex flex-col relative h-[500px]">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between z-10 bg-white absolute top-0 left-0 right-0">
                <h3 class="text-[15px] font-extrabold text-slate-800 flex items-center gap-2 pl-4">
                    <i class="fa-regular fa-map text-slate-400"></i> Peta Geospasial Wilayah & Cuaca
                </h3>
                <span class="text-xs bg-slate-50 text-slate-500 font-bold px-4 py-1.5 rounded-full border border-slate-200 tracking-wide">Data Real-Time</span>
            </div>
            
            {{-- Checkboxes overlay on Map (Top Right) --}}
            <div class="absolute top-[80px] right-4 z-[400] bg-white p-3 rounded-xl border border-slate-200 shadow-lg">
                <label class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2 cursor-pointer">
                    <input type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-4 h-4">
                    Temperatur Global
                </label>
                <label class="flex items-center gap-2 text-xs font-semibold text-slate-600 cursor-pointer">
                    <input type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-4 h-4">
                    Curah Hujan Global
                </label>
            </div>

            <div id="weather-map" class="w-full h-full bg-[#a3c9e2] pt-[65px]"></div>
        </div>

        {{-- Forecast --}}
        <div class="gold-accent overflow-hidden flex flex-col h-[500px]">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-[15px] font-extrabold text-slate-800 flex items-center gap-2 pl-4">
                    <i class="fa-regular fa-calendar text-slate-400"></i> Prakiraan Cuaca 7 Hari
                </h3>
                <span class="text-xs bg-blue-50 text-blue-500 font-bold px-4 py-1.5 rounded-full border border-blue-100 tracking-wide">7 Hari Depan</span>
            </div>
            <div class="p-4 flex-1 overflow-y-auto custom-scrollbar">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b-2 border-slate-50">
                            <th class="text-left py-4 px-2 font-black tracking-widest text-slate-800 text-xs">TANGGAL</th>
                            <th class="text-center py-4 px-2 font-black tracking-widest text-slate-800 text-xs">SUHU<br>MAKS</th>
                            <th class="text-center py-4 px-2 font-black tracking-widest text-slate-800 text-xs">SUHU<br>MIN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $today = \Carbon\Carbon::now();
                            $days = [];
                            
                            // Let's create some dummy data that looks exactly like the image if we can,
                            // or just semi-random realistic data
                            for ($i = 0; $i < 7; $i++) {
                                $d = $today->copy()->addDays($i);
                                
                                // Localize date to Indonesian format if possible, otherwise hardcoded mock
                                $dayName = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][$d->dayOfWeek];
                                $monthName = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'][$d->month - 1];
                                
                                $dateStr = $dayName . ', ' . $d->format('d') . ' ' . $monthName . ' ' . $d->format('Y');

                                $days[] = [
                                    'date' => $dateStr,
                                    'max' => number_format(rand(290, 310) / 10, 1),
                                    'min' => number_format(rand(260, 275) / 10, 1),
                                ];
                            }
                        @endphp
                        @foreach($days as $day)
                        <tr class="border-b border-slate-50 last:border-0 hover:bg-slate-50 transition-colors">
                            <td class="py-5 px-2 font-bold text-slate-700 font-mono text-[13px]">{{ $day['date'] }}</td>
                            <td class="py-5 px-2 text-center">
                                <span class="text-red-500 bg-red-50 font-bold px-3 py-1.5 rounded-full text-xs border border-red-100/50">{{ $day['max'] }}°C</span>
                            </td>
                            <td class="py-5 px-2 text-center">
                                <span class="text-blue-500 bg-blue-50 font-bold px-3 py-1.5 rounded-full text-xs border border-blue-100/50">{{ $day['min'] }}°C</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize Leaflet Map
        const map = L.map('weather-map', {
            zoomControl: false,
            attributionControl: false,
            scrollWheelZoom: true,
            dragging: true,
            tap: true,
            touchZoom: true,
            minZoom: 2,
        }).setView([12.5211, -69.9683], 5); // Focused near Aruba initially

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            maxZoom: 18,
        }).addTo(map);

        L.control.zoom({ position: 'bottomright' }).addTo(map);

        // Custom HTML divIcon factory for Weather markers
        const createWeatherMarker = () => {
            return L.divIcon({
                html: `<div class="text-blue-500 drop-shadow-md" style="font-size:32px;"><i class="fa-solid fa-location-dot"></i></div>`,
                className: '',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });
        };

        const countriesWeather = @json($allCountriesWeather);

        countriesWeather.forEach(c => {
            if (c.latitude && c.longitude) {
                const marker = L.marker([c.latitude, c.longitude], {
                    icon: createWeatherMarker()
                }).addTo(map);

                // Add Popup with specific style matching image
                const temp = c.temperature !== null ? c.temperature + ' °C' : 'N/A';
                const rain = c.rainfall !== null ? c.rainfall + ' mm' : 'N/A';
                const wind = c.wind_speed !== null ? c.wind_speed + ' km/h' : 'N/A';
                const condition = c.condition !== null ? c.condition : 'N/A';

                const popupContent = `
                    <div style="font-family:'Outfit',sans-serif; min-width: 220px; padding: 4px;">
                        <div style="font-weight:900; color:#475569; font-size:15px; margin-bottom:2px;">${c.code}</div>
                        <h4 style="font-weight:900; font-size:18px; color:#1e293b; margin:0 0 14px;">
                            ${c.name}
                        </h4>
                        <div style="font-size:13px; color:#475569; display:flex; flex-direction:column; gap:8px; font-weight:600;">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <span style="width:12px; height:12px; background-color:#ef4444; border-radius:50%; display:inline-block;"></span>
                                <b>Suhu:</b> ${temp}
                            </div>
                            <div style="display:flex; align-items:center; gap:8px;">
                                <span style="width:12px; height:12px; background-color:#3b82f6; border-radius:50%; display:inline-block;"></span>
                                <b>Kelembapan:</b> 68 %
                            </div>
                            <div style="display:flex; align-items:center; gap:8px;">
                                <span style="width:12px; height:12px; background-color:#10b981; border-radius:50%; display:inline-block;"></span>
                                <b>Kec. Angin:</b> ${wind} (Kencang)
                            </div>
                            <div style="display:flex; align-items:center; gap:8px;">
                                <i class="fa-solid fa-cloud-rain" style="color:#a855f7; width:12px; text-align:center;"></i>
                                <b>Hujan:</b> ${rain} (Ya)
                            </div>
                            <div style="display:flex; align-items:center; gap:8px;">
                                <i class="fa-solid fa-cloud-bolt" style="color:#312e81; width:12px; text-align:center;"></i>
                                <b>Badai:</b> Tidak
                            </div>
                        </div>
                    </div>
                `;
                marker.bindPopup(popupContent, { maxWidth: 280 });
                
                // Open popup if it's the selected country
                if(c.code === '{{ $selectedCountry ? $selectedCountry->code : "" }}') {
                    map.setView([c.latitude, c.longitude], 6);
                    marker.openPopup();
                }
            }
        });
    });
</script>
@endsection
