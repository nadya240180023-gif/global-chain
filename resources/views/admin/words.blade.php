@extends('layouts.app')

@section('title', 'Kelola Kamus Sentimen')

@section('content')
<div class="space-y-6 max-w-[1400px] mx-auto" x-data="{
    posSearch: '',
    negSearch: ''
}">
    
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left: Form to Add New Word -->
        <div class="lg:col-span-4 bg-white border border-slate-200/60 p-6 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.02)]">
            <div class="border-b border-slate-100 pb-4 mb-6">
                <h4 class="font-black text-slate-800 text-base flex items-center gap-2">
                    <i class="fa-solid fa-plus text-blue-600 text-sm"></i>
                    Tambah Kata Baru
                </h4>
                <p class="text-xs text-slate-400 font-semibold mt-1">Tambahkan kosakata baru untuk mesin analisis sentimen.</p>
            </div>
            
            <form action="{{ route('admin.words.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-black text-slate-500 block mb-2 uppercase tracking-wider">Kata (Kosakata) *</label>
                    <div class="relative">
                        <input type="text" name="word" required placeholder="Contoh: outstanding" class="w-full border border-slate-200 bg-slate-50/50 rounded-2xl text-sm py-3.5 pl-11 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-bold text-slate-700">
                        <i class="fa-solid fa-font absolute left-4 top-4 text-slate-400"></i>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-black text-slate-500 block mb-2 uppercase tracking-wider">Tipe Sentimen *</label>
                    <div class="relative">
                        <select name="type" required class="w-full border border-slate-200 bg-slate-50/50 rounded-2xl text-sm py-3.5 pl-11 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-bold text-slate-700 appearance-none">
                            <option value="positive">Positif (+)</option>
                            <option value="negative">Negatif (-)</option>
                        </select>
                        <i class="fa-solid fa-circle-half-stroke absolute left-4 top-4 text-slate-400"></i>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-4 text-slate-400 pointer-events-none"></i>
                    </div>
                </div>
                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-black py-4 rounded-2xl text-sm transition-all cursor-pointer shadow-md shadow-blue-500/20 transform hover:-translate-y-0.5">
                    <i class="fa-solid fa-save mr-2"></i>
                    Simpan Kata
                </button>
            </form>
        </div>

        <!-- Right: Lexicon Lists (Positive & Negative side-by-side as Tag Badges) -->
        <div class="lg:col-span-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Positive Words Card -->
            <div class="bg-white border border-slate-200/60 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.02)] overflow-hidden flex flex-col h-[550px]">
                <div class="p-6 border-b border-slate-100">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <h4 class="font-black text-slate-800 text-base">Kata Positif</h4>
                        </div>
                        <span class="text-xs bg-emerald-55 bg-emerald-50 text-emerald-700 border border-emerald-100 px-3 py-1 rounded-full font-extrabold tracking-wide">
                            {{ $positiveWords->count() }} kata
                        </span>
                    </div>
                    
                    {{-- Search bar for Positive Words --}}
                    <div class="relative">
                        <input type="text" x-model="posSearch" placeholder="Cari kata positif..." class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 pl-10 pr-4 text-xs font-semibold text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-6 custom-scrollbar">
                    <div class="flex flex-wrap gap-2.5">
                        @forelse($positiveWords as $pw)
                            <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 text-xs font-bold transition-all hover:bg-emerald-100/80 hover:scale-105" 
                                 x-show="posSearch === '' || '{{ $pw->word }}'.includes(posSearch.toLowerCase())">
                                <span>{{ $pw->word }}</span>
                                <form action="{{ route('admin.words.destroy', ['type' => 'positive', 'word' => $pw->word]) }}" method="POST" onsubmit="return confirm('Hapus kata \'{{ $pw->word }}\' dari kamus?')" class="inline flex items-center">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-emerald-400 hover:text-rose-600 transition-colors cursor-pointer flex items-center justify-center">
                                        <i class="fa-solid fa-circle-xmark text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        @empty
                            <div class="text-center py-12 text-slate-400 w-full">
                                <i class="fa-solid fa-circle-info text-4xl mb-2 text-slate-200"></i>
                                <p class="text-xs font-bold text-slate-500">Belum ada kata positif.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Negative Words Card -->
            <div class="bg-white border border-slate-200/60 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.02)] overflow-hidden flex flex-col h-[550px]">
                <div class="p-6 border-b border-slate-100">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse"></span>
                            <h4 class="font-black text-slate-800 text-base">Kata Negatif</h4>
                        </div>
                        <span class="text-xs bg-rose-55 bg-rose-50 text-rose-700 border border-rose-100 px-3 py-1 rounded-full font-extrabold tracking-wide">
                            {{ $negativeWords->count() }} kata
                        </span>
                    </div>
                    
                    {{-- Search bar for Negative Words --}}
                    <div class="relative">
                        <input type="text" x-model="negSearch" placeholder="Cari kata negatif..." class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 pl-10 pr-4 text-xs font-semibold text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-6 custom-scrollbar">
                    <div class="flex flex-wrap gap-2.5">
                        @forelse($negativeWords as $nw)
                            <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-full bg-rose-50 text-rose-700 border border-rose-100 text-xs font-bold transition-all hover:bg-rose-100/80 hover:scale-105" 
                                 x-show="negSearch === '' || '{{ $nw->word }}'.includes(negSearch.toLowerCase())">
                                <span>{{ $nw->word }}</span>
                                <form action="{{ route('admin.words.destroy', ['type' => 'negative', 'word' => $nw->word]) }}" method="POST" onsubmit="return confirm('Hapus kata \'{{ $nw->word }}\' dari kamus?')" class="inline flex items-center">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-rose-400 hover:text-rose-950 transition-colors cursor-pointer flex items-center justify-center">
                                        <i class="fa-solid fa-circle-xmark text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        @empty
                            <div class="text-center py-12 text-slate-400 w-full">
                                <i class="fa-solid fa-circle-info text-4xl mb-2 text-slate-200"></i>
                                <p class="text-xs font-bold text-slate-500">Belum ada kata negatif.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
