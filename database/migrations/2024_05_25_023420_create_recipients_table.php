<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->integer('receivable_id');
            $table->string('receivable_type');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipients');
    }
};
