<?php

namespace App\Services;

use App\Models\Country;
use App\Models\WeatherData;
use App\Models\ExchangeRate;
use App\Models\GdpData;
use App\Models\InflationData;
use App\Models\PopulationData;
use App\Models\NewsCache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ApiSyncService
{
    protected $sentimentAnalyzer;

    public function __construct(SentimentAnalyzer $sentimentAnalyzer)
    {
        $this->sentimentAnalyzer = $sentimentAnalyzer;
    }

    /**
     * Sync countries from REST Countries API.
     */
    public function syncCountries(): int
    {
        try {
            $response = Http::timeout(45)
                ->withoutVerifying()
                ->get('https://restcountries.com/v3.1/all');

            if ($response->failed()) {
                Log::error('REST Countries API sync failed.');
                return 0;
            }

            $countriesData = $response->json();
            $count = 0;

            foreach ($countriesData as $data) {
                $code = $data['cca2'] ?? null;
                if (!$code) continue;

                $name = $data['name']['common'] ?? null;
                if (!$name) continue;

                $capital = isset($data['capital']) && is_array($data['capital']) ? $data['capital'][0] : null;
                $region = $data['region'] ?? null;
                $subregion = $data['subregion'] ?? null;
                $flag = $data['flags']['png'] ?? ($data['flags']['svg'] ?? null);

                $currency = null;
                $currencyCode = null;
                if (isset($data['currencies']) && is_array($data['currencies'])) {
                    $currCodes = array_keys($data['currencies']);
                    if (!empty($currCodes)) {
                        $currencyCode = $currCodes[0];
                        $currency = $data['currencies'][$currencyCode]['name'] ?? null;
                    }
                }

                $lat = $data['latlng'][0] ?? null;
                $lng = $data['latlng'][1] ?? null;
                $population = $data['population'] ?? null;

                Country::updateOrCreate(
                    ['code' => strtoupper($code)],
                    [
                        'name' => $name,
                        'capital' => $capital,
                        'region' => $region,
                        'subregion' => $subregion,
                        'currency' => $currency,
                        'currency_code' => $currencyCode,
                        'flag' => $flag,
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'population' => $population,
                    ]
                );
                $count++;
            }

            return $count;
        } catch (\Exception $e) {
            Log::error('Error syncing countries: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Sync GDP and Inflation data from World Bank API.
     */
    public function syncWorldBankData(Country $country): bool
    {
        $code = strtoupper($country->code);
        $indicators = [
            'gdp' => 'NY.GDP.MKTP.CD',
            'gdp_growth' => 'NY.GDP.MKTP.KD.ZG',
            'inflation' => 'FP.CPI.TOTL.ZG',
            'population' => 'SP.POP.TOTL'
        ];

        try {
            // Fetch GDP Values (last 10 years)
            $gdpResponse = Http::timeout(30)->withoutVerifying()
                ->get("https://api.worldbank.org/v2/country/{$code}/indicator/{$indicators['gdp']}?format=json&date=2015:2025");

            $gdpData = $gdpResponse->json();
            $gdpList = $gdpData[1] ?? [];

            // Fetch GDP Growth
            $growthResponse = Http::timeout(30)->withoutVerifying()
                ->get("https://api.worldbank.org/v2/country/{$code}/indicator/{$indicators['gdp_growth']}?format=json&date=2015:2025");
            $growthData = $growthResponse->json();
            $growthList = $growthData[1] ?? [];

            $growthMap = [];
            foreach ($growthList as $item) {
                if (isset($item['date']) && isset($item['value'])) {
                    $growthMap[$item['date']] = $item['value'];
                }
            }

            foreach ($gdpList as $item) {
                if (empty($item['date']) || empty($item['value'])) continue;
                $year = intval($item['date']);
                $val = floatval($item['value']);
                $growth = $growthMap[$item['date']] ?? null;

                GdpData::updateOrCreate(
                    ['country_id' => $country->id, 'year' => $year],
                    ['gdp_value' => $val, 'gdp_growth' => $growth]
                );
            }

            // Fetch Inflation Rates
            $infResponse = Http::timeout(30)->withoutVerifying()
                ->get("https://api.worldbank.org/v2/country/{$code}/indicator/{$indicators['inflation']}?format=json&date=2015:2025");
            $infData = $infResponse->json();
            $infList = $infData[1] ?? [];

            foreach ($infList as $item) {
                if (empty($item['date']) || $item['value'] === null) continue;
                $year = intval($item['date']);
                $rate = floatval($item['value']);

                InflationData::updateOrCreate(
                    ['country_id' => $country->id, 'year' => $year],
                    ['inflation_rate' => $rate]
                );
            }

            // Fetch Population trends
            $popResponse = Http::timeout(30)->withoutVerifying()
                ->get("https://api.worldbank.org/v2/country/{$code}/indicator/{$indicators['population']}?format=json&date=2015:2025");
            $popData = $popResponse->json();
            $popList = $popData[1] ?? [];

            foreach ($popList as $item) {
                if (empty($item['date']) || empty($item['value'])) continue;
                $year = intval($item['date']);
                $val = intval($item['value']);

                PopulationData::updateOrCreate(
                    ['country_id' => $country->id, 'year' => $year],
                    ['population' => $val]
                );
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Error syncing World Bank data for {$country->name}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Sync current Exchange Rates from open.er-api.com.
     */
    public function syncExchangeRates(): bool
    {
        try {
            $response = Http::timeout(20)->withoutVerifying()
                ->get('https://open.er-api.com/v6/latest/USD');

            if ($response->failed()) return false;

            $rates = $response->json()['rates'] ?? [];
            $countries = Country::whereNotNull('currency_code')->get();

            foreach ($countries as $country) {
                $code = strtoupper($country->currency_code);
                if (isset($rates[$code])) {
                    ExchangeRate::create([
                        'country_id' => $country->id,
                        'base_currency' => 'USD',
                        'target_currency' => $code,
                        'exchange_rate' => floatval($rates[$code]),
                        'recorded_at' => Carbon::now(),
                    ]);
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error syncing Exchange Rates: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Sync Weather from Open-Meteo.
     */
    public function syncWeatherData(Country $country): bool
    {
        if ($country->latitude === null || $country->longitude === null) return false;

        try {
            $response = Http::timeout(20)->withoutVerifying()
                ->get("https://api.open-meteo.com/v1/forecast", [
                    'latitude' => $country->latitude,
                    'longitude' => $country->longitude,
                    'current' => 'temperature_2m,relative_humidity_2m,precipitation,wind_speed_10m,weather_code',
                ]);

            if ($response->failed()) return false;

            $current = $response->json()['current'] ?? [];
            if (empty($current)) return false;

            $temp = $current['temperature_2m'] ?? null;
            $humidity = $current['relative_humidity_2m'] ?? null;
            $rain = $current['precipitation'] ?? null;
            $wind = $current['wind_speed_10m'] ?? null;
            $code = $current['weather_code'] ?? 0;

            $condition = $this->mapWeatherCode($code);

            WeatherData::create([
                'country_id' => $country->id,
                'temperature' => $temp,
                'rainfall' => $rain,
                'wind_speed' => $wind,
                'humidity' => $humidity,
                'weather_condition' => $condition,
                'recorded_at' => Carbon::now(),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Error syncing weather for {$country->name}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Map WMO weather code to text.
     */
    private function mapWeatherCode(int $code): string
    {
        if ($code === 0) return 'Clear Sky';
        if ($code >= 1 && $code <= 3) return 'Partly Cloudy';
        if ($code >= 45 && $code <= 48) return 'Foggy';
        if ($code >= 51 && $code <= 57) return 'Light Drizzle';
        if ($code >= 61 && $code <= 67) return 'Rainy';
        if ($code >= 71 && $code <= 77) return 'Snowy';
        if ($code >= 80 && $code <= 82) return 'Rain Showers';
        if ($code >= 95 && $code <= 99) return 'Thunderstorm';
        return 'Cloudy';
    }

    /**
     * Sync News for a country (Logistics, Trade, Shipping, Economy).
     */
    public function syncNews(Country $country): int
    {
        $gnewsKey = env('GNEWS_API_KEY');
        $queries = ['logistics', 'trade', 'shipping', 'economy'];
        $articlesSynced = 0;

        // If no GNews API key or mock flag is active, run mock news sync.
        if (empty($gnewsKey)) {
            return $this->generateMockNews($country);
        }

        try {
            foreach ($queries as $query) {
                $response = Http::timeout(25)->withoutVerifying()
                    ->get('https://gnews.io/api/v4/search', [
                        'q' => $query,
                        'lang' => 'en',
                        'country' => strtolower($country->code),
                        'max' => 3,
                        'apikey' => $gnewsKey,
                    ]);

                if ($response->failed()) {
                    Log::warning("GNews API query failed for {$query}. Falling back to mock.");
                    return $this->generateMockNews($country);
                }

                $articles = $response->json()['articles'] ?? [];
                foreach ($articles as $art) {
                    $title = $art['title'] ?? '';
                    $description = $art['description'] ?? '';
                    $content = $art['content'] ?? '';
                    $url = $art['url'] ?? '';
                    $sourceName = $art['source']['name'] ?? 'GNews';
                    $publishedAt = isset($art['publishedAt']) ? Carbon::parse($art['publishedAt']) : Carbon::now();

                    // Analyze sentiment
                    $analysis = $this->sentimentAnalyzer->analyze($title . ' ' . $description);

                    NewsCache::create([
                        'country_id' => $country->id,
                        'title' => $title,
                        'description' => $description,
                        'content' => $content,
                        'url' => $url,
                        'source' => $sourceName,
                        'published_at' => $publishedAt,
                        'category' => $query,
                        'sentiment' => $analysis['sentiment'],
                        'sentiment_score' => $analysis['score'],
                    ]);
                    $articlesSynced++;
                }
            }

            return $articlesSynced;
        } catch (\Exception $e) {
            Log::error("Error calling GNews API. Generating mock news: " . $e->getMessage());
            return $this->generateMockNews($country);
        }
    }

    /**
     * Generate mock news for realistic demo data and offline testing.
     */
    private function generateMockNews(Country $country): int
    {
        $categories = ['logistics', 'trade', 'shipping', 'economy'];
        $count = 0;

        // News templates matching positive/negative lexicon words
        $templates = [
            'economy' => [
                [
                    'title' => 'Economic growth reported in target region due to stable trade agreements',
                    'description' => 'Local financial markets show robust recovery and profit gains this quarter, demonstrating solid expansion.',
                    'source' => 'Financial Pulse',
                    'sentiment_score' => 70, // will be analyzed by Lexicon, but we guide the content
                ],
                [
                    'title' => 'Inflation crisis deepens as consumer index jumps unexpectedly',
                    'description' => 'The economy faces severe recession fears with rising inflation rate, weak currency values, and financial drops.',
                    'source' => 'Global Monitor',
                    'sentiment_score' => -80,
                ],
                [
                    'title' => 'Stable market recovery continues with steady gains',
                    'description' => 'Industrial leaders report successful recovery operations. Analysts remain optimistic about long-term stability.',
                    'source' => 'Economy Today',
                    'sentiment_score' => 60,
                ]
            ],
            'logistics' => [
                [
                    'title' => 'Major port congestion delay resolved, restoring supply chain lines',
                    'description' => 'After days of heavy delays and vessel backlogs, terminal management reports a positive recovery and faster processing.',
                    'source' => 'Logistics Weekly',
                    'sentiment_score' => 40,
                ],
                [
                    'title' => 'Transportation strike causes massive shipping delay across border gates',
                    'description' => 'A sudden union strike has caused total disaster for cross-border logistics. Huge delays and backlogs are expected.',
                    'source' => 'Logistics Weekly',
                    'sentiment_score' => -75,
                ]
            ],
            'shipping' => [
                [
                    'title' => 'Extreme storm forces shipping lines to bypass main port terminals',
                    'description' => 'Severe storm conditions have triggered maritime warning. Ships face severe delays and route diversions.',
                    'source' => 'Maritime Gazette',
                    'sentiment_score' => -60,
                ],
                [
                    'title' => 'New container ship route improves cargo capacity and profit margins',
                    'description' => 'The new lane represents a major improvement in trade, boosting transport efficiency and ensuring stable growth.',
                    'source' => 'Shipping Global',
                    'sentiment_score' => 80,
                ]
            ],
            'trade' => [
                [
                    'title' => 'Geopolitical conflict escalates, triggering international trade sanctions',
                    'description' => 'A escalating border conflict has triggered severe trade sanctions, bringing high risk and supply drops.',
                    'source' => 'Trade Review',
                    'sentiment_score' => -90,
                ],
                [
                    'title' => 'Bilateral trade agreement brings substantial profit and market growth',
                    'description' => 'The new treaty stabilizes tariffs, promoting economic cooperation, mutual gains, and strong growth.',
                    'source' => 'Global Trade',
                    'sentiment_score' => 75,
                ]
            ]
        ];

        // Clean existing news cache for this country to avoid duplicates
        NewsCache::where('country_id', $country->id)->delete();

        foreach ($categories as $cat) {
            $items = $templates[$cat] ?? [];
            foreach ($items as $item) {
                // Add country name to make it feel localized
                $title = str_replace(['region', 'target', 'Local'], $country->name, $item['title']);
                $description = str_replace(['region', 'target', 'Local'], $country->name, $item['description']);
                
                $analysis = $this->sentimentAnalyzer->analyze($title . ' ' . $description);

                NewsCache::create([
                    'country_id' => $country->id,
                    'title' => $title,
                    'description' => $description,
                    'content' => 'Full text content representing detailed report about ' . $title,
                    'url' => '#',
                    'source' => $item['source'],
                    'published_at' => Carbon::now()->subHours(rand(1, 48)),
                    'category' => $cat,
                    'sentiment' => $analysis['sentiment'],
                    'sentiment_score' => $analysis['score'],
                ]);
                $count++;
            }
        }

        return $count;
    }
}
