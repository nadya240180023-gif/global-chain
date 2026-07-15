<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InflationData extends Model
{
    protected $table = 'inflation_data';

    protected $fillable = [
        'country_id',
        'year',
        'inflation_rate',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
