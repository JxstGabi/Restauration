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
        Schema::table('ecoles', function (Blueprint $table) {
            $table->integer('restaurant_id')->nullable()->after('id_externe');
            $table->string('restaurant_nom')->nullable()->after('restaurant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ecoles', function (Blueprint $table) {
            $table->dropColumn(['restaurant_id', 'restaurant_nom']);
        });
    }
};
