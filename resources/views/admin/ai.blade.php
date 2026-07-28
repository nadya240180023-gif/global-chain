@extends('layouts.app')

@section('title', 'AI & Data Science Analytics')

@section('content')
<div class="space-y-8">

    {{-- ══ PAGE HEADER ══ --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-800 flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                    <i class="fa-solid fa-brain text-white text-base"></i>
                </span>
                AI & Data Science Dashboard
            </h1>
        </div>
    </div>

    {{-- ══ CORE STATS GRID ══ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-[0_8px_30px_rgba(0,0,0,0.02)] flex flex-col justify-between">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wide">Rata-rata Risiko</span>
            <div class="mt-4">
                <div class="text-3xl font-black text-slate-800">{{ round($avgRisk, 1) }}</div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-[0_8px_30px_rgba(0,0,0,0.02)] flex flex-col justify-between">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wide">Rata-rata Cuaca</span>
            <div class="mt-4">
                <div class="text-3xl font-black text-sky-500">{{ round($avgWeather, 1) }}</div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-[0_8px_30px_rgba(0,0,0,0.02)] flex flex-col justify-between">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wide">Rata-rata Inflasi</span>
            <div class="mt-4">
                <div class="text-3xl font-black text-red-500">{{ round($avgInflation, 1) }}</div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-[0_8px_30px_rgba(0,0,0,0.02)] flex flex-col justify-between">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wide">Rata-rata Valuta</span>
            <div class="mt-4">
                <div class="text-3xl font-black text-emerald-500">{{ round($avgCurrency, 1) }}</div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-[0_8px_30px_rgba(0,0,0,0.02)] flex flex-col justify-between">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wide">Rata-rata Sentimen</span>
            <div class="mt-4">
                <div class="text-3xl font-black text-purple-500">{{ round($avgNews, 1) }}</div>
            </div>
        </div>
    </div>

    {{-- ══ GLOBAL SENTIMENT ANALYSIS BREAKDOWN ══ --}}
    <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-[0_8px_30px_rgba(0,0,0,0.03)]">
        <div class="border-b border-slate-100 pb-4 mb-4">
            <h3 class="text-base font-extrabold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-indigo-500"></i>
                Sentiment Analysis Berita Global
            </h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center pt-2">
            <div class="bg-emerald-50/60 border border-emerald-100 p-6 rounded-2xl">
                <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider block mb-1">Positive</span>
                <span class="text-3xl font-black text-emerald-600">{{ $newsBreakdown['Positive'] }}%</span>
            </div>
            <div class="bg-amber-50/60 border border-amber-100 p-6 rounded-2xl">
                <span class="text-xs font-bold text-amber-600 uppercase tracking-wider block mb-1">Neutral</span>
                <span class="text-3xl font-black text-amber-600">{{ $newsBreakdown['Neutral'] }}%</span>
            </div>
            <div class="bg-rose-50/60 border border-rose-100 p-6 rounded-2xl">
                <span class="text-xs font-bold text-rose-600 uppercase tracking-wider block mb-1">Negative</span>
                <span class="text-3xl font-black text-rose-600">{{ $newsBreakdown['Negative'] }}%</span>
            </div>
        </div>
    </div>

    {{-- ══ MAIN WORKSPACE GRID ══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        {{-- Left: Sentiment Playground --}}
        <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-[0_8px_30px_rgba(0,0,0,0.03)]">
            <div class="border-b border-slate-100 pb-4 mb-6">
                <h3 class="text-base font-extrabold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-language text-purple-500"></i>
                    Playground Klasifikasi Sentimen NLP
                </h3>
            </div>

            <form action="{{ route('admin.ai.index') }}" method="GET" class="space-y-4">
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wide block mb-2">Teks Berita / Kargo</label>
                    <textarea name="sentiment_text" rows="3" class="block w-full rounded-2xl border border-slate-200 p-3.5 focus:border-indigo-500 focus:ring-indigo-500 text-sm font-semibold text-slate-700 shadow-sm" placeholder="Masukkan berita...">{{ $testText }}</textarea>
                </div>
                <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-extrabold py-3.5 px-6 rounded-2xl text-sm shadow-md shadow-indigo-500/10 transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    Klasifikasikan Sentimen
                </button>
            </form>

            @if($sentimentResult)
                @php
                    $s = $sentimentResult['sentiment'];
                    $badgeBg = 'bg-slate-50 border-slate-200 text-slate-700';
                    $badgeIcon = 'fa-circle-half-stroke';
                    if ($s === 'Positive') {
                        $badgeBg = 'bg-emerald-50 border-emerald-200 text-emerald-700';
                        $badgeIcon = 'fa-face-smile';
                    } elseif ($s === 'Negative') {
                        $badgeBg = 'bg-rose-50 border-rose-200 text-rose-700';
                        $badgeIcon = 'fa-face-frown';
                    } else {
                        $badgeBg = 'bg-amber-50 border-amber-200 text-amber-700';
                        $badgeIcon = 'fa-face-meh';
                    }
                @endphp
                <div class="mt-6 rounded-2xl border border-slate-200/80 overflow-hidden shadow-sm">
                    <!-- Title Bar -->
                    <div class="bg-slate-50/80 border-b border-slate-200/80 px-6 py-4.5 flex items-center justify-between">
                        <span class="text-sm font-extrabold text-slate-600 uppercase tracking-wide">Hasil Analisis Sentimen</span>
                        <div class="flex items-center gap-2 px-3.5 py-1.5 rounded-full border text-sm font-black uppercase tracking-wide {{ $badgeBg }}">
                            <i class="fa-solid {{ $badgeIcon }}"></i>
                            {{ $s }}
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Teks Berita -->
                        <div class="bg-slate-50 p-5 rounded-xl border border-slate-100">
                            <span class="text-xs font-black text-slate-400 uppercase tracking-widest block mb-1.5">Teks Berita</span>
                            <p class="text-base font-bold text-slate-700 italic leading-relaxed">"{{ $testText }}"</p>
                        </div>

                        <!-- Matches Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Positive Matches -->
                            <div class="border border-slate-100 rounded-xl p-5 bg-emerald-50/10">
                                <div class="flex items-center gap-2.5 mb-2.5 text-emerald-700">
                                    <i class="fa-solid fa-circle-plus text-base"></i>
                                    <span class="text-sm font-black uppercase tracking-wider">Positive Matches</span>
                                </div>
                                @if(empty($sentimentResult['positive_matches']))
                                    <span class="text-sm text-slate-400 italic">Tidak ada kata positif cocok</span>
                                @else
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        @foreach($sentimentResult['positive_matches'] as $posWord)
                                            <span class="bg-emerald-50 text-emerald-700 border border-emerald-100 text-sm px-3.5 py-1.5 rounded-xl font-extrabold">{{ $posWord }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <!-- Negative Matches -->
                            <div class="border border-slate-100 rounded-xl p-5 bg-rose-50/10">
                                <div class="flex items-center gap-2.5 mb-2.5 text-rose-700">
                                    <i class="fa-solid fa-circle-minus text-base"></i>
                                    <span class="text-sm font-black uppercase tracking-wider">Negative Matches</span>
                                </div>
                                @if(empty($sentimentResult['negative_matches']))
                                    <span class="text-sm text-slate-400 italic">Tidak ada kata negatif cocok</span>
                                @else
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        @foreach($sentimentResult['negative_matches'] as $negWord)
                                            <span class="bg-rose-50 text-rose-700 border border-rose-100 text-sm px-3.5 py-1.5 rounded-xl font-extrabold">{{ $negWord }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Score Breakdown -->
                        <div class="border-t border-slate-100 pt-5">
                            <span class="text-xs font-black text-slate-400 uppercase tracking-widest block mb-4">Rincian Skor Frekuensi</span>
                            <div class="space-y-4">
                                <!-- Pos Count -->
                                <div>
                                    <div class="flex justify-between text-sm font-black text-slate-600 mb-1.5">
                                        <span>Kata Kunci Positif</span>
                                        <span class="text-emerald-600 font-black">{{ $sentimentResult['pos_count'] }} kali</span>
                                    </div>
                                    <div class="w-full bg-slate-100 h-3 rounded-full overflow-hidden">
                                        <div class="bg-emerald-500 h-full rounded-full animate-pulse" style="width: {{ min(100, $sentimentResult['pos_count'] * 20) }}%"></div>
                                    </div>
                                </div>

                                <!-- Neg Count -->
                                <div>
                                    <div class="flex justify-between text-sm font-black text-slate-600 mb-1.5">
                                        <span>Kata Kunci Negatif</span>
                                        <span class="text-rose-600 font-black">{{ $sentimentResult['neg_count'] }} kali</span>
                                    </div>
                                    <div class="w-full bg-slate-100 h-3 rounded-full overflow-hidden">
                                        <div class="bg-rose-500 h-full rounded-full animate-pulse" style="width: {{ min(100, $sentimentResult['neg_count'] * 20) }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Right: Predictive Risk Simulator --}}
        <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-[0_8px_30px_rgba(0,0,0,0.03)] flex flex-col justify-between">
            <div>
                <div class="border-b border-slate-100 pb-4 mb-6">
                    <h3 class="text-base font-extrabold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-sliders text-indigo-500"></i>
                        Simulator Skor Risiko Rantai Pasok
                    </h3>
                </div>

                <form id="sim-form" action="{{ route('admin.ai.index') }}" method="GET" class="space-y-6">
                    <input type="hidden" name="sentiment_text" value="{{ $testText }}">
                    
                    {{-- Weather Slider --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-bold text-slate-600">Bobot Risiko Cuaca (20%)</span>
                            <span class="text-sm font-black text-indigo-600" id="sim-weather-val">50%</span>
                        </div>
                        <input type="range" name="sim_weather" min="0" max="100" value="{{ $simResult['weather_score'] ?? 50 }}" class="w-full h-2 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-indigo-600" oninput="document.getElementById('sim-weather-val').innerText = this.value + '%'">
                    </div>

                    {{-- Inflation Slider --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-bold text-slate-600">Bobot Risiko Inflasi (20%)</span>
                            <span class="text-sm font-black text-indigo-600" id="sim-inflation-val">40%</span>
                        </div>
                        <input type="range" name="sim_inflation" min="0" max="100" value="{{ $simResult['inflation_score'] ?? 40 }}" class="w-full h-2 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-indigo-600" oninput="document.getElementById('sim-inflation-val').innerText = this.value + '%'">
                    </div>

                    {{-- Currency Slider --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-bold text-slate-600">Bobot Risiko Valuta (20%)</span>
                            <span class="text-sm font-black text-indigo-600" id="sim-currency-val">30%</span>
                        </div>
                        <input type="range" name="sim_currency" min="0" max="100" value="{{ $simResult['currency_score'] ?? 30 }}" class="w-full h-2 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-indigo-600" oninput="document.getElementById('sim-currency-val').innerText = this.value + '%'">
                    </div>

                    {{-- News Slider --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-bold text-slate-600">Bobot Risiko Berita (40%)</span>
                            <span class="text-sm font-black text-indigo-600" id="sim-news-val">60%</span>
                        </div>
                        <input type="range" name="sim_news" min="0" max="100" value="{{ $simResult['news_score'] ?? 60 }}" class="w-full h-2 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-indigo-600" oninput="document.getElementById('sim-news-val').innerText = this.value + '%'">
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-extrabold py-3.5 px-6 rounded-2xl text-sm shadow-md shadow-blue-500/10 transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-calculator"></i>
                        Simulasikan Kalkulasi Risiko
                    </button>
                </form>

                @if($simResult)
                    @php
                        $rl = $simResult['risk_level'];
                        $rlc = $rl === 'High' ? 'bg-rose-50 text-rose-700 border-rose-200' : ($rl === 'Medium' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200');
                    @endphp
                    <div class="mt-6 rounded-2xl border border-slate-200/80 overflow-hidden shadow-sm">
                        <!-- Title Bar -->
                        <div class="bg-slate-50/80 border-b border-slate-200/80 px-6 py-4 flex items-center justify-between">
                            <span class="text-sm font-extrabold text-slate-600 uppercase tracking-wide">Hasil Simulasi Risiko</span>
                            <div class="flex items-center gap-2 px-3.5 py-1.5 rounded-full border text-sm font-black uppercase tracking-wide {{ $rlc }}">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                Risiko {{ $rl === 'High' ? 'Tinggi' : ($rl === 'Medium' ? 'Sedang' : 'Rendah') }}
                            </div>
                        </div>

                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-black text-slate-500">Prediksi Skor Risiko Total:</span>
                                <span class="text-lg font-black text-slate-800">{{ $simResult['total_score'] }} / 100</span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-3.5 overflow-hidden">
                                <div class="h-full rounded-full transition-all {{ $simResult['total_score'] >= 70 ? 'bg-gradient-to-r from-rose-400 to-rose-600 animate-pulse' : ($simResult['total_score'] >= 35 ? 'bg-gradient-to-r from-amber-400 to-amber-500 animate-pulse' : 'bg-gradient-to-r from-emerald-400 to-emerald-600 animate-pulse') }}" style="width: {{ $simResult['total_score'] }}%"></div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ══ DISTRIBUTION CHART CARD ══ --}}
    <div class="bg-white rounded-3xl border border-slate-200/60 shadow-[0_8px_30px_rgba(0,0,0,0.03)] overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-extrabold text-slate-800">Distribusi Tingkat Risiko Global</h3>
            </div>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
            <div class="bg-emerald-50/60 border border-emerald-100 p-6 rounded-2xl">
                <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider block mb-1">Negara Risiko Rendah</span>
                <span class="text-4xl font-black text-emerald-600">{{ $riskDistribution['Low'] }}</span>
            </div>
            <div class="bg-amber-50/60 border border-amber-100 p-6 rounded-2xl">
                <span class="text-xs font-bold text-amber-600 uppercase tracking-wider block mb-1">Negara Risiko Sedang</span>
                <span class="text-4xl font-black text-amber-600">{{ $riskDistribution['Medium'] }}</span>
            </div>
            <div class="bg-rose-50/60 border border-rose-100 p-6 rounded-2xl">
                <span class="text-xs font-bold text-rose-600 uppercase tracking-wider block mb-1">Negara Risiko Tinggi</span>
                <span class="text-4xl font-black text-rose-600">{{ $riskDistribution['High'] }}</span>
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize range sliders values on load
        document.getElementById('sim-weather-val').innerText = document.getElementsByName('sim_weather')[0].value + '%';
        document.getElementById('sim-inflation-val').innerText = document.getElementsByName('sim_inflation')[0].value + '%';
        document.getElementById('sim-currency-val').innerText = document.getElementsByName('sim_currency')[0].value + '%';
        document.getElementById('sim-news-val').innerText = document.getElementsByName('sim_news')[0].value + '%';
    });
</script>
@endsection
