<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('price');
            $table->integer('stock')->default(1);
            $table->enum('status', ['available', 'unavailable'])->default('available');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
