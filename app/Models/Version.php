<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Version extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Get the posts for the category.
     */
    public function posts()
    {
        return $this->belongsToMany(Post::class,'version_post');
    }
}
