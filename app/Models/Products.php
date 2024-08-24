<?php

namespace App\Models;

use App\Http\Controllers\Api\Enum\ProductStatusEnum;
use App\Models\Scopes\ProductAvailableScope;
use Dive\Wishlist\Contracts\Wishable;
use Dive\Wishlist\Models\Concerns\CanBeWished;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class Products extends Model implements Wishable
{
    use CanBeWished, HasFactory, HasUuids, SoftDeletes, Userstamps;

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

    public function shop(): HasOne
    {
        return $this->hasOne(Shop::class, 'product_id', 'id');
    }
}
