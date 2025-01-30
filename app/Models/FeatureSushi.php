<?php

/*
 * Copyright (c) 2025.
 *
 * Filename: FeatureSushi.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Pennant\Feature;
use Sushi\Sushi;

class FeatureSushi extends Model
{
    use Sushi;

    public function getRows(): array
    {
        return collect(Feature::for(auth()->user())->all())
            ->map(fn ($value, $key) => [
                'id' => $key,
                'name' => $name = str(class_basename($key))->snake()->replace('_', ' ')->title()->toString(),
                'state' => $value,
                'description' => "This feature covers $name",
            ])
            ->values()
            ->toArray();
    }
}
