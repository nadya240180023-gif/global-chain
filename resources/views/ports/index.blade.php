@extends('layouts.app')

@section('title', 'Dasbor Pelabuhan')

@section('content')
<div class="space-y-8">

    {{-- ===== HEADER & FILTER ===== --}}
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-5">
            <div>
                <h3 class="text-xl font-extrabold text-slate-800 flex items-center gap-2">
                    <span class="bg-indigo-100 text-indigo-600 w-9 h-9 rounded-xl flex items-center justify-center text-sm shrink-0">
                        <i class="fa-solid fa-anchor"></i>
                    </span>
                    Dasbor Pelabuhan Global
                </h3>
                <p class="text-xs text-slate-500 mt-1.5 ml-11">Peta dan direktori seluruh pelabuhan strategis dalam jaringan rantai pasokan global.</p>
            </div>

            {{-- Stats mini --}}
            <div class="flex items-center gap-4 shrink-0">
                <div class="text-center bg-indigo-50 px-5 py-3 rounded-xl border border-indigo-100">
                    <p class="text-2xl font-black text-indigo-700">{{ $ports->count() }}</p>
                    <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider mt-0.5">Total Pelabuhan</p>
                </div>
                <div class="text-center bg-emerald-50 px-5 py-3 rounded-xl border border-emerald-100">
                    <p class="text-2xl font-black text-emerald-700">{{ $ports->pluck('country_id')->unique()->count() }}</p>
                    <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-wider mt-0.5">Negara Tercakup</p>
                </div>
            </div>
        </div>

        {{-- Filter Bar --}}
        <form action="{{ route('ports.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-sm"></i>
                </div>
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Cari nama atau kode pelabuhan..."
                    class="w-full pl-9 pr-4 py-2.5 border border-slate-300 bg-slate-50 rounded-xl text-sm text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400"
                >
            </div>
            <div class="relative sm:w-56">
                <select name="country_id" onchange="this.form.submit()" class="w-full px-4 py-2.5 border border-slate-300 bg-slate-50 rounded-xl text-sm text-slate-700 font-medium appearance-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="">Semua Negara</option>
                    @foreach($countries as $c)
                        <option value="{{ $c->id }}" {{ $countryId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i>
                </div>
            </div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2 shrink-0 shadow-md shadow-indigo-200">
                <i class="fa-solid fa-filter"></i>
                Filter
            </button>
            @if($search || $countryId)
                <a href="{{ route('ports.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-5 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2 shrink-0">
                    <i class="fa-solid fa-xmark"></i>
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- ===== MAP ===== --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 pt-5 pb-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h4 class="font-bold text-slate-800 text-base">Peta Sebaran Pelabuhan</h4>
                <p class="text-xs text-slate-500 mt-0.5">Klik penanda untuk melihat detail pelabuhan.</p>
            </div>
            <span class="flex items-center gap-1.5 text-[11px] font-bold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-full border border-emerald-100">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse inline-block"></span>
                {{ $ports->count() }} Pelabuhan Ditampilkan
            </span>
        </div>
        <div id="port-map" class="w-full h-[480px]"></div>
    </div>

    {{-- ===== PORT CARDS GRID ===== --}}
    @if($ports->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-16 text-center">
            <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-anchor text-3xl text-slate-400"></i>
            </div>
            <h4 class="font-bold text-slate-700 text-lg">Tidak Ada Pelabuhan Ditemukan</h4>
            <p class="text-sm text-slate-400 mt-1">Coba ubah filter pencarian atau reset untuk melihat semua data.</p>
            <a href="{{ route('ports.index') }}" class="mt-5 inline-flex items-center gap-2 bg-indigo-600 text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-md shadow-indigo-200">
                <i class="fa-solid fa-rotate-left"></i> Tampilkan Semua
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($ports as $port)
                @php
                    $countryColors = [
                        'DE' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'icon_bg' => 'bg-amber-100', 'icon_text' => 'text-amber-600'],
                        'CN' => ['bg' => 'bg-red-50',   'border' => 'border-red-200',   'icon_bg' => 'bg-red-100',   'icon_text' => 'text-red-600'],
                        'ID' => ['bg' => 'bg-rose-50',  'border' => 'border-rose-200',  'icon_bg' => 'bg-rose-100',  'icon_text' => 'text-rose-600'],
                        'AU' => ['bg' => 'bg-sky-50',   'border' => 'border-sky-200',   'icon_bg' => 'bg-sky-100',   'icon_text' => 'text-sky-600'],
                        'US' => ['bg' => 'bg-blue-50',  'border' => 'border-blue-200',  'icon_bg' => 'bg-blue-100',  'icon_text' => 'text-blue-600'],
                        'SG' => ['bg' => 'bg-purple-50','border' => 'border-purple-200','icon_bg' => 'bg-purple-100','icon_text' => 'text-purple-600'],
                        'JP' => ['bg' => 'bg-pink-50',  'border' => 'border-pink-200',  'icon_bg' => 'bg-pink-100',  'icon_text' => 'text-pink-600'],
                        'GB' => ['bg' => 'bg-indigo-50','border' => 'border-indigo-200','icon_bg' => 'bg-indigo-100','icon_text' => 'text-indigo-600'],
                    ];
                    $color = $countryColors[$port->country->code ?? ''] ?? ['bg' => 'bg-slate-50', 'border' => 'border-slate-200', 'icon_bg' => 'bg-slate-100', 'icon_text' => 'text-slate-600'];
                @endphp
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 overflow-hidden flex flex-col">
                    {{-- Card Top Band --}}
                    <div class="h-1.5 w-full {{ str_replace(['bg-', 'text-'], ['bg-', ''], explode(' ', $color['icon_text'])[0]) }} opacity-60"
                         style="background: linear-gradient(90deg, #6366f1, #8b5cf6)"></div>

                    <div class="p-5 flex flex-col flex-1">
                        {{-- Icon + Code --}}
                        <div class="flex items-start justify-between mb-4">
                            <div class="{{ $color['icon_bg'] }} {{ $color['icon_text'] }} w-12 h-12 rounded-xl flex items-center justify-center text-xl shadow-sm">
                                <i class="fa-solid fa-anchor"></i>
                            </div>
                            <span class="text-[10px] font-black tracking-widest text-slate-400 bg-slate-100 px-2.5 py-1 rounded-lg uppercase">
                                {{ $port->code }}
                            </span>
                        </div>

                        {{-- Name & Country --}}
                        <h5 class="font-extrabold text-slate-800 text-sm leading-snug mb-1">{{ $port->name }}</h5>
                        <div class="flex items-center gap-2 mb-4">
                            @if($port->country->flag ?? false)
                                <img src="{{ $port->country->flag }}" class="w-5 h-3.5 object-cover rounded shadow-sm border border-slate-200" alt="">
                            @endif
                            <span class="text-xs text-slate-500 font-semibold">{{ $port->country->name ?? 'N/A' }}</span>
                        </div>

                        {{-- Coordinates --}}
                        <div class="mt-auto pt-3 border-t border-slate-100 grid grid-cols-2 gap-2 text-center">
                            <div>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Lintang</p>
                                <p class="text-xs font-bold text-slate-700 mt-0.5">{{ number_format(floatval($port->latitude), 4) }}°</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Bujur</p>
                                <p class="text-xs font-bold text-slate-700 mt-0.5">{{ number_format(floatval($port->longitude), 4) }}°</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ===== PORTS TABLE ===== --}}
    @if($ports->isNotEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h4 class="font-bold text-slate-800 text-base">Daftar Lengkap Pelabuhan</h4>
            <span class="text-xs text-slate-400 font-semibold">{{ $ports->count() }} data</span>
        </div>
        <div class="overflow-x-auto">
            <table class="table-auto w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 font-bold text-xs uppercase border-b border-slate-200 tracking-wider">
                        <th class="p-4 pl-6">#</th>
                        <th class="p-4">Nama Pelabuhan</th>
                        <th class="p-4">Kode IATA</th>
                        <th class="p-4">Negara</th>
                        <th class="p-4 text-center">Koordinat</th>
                        <th class="p-4 pr-6 text-center">Aksi Peta</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                    @foreach($ports as $i => $port)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-4 pl-6 text-slate-400 font-bold">{{ $i + 1 }}</td>
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="bg-indigo-100 text-indigo-600 w-8 h-8 rounded-lg flex items-center justify-center text-sm shrink-0">
                                        <i class="fa-solid fa-anchor"></i>
                                    </div>
                                    <span class="font-bold text-slate-800">{{ $port->name }}</span>
                                </div>
                            </td>
                            <td class="p-4">
                                <span class="font-black text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg text-xs tracking-wider uppercase">{{ $port->code }}</span>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    @if($port->country->flag ?? false)
                                        <img src="{{ $port->country->flag }}" class="w-7 h-5 object-cover rounded shadow-sm border border-slate-100" alt="">
                                    @endif
                                    <div>
                                        <span class="font-semibold text-slate-700 text-xs">{{ $port->country->name ?? 'N/A' }}</span>
                                        <span class="block text-[10px] text-slate-400 font-bold">{{ $port->country->code ?? '' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 text-center">
                                <span class="text-xs text-slate-500 font-mono">{{ number_format(floatval($port->latitude), 4) }}, {{ number_format(floatval($port->longitude), 4) }}</span>
                            </td>
                            <td class="p-4 pr-6 text-center">
                                <button
                                    onclick="flyToPort({{ floatval($port->latitude) }}, {{ floatval($port->longitude) }}, '{{ $port->name }}')"
                                    class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 mx-auto"
                                >
                                    <i class="fa-solid fa-location-dot"></i>
                                    Lihat di Peta
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>

{{-- ===== LEAFLET MAP JS ===== --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Initialize Map
    const map = L.map('port-map', { zoomControl: true, scrollWheelZoom: true }).setView([20, 15], 2);

    // Premium Dark-ish tile
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '© OpenStreetMap contributors, © CARTO',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(map);

    const portData = @json($mapPorts);
    const markers = {};

    // Custom anchor icon
    function makeIcon(color = '#6366f1') {
        return L.divIcon({
            className: '',
            html: `<div style="
                width: 36px; height: 36px;
                background: ${color};
                border: 3px solid #fff;
                border-radius: 50% 50% 50% 0;
                transform: rotate(-45deg);
                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
                display:flex; align-items:center; justify-content:center;
            ">
                <i class="fa-solid fa-anchor" style="transform: rotate(45deg); color: #fff; font-size: 14px;"></i>
            </div>`,
            iconSize: [36, 36],
            iconAnchor: [18, 36],
            popupAnchor: [0, -38],
        });
    }

    const colors = ['#6366f1','#8b5cf6','#ec4899','#14b8a6','#f59e0b','#10b981','#3b82f6','#ef4444'];

    portData.forEach(function (port, idx) {
        if (!port.latitude || !port.longitude) return;

        const color = colors[idx % colors.length];
        const marker = L.marker([port.latitude, port.longitude], { icon: makeIcon(color) })
            .addTo(map)
            .bindPopup(`
                <div style="font-family:'Outfit',sans-serif; min-width:190px;">
                    <div style="background:${color}; color:#fff; padding:10px 12px; margin:-12px -12px 10px; border-radius: 4px 4px 0 0;">
                        <p style="font-size:14px; font-weight:800; margin:0;">${port.name}</p>
                        <p style="font-size:11px; opacity:0.85; margin:2px 0 0;">${port.code}</p>
                    </div>
                    <p style="font-size:12px; color:#475569; margin:0 0 4px; display:flex; align-items:center; gap:6px;">
                        <span style="font-size:10px;">🌍</span> <b>${port.country_name}</b> (${port.country_code})
                    </p>
                    <p style="font-size:11px; color:#94a3b8; margin:0; font-family:monospace;">
                        ${port.latitude.toFixed(4)}, ${port.longitude.toFixed(4)}
                    </p>
                </div>
            `);

        markers[port.name] = marker;
    });

    // Fly-to function for table row buttons
    window.flyToPort = function(lat, lng, name) {
        map.flyTo([lat, lng], 8, { animate: true, duration: 1.2 });
        if (markers[name]) markers[name].openPopup();
        document.getElementById('port-map').scrollIntoView({ behavior: 'smooth', block: 'center' });
    };
});
</script>
@endsection
