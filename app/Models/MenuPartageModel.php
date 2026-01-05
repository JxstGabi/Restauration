<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MenuPartage extends Model
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
        return $this->belongsTo(Enfant::class, 'enfant_id');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }
}