<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: ProductAvailableScope.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Models\Scopes;

use App\Http\Controllers\Api\Enum\ProductStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ProductAvailableScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  Builder  $builder
     * @param  Model  $model
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where('status', ProductStatusEnum::AVAILABLE);
    }
}
