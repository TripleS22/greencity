<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->timestamp('qr_expired_at')->nullable()->after('doitpay_reference_no')
                  ->comment('When the cached QRIS expires at Doitpay; stale cache is regenerated past this');
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropColumn('qr_expired_at');
        });
    }
};
