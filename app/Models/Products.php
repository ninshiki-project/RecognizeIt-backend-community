<?php
/*
 * Copyright (c) 2024.
 *
 * Filename: Products.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Models;

use App\Http\Controllers\Api\Enum\ProductStatusEnum;
use App\Models\Scopes\ProductAvailableScope;
use Bavix\Wallet\Interfaces\Customer;
use Bavix\Wallet\Interfaces\ProductInterface;
use Bavix\Wallet\Traits\HasWallet;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class Products extends Model implements ProductInterface
{
    use HasFactory, HasUuids, HasWallet, SoftDeletes, Userstamps;

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'status',
        'image',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'stock' => 'integer',
        'status' => ProductStatusEnum::class,
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new ProductAvailableScope);

        static::updated(function ($product) {
            if ($product->stock === 0 && $product->status === 'available') {
                $product->status = 'unavailable';
                $product->save();
            }
        });
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeUnavailable($query)
    {
        return $query->where('status', 'unavailable');
    }

    /**
     * @return bool
     */
    public function isAvailable(): bool
    {
        return $this->stock > 0;
    }

    public function shop(): HasOne
    {
        return $this->hasOne(Shop::class, 'product_id', 'id');
    }

    public function getAmountProduct(Customer $customer): int|string
    {
        return $this->price;
    }

    public function getMetaProduct(): ?array
    {
        return [
            'title' => $this->name,
            'description' => 'Purchase of Product #'.$this->id,
        ];
    }
}
