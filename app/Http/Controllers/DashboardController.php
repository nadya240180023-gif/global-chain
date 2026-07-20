<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Shipment;
use App\Models\Supplier;
use App\Models\RiskAnalysis;
use App\Models\RiskScore;
use App\Models\Watchlist;
use App\Models\Port;
use App\Models\ExchangeRate;
use App\Models\GdpData;
use App\Models\InflationData;
use App\Models\NewsCache;
use App\Models\WeatherData;
use App\Services\ApiSyncService;
use App\Services\RiskScoringEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $apiSync;
    protected $scoringEngine;

    public function __construct(ApiSyncService $apiSync, RiskScoringEngine $scoringEngine)
    {
        $this->apiSync = $apiSync;
        $this->scoringEngine = $scoringEngine;
    }

    public function index(Request $request)
    {
        $countries = Country::orderBy('name')->get();
        
        // Lazy initialize: Calculate initial risk scores for all countries if none exist yet
        if (RiskScore::count() === 0 && $countries->isNotEmpty()) {
            foreach ($countries as $c) {
                $this->apiSync->syncWeatherData($c);
                $this->apiSync->syncWorldBankData($c);
                $this->apiSync->syncNews($c);
                $this->scoringEngine->calculate($c);
            }
        }

        // Select country: from request, or first country in watchlist, or default to ID (Indonesia)
        $selectedCode = $request->query('country', 'ID');
        $selectedCountry = Country::where('code', strtoupper($selectedCode))->first();

        if (!$selectedCountry && $countries->isNotEmpty()) {
            $selectedCountry = $countries->first();
        }

        $riskAnalysis = null;
        $latestScore = null;
        $shipments = collect();
        $isWatchlisted = false;

        if ($selectedCountry) {
            // Check if user has this country on watchlist
            $isWatchlisted = Watchlist::where('user_id', Auth::id())
                ->where('country_id', $selectedCountry->id)
                ->exists();

            // Lazy sync: check if we have weather and inflation data. If not, sync.
            $hasWeather = $selectedCountry->weatherData()->exists();
            $hasInflation = $selectedCountry->inflationData()->exists();
            
            if (!$hasWeather || !$hasInflation) {
                // Fetch weather, world bank, and news
                $this->apiSync->syncWeatherData($selectedCountry);
                $this->apiSync->syncWorldBankData($selectedCountry);
                $this->apiSync->syncExchangeRates(); // Sync global rates
                $this->apiSync->syncNews($selectedCountry);
            }

            // Always calculate latest risk analysis to ensure it is fresh
            $riskAnalysis = $this->scoringEngine->calculate($selectedCountry);
            $latestScore = RiskScore::where('country_id', $selectedCountry->id)
                ->orderBy('recorded_at', 'desc')
                ->first();

            // Fetch suppliers in this country (by checking if their address matches country name)
            $suppliers = Supplier::where('alamat', 'like', '%' . $selectedCountry->name . '%')
                ->orWhere('alamat', 'like', '%' . $selectedCountry->code . '%')
                ->pluck('id');

            $shipments = Shipment::whereIn('supplier_id', $suppliers)
                ->with('supplier')
                ->orderBy('shipping_date', 'desc')
                ->get();
        }

        // Get latest scores of all countries
        $latestScores = RiskScore::whereIn('id', function($query) {
            $query->selectRaw('MAX(id)')
                ->from('risk_scores')
                ->groupBy('country_id');
        })->with('country')->get();

        // Global Metrics calculation
        $globalRiskAvg = round($latestScores->avg('total_score')) ?: 38;
        $highRiskCount = $latestScores->where('risk_level', 'High')->count();
        $mediumRiskCount = $latestScores->where('risk_level', 'Medium')->count();
        $lowRiskCount = $latestScores->where('risk_level', 'Low')->count();
        $monitoredCount = Country::count();

        // Top 5 Highest Risk Countries
        $top5Countries = $latestScores->sortByDesc('total_score')->take(5);

        // Admin statistics (Users and Articles)
        $totalUsers = \App\Models\User::count();
        $totalArticles = \App\Models\Article::count();
        $totalPorts = \App\Models\Port::count();

        // Global Indicators
        $globalGdpValue = GdpData::whereIn('id', function($q) {
            $q->selectRaw('MAX(id)')->from('gdp_data')->groupBy('country_id');
        })->sum('gdp_value') ?: 104.7e12; // fallback to 104.7T

        $globalInflation = InflationData::whereIn('id', function($q) {
            $q->selectRaw('MAX(id)')->from('inflation_data')->groupBy('country_id');
        })->avg('inflation_rate') ?: 4.7;

        $globalCurrencyRate = ExchangeRate::whereIn('id', function($q) {
            $q->selectRaw('MAX(id)')->from('exchange_rates')->groupBy('country_id');
        })->avg('exchange_rate') ?: 105.8;

        $globalTrade = Shipment::count() ?: 23.4;

        // Active Extreme Weather Alert
        $extremeWeather = WeatherData::where('rainfall', '>', 15)
            ->orWhere('wind_speed', '>', 30)
            ->orderBy('recorded_at', 'desc')
            ->with('country')
            ->first();

        // Exchange Rate IDR history for USD/IDR chart
        $currencyHistory = ExchangeRate::whereHas('country', function($q) {
            $q->where('code', 'ID');
        })->orderBy('recorded_at', 'asc')->take(10)->pluck('exchange_rate')->toArray();

        if (empty($currencyHistory)) {
            $currencyHistory = [16350, 16380, 16400, 16390, 16420, 16410, 16415];
        }

        // Port map locations
        $allPorts = Port::with('country')->get();

        // Recent News Intelligence
        $recentNews = NewsCache::orderBy('published_at', 'desc')
            ->with('country')
            ->take(3)
            ->get();

        return view('dashboard', compact(
            'countries',
            'selectedCountry',
            'riskAnalysis',
            'latestScore',
            'shipments',
            'isWatchlisted',
            'latestScores',
            'globalRiskAvg',
            'highRiskCount',
            'mediumRiskCount',
            'lowRiskCount',
            'monitoredCount',
            'top5Countries',
            'globalGdpValue',
            'globalInflation',
            'globalCurrencyRate',
            'globalTrade',
            'extremeWeather',
            'currencyHistory',
            'allPorts',
            'recentNews',
            'totalUsers',
            'totalArticles',
            'totalPorts'
        ));
    }
}