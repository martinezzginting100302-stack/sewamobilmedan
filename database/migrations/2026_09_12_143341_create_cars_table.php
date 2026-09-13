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
    Schema::create('cars', function (Blueprint $table) {
        $table->id();
        $table->string('nama_mobil');
        $table->string('merk');
        $table->string('tipe')->nullable();
        $table->year('tahun')->nullable();
        $table->string('plat_nomor')->unique();
        $table->decimal('harga_sewa', 12, 2);
        $table->enum('status', ['tersedia', 'disewa', 'maintenance'])
              ->default('tersedia');
        $table->text('deskripsi')->nullable();
        $table->string('foto')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
