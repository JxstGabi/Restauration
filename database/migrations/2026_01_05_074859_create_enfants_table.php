<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('enfants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilisateur_id'); // lié à la table users si tu utilises l'auth Laravel
            $table->string('prenom');
            $table->boolean('sexe')->nullable();
            $table->foreignId('ecole_id')->constrained('ecoles')->onDelete('cascade');
            $table->timestamps();

            $table->index('utilisateur_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enfants');
    }
};