@extends('layouts.app')

@section('title', 'Dasbor Pelabuhan')

@section('content')
<div class="space-y-8">

    {{-- ===== HEADER & FILTER ===== --}}
    <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-5">
            <div>
                <h3 class="text-2xl font-extrabold text-slate-800 flex items-center gap-3">
                    <span class="bg-blue-100 text-blue-600 w-10 h-10 rounded-xl flex items-center justify-center text-base shrink-0">
                        <i class="fa-solid fa-anchor"></i>
                    </span>
                    Dasbor Pelabuhan Global
                </h3>
                <p class="text-sm text-slate-400 mt-2 ml-[52px]">Peta dan direktori seluruh pelabuhan strategis dalam jaringan rantai pasokan global.</p>
            </div>

            <div class="flex items-center gap-4 shrink-0">
                <div class="text-center bg-blue-50 px-5 py-3 rounded-xl border border-blue-100 min-w-[120px]">
                    <p class="text-2xl font-black text-blue-700">{{ $ports->count() }}</p>
                    <p class="text-xs font-bold text-blue-500 uppercase tracking-wider mt-0.5">DB Pelabuhan</p>
                </div>
                <div class="text-center bg-indigo-50 px-5 py-3 rounded-xl border border-indigo-100 min-w-[120px]">
                    <p class="text-2xl font-black text-indigo-700">{{ count($allMapPorts) }}</p>
                    <p class="text-xs font-bold text-indigo-500 uppercase tracking-wider mt-0.5">Peta Dunia</p>
                </div>
                <a href="{{ route('ports.world_map') }}"
                   class="flex items-center gap-2.5 px-5 py-3 rounded-xl font-bold text-sm transition-all shadow-lg"
                   style="background: linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;box-shadow:0 4px 20px rgba(99,102,241,0.35);"
                   onmouseover="this.style.boxShadow='0 6px 28px rgba(99,102,241,0.55)';this.style.transform='translateY(-1px)';"
                   onmouseout="this.style.boxShadow='0 4px 20px rgba(99,102,241,0.35)';this.style.transform='translateY(0)';">
                    <i class="fa-solid fa-earth-americas text-base"></i>
                    Fullscreen
                </a>
            </div>
        </div>

        {{-- Filter Bar --}}
        <form action="{{ route('ports.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-base"></i>
                </div>
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Cari nama atau kode pelabuhan..."
                    class="w-full pl-10 pr-4 py-2.5 border border-slate-300 bg-slate-100 rounded-xl text-sm text-slate-700 font-semibold focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                >
            </div>
            <div class="relative sm:w-56">
                <select name="country_id" onchange="this.form.submit()" class="w-full px-4 py-2.5 border border-slate-300 bg-slate-100 rounded-xl text-sm text-slate-700 font-semibold appearance-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">Semua Negara</option>
                    @foreach($countries as $c)
                        <option value="{{ $c->id }}" {{ $countryId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i>
                </div>
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2 shrink-0 shadow-md shadow-blue-200">
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

    {{-- ===== WORLD PORT MAP ===== --}}
    <div class="rounded-2xl overflow-hidden" style="border:1px solid rgba(99,102,241,0.25);box-shadow:0 8px 48px rgba(0,0,0,0.5);">

        {{-- Map header bar --}}
        <div style="background:linear-gradient(135deg,rgba(6,9,22,0.98),rgba(15,19,45,0.97));border-bottom:1px solid rgba(99,102,241,0.2);padding:14px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:32px;height:32px;border-radius:9px;background:linear-gradient(135deg,#6366f1,#7c3aed);display:flex;align-items:center;justify-content:center;box-shadow:0 3px 12px rgba(99,102,241,0.45);">
                    <i class="fa-solid fa-earth-americas" style="color:#fff;font-size:14px;"></i>
                </div>
                <div>
                    <p style="color:#f1f5f9;font-weight:800;font-size:13px;line-height:1;">Peta Pelabuhan Seluruh Dunia</p>
                    <p style="color:#818cf8;font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.14em;margin-top:2px;">{{ count($allMapPorts) }} pelabuhan global · Klik marker untuk detail</p>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                {{-- Region filter pills --}}
                <button class="pm-rbtn on" data-r="all" style="padding:4px 11px;border-radius:50px;font-size:10.5px;font-weight:700;border:1px solid rgba(255,255,255,0.1);background:linear-gradient(135deg,#6366f1,#7c3aed);color:#fff;cursor:pointer;letter-spacing:.03em;white-space:nowrap;">🌐 Semua</button>
                <button class="pm-rbtn" data-r="Asia" style="padding:4px 11px;border-radius:50px;font-size:10.5px;font-weight:700;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.04);color:rgba(255,255,255,0.55);cursor:pointer;white-space:nowrap;">🌏 Asia</button>
                <button class="pm-rbtn" data-r="Europe" style="padding:4px 11px;border-radius:50px;font-size:10.5px;font-weight:700;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.04);color:rgba(255,255,255,0.55);cursor:pointer;white-space:nowrap;">🌍 Eropa</button>
                <button class="pm-rbtn" data-r="Americas" style="padding:4px 11px;border-radius:50px;font-size:10.5px;font-weight:700;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.04);color:rgba(255,255,255,0.55);cursor:pointer;white-space:nowrap;">🌎 Amerika</button>
                <button class="pm-rbtn" data-r="Middle East" style="padding:4px 11px;border-radius:50px;font-size:10.5px;font-weight:700;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.04);color:rgba(255,255,255,0.55);cursor:pointer;white-space:nowrap;">🕌 Timur Tengah</button>
                <button class="pm-rbtn" data-r="Africa" style="padding:4px 11px;border-radius:50px;font-size:10.5px;font-weight:700;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.04);color:rgba(255,255,255,0.55);cursor:pointer;white-space:nowrap;">🌍 Afrika</button>
                <button class="pm-rbtn" data-r="Oceania" style="padding:4px 11px;border-radius:50px;font-size:10.5px;font-weight:700;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.04);color:rgba(255,255,255,0.55);cursor:pointer;white-space:nowrap;">🌊 Oseania</button>
            </div>
        </div>

        {{-- Map canvas (must have explicit height) --}}
        <div id="port-map" style="width:100%;height:520px;background:#c8d8e8;"></div>
    </div>

    {{-- ===== PORT CARDS GRID ===== --}}
    @if($ports->isEmpty())
        <div class="bg-slate-50 rounded-2xl border border-slate-200 shadow-sm p-16 text-center">
            <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-anchor text-3xl text-slate-400"></i>
            </div>
            <h4 class="font-bold text-slate-700 text-lg">Tidak Ada Pelabuhan Ditemukan</h4>
            <p class="text-sm text-slate-400 mt-2">Coba ubah filter pencarian atau reset untuk melihat semua data.</p>
            <a href="{{ route('ports.index') }}" class="mt-5 inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-md shadow-blue-200">
                <i class="fa-solid fa-rotate-left"></i> Tampilkan Semua
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($ports as $port)
                @php
                    $countryColors = [
                        'DE' => ['icon_bg' => 'bg-amber-100', 'icon_text' => 'text-amber-600'],
                        'CN' => ['icon_bg' => 'bg-red-100',   'icon_text' => 'text-red-600'],
                        'ID' => ['icon_bg' => 'bg-rose-100',  'icon_text' => 'text-rose-600'],
                        'AU' => ['icon_bg' => 'bg-sky-100',   'icon_text' => 'text-sky-600'],
                        'US' => ['icon_bg' => 'bg-blue-100',  'icon_text' => 'text-blue-600'],
                        'SG' => ['icon_bg' => 'bg-purple-100','icon_text' => 'text-purple-600'],
                        'JP' => ['icon_bg' => 'bg-pink-100',  'icon_text' => 'text-pink-600'],
                        'GB' => ['icon_bg' => 'bg-blue-100',  'icon_text' => 'text-blue-600'],
                    ];
                    $color = $countryColors[$port->country->code ?? ''] ?? ['icon_bg' => 'bg-slate-100', 'icon_text' => 'text-slate-500'];
                @endphp
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 overflow-hidden flex flex-col">
                    <div class="h-1.5 w-full" style="background: linear-gradient(90deg, #6366f1, #8b5cf6)"></div>
                    <div class="p-5 flex flex-col flex-1">
                        <div class="flex items-start justify-between mb-4">
                            <div class="{{ $color['icon_bg'] }} {{ $color['icon_text'] }} w-12 h-12 rounded-xl flex items-center justify-center text-xl shadow-sm">
                                <i class="fa-solid fa-anchor"></i>
                            </div>
                            <span class="text-xs font-black tracking-widest text-slate-400 bg-slate-100 px-2.5 py-1 rounded-lg uppercase">{{ $port->code }}</span>
                        </div>
                        <h5 class="font-extrabold text-slate-800 text-sm leading-snug mb-1">{{ $port->name }}</h5>
                        <div class="flex items-center gap-2 mb-4">
                            @if(optional($port->country)->flag)
                                <img src="{{ $port->country->flag }}" class="w-6 object-cover rounded shadow-sm border border-slate-200" style="height:18px;" alt="">
                            @endif
                            <span class="text-xs text-slate-400 font-semibold">{{ optional($port->country)->name ?? 'N/A' }}</span>
                        </div>
                        <div class="mt-auto pt-3 border-t border-slate-100 grid grid-cols-2 gap-2 text-center">
                            <div>
                                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Lintang</p>
                                <p class="text-xs font-bold text-slate-700 mt-0.5">{{ number_format(floatval($port->latitude), 4) }}°</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Bujur</p>
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
            <h4 class="font-bold text-slate-800 text-base">Daftar Lengkap Pelabuhan (Database)</h4>
            <span class="text-sm text-slate-400 font-semibold">{{ $ports->count() }} data</span>
        </div>
        <div class="overflow-x-auto">
            <table class="table-auto w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 font-bold text-xs uppercase border-b border-slate-200 tracking-wider">
                        <th class="p-4 pl-6">#</th>
                        <th class="p-4">Nama Pelabuhan</th>
                        <th class="p-4">Kode</th>
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
                                <span class="font-black text-slate-400 bg-slate-100 px-2.5 py-1 rounded-lg text-xs tracking-wider uppercase">{{ $port->code }}</span>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    @if(optional($port->country)->flag)
                                        <img src="{{ $port->country->flag }}" class="w-8 object-cover rounded shadow-sm border border-slate-100" style="height:20px;" alt="">
                                    @endif
                                    <div>
                                        <span class="font-semibold text-slate-700 text-xs">{{ optional($port->country)->name ?? 'N/A' }}</span>
                                        <span class="block text-xs text-slate-400 font-bold">{{ optional($port->country)->code ?? '' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 text-center">
                                <span class="text-xs text-slate-400 font-mono">{{ number_format(floatval($port->latitude), 4) }}, {{ number_format(floatval($port->longitude), 4) }}</span>
                            </td>
                            <td class="p-4 pr-6 text-center">
                                <button
                                    onclick="flyToPort({{ floatval($port->latitude) }}, {{ floatval($port->longitude) }}, '{{ addslashes($port->name) }}')"
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

</div>{{-- /space-y-8 --}}

{{-- =====================================================
     LEAFLET MARKER CLUSTER + WORLD MAP JS
====================================================== --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css">
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster-src.js"></script>

<style>
/* Popup override — atlas light style */
.leaflet-popup-content-wrapper {
    background: rgba(255,252,245,0.98) !important;
    border: 1.5px solid rgba(100,80,40,0.2) !important;
    border-radius: 14px !important;
    box-shadow: 0 8px 32px rgba(80,60,20,0.22) !important;
    padding: 0 !important;
    overflow: hidden !important;
}
.leaflet-popup-tip { background: rgba(255,252,245,0.98) !important; }
.leaflet-popup-content { margin: 0 !important; }
.leaflet-popup-close-button { color:rgba(80,60,20,0.5)!important; font-size:18px!important; top:7px!important; right:9px!important; }
.leaflet-popup-close-button:hover { color:#3d2e0a!important; background:none!important; }
.leaflet-control-zoom a { background:rgba(255,252,245,0.95)!important; border-color:rgba(100,80,40,0.25)!important; color:#4a3a10!important; font-weight:700!important; }
.leaflet-control-zoom a:hover { background:#f0e8d0!important; color:#2d2005!important; }
/* Cluster override */
.marker-cluster-small,.marker-cluster-medium,.marker-cluster-large { background:rgba(99,102,241,0.18)!important; }
.marker-cluster-small div,.marker-cluster-medium div,.marker-cluster-large div { background:rgba(99,102,241,0.88)!important; color:#fff!important; font-family:'Outfit',sans-serif!important; font-weight:800!important; }
/* Region button active */
.pm-rbtn.on { background:linear-gradient(135deg,#6366f1,#7c3aed)!important; color:#fff!important; border-color:transparent!important; box-shadow:0 3px 12px rgba(99,102,241,0.4); }
</style>

<script>
(function () {
    'use strict';

    /* ── All 105+ world ports from controller ── */
    var ALL_PORTS = @json($allMapPorts);

    /* ── Region colour palette ── */
    var RC = {
        'Asia'        : '#6366f1',
        'Europe'      : '#10b981',
        'Americas'    : '#f59e0b',
        'Middle East' : '#ef4444',
        'Africa'      : '#a855f7',
        'Oceania'     : '#06b6d4',
        'Other'       : '#94a3b8',
    };
    function col(r) { return RC[r] || RC['Other']; }

    document.addEventListener('DOMContentLoaded', function () {

        /* ── Init Leaflet ── */
        var map = L.map('port-map', {
            zoomControl     : true,
            scrollWheelZoom : true,
            center          : [20, 10],
            zoom            : 2,
            minZoom         : 1,
            maxZoom         : 18,
            worldCopyJump   : true,
        });

        /* ESRI National Geographic — atlas style */
        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/NatGeo_World_Map/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri &mdash; National Geographic, Esri, DeLorme, NAVTEQ, UNEP-WCMC, USGS, NASA, ESA, METI, NRCAN, GEBCO, NOAA, iPC',
            maxZoom     : 16,
        }).addTo(map);

        /* ── Cluster group ── */
        var cluster = L.markerClusterGroup({
            maxClusterRadius   : 55,
            spiderfyOnMaxZoom  : true,
            showCoverageOnHover: false,
            zoomToBoundsOnClick: true,
            iconCreateFunction: function (c) {
                var n  = c.getChildCount();
                var sz = n > 60 ? 50 : n > 25 ? 43 : 37;
                return L.divIcon({
                    className: '',
                    html: '<div style="width:'+sz+'px;height:'+sz+'px;border-radius:50%;'
                        +'background:rgba(99,102,241,0.9);border:2.5px solid rgba(165,180,252,0.85);'
                        +'display:flex;align-items:center;justify-content:center;'
                        +'font-weight:800;color:#fff;font-size:'+(n>99?10:12)+'px;'
                        +'font-family:Outfit,sans-serif;box-shadow:0 4px 18px rgba(99,102,241,0.55);">'
                        +n+'</div>',
                    iconSize:[sz,sz], iconAnchor:[sz/2,sz/2],
                });
            },
        });

        /* ── Pin icon ── */
        function mkIcon(region) {
            var c = col(region);
            return L.divIcon({
                className: '',
                html: '<div style="width:22px;height:22px;border-radius:50% 50% 50% 0;'
                    +'transform:rotate(-45deg);background:'+c+';'
                    +'border:2px solid rgba(255,255,255,0.85);'
                    +'display:flex;align-items:center;justify-content:center;'
                    +'box-shadow:0 2px 10px '+c+'aa;cursor:pointer;">'
                    +'<i class="fa-solid fa-anchor" style="transform:rotate(45deg);font-size:8px;color:#fff;"></i>'
                    +'</div>',
                iconSize:[22,22], iconAnchor:[11,22], popupAnchor:[0,-24],
            });
        }

        /* ── Named marker store (for fly-to from table) ── */
        var markerStore = {};

        /* ── Build markers ── */
        function buildMarkers(ports) {
            cluster.clearLayers();
            markerStore = {};

            ports.forEach(function (p) {
                var lat = parseFloat(p.latitude), lng = parseFloat(p.longitude);
                if (isNaN(lat)||isNaN(lng)) return;

                var c  = col(p.region||'Other');
                var mk = L.marker([lat,lng],{icon:mkIcon(p.region||'Other')});

                mk.bindPopup(
                    '<div style="font-family:Outfit,sans-serif;min-width:195px;">'
                    +'<div style="background:'+c+';padding:11px 13px;">'
                    +'<p style="color:#fff;font-size:13.5px;font-weight:800;margin:0;line-height:1.35;">'+esc(p.name)+'</p>'
                    +'<p style="color:rgba(255,255,255,0.65);font-size:10.5px;font-weight:700;letter-spacing:.08em;margin:3px 0 0;">'+esc(p.code)+'</p>'
                    +'</div>'
                    +'<div style="padding:11px 13px 13px;">'
                    +'<span style="display:inline-block;background:rgba(255,255,255,0.07);border-radius:5px;padding:2px 7px;font-size:9.5px;color:rgba(255,255,255,0.5);font-weight:700;text-transform:uppercase;margin-bottom:7px;">'+esc(p.region||'')+'</span>'
                    +'<p style="color:rgba(255,255,255,0.8);font-size:12.5px;font-weight:600;margin:0 0 3px;">'+esc(p.country_name)+' <span style="opacity:.4;">('+esc(p.country_code)+')</span></p>'
                    +'<p style="color:rgba(255,255,255,0.28);font-size:10.5px;font-family:monospace;margin:0;">'+lat.toFixed(4)+', '+lng.toFixed(4)+'</p>'
                    +'</div></div>',
                    {maxWidth:230}
                );

                cluster.addLayer(mk);
                markerStore[p.name] = mk;
            });

            map.addLayer(cluster);
        }

        /* ── Region filter ── */
        var activeRegion = 'all';

        document.querySelectorAll('.pm-rbtn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.pm-rbtn').forEach(function (b) {
                    b.classList.remove('on');
                    b.style.background = 'rgba(255,255,255,0.04)';
                    b.style.color      = 'rgba(255,255,255,0.55)';
                    b.style.borderColor= 'rgba(255,255,255,0.1)';
                    b.style.boxShadow  = 'none';
                });
                this.classList.add('on');
                this.style.background  = 'linear-gradient(135deg,#6366f1,#7c3aed)';
                this.style.color       = '#fff';
                this.style.borderColor = 'transparent';
                this.style.boxShadow   = '0 3px 12px rgba(99,102,241,0.4)';

                activeRegion = this.getAttribute('data-r');
                var list = activeRegion === 'all'
                    ? ALL_PORTS
                    : ALL_PORTS.filter(function(p){ return p.region === activeRegion; });
                buildMarkers(list);
            });
        });

        /* ── Fly-to from table buttons ── */
        window.flyToPort = function (lat, lng, name) {
            map.flyTo([lat,lng], 9, {animate:true, duration:1.3});
            if (markerStore[name]) {
                setTimeout(function(){ markerStore[name].openPopup(); }, 1400);
            }
            document.getElementById('port-map').scrollIntoView({behavior:'smooth', block:'center'});
        };

        /* ── First render ── */
        buildMarkers(ALL_PORTS);
        setTimeout(function(){ map.invalidateSize(); }, 200);

        /* ── Legend (bottom-left) — atlas style ── */
        var legend = L.control({position:'bottomleft'});
        legend.onAdd = function () {
            var d = L.DomUtil.create('div');
            d.style.cssText = 'background:rgba(255,252,242,0.97);border:1.5px solid rgba(100,80,40,0.2);border-radius:12px;padding:11px 14px;font-family:Outfit,sans-serif;pointer-events:none;box-shadow:0 4px 16px rgba(80,60,20,0.18);';
            var h = '<p style="color:rgba(80,60,20,0.5);font-size:9px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;margin:0 0 8px;">Kawasan</p>';
            Object.keys(RC).forEach(function(r){
                if(r==='Other') return;
                h += '<div style="display:flex;align-items:center;gap:7px;margin-bottom:5px;">'
                   + '<span style="width:10px;height:10px;border-radius:50%;background:'+RC[r]+';flex-shrink:0;border:1.5px solid rgba(0,0,0,0.15);"></span>'
                   + '<span style="color:rgba(50,35,5,0.8);font-size:11px;font-weight:700;">'+r+'</span>'
                   + '</div>';
            });
            d.innerHTML = h;
            return d;
        };
        legend.addTo(map);

        /* ── Counter badge (top-right) — atlas style ── */
        var badge = L.control({position:'topright'});
        badge.onAdd = function () {
            var d = L.DomUtil.create('div');
            d.style.cssText = 'background:rgba(255,252,242,0.97);border:1.5px solid rgba(99,102,241,0.35);border-radius:12px;padding:10px 14px;font-family:Outfit,sans-serif;pointer-events:none;text-align:center;min-width:80px;box-shadow:0 4px 16px rgba(80,60,20,0.18);';
            d.innerHTML = '<p style="color:#6366f1;font-size:20px;font-weight:800;margin:0;line-height:1;">'+ALL_PORTS.length+'</p>'
                        + '<p style="color:rgba(50,35,5,0.5);font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;margin:3px 0 0;">Port Dunia</p>';
            return d;
        };
        badge.addTo(map);

    });

    /* ── Popup text — atlas style (dark text on cream) ── */
    function buildAtlasPopup(p, c) {
        var lat = parseFloat(p.latitude), lng = parseFloat(p.longitude);
        return '<div style="font-family:Outfit,sans-serif;min-width:195px;">'
            +'<div style="background:'+c+';padding:11px 13px;">'
            +'<p style="color:#fff;font-size:13.5px;font-weight:800;margin:0;line-height:1.35;">'+esc(p.name)+'</p>'
            +'<p style="color:rgba(255,255,255,0.75);font-size:10.5px;font-weight:700;letter-spacing:.08em;margin:3px 0 0;">'+esc(p.code)+'</p>'
            +'</div>'
            +'<div style="padding:11px 13px 13px;background:#fffcf2;">'
            +'<span style="display:inline-block;background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.2);border-radius:5px;padding:2px 7px;font-size:9.5px;color:#6366f1;font-weight:700;text-transform:uppercase;margin-bottom:7px;">'+esc(p.region||'')+'</span>'
            +'<p style="color:#3d2e0a;font-size:12.5px;font-weight:700;margin:0 0 3px;">'+esc(p.country_name)+' <span style="color:#7a6030;opacity:.7;">('+esc(p.country_code)+')</span></p>'
            +'<p style="color:#a0855a;font-size:10.5px;font-family:monospace;margin:0;">'+lat.toFixed(4)+', '+lng.toFixed(4)+'</p>'
            +'</div></div>';
    }

    function esc(s){
        return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    }
})();
</script>

@endsection
