@extends('layouts.app')

@section('title', 'Intelijen Berita')

@section('content')

@php
    // Calculate News Alert Level dynamically based on negative news percentage
    $negPercent = $breakdown['Negative'] ?? 0;
    
    $alertLevel = 'Aman / Stabil';
    $alertColor = 'text-emerald-600';
    $alertBg = 'bg-emerald-50 border-emerald-200';
    $alertDesc = 'Mayoritas berita bernada positif/netral. Risiko gangguan rantai pasok rendah.';
    
    if ($negPercent > 45) {
        $alertLevel = 'Kritis / Bahaya';
        $alertColor = 'text-rose-600';
        $alertBg = 'bg-rose-50 border-rose-200';
        $alertDesc = 'Banyak berita negatif terdeteksi! Risiko gangguan operasional sangat tinggi.';
    } elseif ($negPercent > 20) {
        $alertLevel = 'Waspada Gangguan';
        $alertColor = 'text-amber-600';
        $alertBg = 'bg-amber-50 border-amber-200';
        $alertDesc = 'Beberapa berita negatif terdeteksi. Awasi potensi kendala di pelabuhan/ekonomi.';
    }

    // Extract all keywords to find Top Keywords (Hot Topics)
    $allKeywords = [];
    foreach($analyzedNews as $art) {
        foreach($art['positive_matches'] ?? [] as $w) {
            $allKeywords[$w] = ($allKeywords[$w] ?? 0) + 1;
        }
        foreach($art['negative_matches'] ?? [] as $w) {
            $allKeywords[$w] = ($allKeywords[$w] ?? 0) + 1;
        }
    }
    arsort($allKeywords);
    $topKeywords = array_slice($allKeywords, 0, 6, true);
@endphp

