<?php
/*
 * Copyright (c) 2024.
 *
 * Filename: 2021_11_02_202021_update_wallets_uuid_table.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

declare(strict_types=1);

use Bavix\Wallet\Internal\Service\UuidFactoryServiceInterface;
use Bavix\Wallet\Models\Wallet;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn($this->table(), 'uuid')) {
            return;
        }

        // upgrade from 6.x
        Schema::table($this->table(), static function (Blueprint $table) {
            $table->uuid('uuid')
                ->after('slug')
                ->nullable()
                ->unique();
        });

        Wallet::query()->chunk(10000, static function (Collection $wallets) {
            $wallets->each(function (Wallet $wallet) {
                $wallet->uuid = app(UuidFactoryServiceInterface::class)->uuid4();
                $wallet->save();
            });
        });

        Schema::table($this->table(), static function (Blueprint $table) {
            $table->uuid('uuid')
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table($this->table(), function (Blueprint $table) {
            if (Schema::hasColumn($this->table(), 'uuid')) {
                $table->dropIndex('wallets_uuid_unique');
                $table->dropColumn('uuid');
            }
        });
    }

    private function table(): string
    {
        return (new Wallet)->getTable();
    }
};
