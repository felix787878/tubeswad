<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ukm_ormawas', function (Blueprint $table) {
            $table->string('status')->default('pending_verification')->after('registration_deadline'); // pending_verification, approved, rejected, needs_update

            // Hapus kolom is_visible jika sudah ada dari langkah sebelumnya
            if (Schema::hasColumn('ukm_ormawas', 'is_visible')) {
                $table->dropColumn('is_visible');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ukm_ormawas', function (Blueprint $table) {
            $table->dropColumn('status');
            // Jika Anda menghapus is_visible, tambahkan kembali di sini untuk rollback yang benar
            $table->boolean('is_visible')->default(false)->after('registration_deadline');
        });
    }
};