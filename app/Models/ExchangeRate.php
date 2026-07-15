<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $fillable = [
        'country_id',
        'base_currency',
        'target_currency',
        'exchange_rate',
        'recorded_at',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
