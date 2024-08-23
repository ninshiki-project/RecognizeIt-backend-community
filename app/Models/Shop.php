<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shop extends Model
{
    use HasFactory, HasUuids;


    protected $fillable = [
        'product_id',
    ];

    protected $casts = [
        'id' => 'string',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Products::class, 'product_id', 'id');
    }
}
