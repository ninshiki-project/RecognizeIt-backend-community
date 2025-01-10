<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('redeems', function (Blueprint $table) {
            $table->after('status', function ($table) {
                $table->text('decline_reason')->nullable();
                $table->timestamp('decline_date')->nullable();
            });

        });
    }

    public function down(): void
    {
        Schema::table('redeems', function (Blueprint $table) {
            $table->dropColumn('decline_reason');
            $table->dropColumn('decline_date');
        });
    }
};
