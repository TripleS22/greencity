<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warga_id')->constrained('warga')->cascadeOnDelete();
            $table->string('bulan')->comment('e.g. Januari 2026');
            $table->decimal('jumlah', 15, 2);
            $table->enum('status', ['lunas', 'belum_lunas', 'pending'])->default('belum_lunas');
            $table->string('metode_bayar')->nullable()->comment('midtrans, manual, transfer');
            $table->string('snap_token')->nullable();
            $table->string('order_id')->nullable()->unique();
            $table->string('transaction_id')->nullable();
            $table->timestamp('tanggal_bayar')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
