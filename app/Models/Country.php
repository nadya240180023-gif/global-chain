<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory;

   protected $fillable = [
    'name',
    'code',
    'capital',
    'region',
    'subregion',
    'currency',
    'currency_code',
    'flag',
    'population',
    'latitude',
    'longitude',
];
    public function weatherData()
    {
        return $this->hasMany(WeatherData::class);
    }

    public function exchangeRates()
    {
        return $this->hasMany(ExchangeRate::class);
    }

    public function gdpData()
    {
        return $this->hasMany(GdpData::class);
    }

    public function inflationData()
    {
        return $this->hasMany(InflationData::class);
    }

    public function riskIndicators()
    {
        return $this->hasMany(RiskIndicator::class);
    }

    public function riskScores()
    {
        return $this->hasMany(RiskScore::class);
    }

    public function newsCache()
    {
        return $this->hasMany(NewsCache::class);
    }

    public function populationData()
    {
        return $this->hasMany(PopulationData::class);
    }

    public function ports()
    {
        return $this->hasMany(Port::class);
    }
}