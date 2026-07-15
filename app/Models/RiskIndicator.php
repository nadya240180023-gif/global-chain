<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiskIndicator extends Model
{
    protected $fillable = [
        'country_id',
        'temperature',
        'rainfall',
        'wind_speed',
        'gdp',
        'inflation',
        'exchange_rate',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
