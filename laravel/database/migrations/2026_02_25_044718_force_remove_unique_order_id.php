<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            // Force drop index using raw SQL for maximum reliability
            \DB::statement('ALTER TABLE pembayaran DROP INDEX pembayaran_order_id_unique');
        } catch (\Exception $e) {
            // Silently fail if index doesn't exist
        }
    }

    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->unique('order_id');
        });
    }
};
