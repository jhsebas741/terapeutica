<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('routine_steps', function (Blueprint $table) {
            $table->dropColumn('estimated_time_sec');
        });

        Schema::table('patient_settings', function (Blueprint $table) {
            $table->dropColumn('default_routine_time_sec');
        });
    }

    public function down(): void
    {
        Schema::table('routine_steps', function (Blueprint $table) {
            $table->integer('estimated_time_sec')->default(300);
        });

        Schema::table('patient_settings', function (Blueprint $table) {
            $table->integer('default_routine_time_sec')->default(300);
        });
    }
};
