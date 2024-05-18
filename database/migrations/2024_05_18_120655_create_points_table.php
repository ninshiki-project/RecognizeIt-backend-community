<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->integer('points_earned')->default(0);
            $table->integer('credits')->default(30);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('points');
    }
};
