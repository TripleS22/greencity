<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->text('qr_content')->nullable()->after('snap_token')
                  ->comment('QRIS QR content string from Doitpay');
            $table->string('doitpay_reference_no')->nullable()->after('qr_content')
                  ->comment('Doitpay reference number for query/callback');
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropColumn(['qr_content', 'doitpay_reference_no']);
        });
    }
};