<div class="space-y-8">

    {{-- ══ PAGE HEADER ══ --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-800 flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/20">
                    <i class="fa-solid fa-newspaper text-slate-800 text-base"></i>
                </span>
                Dasbor Intelijen Berita
            </h1>
            <p class="text-sm text-slate-400 font-medium mt-2 ml-[52px]">
                Analisis sentimen waktu nyata mengenai logistik, tarif perdagangan, dan gangguan ekonomi
            </p>
        </div>

        {{-- Country Selector inside Header --}}
        <form action="{{ route('news.index') }}" method="GET" class="flex items-center gap-3">
            <span class="text-sm font-bold text-slate-400 shrink-0">Negara:</span>
            <div class="relative">
                <select name="country" onchange="this.form.submit()" class="bg-slate-100 border border-slate-200 text-slate-800 text-sm font-bold rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 pr-8 appearance-none cursor-pointer min-w-[200px] shadow-sm transition-all">
                    @foreach($countries as $c)
                        <option value="{{ $c->code }}" {{ ($selectedCountry && $selectedCountry->code === $c->code) ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                    <i class="fa-solid fa-chevron-down text-xs"></i>
                </div>
            </div>
        </form>
    </div>

    {{-- ══ SENTIMENT BREAKDOWN SUMMARY CARDS ══ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        
        {{-- Total Articles --}}
        <div class="glass-panel p-6 rounded-3xl flex flex-col justify-center gap-2 relative overflow-hidden group">
            <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Total Artikel</p>
            <p class="text-4xl font-black text-slate-800 tracking-tight">{{ $breakdown['total'] }}</p>
            <div class="absolute -right-6 -bottom-6 opacity-5 text-slate-900 text-8xl group-hover:scale-110 transition-transform duration-500">
                <i class="fa-solid fa-paste"></i>
            </div>
        </div>

        {{-- Positive News --}}
        <div class="glass-panel p-6 rounded-3xl flex flex-col justify-center gap-2 relative overflow-hidden group">
            <p class="text-xs font-bold uppercase tracking-widest text-emerald-500 flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-trend-up"></i> Positif
            </p>
            <div class="flex items-end gap-2">
                <p class="text-4xl font-black text-emerald-600 tracking-tight">{{ $breakdown['pos_count'] }}</p>
                <p class="text-sm font-semibold text-emerald-600/70 mb-1">({{ $breakdown['Positive'] }}%)</p>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-5 text-emerald-600 text-8xl group-hover:scale-110 transition-transform duration-500">
                <i class="fa-solid fa-face-smile"></i>
            </div>
        </div>

        {{-- Negatif News --}}
        <div class="glass-panel p-6 rounded-3xl flex flex-col justify-center gap-2 relative overflow-hidden group">
            <p class="text-xs font-bold uppercase tracking-widest text-rose-500 flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-trend-down"></i> Negatif
            </p>
            <div class="flex items-end gap-2">
                <p class="text-4xl font-black text-rose-600 tracking-tight">{{ $breakdown['neg_count'] }}</p>
                <p class="text-sm font-semibold text-rose-600/70 mb-1">({{ $breakdown['Negative'] }}%)</p>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-5 text-rose-600 text-8xl group-hover:scale-110 transition-transform duration-500">
                <i class="fa-solid fa-face-frown-open"></i>
            </div>
        </div>

        {{-- Neutral News --}}
        <div class="glass-panel p-6 rounded-3xl flex flex-col justify-center gap-2 relative overflow-hidden group">
            <p class="text-xs font-bold uppercase tracking-widest text-amber-500 flex items-center gap-1.5">
                <i class="fa-solid fa-minus"></i> Netral
            </p>
            <div class="flex items-end gap-2">
                <p class="text-4xl font-black text-amber-600 tracking-tight">{{ $breakdown['neu_count'] }}</p>
                <p class="text-sm font-semibold text-amber-600/70 mb-1">({{ $breakdown['Neutral'] }}%)</p>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-5 text-amber-600 text-8xl group-hover:scale-110 transition-transform duration-500">
                <i class="fa-solid fa-face-meh"></i>
            </div>
        </div>

    </div>

    {{-- ══ SENTIMENT BAR CHART ══ --}}
    @if($breakdown['total'] > 0)
    <div class="glass-panel p-8 rounded-3xl">
        <h4 class="font-extrabold text-slate-800 text-base tracking-tight mb-6">Proporsi Distribusi Sentimen Publik</h4>
        
        {{-- Thin elegant bar --}}
        <div class="flex items-center h-2.5 rounded-full overflow-hidden w-full bg-slate-100">
            @if($breakdown['Positive'] > 0)
                <div class="h-full bg-emerald-500 transition-all" style="width: {{ $breakdown['Positive'] }}%"></div>
            @endif
            @if($breakdown['Neutral'] > 0)
                <div class="h-full bg-amber-400 transition-all" style="width: {{ $breakdown['Neutral'] }}%"></div>
            @endif
            @if($breakdown['Negative'] > 0)
                <div class="h-full bg-rose-500 transition-all" style="width: {{ $breakdown['Negative'] }}%"></div>
            @endif
        </div>
        
        {{-- Legend --}}
        <div class="flex gap-8 mt-5 text-sm font-semibold text-slate-600">
            <span class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block shadow-sm"></span> 
                Positif <span class="font-bold text-slate-800 ml-1">{{ $breakdown['Positive'] }}%</span>
            </span>
            <span class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-amber-400 inline-block shadow-sm"></span> 
                Netral <span class="font-bold text-slate-800 ml-1">{{ $breakdown['Neutral'] }}%</span>
            </span>
            <span class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-rose-500 inline-block shadow-sm"></span> 
                Negatif <span class="font-bold text-slate-800 ml-1">{{ $breakdown['Negative'] }}%</span>
            </span>
        </div>
    </div>
    @endif

    {{-- ══ TWO COLUMN WORKSPACE ══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Left 2-Columns: News Articles List --}}
        <div class="lg:col-span-2">
            <h3 class="text-xl font-extrabold text-slate-800 tracking-tight mb-4">Artikel Intelijen Terkini</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @if($analyzedNews->isEmpty())
                <div class="col-span-1 md:col-span-2 glass-panel rounded-3xl p-16 text-center text-slate-400 flex flex-col items-center justify-center min-h-[300px]">
                    <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fa-solid fa-newspaper text-4xl text-slate-300"></i>
                    </div>
                    <p class="font-extrabold text-slate-600 text-base">Tidak Ada Artikel Berita</p>
                    <p class="text-sm text-slate-500 mt-2 max-w-sm mx-auto">Pilih negara lain atau sinkronisasikan ulang API global untuk melihat data berita logistik terkini.</p>
                </div>
            @else
                @foreach($analyzedNews as $article)
                    @php
                        $sentimentDot = match($article['sentiment']) {
                            'Positive' => 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]',
                            'Negative' => 'bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.5)]',
                            default => 'bg-amber-400 shadow-[0_0_8px_rgba(251,191,36,0.5)]',
                        };
                        $sentimentLabelIndo = match($article['sentiment']) {
                            'Positive' => 'Positif',
                            'Negative' => 'Negatif',
                            default => 'Netral',
                        };
                        $categoryLabel = match($article['category']) {
                            'economy'   => 'Ekonomi',
                            'logistics' => 'Logistik',
                            'shipping'  => 'Pengiriman',
                            'trade'     => 'Perdagangan',
                            default     => ucfirst($article['category']),
                        };
                    @endphp
                    <div class="glass-panel rounded-[2rem] overflow-hidden transition-all duration-300 hover:shadow-lg hover:border-slate-300/80 flex flex-col group">
                        
                        {{-- Article Image --}}
                        <div class="relative w-full h-48 bg-slate-100 overflow-hidden">
                            @if($article['url'] && $article['url'] !== '#')
                                <a href="{{ $article['url'] }}" target="_blank" class="block w-full h-full">
                            @endif
                            
                            @if(!empty($article['image_url']))
                                <img src="{{ $article['image_url'] }}" alt="News Image" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-slate-200 text-slate-400">
                                    <i class="fa-solid fa-image text-4xl"></i>
                                </div>
                            @endif
                            
                            @if($article['url'] && $article['url'] !== '#')
                                </a>
                            @endif
                            
                            <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-full flex items-center gap-2 shadow-sm pointer-events-none">
                                <div class="w-2 h-2 rounded-full {{ $sentimentDot }}"></div>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-700">{{ $categoryLabel }}</span>
                            </div>
                        </div>

                        <div class="p-6 flex flex-col flex-1">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-[10px] text-slate-500 font-bold border border-slate-200">
                                        {{ substr($article['source'], 0, 1) }}
                                    </span>
                                    <span class="text-xs font-bold text-slate-600">{{ $article['source'] }}</span>
                                </div>
                                <span class="text-xs text-slate-400 font-semibold tracking-wide">
                                    {{ \Carbon\Carbon::parse($article['published_at'])->diffForHumans() }}
                                </span>
                            </div>

                            {{-- Article Title --}}
                            <h5 class="font-extrabold text-slate-800 text-lg leading-snug mb-3 tracking-tight group-hover:text-blue-600 transition-colors line-clamp-2">
                                @if($article['url'] && $article['url'] !== '#')
                                    <a href="{{ $article['url'] }}" target="_blank" class="hover:underline">
                                        {{ $article['title'] }}
                                    </a>
                                @else
                                    {{ $article['title'] }}
                                @endif
                            </h5>

                            {{-- Description --}}
                            <p class="text-sm text-slate-500 font-medium leading-relaxed mb-6 line-clamp-3 flex-1">{{ $article['description'] }}</p>
                            
                            <div class="flex flex-col gap-4 mt-auto">
                                {{-- Word Match Highlights --}}
                                @if(!empty($article['positive_matches']) || !empty($article['negative_matches']))
                                    <div class="flex flex-wrap gap-1.5 items-center">
                                        @foreach($article['positive_matches'] as $word)
                                            <span class="px-2 py-1 bg-emerald-50 text-emerald-600 rounded text-[10px] font-bold uppercase tracking-wider">+ {{ $word }}</span>
                                        @endforeach
                                        @foreach($article['negative_matches'] as $word)
                                            <span class="px-2 py-1 bg-rose-50 text-rose-600 rounded text-[10px] font-bold uppercase tracking-wider">− {{ $word }}</span>
                                        @endforeach
                                    </div>
                                @endif
                                
                                {{-- Read More Link --}}
                                <div class="pt-4 border-t border-slate-100">
                                    @if($article['url'] && $article['url'] !== '#')
                                        <a href="{{ $article['url'] }}" target="_blank" class="inline-flex items-center gap-2 text-sm font-bold text-blue-600 hover:text-blue-700 transition-colors">
                                            Baca Selengkapnya <i class="fa-solid fa-arrow-right text-xs"></i>
                                        </a>
                                    @else
                                        <span class="inline-flex items-center gap-2 text-sm font-bold text-slate-400">
                                            Tautan Tidak Tersedia
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
            </div>
        </div>

        {{-- Right Column: Dynamic Alerts & Hot Topics --}}
        <div class="lg:col-span-1 space-y-8">
            
            {{-- Alert Status Card --}}
            <div class="glass-panel p-6 rounded-3xl border {{ $alertBg }} flex flex-col justify-between min-h-[180px] shadow-md">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block mb-1">Status Risiko Berita</span>
                    <h4 class="text-lg font-black {{ $alertColor }} flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        {{ $alertLevel }}
                    </h4>
                    <p class="text-sm text-slate-700 font-semibold leading-relaxed mt-3">
                        {{ $alertDesc }}
                    </p>
                </div>
                <div class="border-t border-slate-200/60 pt-3 mt-4 text-xs font-bold text-slate-400">
                    Berdasarkan sentimen leksikon terkini.
                </div>
            </div>

            {{-- Hot Topics / Top Keywords Card --}}
            <div class="glass-panel p-8 rounded-3xl">
                <h3 class="text-lg font-extrabold text-slate-800 mb-1 tracking-tight">Topik Hangat</h3>
                <p class="text-xs text-slate-500 font-medium leading-relaxed mb-6">Kata kunci yang paling sering muncul dari analisis berita hari ini.</p>
                
                @if(empty($topKeywords))
                    <p class="text-xs text-slate-500 font-semibold py-4 text-center">Tidak ada topik hangat saat ini.</p>
                @else
                    <div class="flex flex-wrap gap-2">
                        @foreach($topKeywords as $word => $count)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100/50 text-slate-700 border border-slate-200/50 rounded-lg text-xs font-bold hover:bg-slate-200/50 transition-colors">
                                <span class="text-slate-400">#</span>{{ $word }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Logistics Tips Card --}}
            <div class="glass-panel p-6 rounded-3xl flex flex-col justify-between border-l-4 border-l-amber-400 bg-amber-50/30">
                <div>
                    <h4 class="text-base font-extrabold text-slate-800 mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-lightbulb text-amber-500"></i> Tips Analisis Logistik
                    </h4>
                    <p class="text-sm text-slate-600 leading-relaxed font-semibold">
                        Gunakan sentimen negatif untuk mendeteksi krisis pelabuhan, pemogokan buruh, kenaikan tarif bea cukai, atau krisis bahan bakar 2-3 minggu sebelum berdampak pada pengapalan kontainer Anda.
                    </p>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
