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
        Schema::table('ukm_applications', function (Blueprint $table) {
            $table->string('province')->nullable()->after('phone_contact')->comment('Provinsi dari API Lokasi');
            $table->string('city')->nullable()->after('province')->comment('Kota/Kabupaten dari API Lokasi');
            $table->string('district')->nullable()->after('city')->comment('Kecamatan dari API Lokasi');
            $table->string('village')->nullable()->after('district')->comment('Kelurahan/Desa dari API Lokasi');
            $table->text('full_address')->nullable()->after('village')->comment('Alamat lengkap (jalan, nomor rumah, dll)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ukm_applications', function (Blueprint $table) {
            $table->dropColumn('province');
            $table->dropColumn('city');
            $table->dropColumn('district');
            $table->dropColumn('village');
            $table->dropColumn('full_address');
        });
    }
};