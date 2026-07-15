<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeatherData extends Model
{
    protected $table = 'weather_data';

    protected $fillable = [
        'country_id',
        'temperature',
        'rainfall',
        'wind_speed',
        'humidity',
        'weather_condition',
        'recorded_at',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
