<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\ExchangeRate;
use App\Services\ApiSyncService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    protected $apiSync;

    public function __construct(ApiSyncService $apiSync)
    {
        $this->apiSync = $apiSync;
    }

    public function index(Request $request)
    {
        $countries = Country::whereNotNull('currency_code')->orderBy('name')->get();
        $selectedCode = $request->query('country', 'ID');
        $selectedCountry = Country::where('code', strtoupper($selectedCode))->first();

        if (!$selectedCountry && $countries->isNotEmpty()) {
            $selectedCountry = $countries->first();
        }

        if ($selectedCountry) {
            // Delete old exchange rates for the selected country to ensure fresh, real-world API data
            ExchangeRate::where('country_id', $selectedCountry->id)->delete();
        }

        // Trigger real-time sync of all exchange rates from open.er-api.com on load
        try {
            $this->apiSync->syncExchangeRates();
        } catch (\Exception $e) {
            logger()->error("Failed to sync exchange rates real-time: " . $e->getMessage());
        }

        // Seed the 29-day history centered around the real-world rate
        if ($selectedCountry) {
            $this->seedDummyRates($selectedCountry);
        }

        $latestRate = $selectedCountry
            ? ExchangeRate::where('country_id', $selectedCountry->id)
                ->orderBy('recorded_at', 'desc')
                ->first()
            : null;

        $rateHistory = $selectedCountry
            ? ExchangeRate::where('country_id', $selectedCountry->id)
                ->orderBy('recorded_at', 'desc')
                ->take(30)
                ->get()
                ->reverse()
                ->values()
            : collect();

        return view('currency.index', compact('countries', 'selectedCountry', 'latestRate', 'rateHistory'));
    }

    /**
     * Seed realistic dummy exchange rates so the chart always has data to display.
     */
    private function seedDummyRates(Country $country): void
    {
        $code = strtoupper($country->currency_code);
        
        // Find the latest rate synced from open.er-api.com
        $latest = ExchangeRate::where('country_id', $country->id)
            ->orderBy('recorded_at', 'desc')
            ->first();

        $baseRate = $latest ? floatval($latest->exchange_rate) : 1.0;

        // Generate 29 days of realistic-looking historical data based on this real rate
        for ($i = 29; $i >= 1; $i--) {
            $variance = $baseRate * 0.008; // 0.8% daily variance
            $rate = $baseRate + (mt_rand(-100, 100) / 100) * $variance;

            ExchangeRate::create([
                'country_id'      => $country->id,
                'base_currency'   => 'USD',
                'target_currency' => $code,
                'exchange_rate'   => round($rate, 4),
                'recorded_at'     => Carbon::now()->subDays($i),
            ]);
        }
    }
}
