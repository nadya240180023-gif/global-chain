@extends('layouts.app')

@section('title', 'Intelijen Berita')

@section('content')
<div class="space-y-8">

    {{-- Header Filter Row --}}
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h3 class="font-bold text-slate-800 text-lg">Dasbor Intelijen Berita</h3>
            <p class="text-xs text-slate-500 mt-0.5">Analisis sentimen berita logistik & perdagangan secara real-time berdasarkan leksikon kata kunci.</p>
        </div>
        <form action="{{ route('news.index') }}" method="GET" class="flex items-center gap-3">
            <label class="text-xs font-bold text-slate-500 shrink-0">Pilih Negara:</label>
            <div class="relative">
                <select name="country" onchange="this.form.submit()" class="bg-slate-50 border border-slate-300 text-slate-800 text-sm font-semibold rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 pr-8 appearance-none cursor-pointer min-w-[220px]">
                    @foreach($countries as $c)
                        <option value="{{ $c->code }}" {{ ($selectedCountry && $selectedCountry->code === $c->code) ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
                    <i class="fa-solid fa-chevron-down text-xs"></i>
                </div>
            </div>
        </form>
    </div>

    {{-- Sentiment Breakdown Summary Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm text-center">
            <p class="text-3xl font-black text-slate-800">{{ $breakdown['total'] }}</p>
            <p class="text-xs text-slate-500 font-semibold mt-1">Total Artikel</p>
        </div>
        <div class="bg-emerald-50 p-5 rounded-xl border border-emerald-100 shadow-sm text-center">
            <p class="text-3xl font-black text-emerald-600">{{ $breakdown['pos_count'] }}</p>
            <p class="text-xs text-emerald-600 font-semibold mt-1">
                <i class="fa-solid fa-arrow-trend-up mr-1"></i>Positif ({{ $breakdown['Positive'] }}%)
            </p>
        </div>
        <div class="bg-rose-50 p-5 rounded-xl border border-rose-100 shadow-sm text-center">
            <p class="text-3xl font-black text-rose-600">{{ $breakdown['neg_count'] }}</p>
            <p class="text-xs text-rose-600 font-semibold mt-1">
                <i class="fa-solid fa-arrow-trend-down mr-1"></i>Negatif ({{ $breakdown['Negative'] }}%)
            </p>
        </div>
        <div class="bg-amber-50 p-5 rounded-xl border border-amber-100 shadow-sm text-center">
            <p class="text-3xl font-black text-amber-600">{{ $breakdown['neu_count'] }}</p>
            <p class="text-xs text-amber-600 font-semibold mt-1">
                <i class="fa-solid fa-minus mr-1"></i>Netral ({{ $breakdown['Neutral'] }}%)
            </p>
        </div>
    </div>

    {{-- Sentiment Bar Chart --}}
    @if($breakdown['total'] > 0)
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <h4 class="font-bold text-slate-800 text-sm mb-4">Distribusi Sentimen Berita</h4>
        <div class="flex items-center gap-2 h-6 rounded-full overflow-hidden w-full">
            @if($breakdown['Positive'] > 0)
                <div class="h-full bg-emerald-500 rounded-l-full flex items-center justify-center text-[10px] font-bold text-white"
                     style="width: {{ $breakdown['Positive'] }}%">
                    {{ $breakdown['Positive'] }}%
                </div>
            @endif
            @if($breakdown['Neutral'] > 0)
                <div class="h-full bg-amber-400 flex items-center justify-center text-[10px] font-bold text-white"
                     style="width: {{ $breakdown['Neutral'] }}%">
                    {{ $breakdown['Neutral'] }}%
                </div>
            @endif
            @if($breakdown['Negative'] > 0)
                <div class="h-full bg-rose-500 rounded-r-full flex items-center justify-center text-[10px] font-bold text-white"
                     style="width: {{ $breakdown['Negative'] }}%">
                    {{ $breakdown['Negative'] }}%
                </div>
            @endif
        </div>
        <div class="flex gap-6 mt-3 text-xs font-semibold text-slate-500">
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span> Positif</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-400 inline-block"></span> Netral</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block"></span> Negatif</span>
        </div>
    </div>
    @endif

    {{-- News Articles List --}}
    <div class="space-y-4">
        @if($analyzedNews->isEmpty())
            <div class="bg-white rounded-xl border border-slate-200 p-12 text-center text-slate-400">
                <i class="fa-solid fa-newspaper text-5xl mb-3"></i>
                <p class="font-bold text-slate-600 text-base">Tidak ada artikel berita</p>
                <p class="text-xs mt-1">Pilih negara yang tersedia di database untuk melihat berita terkait.</p>
            </div>
        @else
            @foreach($analyzedNews as $article)
                @php
                    $sentimentColor = match($article['sentiment']) {
                        'Positive' => 'border-l-emerald-400 bg-emerald-50',
                        'Negative' => 'border-l-rose-400 bg-rose-50',
                        default => 'border-l-amber-400 bg-amber-50',
                    };
                    $sentimentBadge = match($article['sentiment']) {
                        'Positive' => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
                        'Negative' => 'bg-rose-100 text-rose-700 border border-rose-200',
                        default => 'bg-amber-100 text-amber-700 border border-amber-200',
                    };
                    $sentimentIcon = match($article['sentiment']) {
                        'Positive' => 'fa-circle-check text-emerald-500',
                        'Negative' => 'fa-circle-xmark text-rose-500',
                        default => 'fa-circle-minus text-amber-500',
                    };
                    $categoryColor = match($article['category']) {
                        'economy'   => 'bg-blue-100 text-blue-700',
                        'logistics' => 'bg-purple-100 text-purple-700',
                        'shipping'  => 'bg-cyan-100 text-cyan-700',
                        'trade'     => 'bg-orange-100 text-orange-700',
                        default     => 'bg-slate-100 text-slate-600',
                    };
                    $categoryLabel = match($article['category']) {
                        'economy'   => 'Ekonomi',
                        'logistics' => 'Logistik',
                        'shipping'  => 'Pengiriman',
                        'trade'     => 'Perdagangan',
                        default     => ucfirst($article['category']),
                    };
                @endphp
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm border-l-4 {{ $sentimentColor }} overflow-hidden">
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                    {{-- Category Badge --}}
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $categoryColor }}">
                                        {{ $categoryLabel ?? ucfirst($article['category']) }}
                                    </span>
                                    {{-- Sentiment Badge --}}
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold flex items-center gap-1 {{ $sentimentBadge }}">
                                        <i class="fa-solid {{ $sentimentIcon }}"></i>
                                        {{ $article['sentiment'] }} ({{ $article['sentiment_score'] > 0 ? '+' : '' }}{{ $article['sentiment_score'] }})
                                    </span>
                                </div>

                                <h5 class="font-bold text-slate-800 text-sm leading-snug mb-1.5">
                                    @if($article['url'] && $article['url'] !== '#')
                                        <a href="{{ $article['url'] }}" target="_blank" class="hover:text-purple-600 transition-colors">
                                            {{ $article['title'] }}
                                        </a>
                                    @else
                                        {{ $article['title'] }}
                                    @endif
                                </h5>

                                <p class="text-xs text-slate-500 leading-relaxed">{{ $article['description'] }}</p>
                            </div>

                            <div class="shrink-0 text-right">
                                <p class="text-[10px] text-slate-400 font-semibold">{{ $article['source'] }}</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">
                                    {{ \Carbon\Carbon::parse($article['published_at'])->diffForHumans() }}
                                </p>
                            </div>
                        </div>

                        {{-- Word Match Highlights --}}
                        @if(!empty($article['positive_matches']) || !empty($article['negative_matches']))
                            <div class="mt-3 pt-3 border-t border-slate-100 flex flex-wrap gap-2 items-center">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mr-1">Kata Kunci Terdeteksi:</span>
                                @foreach($article['positive_matches'] as $word)
                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded text-[10px] font-bold">+ {{ $word }}</span>
                                @endforeach
                                @foreach($article['negative_matches'] as $word)
                                    <span class="px-2 py-0.5 bg-rose-100 text-rose-700 rounded text-[10px] font-bold">− {{ $word }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
    </div>

</div>
@endsection
