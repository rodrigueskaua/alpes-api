<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id', 'type', 'brand', 'model', 'version',
        'year_model', 'year_build', 'optionals', 'doors',
        'board', 'chassi', 'transmission', 'km', 'description',
        'sold', 'category', 'url_car', 'old_price', 'price',
        'color', 'fuel', 'photos',
    ];

    protected $casts = [
        'optionals' => 'array',
        'photos' => 'array',
        'sold' => 'boolean',
        'price' => 'decimal:2',
        'old_price' => 'decimal:2',
        'doors' => 'integer',
        'km' => 'integer',          
    ];
}