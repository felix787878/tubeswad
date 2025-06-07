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
        // Gunakan Schema::table untuk memodifikasi tabel yang sudah ada
        Schema::table('activity_attendances', function (Blueprint $table) {
            // Definisikan ulang kolom 'status' dengan daftar nilai yang baru,
            // lalu tambahkan method .change() di akhir.
            $table->enum('status', ['hadir', 'absen', 'izin', 'Terdaftar'])->default('Terdaftar')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // (Opsional) Logika untuk mengembalikan jika Anda melakukan rollback migrasi
        Schema::table('activity_attendances', function (Blueprint $table) {
            $table->enum('status', ['hadir', 'absen', 'izin'])->change();
        });
    }
};
