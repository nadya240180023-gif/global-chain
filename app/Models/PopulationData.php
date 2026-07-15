<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PopulationData extends Model
{
    use HasFactory;

    protected $table = 'population_data';

    protected $fillable = ['country_id', 'year', 'population'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
