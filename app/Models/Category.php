<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'parent_id'];

    // Relacionamento com subcategorias
    public function subcategories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Relacionamento com categoria pai
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Relacionamento com usuários
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_categories');
    }
}
