<?php

namespace App\Services;

use App\Models\Country;
use App\Models\RiskIndicator;
use App\Models\RiskAnalysis;
use App\Models\RiskScore;
use App\Models\WeatherData;
use App\Models\ExchangeRate;
use App\Models\InflationData;
use App\Models\NewsCache;
use Carbon\Carbon;

class RiskScoringEngine
{
    /**
     * Calculate and persist the risk score for a country.
     *
     * @param Country $country
     * @return RiskAnalysis
     */
    public function calculate(Country $country): RiskAnalysis
    {
        // 1. Weather Risk (0 - 100)
        $weather = WeatherData::where('country_id', $country->id)
            ->orderBy('recorded_at', 'desc')
            ->first();

        $weatherScore = 20; // baseline
        if ($weather) {
            $tempRisk = 0;
            if ($weather->temperature !== null) {
                // Optimal temperature is around 20C. Higher deviation increases risk.
                $tempRisk = min(100, max(0, abs($weather->temperature - 20) * 3));
            }

            $rainRisk = 0;
            if ($weather->rainfall !== null) {
                // Rain > 10mm starts to increase risk.
                $rainRisk = min(100, max(0, $weather->rainfall * 5));
            }

            $windRisk = 0;
            if ($weather->wind_speed !== null) {
                // Wind > 25 km/h starts to increase risk.
                $windRisk = min(100, max(0, $weather->wind_speed * 2));
            }

            $weatherScore = round(($tempRisk * 0.3) + ($rainRisk * 0.4) + ($windRisk * 0.3));
        }

        // 2. Inflation Risk (0 - 100)
        $inflation = InflationData::where('country_id', $country->id)
            ->orderBy('year', 'desc')
            ->first();

        $inflationRate = $inflation ? $inflation->inflation_rate : 2.5;
        $inflationScore = 15; // baseline
        if ($inflationRate !== null) {
            if ($inflationRate > 15) {
                $inflationScore = 95; // Hyperinflation
            } elseif ($inflationRate > 8) {
                $inflationScore = 75; // High inflation
            } elseif ($inflationRate > 4) {
                $inflationScore = 45; // Moderate-high
            } elseif ($inflationRate < 0) {
                $inflationScore = min(100, abs($inflationRate) * 15); // Deflation risk
            } else {
                $inflationScore = max(10, round($inflationRate * 8)); // Normal stable range
            }
        }

        // 3. Currency Volatility Risk (0 - 100)
        $latestRates = ExchangeRate::where('country_id', $country->id)
            ->orderBy('recorded_at', 'desc')
            ->take(10)
            ->get();

        $currencyScore = 20; // baseline
        $latestRateVal = 1.0;
        if ($latestRates->isNotEmpty()) {
            $latestRateVal = $latestRates->first()->exchange_rate;
            if ($latestRates->count() > 1) {
                $ratesArray = $latestRates->pluck('exchange_rate')->toArray();
                $mean = array_sum($ratesArray) / count($ratesArray);
                $variance = 0.0;
                foreach ($ratesArray as $r) {
                    $variance += pow($r - $mean, 2);
                }
                $stdDev = sqrt($variance / count($ratesArray));
                $volatility = $mean > 0 ? ($stdDev / $mean) * 100 : 0;
                
                // Scale volatility to a risk score
                $currencyScore = min(100, max(10, round($volatility * 15)));
            } else {
                // If only 1 record, base risk on inflation rate
                $currencyScore = min(100, max(10, round($inflationScore * 0.8)));
            }
        }

        // 4. News Sentiment Risk (0 - 100)
        $newsArticles = NewsCache::where('country_id', $country->id)
            ->orderBy('published_at', 'desc')
            ->take(10)
            ->get();

        $newsScore = 30; // baseline
        if ($newsArticles->isNotEmpty()) {
            $scores = $newsArticles->pluck('sentiment_score')->toArray();
            $avgSentiment = array_sum($scores) / count($scores); // Range -100 to +100
            
            // Convert sentiment to risk: negative sentiment increases risk
            // If avg sentiment is -100, news risk is 100. If avg is +100, news risk is 0.
            $newsScore = round(50 - ($avgSentiment / 2));
            $newsScore = min(100, max(0, $newsScore));
        }

        // 5. Total Weighted Score
        // Weight: Weather (20%), Inflation (20%), Currency (20%), News Sentiment (40%)
        $totalScore = round(
            ($weatherScore * 0.20) +
            ($inflationScore * 0.20) +
            ($currencyScore * 0.20) +
            ($newsScore * 0.40)
        );

        $riskLevel = 'Low';
        if ($totalScore >= 70) {
            $riskLevel = 'High';
        } elseif ($totalScore >= 35) {
            $riskLevel = 'Medium';
        }

        // 6. Generate Recommendation
        $recommendation = $this->getRecommendation($country->name, $riskLevel, $weatherScore, $inflationScore, $currencyScore, $newsScore);

        // 7. Save Risk Indicator Snapshot
        $indicator = RiskIndicator::create([
            'country_id' => $country->id,
            'temperature' => $weather ? $weather->temperature : null,
            'rainfall' => $weather ? $weather->rainfall : null,
            'wind_speed' => $weather ? $weather->wind_speed : null,
            'gdp' => $country->gdpData()->orderBy('year', 'desc')->first()?->gdp_value,
            'inflation' => $inflationRate,
            'exchange_rate' => $latestRateVal,
        ]);

        // 8. Save Risk Analysis
        $analysis = RiskAnalysis::create([
            'country_id' => $country->id,
            'risk_indicator_id' => $indicator->id,
            'risk_score' => $totalScore,
            'risk_level' => $riskLevel,
            'recommendation' => $recommendation,
        ]);

        // 9. Save Risk Score history
        RiskScore::create([
            'country_id' => $country->id,
            'weather_score' => $weatherScore,
            'inflation_score' => $inflationScore,
            'currency_score' => $currencyScore,
            'news_score' => $newsScore,
            'total_score' => $totalScore,
            'risk_level' => $riskLevel,
            'recorded_at' => Carbon::now(),
        ]);

        return $analysis;
    }

