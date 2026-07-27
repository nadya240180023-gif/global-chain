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
                <div class="mt-6 p-6 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-4 font-mono text-sm text-slate-700">
                    <div>
                        <span class="font-bold text-slate-400 uppercase text-xs tracking-wider block mb-1">Teks Berita:</span>
                        <span class="italic">"{{ $testText }}"</span>
                    </div>

                    <div class="border-t border-slate-200 pt-4">
                        <span class="font-bold text-slate-400 uppercase text-xs tracking-wider block mb-2">Maka program akan:</span>
                        <div class="space-y-1.5 ml-4">
                            <div>
                                <span class="text-slate-500">- Positive matches:</span> 
                                @if(empty($sentimentResult['positive_matches']))
                                    <span class="text-slate-400 italic">none</span>
                                @else
                                    <span class="text-emerald-600 font-extrabold">{{ implode(', ', $sentimentResult['positive_matches']) }}</span>
                                @endif
                            </div>
                            <div>
                                <span class="text-slate-500">- Negative matches:</span> 
                                @if(empty($sentimentResult['negative_matches']))
                                    <span class="text-slate-400 italic">none</span>
                                @else
                                    <span class="text-rose-600 font-extrabold">{{ implode(', ', $sentimentResult['negative_matches']) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-slate-200 pt-4 space-y-1.5">
                        <span class="font-bold text-slate-400 uppercase text-xs tracking-wider block mb-2">Hasil:</span>
                        <div class="ml-4 space-y-1.5">
                            <div>- Positive: <strong class="text-emerald-600 font-black">{{ $sentimentResult['pos_count'] }}</strong></div>
                            <div>- Negative: <strong class="text-rose-600 font-black">{{ $sentimentResult['neg_count'] }}</strong></div>
                            <div class="mt-2 text-sm">
                                - Sentiment = 
                                @php
                                    $s = $sentimentResult['sentiment'];
                                    $sc = $s === 'Positive' ? 'text-emerald-600 font-black' : ($s === 'Negative' ? 'text-rose-600 font-black' : 'text-amber-600 font-black');
                                @endphp
                                <span class="{{ $sc }} font-black">{{ $s }}</span>
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
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-bold text-slate-600">Bobot Risiko Cuaca (20%)</span>
                            <span class="text-xs font-black text-indigo-600" id="sim-weather-val">50%</span>
                        </div>
                        <input type="range" name="sim_weather" min="0" max="100" value="{{ $simResult['weather_score'] ?? 50 }}" class="w-full h-1.5 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-indigo-600" oninput="document.getElementById('sim-weather-val').innerText = this.value + '%'">
                    </div>

                    {{-- Inflation Slider --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-bold text-slate-600">Bobot Risiko Inflasi (20%)</span>
                            <span class="text-xs font-black text-indigo-600" id="sim-inflation-val">40%</span>
                        </div>
                        <input type="range" name="sim_inflation" min="0" max="100" value="{{ $simResult['inflation_score'] ?? 40 }}" class="w-full h-1.5 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-indigo-600" oninput="document.getElementById('sim-inflation-val').innerText = this.value + '%'">
                    </div>

                    {{-- Currency Slider --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-bold text-slate-600">Bobot Risiko Valuta (20%)</span>
                            <span class="text-xs font-black text-indigo-600" id="sim-currency-val">30%</span>
                        </div>
                        <input type="range" name="sim_currency" min="0" max="100" value="{{ $simResult['currency_score'] ?? 30 }}" class="w-full h-1.5 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-indigo-600" oninput="document.getElementById('sim-currency-val').innerText = this.value + '%'">
                    </div>

                    {{-- News Slider --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-bold text-slate-600">Bobot Risiko Berita (40%)</span>
                            <span class="text-xs font-black text-indigo-600" id="sim-news-val">60%</span>
                        </div>
                        <input type="range" name="sim_news" min="0" max="100" value="{{ $simResult['news_score'] ?? 60 }}" class="w-full h-1.5 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-indigo-600" oninput="document.getElementById('sim-news-val').innerText = this.value + '%'">
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-extrabold py-3.5 px-6 rounded-2xl text-sm shadow-md shadow-blue-500/10 transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-calculator"></i>
                        Simulasikan Kalkulasi Risiko
                    </button>
                </form>

                @if($simResult)
                    <div class="mt-6 p-6 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-500">Prediksi Tingkat Risiko:</span>
                            @php
                                $rl = $simResult['risk_level'];
                                $rlc = $rl === 'High' ? 'bg-rose-100 text-rose-700 border-rose-200' : ($rl === 'Medium' ? 'bg-amber-100 text-amber-700 border-amber-200' : 'bg-emerald-100 text-emerald-700 border-emerald-200');
                            @endphp
                            <span class="px-3 py-1 rounded-lg border text-xs font-black uppercase {{ $rlc }}">RISIKO {{ $rl === 'High' ? 'TINGGI' : ($rl === 'Medium' ? 'SEDANG' : 'RENDAH') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-500">Prediksi Skor Risiko Total:</span>
                            <span class="text-base font-black text-slate-800">{{ $simResult['total_score'] }} / 100</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
                            <div class="h-full rounded-full transition-all {{ $simResult['total_score'] >= 70 ? 'bg-gradient-to-r from-rose-400 to-rose-600' : ($simResult['total_score'] >= 35 ? 'bg-gradient-to-r from-amber-400 to-amber-500' : 'bg-gradient-to-r from-emerald-400 to-emerald-600') }}" style="width: {{ $simResult['total_score'] }}%"></div>
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
