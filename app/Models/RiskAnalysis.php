<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiskAnalysis extends Model
{
    protected $table = 'risk_analyses';

    protected $fillable = [
        'country_id',
        'risk_indicator_id',
        'risk_score',
        'risk_level',
        'recommendation',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function riskIndicator()
    {
        return $this->belongsTo(RiskIndicator::class);
    }
}
