<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feature_segments', function (Blueprint $table) {
            $table->id();
            $table->string('feature')->unique();
            $table->string('scope')->unique();
            $table->json('values');
            $table->boolean('active')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feature_segments');
    }
};
