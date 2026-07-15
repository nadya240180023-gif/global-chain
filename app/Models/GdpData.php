<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GdpData extends Model
{
    protected $table = 'gdp_data';

    protected $fillable = [
        'country_id',
        'year',
        'gdp_value',
        'gdp_growth',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
