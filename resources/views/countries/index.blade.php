@extends('layouts.app')

@section('title', 'Manajemen Negara & Risiko Global')

@section('content')
<div class="space-y-6">

    <!-- Top Action Banner -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <h3 class="text-lg font-bold text-slate-800">Hub Sinkronisasi API</h3>
            <p class="text-xs text-slate-500 mt-1">Gunakan tombol berikut untuk menyegarkan database lokal dengan daftar negara dari REST Countries API.</p>
        </div>
        <a href="{{ route('countries.sync') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2.5 rounded-lg text-sm font-bold shadow-sm transition-all flex items-center gap-2">
            <i class="fa-solid fa-arrows-spin"></i>
            Sinkronkan Daftar Negara
        </a>
    </div>

    <!-- Country Listing Grid -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex items-center justify-between">
            <h4 class="font-bold text-slate-800 text-base">Database Negara Terdaftar</h4>
            <span class="text-xs bg-slate-100 text-slate-700 font-bold px-3 py-1.5 rounded-full">Total: {{ $countries->count() }} Negara</span>
        </div>

        <div class="overflow-x-auto">
            @if($countries->isEmpty())
                <div class="text-center py-16 text-slate-400">
                    <i class="fa-solid fa-flag-usa text-6xl mb-4"></i>
                    <p class="font-bold text-slate-600 text-base">Belum Ada Data Negara</p>
                    <p class="text-xs mt-1">Silakan klik tombol "Sinkronkan Daftar Negara" di atas untuk memuat data.</p>
                </div>
            @else
                <table class="table-auto w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-400 font-bold text-xs uppercase border-b border-slate-200">
                            <th class="p-4 pl-6">Bendera</th>
                            <th class="p-4">Kode</th>
                            <th class="p-4">Nama Negara</th>
                            <th class="p-4">Ibukota</th>
                            <th class="p-4">Wilayah / Sub-wilayah</th>
                            <th class="p-4 text-center">Mata Uang</th>
                            <th class="p-4 text-right">Jumlah Penduduk</th>
                            <th class="p-4 text-center">Status Risiko</th>
                            <th class="p-4 pr-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-150 text-sm text-slate-700">
                        @foreach($countries as $country)
                            @php
                                $latestScore = $country->riskScores->first();
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors">
                                <!-- Flag -->
                                <td class="p-4 pl-6">
                                    <img src="{{ $country->flag }}" class="w-9 h-6 object-cover rounded shadow-sm border border-slate-200" alt="{{ $country->name }}">
                                </td>
                                <!-- Code -->
                                <td class="p-4 font-bold text-slate-500">{{ $country->code }}</td>
                                <!-- Name -->
                                <td class="p-4 font-bold text-slate-800">
                                    <a href="{{ route('dashboard', ['country' => $country->code]) }}" class="hover:text-purple-600 transition-colors">
                                        {{ $country->name }}
                                    </a>
                                </td>
                                <!-- Capital -->
                                <td class="p-4 font-semibold text-slate-600">{{ $country->capital ?? 'N/A' }}</td>
                                <!-- Region -->
                                <td class="p-4">
                                    <span class="font-medium block text-slate-700">{{ $country->region }}</span>
                                    <span class="text-[10px] text-slate-400 block font-semibold">{{ $country->subregion }}</span>
                                </td>
                                <!-- Currency -->
                                <td class="p-4 text-center font-bold text-slate-600">
                                    {{ $country->currency_code }}
                                    <span class="text-[10px] text-slate-400 block font-normal">{{ $country->currency }}</span>
                                </td>
                                <!-- Population -->
                                <td class="p-4 text-right font-bold text-slate-600">
                                    {{ number_format($country->population) }}
                                </td>
                                <!-- Risk Level -->
                                <td class="p-4 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold
                                        {{ $latestScore?->risk_level === 'High' ? 'bg-rose-50 border border-rose-200 text-rose-700' : '' }}
                                        {{ $latestScore?->risk_level === 'Medium' ? 'bg-amber-50 border border-amber-250 text-amber-700' : '' }}
                                        {{ $latestScore?->risk_level === 'Low' ? 'bg-emerald-50 border border-emerald-250 text-emerald-700' : '' }}
                                        {{ !$latestScore ? 'bg-slate-100 text-slate-500' : '' }}
                                    ">
                                        @if($latestScore)
                                            <i class="fa-solid fa-triangle-exclamation"></i>
                                            {{ $latestScore->total_score }} ({{ $latestScore->risk_level === 'High' ? 'Tinggi' : ($latestScore->risk_level === 'Medium' ? 'Sedang' : 'Rendah') }})
                                        @else
                                            Belum Dihitung
                                        @endif
                                    </span>
                                </td>
                                <!-- Action -->
                                <td class="p-4 pr-6 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('dashboard', ['country' => $country->code]) }}" class="text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 p-2 rounded-lg text-xs font-bold transition-all" title="Lihat Dasbor">
                                            <i class="fa-solid fa-eye text-sm"></i>
                                        </a>
                                        <a href="{{ route('countries.sync_single', $country->id) }}" class="text-purple-600 hover:text-purple-800 bg-purple-50 hover:bg-purple-100 p-2 rounded-lg text-xs font-bold transition-all" title="Sinkronkan &amp; Hitung Risiko">
                                            <i class="fa-solid fa-rotate text-sm"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection