<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $fillable = [
        'supplier_id',
        'product_name',
        'quantity',
        'shipping_date',
        'estimated_arrival',
        'status',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
