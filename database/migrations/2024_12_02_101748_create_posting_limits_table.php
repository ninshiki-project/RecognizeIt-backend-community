<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posting_limits', function (Blueprint $table) {
            $table->uuid('id');
            $table->foreignUuid('designations_id');
            $table->integer('limit');
            $table->timestamps();
        });
    }
};
