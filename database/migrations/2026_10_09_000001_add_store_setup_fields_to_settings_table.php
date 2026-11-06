<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->json('shipping_policy')->nullable();
            $table->json('return_policy')->nullable();
            $table->decimal('free_shipping_minimum', 10, 2)->default(5000);
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['shipping_policy', 'return_policy', 'free_shipping_minimum']);
        });
    }
};
