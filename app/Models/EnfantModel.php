<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EnfantModel extends Model
{
    use HasFactory;

    protected $table = 'enfants';

    protected $fillable = [
        'utilisateur_id',
        'prenom',
        'ecole_id',
        'sexe',
    ];

    public function ecole()
    {
        return $this->belongsTo(EcoleModel::class, 'ecole_id');
    }

    public function partages()
    {
        return $this->hasMany(MenuPartageModel::class, 'enfant_id');
    }
}