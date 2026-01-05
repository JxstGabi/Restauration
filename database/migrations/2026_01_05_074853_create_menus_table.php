<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ecole_id')->constrained('ecoles')->onDelete('cascade');
            $table->date('date_menu');
            $table->integer('numero_semaine');
            $table->text('entree')->nullable();
            $table->text('plat_principal')->nullable();
            $table->text('accompagnement')->nullable();
            $table->text('dessert')->nullable();
            $table->boolean('est_vegetarien')->default(false);
            $table->boolean('est_biologique')->default(false);
            $table->json('donnees_brutes')->nullable();
            $table->timestamps();

            $table->index(['ecole_id', 'date_menu']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};