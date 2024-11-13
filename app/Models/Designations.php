<?php
/*
 * Copyright (c) 2024.
 *
 * Filename: Designations.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Designations
 *
 * @property mixed $id
 * @property string $name
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Designations newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Designations newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Designations query()
 * @method static \Illuminate\Database\Eloquent\Builder|Designations whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Designations whereName($value)
 *
 * @mixin \Eloquent
 */
class Designations extends Model
{
    use \Sushi\Sushi;

    /**
     * @var array|string[]
     */
    protected array $schema = [
        'id' => 'integer',
        'name' => 'string',
    ];

    /**
     * @return array[]
     */
    public function getRows(): array
    {
        return [
            ['id' => 1, 'name' => 'Senior Developer'],
            ['id' => 2, 'name' => 'Human Resource'],
            ['id' => 3, 'name' => 'Project Manager'],
            ['id' => 4, 'name' => 'Delivery Manager'],
            ['id' => 5, 'name' => 'Quality Assurance'],
            ['id' => 6, 'name' => 'Junior Developer'],
            ['id' => 7, 'name' => 'CTO'],
            ['id' => 8, 'name' => 'IT Head'],
        ];
    }
}
