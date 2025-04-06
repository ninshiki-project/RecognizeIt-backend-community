<?php

/*
 * Copyright (c) 2025.
 *
 * Filename: 2025_03_13_135841_create_gifts_table.php
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
        Schema::create('gifts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('by')->nullable();
            $table->foreignId('to');
            $table->string('type');
            $table->json('gift')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gifts');
    }
};
