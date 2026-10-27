<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warga', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('unit')->comment('e.g. Blok A-12');
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->string('alamat')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warga');
    }
};
