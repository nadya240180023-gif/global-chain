@extends('layouts.app')

@section('title', 'Kelola Dataset Pelabuhan')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-3xl border border-slate-200/60 p-6 shadow-sm">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Add Port Form -->
            <div class="bg-slate-50 border border-slate-200/60 p-5 rounded-2xl">
                <h4 class="font-extrabold text-slate-800 text-sm mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-plus text-indigo-600"></i>
                    Tambah Pelabuhan Baru
                </h4>
                <form action="{{ route('admin.ports.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="text-xs font-bold text-slate-500 block mb-1.5">Nama Pelabuhan *</label>
                        <input type="text" name="name" required placeholder="Port of Rotterdam" class="w-full border border-slate-200 bg-white rounded-xl text-sm p-3 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 block mb-1.5">Kode IATA / UN/LOCODE *</label>
                        <input type="text" name="code" required placeholder="NLRTM" class="w-full border border-slate-200 bg-white rounded-xl text-sm p-3 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 block mb-1.5">Negara *</label>
                        <select name="country_id" required class="w-full border border-slate-200 bg-white rounded-xl text-sm p-3 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer">
                            <option value="">-- Pilih Negara --</option>
                            @foreach($countries as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-bold text-slate-500 block mb-1.5">Latitude *</label>
                            <input type="number" name="latitude" step="0.000001" required placeholder="51.9045" class="w-full border border-slate-200 bg-white rounded-xl text-sm p-3 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 block mb-1.5">Longitude *</label>
                            <input type="number" name="longitude" step="0.000001" required placeholder="4.1459" class="w-full border border-slate-200 bg-white rounded-xl text-sm p-3 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-slate-800 font-extrabold py-3.5 rounded-xl text-sm transition-all cursor-pointer shadow-md shadow-indigo-500/10">
                        <i class="fa-solid fa-floppy-disk mr-1"></i>
                        Simpan Pelabuhan
                    </button>
                </form>
            </div>

            <!-- Ports List -->
            <div class="lg:col-span-2 overflow-x-auto rounded-2xl border border-slate-200">
                <table class="table-auto w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-400 font-bold text-xs uppercase border-b border-slate-200">
                            <th class="p-4 pl-6">Nama Pelabuhan</th>
                            <th class="p-4">Kode</th>
                            <th class="p-4">Negara</th>
                            <th class="p-4">Koordinat</th>
                            <th class="p-4 pr-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                        @foreach($ports as $port)
                        <tr class="hover:bg-slate-50 transition-colors duration-150">
                            <td class="p-4 pl-6 font-bold text-slate-800">{{ $port->name }}</td>
                            <td class="p-4 font-mono font-bold text-indigo-600">{{ $port->code }}</td>
                            <td class="p-4 font-semibold">{{ $port->country->name }}</td>
                            <td class="p-4 text-slate-400 font-mono">{{ $port->latitude }}, {{ $port->longitude }}</td>
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
@endsection
