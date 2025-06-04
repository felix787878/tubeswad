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
        Schema::table('articles', function (Blueprint $table) {
            // Tambahkan kolom is_published setelah kolom 'image' (atau sesuaikan posisinya)
            // Kolom ini akan bertipe boolean dan defaultnya false (tidak dipublikasikan)
            $table->boolean('is_published')->default(false)->after('image'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            //
            $table->dropColumn('is_published');
        });
    }
};
