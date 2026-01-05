<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MenuPartageModel extends Model
{
    use HasFactory;

    protected $table = 'menus_partages';

    protected $fillable = [
        'enfant_id',
        'menu_id',
        'token_partage',
    ];

    public function enfant()
    {
        return $this->belongsTo(EnfantModel::class, 'enfant_id');
    }

    public function menu()
    {
        return $this->belongsTo(MenuModel::class, 'menu_id');
    }
}