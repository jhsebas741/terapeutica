<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pictograms', function (Blueprint $table) {
            $table->string('icon_text', 10)->nullable()->after('image_url');
        });
    }

    public function down(): void
    {
        Schema::table('pictograms', function (Blueprint $table) {
            $table->dropColumn('icon_text');
        });
    }
};