    /**
     * Generate dynamic guidelines and actions based on risk indicators.
     */
    private function getRecommendation(string $name, string $level, int $weather, int $inflation, int $currency, int $news): string
    {
        $bullets = [];

        if ($level === 'High') {
            $bullets[] = "Sangat Disarankan: Tunda atau batasi pengiriman baru dari {$name} sampai situasi membaik.";
            $bullets[] = "Alternatif: Cari pemasok cadangan di wilayah dengan tingkat risiko rendah.";
        } elseif ($level === 'Medium') {
            $bullets[] = "Rekomendasi: Lakukan pengawasan ketat dan tingkatkan persediaan pengaman (safety stock).";
            $bullets[] = "Keuangan: Pertimbangkan lindung nilai (hedging) valuta asing untuk mengantisipasi gejolak mata uang.";
        } else {
            $bullets[] = "Status: Wilayah operasional stabil. Lanjutkan aktivitas logistik standar.";
        }

        if ($weather > 50) {
            $bullets[] = "Logistik Cuaca: Waspadai potensi keterlambatan di pelabuhan/bandara karena cuaca ekstrem (Rain/Wind Risk: {$weather}%).";
        }
        if ($inflation > 50) {
            $bullets[] = "Biaya Produksi: Tekanan inflasi tinggi ({$inflation}%) dapat meningkatkan harga bahan baku secara signifikan.";
        }
        if ($currency > 50) {
            $bullets[] = "Risiko Keuangan: Volatilitas nilai tukar ({$currency}%) berisiko menggerus margin keuntungan transaksi ekspor-impor.";
        }
        if ($news > 50) {
            $bullets[] = "Sentimen Media: Berita geopolitik atau ekonomi belakangan didominasi sentimen negatif (Risiko Berita: {$news}%).";
        }

        return implode("\n", $bullets);
    }
}
