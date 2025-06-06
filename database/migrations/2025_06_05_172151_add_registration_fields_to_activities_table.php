<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->boolean('is_registration_open')->default(false)->after('is_published');
            $table->dateTime('registration_deadline_activity')->nullable()->after('is_registration_open');
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn('is_registration_open');
            $table->dropColumn('registration_deadline_activity');
        });
    }
};