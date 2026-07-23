@extends('layouts.app')

@section('title', 'Kelola Dataset Pelabuhan')

@section('content')
<style>
    /* Styling for premium Leaflet integration */
    .admin-port-tooltip.leaflet-tooltip {
        background: #1e293b !important;
        border: none !important;
        border-radius: 8px !important;
        color: #ffffff !important;
        padding: 5px 10px !important;
        font-family: 'Outfit', sans-serif !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
    }
</style>

<div class="space-y-6 max-w-[1400px] mx-auto">
    
    <!-- Peta Interaktif Pelabuhan (Full-Width) -->
    <div class="bg-white rounded-3xl border border-slate-200/60 p-6 shadow-[0_8px_30px_rgb(0,0,0,0.02)] flex flex-col gap-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h3 class="text-base font-extrabold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-map-location-dot text-indigo-600"></i>
                    Peta Interaktif Pelabuhan Dunia
                </h3>
                <p class="text-xs text-slate-400 font-semibold mt-0.5">
                    Klik di mana saja pada peta untuk mendapatkan titik koordinat (Latitude & Longitude) secara otomatis untuk form di bawah.
                </p>
            </div>
            <span class="bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider">
                Mode Penjajakan Koordinat
            </span>
        </div>
        
        <!-- Map Canvas -->
        <div class="relative overflow-hidden rounded-2xl border border-slate-200">
            <div id="admin-port-map" class="h-[360px] w-full bg-slate-100 z-10"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left: Add Port Form -->
        <div class="bg-white border border-slate-200/60 p-6 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.02)]">
            <h4 class="font-extrabold text-slate-800 text-sm mb-4 flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="fa-solid fa-plus text-blue-600"></i>
                Tambah Pelabuhan Baru
            </h4>
            <form action="{{ route('admin.ports.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-bold text-slate-500 block mb-1.5 uppercase tracking-wider">Nama Pelabuhan *</label>
                    <input type="text" name="name" required placeholder="Contoh: Port of Rotterdam" class="w-full border border-slate-200 bg-slate-50/50 rounded-xl text-sm p-3 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-bold text-slate-700">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 block mb-1.5 uppercase tracking-wider">Kode Pelabuhan *</label>
                    <input type="text" name="code" required placeholder="Contoh: NLRTM" class="w-full border border-slate-200 bg-slate-50/50 rounded-xl text-sm p-3 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-mono font-bold text-slate-700">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 block mb-1.5 uppercase tracking-wider">Negara *</label>
                    <select name="country_id" required class="w-full border border-slate-200 bg-slate-50/50 rounded-xl text-sm p-3 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer font-bold text-slate-700">
                        <option value="">-- Pilih Negara --</option>
                        @foreach($countries as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-bold text-slate-500 block mb-1.5 uppercase tracking-wider">Latitude *</label>
                        <input type="number" name="latitude" id="input-lat" step="0.000001" required placeholder="51.9244" class="w-full border border-slate-200 bg-slate-50/50 rounded-xl text-sm p-3 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-mono font-bold text-slate-700">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 block mb-1.5 uppercase tracking-wider">Longitude *</label>
                        <input type="number" name="longitude" id="input-lng" step="0.000001" required placeholder="4.4777" class="w-full border border-slate-200 bg-slate-50/50 rounded-xl text-sm p-3 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-mono font-bold text-slate-700">
                    </div>
                </div>
                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-black py-3.5 rounded-xl text-sm transition-all cursor-pointer shadow-md shadow-indigo-500/20 transform hover:-translate-y-0.5">
                    <i class="fa-solid fa-floppy-disk mr-1"></i>
                    Simpan Pelabuhan
                </button>
            </form>
        </div>

        <!-- Right: Ports List -->
        <div class="lg:col-span-2 bg-white border border-slate-200/60 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.02)] overflow-hidden flex flex-col h-[520px]">
            <div class="p-6 border-b border-slate-100">
                <h4 class="font-extrabold text-slate-800 text-sm">Daftar Geodataset Pelabuhan</h4>
                <p class="text-xs text-slate-400 font-semibold mt-1">Total terdaftar: <span class="font-bold text-indigo-600">{{ $ports->count() }}</span> pelabuhan.</p>
            </div>
            
            <div class="overflow-y-auto flex-1 custom-scrollbar">
                <table class="table-auto w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-400 font-bold text-xs uppercase border-b border-slate-200 sticky top-0 z-20">
                            <th class="p-4 pl-6">Nama Pelabuhan</th>
                            <th class="p-4">Kode</th>
                            <th class="p-4">Negara</th>
                            <th class="p-4 pr-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                        @foreach($ports as $port)
                        <tr class="hover:bg-slate-50 transition-colors duration-150">
                            <td class="p-4 pl-6 font-bold text-slate-800">{{ $port->name }}</td>
                            <td class="p-4 font-mono font-bold text-indigo-600">{{ $port->code }}</td>
                            <td class="p-4 font-semibold">{{ $port->country->name }}</td>
                            <td class="p-4 pr-6 text-center">
                                <form action="{{ route('admin.ports.destroy', $port->id) }}" method="POST" onsubmit="return confirm('Hapus pelabuhan {{ $port->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-rose-500 hover:text-rose-800 p-2 rounded-xl hover:bg-rose-50 transition-all cursor-pointer">
                                        <i class="fa-solid fa-trash text-sm"></i>
                                    </button>
                                </form>
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
        // Inputs
        const latInput = document.getElementById('input-lat');
        const lngInput = document.getElementById('input-lng');

        // Initialize Map centered globally
        const map = L.map('admin-port-map').setView([20, 10], 2);

        // OpenStreetMap Premium Grey Theme
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors &copy; <a href=\"https://carto.com/attributions\">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);

        // Define highly stylized custom DivIcons
        const portIcon = L.divIcon({
            className: '',
            html: '<div style="width: 26px; height: 26px;" class="bg-indigo-600 border border-white flex items-center justify-center rounded-full text-white shadow-md shadow-indigo-500/30 hover:scale-110 hover:bg-indigo-700 transition-all duration-150 cursor-pointer"><i class="fa-solid fa-anchor text-[9px]"></i></div>',
            iconSize: [26, 26],
            iconAnchor: [13, 13]
        });

        // Loop and add markers for all existing ports
        @foreach($ports as $port)
            L.marker([{{ $port->latitude }}, {{ $port->longitude }}], { icon: portIcon })
                .addTo(map)
                .bindTooltip("{{ addslashes($port->name) }} ({{ $port->code }})", {
                    permanent: false,
                    direction: 'top',
                    className: 'admin-port-tooltip'
                });
        @endforeach

        // Temporary Marker to show clicked point
        let tempMarker = null;

        // Click handler to get coordinates
        map.on('click', function(e) {
            const lat = e.latlng.lat.toFixed(6);
            const lng = e.latlng.lng.toFixed(6);

            // Populate Form inputs
            latInput.value = lat;
            lngInput.value = lng;

            // Place or move red pulsating target marker
            const redIcon = L.divIcon({
                className: '',
                html: '<div class="relative flex items-center justify-center">' +
                      '<span class="animate-ping absolute inline-flex h-7 w-7 rounded-full bg-rose-400 opacity-75"></span>' +
                      '<div style="width: 26px; height: 26px;" class="relative bg-rose-600 border border-white flex items-center justify-center rounded-full text-white shadow-lg cursor-grab active:cursor-grabbing"><i class="fa-solid fa-location-crosshairs text-[10px]"></i></div>' +
                      '</div>',
                iconSize: [26, 26],
                iconAnchor: [13, 13]
            });

            if (tempMarker) {
                tempMarker.setLatLng(e.latlng);
            } else {
                tempMarker = L.marker(e.latlng, { icon: redIcon, draggable: true })
                    .addTo(map)
                    .bindPopup("<div class='font-bold text-xs p-1'>Titik Baru Pelabuhan<br><span class='font-mono font-normal text-[10px] text-slate-500'>" + lat + ", " + lng + "</span></div>")
                    .openPopup();

                // Track drag events to update inputs
                tempMarker.on('dragend', function(event) {
                    const marker = event.target;
                    const position = marker.getLatLng();
                    latInput.value = position.lat.toFixed(6);
                    lngInput.value = position.lng.toFixed(6);
                });
            }
        });
    });
</script>
@endsection
