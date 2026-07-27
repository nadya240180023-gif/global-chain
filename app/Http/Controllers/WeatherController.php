<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\WeatherData;
use App\Services\ApiSyncService;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    protected $apiSync;

    public function __construct(ApiSyncService $apiSync)
    {
        $this->apiSync = $apiSync;
    }

    public function index(Request $request)
    {
        $countries = Country::orderBy('name')->get();
        
        // Fallback to default ID if request has no country parameter or country is empty
        if (!$request->has('country') || empty(trim($request->query('country')))) {
            $selectedCountry = Country::where('code', 'ID')->first();
        } else {
            $countryQuery = trim($request->query('country'));
            // Find exact code match first, then search by exact name, then partial name
            $selectedCountry = Country::where('code', strtoupper($countryQuery))
                ->orWhere('name', $countryQuery)
                ->orWhere('name', 'like', '%' . $countryQuery . '%')
                ->first();
        }

        if (!$selectedCountry && $countries->isNotEmpty()) {
            $selectedCountry = $countries->first();
        }

        if ($selectedCountry) {
            // Always sync weather on load for the selected country and all countries with ports to keep the global weather map real-time
            $countriesToSync = Country::whereHas('ports')
                ->orWhere('id', $selectedCountry->id)
                ->get();

            foreach ($countriesToSync as $c) {
                try {
                    $this->apiSync->syncWeatherData($c);
                } catch (\Exception $e) {
                    logger()->error("Failed to sync weather real-time for " . $c->name . ": " . $e->getMessage());
                }
            }
        }

        $weatherHistory = $selectedCountry 
            ? WeatherData::where('country_id', $selectedCountry->id)
                ->orderBy('recorded_at', 'desc')
                ->take(15)
                ->get()
            : collect();

        // Get latest weather for all countries for the global weather map
        $allCountriesWeather = Country::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function ($c) {
                $latest = WeatherData::where('country_id', $c->id)
                    ->orderBy('recorded_at', 'desc')
                    ->first();
                
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'code' => $c->code,
                    'latitude' => floatval($c->latitude),
                    'longitude' => floatval($c->longitude),
                    'temperature' => $latest ? floatval($latest->temperature) : null,
                    'humidity' => $latest ? floatval($latest->humidity) : null,
                    'rainfall' => $latest ? floatval($latest->rainfall) : null,
                    'wind_speed' => $latest ? floatval($latest->wind_speed) : null,
                    'condition' => $latest ? $latest->weather_condition : 'Unknown',
                ];
            });

        $latestScore = $selectedCountry 
            ? \App\Models\RiskScore::where('country_id', $selectedCountry->id)
                ->orderBy('recorded_at', 'desc')
                ->first()
            : null;

        return view('weather.index', compact('countries', 'selectedCountry', 'weatherHistory', 'allCountriesWeather', 'latestScore'));
    }
}
