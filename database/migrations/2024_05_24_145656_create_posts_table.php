<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: 2024_05_24_145656_create_posts_table.php
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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->longText('content');
            $table->enum('attachment_type', ['image', 'gif'])->nullable();
            $table->string('cloudinary_id')->nullable();
            $table->string('attachment_url')->nullable();
            $table->enum('type', ['system', 'user']);
            $table->foreignId('posted_by');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
