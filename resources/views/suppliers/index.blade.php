@extends('layouts.app')

@section('title', 'Manajemen Data Supplier')

@section('content')
<div class="space-y-8">

    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-bold text-slate-800">Daftar Supplier</h3>
            <p class="text-xs text-slate-400 mt-0.5">Data pemasok dan mitra logistik yang terdaftar dalam sistem.</p>
        </div>
        <a href="{{ route('suppliers.create') }}" class="bg-purple-600 hover:bg-purple-700 text-slate-800 px-5 py-2.5 rounded-lg text-sm font-bold shadow-sm transition-all flex items-center gap-2">
            <i class="fa-solid fa-plus"></i>
            Tambah Supplier
        </a>
    </div>

    <div class="bg-slate-50 rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            @if($suppliers->isEmpty())
                <div class="text-center py-16 text-slate-400">
                    <i class="fa-solid fa-truck-field text-6xl mb-4"></i>
                    <p class="font-bold text-slate-600">Belum ada data supplier.</p>
                </div>
            @else
                <table class="table-auto w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-100 text-slate-400 font-bold text-xs uppercase border-b border-slate-200">
                            <th class="p-4 pl-6">Kode Supplier</th>
                            <th class="p-4">Nama Supplier</th>
                            <th class="p-4">Email</th>
                            <th class="p-4">Telepon</th>
                            <th class="p-4">Alamat</th>
                            <th class="p-4 text-center">Status</th>
                            <th class="p-4 pr-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-150 text-sm text-slate-700">
                        @foreach($suppliers as $supplier)
                            <tr class="hover:bg-slate-100 transition-colors">
                                <td class="p-4 pl-6 font-mono font-bold text-purple-600">{{ $supplier->kode_supplier }}</td>
                                <td class="p-4 font-bold text-slate-800">{{ $supplier->nama_supplier }}</td>
                                <td class="p-4 text-slate-400">{{ $supplier->email ?? '—' }}</td>
                                <td class="p-4 font-semibold">{{ $supplier->telepon }}</td>
                                <td class="p-4 text-slate-400 max-w-xs truncate">{{ $supplier->alamat ?? '—' }}</td>
                                <td class="p-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $supplier->status === 'Aktif' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-400' }}">
                                        {{ $supplier->status }}
                                    </span>
                                </td>
                                <td class="p-4 pr-6 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('suppliers.edit', $supplier->id) }}" class="text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg text-xs font-bold transition-all">
                                            <i class="fa-solid fa-pen text-sm"></i>
                                        </a>
                                        <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" onsubmit="return confirm('Hapus supplier {{ $supplier->nama_supplier }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-rose-500 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 p-2 rounded-lg text-xs font-bold transition-all cursor-pointer">
                                                <i class="fa-solid fa-trash text-sm"></i>
                                            </button>
                                        </form>
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