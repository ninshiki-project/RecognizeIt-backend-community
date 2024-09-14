<?php
/*
 * Copyright (c) 2024.
 *
 * Filename: 2023_12_30_204610_soft_delete.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

declare(strict_types=1);

use Bavix\Wallet\Models\Transaction;
use Bavix\Wallet\Models\Transfer;
use Bavix\Wallet\Models\Wallet;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table((new Wallet)->getTable(), static function (Blueprint $table) {
            $table->softDeletesTz();
        });
        Schema::table((new Transfer)->getTable(), static function (Blueprint $table) {
            $table->softDeletesTz();
        });
        Schema::table((new Transaction)->getTable(), static function (Blueprint $table) {
            $table->softDeletesTz();
        });
    }

    public function down(): void
    {
        Schema::table((new Wallet)->getTable(), static function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table((new Transfer)->getTable(), static function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table((new Transaction)->getTable(), static function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
