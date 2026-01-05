<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menus';

    protected $fillable = [
        'ecole_id',
        'date_menu',
        'numero_semaine',
        'entree',
        'plat_principal',
        'accompagnement',
        'dessert',
        'est_vegetarien',
        'est_biologique',
        'donnees_brutes',
    ];

    protected $casts = [
        'date_menu' => 'date',
        'donnees_brutes' => 'array',
        'est_vegetarien' => 'boolean',
        'est_biologique' => 'boolean',
    ];

    public function ecole()
    {
        return $this->belongsTo(Ecole::class, 'ecole_id');
    }

    public function partages()
    {
        return $this->hasMany(MenuPartage::class, 'menu_id');
    }
}