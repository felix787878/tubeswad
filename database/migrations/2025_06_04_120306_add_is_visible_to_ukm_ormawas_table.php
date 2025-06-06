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
        Schema::table('ukm_ormawas', function (Blueprint $table) {
             // Tambahkan kolom is_visible setelah registration_deadline (atau sesuaikan)
            // Default false, artinya UKM/Ormawa tidak visible sampai diubah oleh pengurus
            $table->boolean('is_visible')->default(false)->after('registration_deadline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('ukm_ormawas', function (Blueprint $table) {
        //     $table->dropColumn('is_visible');
        // });
    }
};
