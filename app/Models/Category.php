<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'parent_id', 'description'];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with Threads
    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    // Recursive relationship to get all nested subcategories
    public function allSubcategories()
    {
        return $this->hasMany(Category::class, 'parent_id')->with('allSubcategories');
    }

    // Relationship with Subcategories
    public function subcategories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Relationship with Parent Category (for subcategories)
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
}

