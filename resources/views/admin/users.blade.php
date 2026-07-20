@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-3xl border border-slate-200/60 p-6 shadow-sm">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Add User Form -->
            <div class="bg-slate-50 border border-slate-200/60 p-5 rounded-2xl">
                <h4 class="font-extrabold text-slate-800 text-sm mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-user-plus text-indigo-600"></i>
                    Tambah Pengguna Baru
                </h4>
                <form action="{{ route('admin.user.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="text-xs font-bold text-slate-500 block mb-1.5">Nama Lengkap *</label>
                        <input type="text" name="name" required placeholder="Contoh: Nadya Zahra" class="w-full border border-slate-200 bg-white rounded-xl text-sm p-3 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 block mb-1.5">Alamat Email *</label>
                        <input type="email" name="email" required placeholder="Contoh: nadya@gsc.com" class="w-full border border-slate-200 bg-white rounded-xl text-sm p-3 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 block mb-1.5">Kata Sandi (Min. 8 Karakter) *</label>
                        <input type="password" name="password" minlength="8" required placeholder="Masukkan kata sandi..." class="w-full border border-slate-200 bg-white rounded-xl text-sm p-3 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                    </div>
                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-slate-800 font-extrabold py-3.5 rounded-xl text-sm transition-all cursor-pointer shadow-md shadow-indigo-500/10">
                        <i class="fa-solid fa-user-check mr-1"></i>
                        Daftarkan Pengguna
                    </button>
                </form>
            </div>

            <!-- Users List -->
            <div class="lg:col-span-2 overflow-x-auto rounded-2xl border border-slate-200">
                <table class="table-auto w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-400 font-bold text-xs uppercase border-b border-slate-200">
                            <th class="p-4 pl-6">#</th>
                            <th class="p-4">Nama Pengguna</th>
                            <th class="p-4">Email</th>
                            <th class="p-4">Bergabung Sejak</th>
                            <th class="p-4 pr-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach($users as $user)
                        <tr class="hover:bg-slate-50 transition-colors duration-150">
                            <td class="p-4 pl-6 font-bold text-slate-400">{{ $loop->iteration }}</td>
                            <td class="p-4 font-bold text-slate-800 flex items-center gap-3">
                                <div class="bg-indigo-50 text-indigo-700 font-black p-2 rounded-xl w-9 h-9 flex items-center justify-center text-xs uppercase shrink-0 border border-indigo-100">
                                    {{ substr($user->name, 0, 2) }}
                                </div>
                                <div>
                                    <span class="block text-slate-800 font-bold">{{ $user->name }}</span>
                                    @if($user->id === Auth::id())
                                        <span class="inline-block text-[9px] bg-purple-50 text-purple-700 font-black px-1.5 py-0.5 rounded border border-purple-100 uppercase tracking-wide mt-0.5">Sesi Anda</span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-4 text-slate-600 font-medium">{{ $user->email }}</td>
                            <td class="p-4 text-slate-400 font-semibold">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="p-4 pr-6 text-center">
                                @if($user->id !== Auth::id())
                                <form action="{{ route('admin.user.toggle', $user->id) }}" method="POST" onsubmit="return confirm('Hapus pengguna {{ $user->name }}?')">
                                    @csrf
                                    <button type="submit" class="text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 px-3.5 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer border border-rose-100">
                                        <i class="fa-solid fa-trash mr-1"></i> Hapus
                                    </button>
                                </form>
                                @else
                                <span class="text-slate-300 text-xs font-semibold">—</span>
                                @endif
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
