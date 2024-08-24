<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('redeems', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('shop_id');
            $table->foreignUuid('product_id');
            $table->foreignId('user_id');
            $table->enum('status', [
                'Redeemed',
                'Approved',
                'Declined',
                'Processing',
                'Waiting-Approval',
                'Canceled',
            ]);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redeems');
    }
};
