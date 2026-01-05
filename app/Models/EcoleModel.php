<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EcoleModel extends Model
{
    use HasFactory;

    protected $table = 'ecoles';

    protected $fillable = [
        'id_externe',
        'nom',
        'type',
        'adresse',
        'ville',
        'code_postal',
        'latitude',
        'longitude',
    ];

    public function menus()
    {
        return $this->hasMany(MenuModel::class, 'ecole_id');
    }

    public function enfants()
    {
        return $this->hasMany(EnfantModel::class, 'ecole_id');
    }
}