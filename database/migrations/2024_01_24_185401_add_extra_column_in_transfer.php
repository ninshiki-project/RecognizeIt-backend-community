<?php
/*
 * Copyright (c) 2024.
 *
 * Filename: 2024_01_24_185401_add_extra_column_in_transfer.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

declare(strict_types=1);

use Bavix\Wallet\Models\Transfer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table($this->table(), static function (Blueprint $table) {
            $table->json('extra')
                ->nullable()
                ->after('fee');
        });
    }

    public function down(): void
    {
        Schema::dropColumns($this->table(), ['extra']);
    }

    private function table(): string
    {
        return (new Transfer)->getTable();
    }
};
