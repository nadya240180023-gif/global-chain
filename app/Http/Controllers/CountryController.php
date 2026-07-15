<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Services\ApiSyncService;
use App\Services\RiskScoringEngine;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    protected $apiSync;
    protected $scoringEngine;

    public function __construct(ApiSyncService $apiSync, RiskScoringEngine $scoringEngine)
    {
        $this->apiSync = $apiSync;
        $this->scoringEngine = $scoringEngine;
    }

    /**
     * Display a listing of countries with their risk scores.
     */
    public function index()
    {
        $countries = Country::with(['riskScores' => function ($q) {
            $q->orderBy('recorded_at', 'desc');
        }])->orderBy('name')->get();

        return view('countries.index', compact('countries'));
    }

    /**
     * Trigger global sync for countries and exchange rates.
     */
    public function sync()
    {
        $countryCount = $this->apiSync->syncCountries();
        $this->apiSync->syncExchangeRates();

        if ($countryCount > 0) {
            return redirect()->route('countries.index')
                ->with('success', "Berhasil menyinkronkan {$countryCount} negara dari REST Countries API.");
        }

        return redirect()->route('countries.index')
            ->with('error', "Gagal menyinkronkan data negara.");
    }

    /**
     * Trigger dynamic details sync and recalculate risk for a single country.
     */
    public function syncSingle(Country $country)
    {
        $weatherSynced = $this->apiSync->syncWeatherData($country);
        $wbSynced = $this->apiSync->syncWorldBankData($country);
        $newsSynced = $this->apiSync->syncNews($country);
        
        $analysis = $this->scoringEngine->calculate($country);

        return redirect()->back()
            ->with('success', "Data cuaca, indikator ekonomi, dan berita untuk {$country->name} berhasil diperbarui. Skor Risiko terbaru: {$analysis->risk_score} ({$analysis->risk_level}).");
    }
}