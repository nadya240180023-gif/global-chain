@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
<div class="space-y-6 max-w-[1400px] mx-auto" x-data="{ 
    editOpen: false, 
    editUser: { id: '', name: '', email: '', is_admin: false } 
}">
    
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left Column: Add User Form -->
        <div class="lg:col-span-4 bg-white border border-slate-200/60 p-6 rounded-3xl shadow-[0_4px_20px_rgba(0,0,0,0.02)]">
            <div class="border-b border-slate-100 pb-4 mb-6">
                <h4 class="font-black text-slate-800 text-base flex items-center gap-2">
                    <i class="fa-solid fa-user-plus text-blue-600 text-sm"></i>
                    Tambah Pengguna Baru
                </h4>
                <p class="text-xs text-slate-400 font-semibold mt-1">Buat kredensial akun pengguna sistem baru.</p>
            </div>
            
            <form action="{{ route('admin.user.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-black text-slate-500 block mb-2 uppercase tracking-wider">Nama Lengkap *</label>
                    <div class="relative flex items-center">
                        <input type="text" name="name" required placeholder="Contoh: Nadya Zahra" class="w-full border border-slate-200 bg-slate-50/50 rounded-2xl text-sm py-3.5 pl-11 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-bold text-slate-700">
                        <i class="fa-regular fa-user absolute left-4 text-slate-400"></i>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-black text-slate-500 block mb-2 uppercase tracking-wider">Alamat Email *</label>
                    <div class="relative flex items-center">
                        <input type="email" name="email" required placeholder="Contoh: nadya@gsc.com" class="w-full border border-slate-200 bg-slate-50/50 rounded-2xl text-sm py-3.5 pl-11 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-bold text-slate-700">
                        <i class="fa-regular fa-envelope absolute left-4 text-slate-400"></i>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-black text-slate-500 block mb-2 uppercase tracking-wider">Kata Sandi (Min. 8 Karakter) *</label>
                    <div class="relative flex items-center">
                        <input type="password" name="password" minlength="8" required placeholder="Masukkan kata sandi..." class="w-full border border-slate-200 bg-slate-50/50 rounded-2xl text-sm py-3.5 pl-11 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-bold text-slate-700">
                        <i class="fa-solid fa-lock absolute left-4 text-slate-400"></i>
                    </div>
                </div>
                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-black py-4 rounded-2xl text-sm transition-all cursor-pointer shadow-md shadow-blue-500/20 transform hover:-translate-y-0.5">
                    <i class="fa-solid fa-user-check mr-2"></i>
                    Daftarkan Pengguna
                </button>
            </form>
        </div>

        <!-- Right Column: Users List -->
        <div class="lg:col-span-8 bg-white border border-slate-200/60 rounded-3xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] overflow-hidden">
            
            <!-- Table Header / Search Tool -->
            <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h4 class="font-black text-slate-800 text-base">Daftar Pengguna Terdaftar</h4>
                    <p class="text-xs text-slate-400 font-medium mt-1">Total: <span class="font-bold text-indigo-600" id="total-badge">{{ $users->count() }}</span> akun terdaftar.</p>
                </div>
                
                <!-- Search bar -->
                <div class="relative w-full sm:w-64">
                    <input type="text" id="search-users" placeholder="Cari nama atau email..." class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 pl-10 pr-4 text-xs font-semibold text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3.5 text-slate-400 text-xs"></i>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-400 font-bold text-xs uppercase border-b border-slate-200">
                            <th class="p-4 pl-6 w-12 text-center">No</th>
                            <th class="p-4">Nama Pengguna</th>
                            <th class="p-4">Email</th>
                            <th class="p-4">Bergabung</th>
                            <th class="p-4 pr-6 text-center w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm" id="users-table-body">
                        @foreach($users as $user)
                        @php
                            $isAdmin = $user->email === 'admin@gsc.com';
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-all duration-155 user-row" data-search="{{ strtolower($user->name) }} {{ strtolower($user->email) }}">
                            <td class="p-4 pl-6 font-bold text-slate-400 text-center">{{ $loop->iteration }}</td>
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    @php
                                        $avatarBg = $isAdmin ? 'from-purple-500 to-indigo-600 text-white' : 'bg-slate-100 text-slate-700 border-slate-200';
                                    @endphp
                                    <div class="bg-gradient-to-tr {{ $avatarBg }} font-black p-2 rounded-xl w-9 h-9 flex items-center justify-center text-xs uppercase shrink-0 border shadow-sm">
                                        {{ substr($user->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <span class="block text-slate-800 font-bold leading-tight">{{ $user->name }}</span>
                                        @if($isAdmin)
                                            <span class="inline-block text-[9px] bg-purple-50 text-purple-700 border border-purple-100 font-extrabold px-1.5 py-0.5 rounded-md mt-0.5">Admin Utama</span>
                                        @elseif($user->id === Auth::id())
                                            <span class="inline-block text-[9px] bg-blue-50 text-blue-700 border border-blue-100 font-extrabold px-1.5 py-0.5 rounded-md mt-0.5">Sesi Anda</span>
                                        @else
                                            <span class="inline-block text-[9px] bg-slate-50 text-slate-500 border border-slate-200/60 font-extrabold px-1.5 py-0.5 rounded-md mt-0.5">User Biasa</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 text-slate-600 font-semibold">{{ $user->email }}</td>
                            <td class="p-4 text-slate-400 font-medium">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="p-4 pr-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Edit Trigger -->
                                    <button @click="
                                        editUser.id = '{{ $user->id }}';
                                        editUser.name = '{{ addslashes($user->name) }}';
                                        editUser.email = '{{ $user->email }}';
                                        editUser.is_admin = {{ $isAdmin ? 'true' : 'false' }};
                                        editOpen = true;
                                    " class="text-indigo-600 hover:text-indigo-700 bg-indigo-50 border border-indigo-100 p-2 rounded-xl transition-all cursor-pointer hover:scale-105" title="Ubah User">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>

                                    <!-- Delete Trigger -->
                                    @if($user->id !== Auth::id() && !$isAdmin)
                                    <form action="{{ route('admin.user.toggle', $user->id) }}" method="POST" onsubmit="return confirm('Hapus akun {{ $user->name }} dari sistem?')">
                                        @csrf
                                        <button type="submit" class="text-rose-600 hover:text-rose-700 bg-rose-50/50 hover:bg-rose-100/50 border border-rose-100 p-2 rounded-xl transition-all cursor-pointer hover:scale-105">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                    @else
                                    <span class="text-slate-300 text-xs font-semibold px-2">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit User Modal (AlpineJS overlay) -->
    <div x-show="editOpen" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <div class="bg-white border border-slate-100 rounded-3xl w-full max-w-md p-6 shadow-2xl relative mx-4"
             @click.away="editOpen = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="scale-95 translate-y-4"
             x-transition:enter-end="scale-100 translate-y-0">
            
            <div class="border-b border-slate-100 pb-4 mb-5 flex items-center justify-between">
                <h3 class="text-lg font-black text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-user-pen text-blue-600"></i>
                    Ubah Informasi Pengguna
                </h3>
                <button @click="editOpen = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Form Action URL resolved dynamically with Javascript inside form submit -->
            <form :action="'/admin/users/' + editUser.id + '/update'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-black text-slate-500 block mb-1.5 uppercase tracking-wider">Nama Lengkap *</label>
                    <input type="text" name="name" required x-model="editUser.name" class="w-full border border-slate-200 bg-slate-50/50 rounded-2xl text-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-bold text-slate-700">
                </div>
                <div>
                    <label class="text-xs font-black text-slate-500 block mb-1.5 uppercase tracking-wider">Alamat Email *</label>
                    <input type="email" name="email" required x-model="editUser.email" :disabled="editUser.is_admin" class="w-full border border-slate-200 bg-slate-50/50 rounded-2xl text-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-bold text-slate-700 disabled:opacity-50 disabled:cursor-not-allowed">
                    <template x-if="editUser.is_admin">
                        <p class="text-[10px] text-amber-600 font-semibold mt-1">Email admin utama tidak dapat diubah demi keamanan.</p>
                    </template>
                </div>
                <div>
                    <label class="text-xs font-black text-slate-500 block mb-1.5 uppercase tracking-wider">Kata Sandi Baru (Kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" minlength="8" placeholder="Masukkan kata sandi baru..." class="w-full border border-slate-200 bg-slate-50/50 rounded-2xl text-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-bold text-slate-700">
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 mt-6">
                    <button type="button" @click="editOpen = false" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-black px-5 py-3 rounded-2xl text-xs transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-black px-6 py-3 rounded-2xl text-xs transition-all shadow-md shadow-blue-500/10">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById('search-users');
        const tableRows = document.querySelectorAll('.user-row');
        const totalBadge = document.getElementById('total-badge');

        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const query = e.target.value.toLowerCase().trim();
                let visibleCount = 0;
                
                tableRows.forEach(row => {
                    const searchData = row.getAttribute('data-search') || '';
                    if (searchData.includes(query)) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                if (totalBadge) {
                    totalBadge.textContent = visibleCount;
                }
            });
        }
    });
</script>
@endsection
