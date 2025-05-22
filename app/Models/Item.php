<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    // Specify which fields are mass assignable for easier bulk insert/update
    protected $fillable = [
        'name',
        'description',
        'price',
        'stripe_price_id',
        'title',
        'version',
        'description',
        'gitUrl',
        ''
    ];
}
