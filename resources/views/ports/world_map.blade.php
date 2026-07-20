@extends('layouts.app')

@section('title', 'Peta Pelabuhan Dunia')

@section('content')
{{-- ===================================================
     WORLD PORT MAP  –  Global Port Intelligence
     =================================================== --}}

<style>
/* ──────────────────────────────────────────
   ROOT LAYOUT
────────────────────────────────────────── */
#portmap-root {
    position: relative;
    display: flex;
    flex-direction: column;
    height: calc(100vh - 128px);
    gap: 10px;
    min-height: 600px;
}

/* ──────────────────────────────────────────
   GLASS CONTROL BAR
────────────────────────────────────────── */
#portmap-bar {
    flex-shrink: 0;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    padding: 11px 16px;
    border-radius: 16px;
    background: linear-gradient(135deg, rgba(6,9,22,0.97), rgba(15,19,42,0.95));
    border: 1px solid rgba(99,102,241,0.28);
    box-shadow: 0 2px 30px rgba(0,0,0,0.55), inset 0 1px 0 rgba(255,255,255,0.04);
    backdrop-filter: blur(24px);
}

/* ──────────────────────────────────────────
   MAP WRAPPER
────────────────────────────────────────── */
#portmap-wrap {
    flex: 1;
    min-height: 0;
    position: relative;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid rgba(99,102,241,0.22);
    box-shadow: 0 8px 48px rgba(0,0,0,0.7), inset 0 0 0 1px rgba(255,255,255,0.02);
}

/* ──────────────────────────────────────────
   LEAFLET MAP CANVAS — must have explicit height
────────────────────────────────────────── */
#portmap {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    background: #c8d8e8;
    z-index: 1;
}

/* ──────────────────────────────────────────
   LOADING SCREEN
────────────────────────────────────────── */
#portmap-loader {
    position: absolute;
    inset: 0;
    z-index: 2000;
    background: rgba(6,9,22,0.97);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 18px;
    border-radius: 16px;
}
.pm-loader-ring {
    width: 56px; height: 56px;
    border-radius: 50%;
    border: 4px solid rgba(99,102,241,0.15);
    border-top-color: #6366f1;
    animation: pm-spin 0.75s linear infinite;
}
@keyframes pm-spin { to { transform: rotate(360deg); } }

/* ──────────────────────────────────────────
   DETAIL SIDE DRAWER
────────────────────────────────────────── */
#portmap-drawer {
    position: absolute;
    top: 0; right: 0; bottom: 0;
    width: 0;
    z-index: 1000;
    overflow: hidden;
    transition: width 0.36s cubic-bezier(.4,0,.2,1);
    display: flex;
    flex-direction: column;
    background: linear-gradient(180deg, rgba(6,9,22,0.99), rgba(11,15,35,0.99));
    border-left: 1px solid rgba(99,102,241,0.2);
}
#portmap-drawer.open { width: 330px; }

#portmap-drawer-inner {
    width: 330px;
    flex: 1;
    overflow-y: auto;
    padding: 22px 20px 28px;
}
#portmap-drawer-inner::-webkit-scrollbar { width: 4px; }
#portmap-drawer-inner::-webkit-scrollbar-track { background: transparent; }
#portmap-drawer-inner::-webkit-scrollbar-thumb { background: rgba(99,102,241,0.35); border-radius: 4px; }

/* ──────────────────────────────────────────
   CONTROL BAR ELEMENTS
────────────────────────────────────────── */
.pm-search {
    position: relative;
    display: flex;
    align-items: center;
}
.pm-search input {
    width: 195px;
    padding: 7px 12px 7px 32px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 10px;
    color: #e2e8f0;
    font-size: 12.5px;
    font-family: 'Outfit', sans-serif;
    font-weight: 500;
    outline: none;
    transition: .2s;
}
.pm-search input:focus {
    border-color: #6366f1;
    background: rgba(99,102,241,0.1);
    box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
}
.pm-search input::placeholder { color: rgba(255,255,255,0.28); }
.pm-search .ico {
    position: absolute;
    left: 10px;
    color: rgba(255,255,255,0.3);
    font-size: 11px;
    pointer-events: none;
}

