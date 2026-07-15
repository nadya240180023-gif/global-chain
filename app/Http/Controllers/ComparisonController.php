<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\RiskScore;
use App\Models\WeatherData;
use App\Models\ExchangeRate;
use App\Models\GdpData;
use App\Models\InflationData;
use Illuminate\Http\Request;

class ComparisonController extends Controller
{
    public function index(Request $request)
    {
        $countries = Country::orderBy('name')->get();

        $countryCode1 = $request->query('country1', 'DE');
        $countryCode2 = $request->query('country2', 'AU');

        $country1 = Country::where('code', strtoupper($countryCode1))->first();
        $country2 = Country::where('code', strtoupper($countryCode2))->first();

        if (!$country1 && $countries->isNotEmpty()) $country1 = $countries->first();
        if (!$country2 && $countries->count() > 1)  $country2 = $countries->get(1);

        $compareData = [];

        foreach (['country1' => $country1, 'country2' => $country2] as $key => $country) {
            if ($country) {
                $latestWeather   = WeatherData::where('country_id', $country->id)->orderBy('recorded_at', 'desc')->first();
                $latestRate      = ExchangeRate::where('country_id', $country->id)->orderBy('recorded_at', 'desc')->first();
                $latestGdp       = GdpData::where('country_id', $country->id)->orderBy('year', 'desc')->first();
                $latestInflation = InflationData::where('country_id', $country->id)->orderBy('year', 'desc')->first();
                $latestRisk      = RiskScore::where('country_id', $country->id)->orderBy('recorded_at', 'desc')->first();

                $gdpHistory       = GdpData::where('country_id', $country->id)->orderBy('year', 'asc')->take(10)->get();
                $inflationHistory = InflationData::where('country_id', $country->id)->orderBy('year', 'asc')->take(10)->get();
                $exchangeHistory  = ExchangeRate::where('country_id', $country->id)->orderBy('recorded_at', 'asc')->take(15)->get();

                $compareData[$key] = [
                    'model'            => $country,
                    'weather'          => $latestWeather,
                    'rate'             => $latestRate,
                    'gdp'              => $latestGdp,
                    'inflation'        => $latestInflation,
                    'risk'             => $latestRisk,
                    'gdp_history'      => $gdpHistory,
                    'inflation_history'=> $inflationHistory,
                    'exchange_history' => $exchangeHistory,
                ];
            } else {
                $compareData[$key] = null;
            }
        }

        return view('comparison.index', compact('countries', 'compareData', 'countryCode1', 'countryCode2'));
    }
}
