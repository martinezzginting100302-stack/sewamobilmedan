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
    Schema::create('bookings', function (Blueprint $table) {
        $table->id();

        $table->foreignId('car_id')
              ->constrained('cars')
              ->cascadeOnDelete();

        $table->string('nama_penyewa');
        $table->string('no_telepon');
        $table->string('alamat')->nullable();

        $table->date('tanggal_mulai');
        $table->date('tanggal_selesai');

        $table->integer('jumlah_hari');

        $table->decimal('harga_per_hari', 12, 2);
        $table->decimal('total_harga', 12, 2);

        $table->enum('status', [
            'menunggu',
            'dikonfirmasi',
            'selesai',
            'dibatalkan'
        ])->default('menunggu');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
