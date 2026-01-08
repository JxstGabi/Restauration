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
        Schema::table('enfants', function (Blueprint $table) {
            if (!Schema::hasColumn('enfants', 'sexe')) {
                $table->tinyInteger('sexe')->nullable()->comment('0: Garçon, 1: Fille')->after('prenom');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enfants', function (Blueprint $table) {
            if (Schema::hasColumn('enfants', 'sexe')) {
                $table->dropColumn('sexe');
            }
        });
    }
};
