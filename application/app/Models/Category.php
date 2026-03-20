<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Solutions;

use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'depth'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Parent category
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Child categories
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Solutions belonging to this category
    public function solutions()
    {
        return $this->hasMany(Solutions::class, 'category_id');
    }
}