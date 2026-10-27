<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategori_pengeluaran')->cascadeOnDelete();
            $table->string('keterangan');
            $table->decimal('jumlah', 15, 2);
            $table->date('tanggal');
            $table->text('bukti')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengeluaran');
    }
};
