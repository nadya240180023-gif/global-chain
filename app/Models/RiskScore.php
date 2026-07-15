<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RiskScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'weather_score',
        'inflation_score',
        'currency_score',
        'news_score',
        'total_score',
        'risk_level',
        'recorded_at',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
