@extends('layouts.app')

@section('title', 'Edit Data Supplier')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-slate-50 rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <h3 class="font-bold text-slate-800 text-base">Edit Data Supplier: <span class="text-purple-600">{{ $supplier->kode_supplier }}</span></h3>
            <p class="text-xs text-slate-400 mt-0.5">Perbarui informasi supplier sesuai kebutuhan.</p>
        </div>
        <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PATCH')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="kode_supplier" class="text-xs font-bold text-slate-400 block mb-1.5">Kode Supplier *</label>
                    <input type="text" id="kode_supplier" name="kode_supplier" value="{{ old('kode_supplier', $supplier->kode_supplier) }}" required
                           class="w-full border @error('kode_supplier') border-rose-400 @else border-slate-300 @enderror rounded-lg text-sm p-2.5 focus:ring-purple-500 focus:border-purple-500">
                    @error('kode_supplier') <p class="text-rose-600 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="nama_supplier" class="text-xs font-bold text-slate-400 block mb-1.5">Nama Supplier *</label>
                    <input type="text" id="nama_supplier" name="nama_supplier" value="{{ old('nama_supplier', $supplier->nama_supplier) }}" required
                           class="w-full border @error('nama_supplier') border-rose-400 @else border-slate-300 @enderror rounded-lg text-sm p-2.5 focus:ring-purple-500 focus:border-purple-500">
                    @error('nama_supplier') <p class="text-rose-600 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="email" class="text-xs font-bold text-slate-400 block mb-1.5">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $supplier->email) }}"
                           class="w-full border @error('email') border-rose-400 @else border-slate-300 @enderror rounded-lg text-sm p-2.5 focus:ring-purple-500 focus:border-purple-500">
                    @error('email') <p class="text-rose-600 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="telepon" class="text-xs font-bold text-slate-400 block mb-1.5">Nomor Telepon *</label>
                    <input type="text" id="telepon" name="telepon" value="{{ old('telepon', $supplier->telepon) }}" required
                           class="w-full border @error('telepon') border-rose-400 @else border-slate-300 @enderror rounded-lg text-sm p-2.5 focus:ring-purple-500 focus:border-purple-500">
                    @error('telepon') <p class="text-rose-600 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                </div>
            </div>
            <div>
                <label for="alamat" class="text-xs font-bold text-slate-400 block mb-1.5">Alamat Lengkap</label>
                <textarea id="alamat" name="alamat" rows="3"
                          class="w-full border border-slate-300 rounded-lg text-sm p-2.5 focus:ring-purple-500 focus:border-purple-500 resize-none">{{ old('alamat', $supplier->alamat) }}</textarea>
            </div>
            <div>
                <label for="status" class="text-xs font-bold text-slate-400 block mb-1.5">Status *</label>
                <select id="status" name="status" class="bg-slate-100 border border-slate-300 text-slate-800 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5">
                    <option value="Aktif" {{ old('status', $supplier->status) === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Nonaktif" {{ old('status', $supplier->status) === 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-slate-800 font-bold px-6 py-2.5 rounded-lg text-sm transition-all cursor-pointer flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Simpan Perubahan
                </button>
                <a href="{{ route('suppliers.index') }}" class="text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 font-bold px-5 py-2.5 rounded-lg text-sm transition-all">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection