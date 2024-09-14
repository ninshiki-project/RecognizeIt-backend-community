<?php
/*
 * Copyright (c) 2024.
 *
 * Filename: Provider.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $fillable = ['provider', 'provider_id', 'user_id', 'avatar'];

    protected $hidden = ['created_at', 'updated_at'];
}
