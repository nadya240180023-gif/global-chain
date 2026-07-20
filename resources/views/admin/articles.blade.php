@extends('layouts.app')

@section('title', 'Kelola Artikel Analisis')

@section('content')
<div class="space-y-6">
    <!-- Add Article Form -->
    <div class="bg-white rounded-3xl border border-slate-200/60 p-6 shadow-sm">
        <h4 class="font-extrabold text-slate-800 text-sm mb-4 flex items-center gap-2">
            <i class="fa-solid fa-pen-to-square text-indigo-600"></i>
            Tulis Artikel Analisis Baru
        </h4>
        <form action="{{ route('admin.articles.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="text-xs font-bold text-slate-500 block mb-1.5">Judul Artikel *</label>
                <input type="text" name="title" required placeholder="Contoh: Analisis Risiko Supply Chain Kuartal III 2025" class="w-full border border-slate-200 bg-white rounded-xl text-sm p-3 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500 block mb-1.5">Isi Artikel *</label>
                <textarea name="content" required rows="5" placeholder="Tulis analisis mendalam Anda di sini..." class="w-full border border-slate-200 bg-white rounded-xl text-sm p-3 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all resize-none"></textarea>
            </div>
            <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-slate-800 font-extrabold px-6 py-3 rounded-xl text-sm transition-all cursor-pointer shadow-md shadow-indigo-500/10">
                <i class="fa-solid fa-paper-plane mr-1"></i>
                Publikasikan Artikel
            </button>
        </form>
    </div>

    <!-- Articles List -->
    <div class="bg-white rounded-3xl border border-slate-200/60 p-6 shadow-sm">
        <h4 class="font-extrabold text-slate-800 text-sm mb-5 flex items-center gap-2">
            <i class="fa-solid fa-newspaper text-indigo-600"></i>
            Daftar Artikel Diterbitkan
        </h4>
        
        @if($articles->isEmpty())
            <div class="text-center py-12 text-slate-400">
                <i class="fa-solid fa-newspaper text-5xl mb-3 text-slate-200"></i>
                <p class="font-semibold text-slate-500">Belum ada artikel yang ditulis.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($articles as $article)
                    <div class="bg-slate-50 border border-slate-200/60 rounded-2xl p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <h5 class="font-bold text-slate-800 text-sm leading-snug">{{ $article->title }}</h5>
                                <p class="text-xs text-slate-400 mt-1.5 font-semibold">
                                    Oleh <span class="font-bold text-indigo-600">{{ $article->author->name }}</span> &bull; {{ $article->created_at->format('d M Y') }}
                                </p>
                                <p class="text-xs text-slate-600 mt-3 leading-relaxed">{{ $article->content }}</p>
                            </div>
                            <form action="{{ route('admin.articles.destroy', $article->id) }}" method="POST" onsubmit="return confirm('Hapus artikel ini?')" class="shrink-0">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-rose-500 hover:text-rose-700 p-2 hover:bg-rose-50 rounded-xl transition-all cursor-pointer border border-transparent hover:border-rose-100">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