.pm-rfilter { display: flex; align-items: center; gap: 5px; flex-wrap: wrap; }
.pm-rbtn {
    padding: 5px 11px;
    border-radius: 50px;
    font-size: 11px;
    font-weight: 700;
    border: 1px solid rgba(255,255,255,0.1);
    background: rgba(255,255,255,0.04);
    color: rgba(255,255,255,0.5);
    cursor: pointer;
    white-space: nowrap;
    letter-spacing: .03em;
    transition: all .18s;
}
.pm-rbtn:hover { background: rgba(255,255,255,0.09); color: #e2e8f0; border-color: rgba(255,255,255,0.2); }
.pm-rbtn.on {
    background: linear-gradient(135deg,#6366f1,#7c3aed);
    color: #fff; border-color: transparent;
    box-shadow: 0 3px 14px rgba(99,102,241,0.4);
}

.pm-stat {
    display: flex; align-items: center; gap: 6px;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 9px;
    padding: 5px 12px;
    font-size: 12px; font-weight: 700;
    color: rgba(255,255,255,0.6);
    white-space: nowrap;
}
.pm-stat .n { color: #a5b4fc; font-size: 14px; font-weight: 800; }

@keyframes pm-pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.4;transform:scale(1.6)} }
.pm-dot { width:8px;height:8px;border-radius:50%;display:inline-block;animation:pm-pulse 1.9s ease-in-out infinite; }

/* ──────────────────────────────────────────
   LEAFLET POPUP OVERRIDE — atlas style
────────────────────────────────────────── */
.leaflet-popup-content-wrapper {
    background: rgba(255,252,245,0.98) !important;
    border: 1.5px solid rgba(100,80,40,0.2) !important;
    border-radius: 14px !important;
    box-shadow: 0 8px 32px rgba(80,60,20,0.2) !important;
    padding: 0 !important;
    overflow: hidden !important;
}
.leaflet-popup-tip { background: rgba(255,252,245,0.98) !important; }
.leaflet-popup-content { margin: 0 !important; }
.leaflet-popup-close-button {
    color: rgba(80,60,20,0.5) !important;
    font-size: 20px !important;
    top: 7px !important; right: 9px !important;
    z-index: 10;
}
.leaflet-popup-close-button:hover { color: #3d2e0a !important; background: none !important; }

/* Zoom control — atlas style */
.leaflet-control-zoom a {
    background: rgba(255,252,245,0.96) !important;
    border-color: rgba(100,80,40,0.22) !important;
    color: #4a3a10 !important;
    font-weight: 700 !important;
}
.leaflet-control-zoom a:hover { background: #f0e8d0 !important; color: #2d2005 !important; }

/* ──────────────────────────────────────────
   DETAIL DRAWER ROWS
────────────────────────────────────────── */
.pm-drow {
    display: flex; justify-content: space-between; align-items: center;
    padding: 7px 0;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    font-size: 12.5px;
}
.pm-drow:last-child { border-bottom: none; }
.pm-drow .l { color: rgba(255,255,255,0.35); font-weight: 600; font-size: 10px; text-transform: uppercase; letter-spacing: .08em; }
.pm-drow .v { color: #cbd5e1; font-weight: 700; text-align: right; }
</style>

<div id="portmap-root">

    {{-- ══════════════════════════════════════
         CONTROL BAR
    ══════════════════════════════════════ --}}
    <div id="portmap-bar">

        {{-- Brand --}}
        <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
            <div style="width:36px;height:36px;border-radius:11px;background:linear-gradient(135deg,#6366f1,#7c3aed);display:flex;align-items:center;justify-content:center;box-shadow:0 4px 16px rgba(99,102,241,0.45);">
                <i class="fa-solid fa-earth-americas" style="color:#fff;font-size:16px;"></i>
            </div>
            <div>
                <p style="color:#f1f5f9;font-weight:800;font-size:13.5px;line-height:1;letter-spacing:-.01em;">Peta Pelabuhan Dunia</p>
                <p style="color:#818cf8;font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.15em;margin-top:3px;">Global Port Intelligence</p>
            </div>
        </div>

        <div style="width:1px;height:30px;background:rgba(255,255,255,0.08);flex-shrink:0;"></div>

        {{-- Search --}}
        <div class="pm-search">
            <i class="fa-solid fa-magnifying-glass ico"></i>
            <input id="pm-search" type="text" placeholder="Cari pelabuhan, negara...">
        </div>

        {{-- Region filters --}}
        <div class="pm-rfilter">
            <button class="pm-rbtn on" data-r="all">🌐 Semua</button>
            <button class="pm-rbtn" data-r="Asia">🌏 Asia</button>
            <button class="pm-rbtn" data-r="Europe">🌍 Eropa</button>
            <button class="pm-rbtn" data-r="Americas">🌎 Amerika</button>
            <button class="pm-rbtn" data-r="Middle East">🕌 Timur Tengah</button>
            <button class="pm-rbtn" data-r="Africa">🌍 Afrika</button>
            <button class="pm-rbtn" data-r="Oceania">🌊 Oseania</button>
        </div>

        <div style="flex:1;min-width:8px;"></div>

        {{-- Stats --}}
        <div style="display:flex;align-items:center;gap:7px;flex-wrap:wrap;">
            <div class="pm-stat">
                <span class="pm-dot" style="background:#818cf8;"></span>
                <span class="n" id="pm-count">{{ $totalPorts }}</span>
                <span>Pelabuhan</span>
            </div>
            <div class="pm-stat">
                <i class="fa-solid fa-flag" style="color:#34d399;font-size:10px;"></i>
                <span class="n">{{ $totalCountries }}</span>
                <span>Negara</span>
            </div>
            <div class="pm-stat">
                <i class="fa-solid fa-globe" style="color:#fb923c;font-size:10px;"></i>
                <span class="n">{{ count($regionCounts) }}</span>
                <span>Kawasan</span>
            </div>
        </div>

        <div style="width:1px;height:30px;background:rgba(255,255,255,0.08);flex-shrink:0;"></div>

        {{-- Dasbor Pelabuhan link --}}
        <a href="{{ route('ports.index') }}"
           style="flex-shrink:0;display:flex;align-items:center;gap:6px;padding:6px 13px;border-radius:10px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.1);color:rgba(255,255,255,0.6);font-size:12px;font-weight:700;text-decoration:none;transition:.2s;"
           onmouseover="this.style.background='rgba(255,255,255,0.09)';this.style.color='#e2e8f0'"
           onmouseout="this.style.background='rgba(255,255,255,0.04)';this.style.color='rgba(255,255,255,0.6)'">
            <i class="fa-solid fa-list-ul" style="font-size:11px;"></i> Daftar
        </a>
    </div>

    {{-- ══════════════════════════════════════
         MAP AREA
    ══════════════════════════════════════ --}}
    <div id="portmap-wrap">

        {{-- Loading --}}
        <div id="portmap-loader">
            <div class="pm-loader-ring"></div>
            <p style="color:#818cf8;font-weight:700;font-size:14px;letter-spacing:.04em;">Memuat Peta Pelabuhan Dunia…</p>
            <p style="color:rgba(255,255,255,0.25);font-size:11.5px;font-weight:600;">
                {{ $totalPorts }} pelabuhan · {{ $totalCountries }} negara · {{ count($regionCounts) }} kawasan
            </p>
        </div>

        {{-- Map --}}
        <div id="portmap"></div>

        {{-- Detail Drawer --}}
        <div id="portmap-drawer">
            <div id="portmap-drawer-inner">

                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
                    <p style="color:rgba(255,255,255,0.3);font-size:9.5px;font-weight:700;text-transform:uppercase;letter-spacing:.14em;">Detail Pelabuhan</p>
                    <button id="pm-close"
                        style="width:26px;height:26px;border-radius:8px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:rgba(255,255,255,0.4);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:.2s;"
                        onmouseover="this.style.background='rgba(255,255,255,0.12)';this.style.color='#fff'"
                        onmouseout="this.style.background='rgba(255,255,255,0.05)';this.style.color='rgba(255,255,255,0.4)'">
                        <i class="fa-solid fa-xmark" style="font-size:12px;"></i>
                    </button>
                </div>

                {{-- Port hero --}}
                <div style="display:flex;flex-direction:column;align-items:center;text-align:center;margin-bottom:20px;">
                    <div id="pm-d-icon" style="width:64px;height:64px;border-radius:20px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;background:linear-gradient(135deg,#6366f1,#7c3aed);box-shadow:0 8px 28px rgba(99,102,241,0.45);">
                        <i class="fa-solid fa-anchor" style="color:#fff;font-size:24px;"></i>
                    </div>
                    <h3 id="pm-d-name" style="color:#f8fafc;font-weight:800;font-size:16px;line-height:1.35;margin-bottom:11px;">—</h3>
                    <div style="display:flex;gap:7px;flex-wrap:wrap;justify-content:center;">
                        <span id="pm-d-code" style="padding:3px 11px;border-radius:50px;background:rgba(99,102,241,0.12);border:1px solid rgba(99,102,241,0.28);color:#a5b4fc;font-size:10.5px;font-weight:800;letter-spacing:.1em;">—</span>
                        <span id="pm-d-region" style="padding:3px 11px;border-radius:50px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:rgba(255,255,255,0.55);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;">—</span>
                    </div>
                </div>

                {{-- Info block --}}
                <div style="background:rgba(255,255,255,0.025);border:1px solid rgba(255,255,255,0.06);border-radius:13px;padding:12px 14px;margin-bottom:12px;">
                    <div class="pm-drow"><span class="l">Negara</span><span id="pm-d-country" class="v">—</span></div>
                    <div class="pm-drow"><span class="l">Kode Negara</span><span id="pm-d-cc" class="v" style="color:#fde68a;">—</span></div>
                    <div class="pm-drow"><span class="l">Lintang</span><span id="pm-d-lat" class="v" style="color:#67e8f9;font-family:monospace;">—</span></div>
                    <div class="pm-drow"><span class="l">Bujur</span><span id="pm-d-lng" class="v" style="color:#67e8f9;font-family:monospace;">—</span></div>
                    <div class="pm-drow"><span class="l">Kawasan</span><span id="pm-d-reg2" class="v">—</span></div>
                    <div class="pm-drow"><span class="l">Sumber</span><span id="pm-d-src" class="v">—</span></div>
                </div>

                {{-- Coord block --}}
                <div style="background:linear-gradient(135deg,rgba(99,102,241,0.07),rgba(124,58,237,0.07));border:1px solid rgba(99,102,241,0.16);border-radius:13px;padding:12px 14px;margin-bottom:12px;">
                    <p style="color:rgba(255,255,255,0.28);font-size:9.5px;font-weight:700;text-transform:uppercase;letter-spacing:.12em;margin-bottom:7px;">Koordinat GPS</p>
                    <p id="pm-d-coords" style="color:#e2e8f0;font-family:monospace;font-size:12.5px;font-weight:700;word-break:break-all;">—</p>
                </div>

                {{-- Action buttons --}}
                <a id="pm-d-gmaps" href="#" target="_blank"
                   style="display:flex;align-items:center;justify-content:center;gap:8px;padding:11px;border-radius:12px;margin-bottom:8px;text-decoration:none;background:linear-gradient(135deg,#6366f1,#7c3aed);color:#fff;font-weight:700;font-size:12.5px;box-shadow:0 4px 18px rgba(99,102,241,0.38);transition:.2s;"
                   onmouseover="this.style.boxShadow='0 6px 26px rgba(99,102,241,0.6)';this.style.transform='translateY(-1px)'"
                   onmouseout="this.style.boxShadow='0 4px 18px rgba(99,102,241,0.38)';this.style.transform='translateY(0)'">
                    <i class="fa-solid fa-map-location-dot"></i> Buka di Google Maps
                </a>
                <button id="pm-d-fly"
                    style="width:100%;display:flex;align-items:center;justify-content:center;gap:8px;padding:11px;border-radius:12px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.1);color:rgba(255,255,255,0.65);font-weight:700;font-size:12.5px;cursor:pointer;transition:.2s;"
                    onmouseover="this.style.background='rgba(255,255,255,0.09)';this.style.color='#fff'"
                    onmouseout="this.style.background='rgba(255,255,255,0.04)';this.style.color='rgba(255,255,255,0.65)'">
                    <i class="fa-solid fa-location-crosshairs"></i> Terbang ke Lokasi
                </button>
            </div>
        </div>{{-- /#portmap-drawer --}}

    </div>{{-- /#portmap-wrap --}}

</div>{{-- /#portmap-root --}}

{{-- ══════════════════════════════════════
     LEAFLET MARKER CLUSTER
══════════════════════════════════════ --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css">
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster-src.js"></script>

<script>
/* ────────────────────────────────────────────────────────────
   WORLD PORT MAP — main script
──────────────────────────────────────────────────────────── */
(function () {
    'use strict';

    /* ─── Port data from PHP ─── */
    var ALL_PORTS = @json($allPorts);
    var REGION_COUNTS = @json($regionCounts);

    /* ─── Colour palette ─── */
    var C = {
        'Asia':        '#6366f1',
        'Europe':      '#10b981',
        'Americas':    '#f59e0b',
        'Middle East': '#ef4444',
        'Africa':      '#a855f7',
        'Oceania':     '#06b6d4',
        'Other':       '#94a3b8',
    };
    function portColor(region) { return C[region] || C['Other']; }

    /* ─── Wait for DOM + Leaflet ─── */
    document.addEventListener('DOMContentLoaded', function () {

        /* ── 1. Init map ── */
        var map = L.map('portmap', {
            zoomControl      : false,
            scrollWheelZoom  : true,
            attributionControl: true,
            worldCopyJump    : true,
            center           : [20, 10],
            zoom             : 2,
            minZoom          : 1,
            maxZoom          : 18,
        });

        /* Custom zoom control */
        L.control.zoom({ position: 'bottomright' }).addTo(map);

        /* ESRI National Geographic — atlas style */
        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/NatGeo_World_Map/MapServer/tile/{z}/{y}/{x}', {
            attribution : 'Tiles &copy; Esri &mdash; National Geographic, Esri, DeLorme, NAVTEQ, UNEP-WCMC, USGS, NASA, ESA, METI, NRCAN, GEBCO, NOAA, iPC',
            maxZoom     : 16,
        }).addTo(map);

        /* ── 2. Marker cluster ── */
        var cluster = L.markerClusterGroup({
            maxClusterRadius   : 52,
            spiderfyOnMaxZoom  : true,
            showCoverageOnHover: false,
            zoomToBoundsOnClick: true,
            iconCreateFunction : function (c) {
                var n  = c.getChildCount();
                var sz = n > 60 ? 52 : n > 25 ? 44 : 38;
                var fs = n > 99 ? 10 : 12;
                return L.divIcon({
                    className: '',
                    html: '<div style="'
                        + 'width:' + sz + 'px;height:' + sz + 'px;'
                        + 'border-radius:50%;'
                        + 'background:rgba(99,102,241,0.9);'
                        + 'border:2.5px solid rgba(165,180,252,0.9);'
                        + 'display:flex;align-items:center;justify-content:center;'
                        + 'font-weight:800;color:#fff;font-size:' + fs + 'px;'
                        + 'font-family:Outfit,sans-serif;'
                        + 'box-shadow:0 4px 20px rgba(99,102,241,0.6);'
                        + '">' + n + '</div>',
                    iconSize  : [sz, sz],
                    iconAnchor: [sz / 2, sz / 2],
                });
            },
        });

        /* ── 3. Custom pin icon ── */
        function mkIcon(region) {
            var col = portColor(region);
            return L.divIcon({
                className: '',
                html: '<div style="'
                    + 'width:22px;height:22px;'
                    + 'border-radius:50% 50% 50% 0;'
                    + 'transform:rotate(-45deg);'
                    + 'background:' + col + ';'
                    + 'border:2px solid rgba(255,255,255,0.85);'
                    + 'display:flex;align-items:center;justify-content:center;'
                    + 'box-shadow:0 2px 10px ' + col + 'aa;'
                    + '">'
                    + '<i class="fa-solid fa-anchor" style="'
                    + 'transform:rotate(45deg);font-size:7px;color:#fff;'
                    + '"></i></div>',
                iconSize  : [22, 22],
                iconAnchor: [11, 22],
                popupAnchor: [0, -24],
            });
        }

        /* ── 4. Detail drawer ── */
        var drawer  = document.getElementById('portmap-drawer');
        var curPort = null;

        function openDrawer(port) {
            curPort = port;
            var lat = parseFloat(port.latitude);
            var lng = parseFloat(port.longitude);
            var col = portColor(port.region);

            // Icon colour
            var icon = document.getElementById('pm-d-icon');
            icon.style.background = 'linear-gradient(135deg,' + col + ',' + col + 'bb)';
            icon.style.boxShadow  = '0 8px 28px ' + col + '55';

            // Text fields
            t('pm-d-name',    port.name);
            t('pm-d-code',    port.code);
            t('pm-d-region',  port.region);
            t('pm-d-country', port.country_name);
            t('pm-d-cc',      port.country_code);
            t('pm-d-lat',     lat.toFixed(6) + '°');
            t('pm-d-lng',     lng.toFixed(6) + '°');
            t('pm-d-reg2',    port.region);
            t('pm-d-coords',  lat.toFixed(6) + ',  ' + lng.toFixed(6));

            document.getElementById('pm-d-gmaps').href = 'https://www.google.com/maps?q=' + lat + ',' + lng;

            // Source badge
            var srcEl = document.getElementById('pm-d-src');
            if (port.type === 'database') {
                srcEl.innerHTML = '<span style="background:rgba(16,185,129,0.15);color:#6ee7b7;border:1px solid rgba(16,185,129,0.25);padding:2px 9px;border-radius:50px;font-size:10px;font-weight:700;">'
                    + '<i class="fa-solid fa-database" style="font-size:8px;"></i> Database</span>';
            } else {
                srcEl.innerHTML = '<span style="background:rgba(245,158,11,0.12);color:#fde68a;border:1px solid rgba(245,158,11,0.22);padding:2px 9px;border-radius:50px;font-size:10px;font-weight:700;">'
                    + '<i class="fa-solid fa-globe" style="font-size:8px;"></i> Global Data</span>';
            }

            drawer.classList.add('open');
        }

        function closeDrawer() { drawer.classList.remove('open'); curPort = null; }

        function t(id, val) {
            var el = document.getElementById(id);
            if (el) el.textContent = val;
        }

        document.getElementById('pm-close').addEventListener('click', closeDrawer);
        document.getElementById('pm-d-fly').addEventListener('click', function () {
            if (!curPort) return;
            map.flyTo([parseFloat(curPort.latitude), parseFloat(curPort.longitude)], 9, { duration: 1.5 });
        });

        /* ── 5. Build markers ── */
        var activeRegion = 'all';
        var activeSearch = '';

        function buildMarkers(ports) {
            cluster.clearLayers();
            var added = 0;

            ports.forEach(function (p) {
                var lat = parseFloat(p.latitude);
                var lng = parseFloat(p.longitude);
                if (isNaN(lat) || isNaN(lng)) return;

                var col = portColor(p.region);
                var mk  = L.marker([lat, lng], { icon: mkIcon(p.region) });

                /* Popup HTML */
                mk.bindPopup(buildPopup(p, col), { maxWidth: 230, className: 'pm-popup' });
                mk.on('click', function () { openDrawer(p); });

                cluster.addLayer(mk);
                added++;
            });

            map.addLayer(cluster);
            var el = document.getElementById('pm-count');
            if (el) el.textContent = added;
        }

        function buildPopup(p, col) {
            var lat = parseFloat(p.latitude), lng = parseFloat(p.longitude);
            return '<div style="font-family:Outfit,sans-serif;min-width:195px;">'
                + '<div style="background:' + col + ';padding:11px 13px;">'
                + '<p style="color:#fff;font-size:13.5px;font-weight:800;margin:0;line-height:1.35;">' + esc(p.name) + '</p>'
                + '<p style="color:rgba(255,255,255,0.75);font-size:10.5px;font-weight:700;letter-spacing:.08em;margin:3px 0 0;">' + esc(p.code) + '</p>'
                + '</div>'
                + '<div style="padding:11px 13px 13px;background:#fffcf2;">'
                + '<span style="display:inline-block;background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.2);border-radius:5px;padding:2px 7px;font-size:9.5px;color:#6366f1;font-weight:700;text-transform:uppercase;margin-bottom:7px;">' + esc(p.region) + '</span>'
                + '<p style="color:#3d2e0a;font-size:12.5px;font-weight:700;margin:0 0 3px;">' + esc(p.country_name) + ' <span style="color:#7a6030;opacity:.7;">(' + esc(p.country_code) + ')</span></p>'
                + '<p style="color:#a0855a;font-size:10.5px;font-family:monospace;margin:0 0 12px;">' + lat.toFixed(4) + ', ' + lng.toFixed(4) + '</p>'
                + '<button data-pid="' + esc(p.code) + '" class="pm-popup-btn" '
                + 'style="width:100%;background:' + col + ';border:none;color:#fff;border-radius:9px;padding:8px;font-family:Outfit,sans-serif;font-size:12px;font-weight:700;cursor:pointer;letter-spacing:.04em;">'
                + '<i class="fa-solid fa-circle-info" style="margin-right:4px;"></i>Detail Lengkap'
                + '</button>'
                + '</div></div>';
        }

        /* Popup button delegated click */
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.pm-popup-btn');
            if (!btn) return;
            var code = btn.getAttribute('data-pid');
            var port = ALL_PORTS.find(function (p) { return p.code === code; });
            if (port) { openDrawer(port); map.closePopup(); }
        });

        /* ── 6. Filtering ── */
        function applyFilters() {
            var list = ALL_PORTS;
            if (activeRegion !== 'all') {
                list = list.filter(function (p) { return p.region === activeRegion; });
            }
            if (activeSearch) {
                var q = activeSearch.toLowerCase();
                list = list.filter(function (p) {
                    return p.name.toLowerCase().indexOf(q) !== -1
                        || p.code.toLowerCase().indexOf(q) !== -1
                        || p.country_name.toLowerCase().indexOf(q) !== -1;
                });
            }
            closeDrawer();
            buildMarkers(list);
            if (list.length === 1) {
                map.flyTo([parseFloat(list[0].latitude), parseFloat(list[0].longitude)], 9, { duration: 1.2 });
            }
        }

        /* Region buttons */
        document.querySelectorAll('.pm-rbtn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.pm-rbtn').forEach(function (b) { b.classList.remove('on'); });
                this.classList.add('on');
                activeRegion = this.getAttribute('data-r');
                applyFilters();
            });
        });

        /* Search */
        var stimer;
        document.getElementById('pm-search').addEventListener('input', function () {
            clearTimeout(stimer);
            var v = this.value.trim();
            stimer = setTimeout(function () { activeSearch = v; applyFilters(); }, 280);
        });

        /* Close drawer on map background click */
        map.on('click', function () { closeDrawer(); });

        /* ── 7. First render ── */
        buildMarkers(ALL_PORTS);
        setTimeout(function () {
            map.invalidateSize();
            document.getElementById('portmap-loader').style.display = 'none';
        }, 500);

        /* ── 8. Legend control (bottom-left) — atlas style ── */
        var legend = L.control({ position: 'bottomleft' });
        legend.onAdd = function () {
            var div = L.DomUtil.create('div');
            div.style.cssText = 'background:rgba(255,252,242,0.97);border:1.5px solid rgba(100,80,40,0.2);border-radius:13px;padding:12px 15px;font-family:Outfit,sans-serif;pointer-events:none;box-shadow:0 4px 18px rgba(80,60,20,0.18);';
            var h = '<p style="color:rgba(80,60,20,0.5);font-size:9px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;margin:0 0 9px;">Kawasan</p>';
            Object.keys(C).forEach(function (r) {
                if (r === 'Other') return;
                h += '<div style="display:flex;align-items:center;gap:8px;margin-bottom:5px;">'
                   + '<span style="width:10px;height:10px;border-radius:50%;background:' + C[r] + ';flex-shrink:0;border:1.5px solid rgba(0,0,0,0.12);"></span>'
                   + '<span style="color:rgba(50,35,5,0.8);font-size:11px;font-weight:700;">' + r + '</span>'
                   + '</div>';
            });
            div.innerHTML = h;
            return div;
        };
        legend.addTo(map);

        /* ── 9. Stats overlay (top-right) ── */
        var statsCtrl = L.control({ position: 'topright' });
        statsCtrl.onAdd = function () {
            var div = L.DomUtil.create('div');
            var mx  = Math.max.apply(null, Object.values(REGION_COUNTS));
            div.style.cssText = 'background:rgba(6,9,22,0.94);backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,0.07);border-radius:13px;padding:13px 15px;font-family:Outfit,sans-serif;min-width:158px;pointer-events:none;';
            var h = '<p style="color:rgba(255,255,255,0.28);font-size:9px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;margin:0 0 11px;">Statistik Kawasan</p>';
            Object.keys(REGION_COUNTS).forEach(function (r) {
                var col2 = C[r] || C['Other'];
                var pct  = Math.round(REGION_COUNTS[r] / mx * 100);
                h += '<div style="margin-bottom:8px;">'
                   + '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:3px;">'
                   + '<span style="color:rgba(255,255,255,0.55);font-size:10.5px;font-weight:600;">' + r + '</span>'
                   + '<span style="color:' + col2 + ';font-size:11.5px;font-weight:800;">' + REGION_COUNTS[r] + '</span>'
                   + '</div>'
                   + '<div style="height:3px;background:rgba(255,255,255,0.05);border-radius:3px;overflow:hidden;">'
                   + '<div style="height:100%;width:' + pct + '%;background:' + col2 + ';border-radius:3px;"></div>'
                   + '</div></div>';
            });
            div.innerHTML = h;
            return div;
        };
        statsCtrl.addTo(map);

    }); /* end DOMContentLoaded */

    /* ─── XSS-safe string escape ─── */
    function esc(s) {
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

})();
</script>

@endsection
