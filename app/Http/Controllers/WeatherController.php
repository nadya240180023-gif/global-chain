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
        $selectedCode = $request->query('country', 'ID');
        $selectedCountry = Country::where('code', strtoupper($selectedCode))->first();

        if (!$selectedCountry && $countries->isNotEmpty()) {
            $selectedCountry = $countries->first();
        }

        if ($selectedCountry) {
            // Check if weather exists, if not sync
            if (!$selectedCountry->weatherData()->exists()) {
                $this->apiSync->syncWeatherData($selectedCountry);
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
                    'rainfall' => $latest ? floatval($latest->rainfall) : null,
                    'wind_speed' => $latest ? floatval($latest->wind_speed) : null,
                    'condition' => $latest ? $latest->weather_condition : 'Unknown',
                ];
            });

        return view('weather.index', compact('countries', 'selectedCountry', 'weatherHistory', 'allCountriesWeather'));
    }
}
