<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\ExchangeRate;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function index(Request $request)
    {
        $countries = Country::whereNotNull('currency_code')->orderBy('name')->get();
        $selectedCode = $request->query('country', 'ID');
        $selectedCountry = Country::where('code', strtoupper($selectedCode))->first();

        if (!$selectedCountry && $countries->isNotEmpty()) {
            $selectedCountry = $countries->first();
        }

        // If no rates exist for this country, seed realistic dummy data (no API call)
        if ($selectedCountry && !$selectedCountry->exchangeRates()->exists()) {
            $this->seedDummyRates($selectedCountry);
        }

        $latestRate = $selectedCountry
            ? ExchangeRate::where('country_id', $selectedCountry->id)
                ->orderBy('recorded_at', 'desc')
                ->first()
            : null;

        $rateHistory = $selectedCountry
            ? ExchangeRate::where('country_id', $selectedCountry->id)
                ->orderBy('recorded_at', 'asc')
                ->take(30)
                ->get()
            : collect();

        return view('currency.index', compact('countries', 'selectedCountry', 'latestRate', 'rateHistory'));
    }

    /**
     * Seed realistic dummy exchange rates so the chart always has data to display.
     */
    private function seedDummyRates(Country $country): void
    {
        // Default base rates for common currency codes (per 1 USD)
        $baseRates = [
            'IDR' => 16300, 'MYR' => 4.72, 'SGD' => 1.35, 'THB' => 35.2,
            'PHP' => 57.8,  'VND' => 25400, 'JPY' => 149.5, 'CNY' => 7.24,
            'KRW' => 1330,  'INR' => 83.5, 'AUD' => 1.54, 'EUR' => 0.92,
            'GBP' => 0.79,  'BRL' => 4.97, 'MXN' => 17.1, 'ZAR' => 18.6,
            'AED' => 3.67,  'SAR' => 3.75, 'TRY' => 32.1, 'EGP' => 47.5,
        ];

        $code = strtoupper($country->currency_code);
        $baseRate = $baseRates[$code] ?? 1.0;

        // Generate 30 days of realistic-looking data with small variance
        for ($i = 29; $i >= 0; $i--) {
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
