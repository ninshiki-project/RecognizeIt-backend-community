<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: 2024_08_23_140738_create_redeems_table.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('redeems', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('shop_id');
            $table->foreignUuid('product_id');
            $table->foreignId('user_id');
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redeems');
    }
};
