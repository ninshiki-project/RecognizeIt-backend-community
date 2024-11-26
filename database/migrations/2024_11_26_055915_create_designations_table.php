<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('designations', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('name');
            $table->foreignId('departments_id');
            $table->timestamps();
        });
    }
};
