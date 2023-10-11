<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;


class Post extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = ['title', 'content', 'user_id','details'];


    /**
     * Get the reviews of the product.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category that owns the post.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_post');
    }

    public function versions()
    {
        return $this->belongsToMany(Version::class, 'version_post');
    }
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')
            ->useFallbackUrl('/images/placeholder.jpg')
            ->useFallbackPath(public_path('/images/placeholder.jpg'))
            ->singleFile();
    }

    public function registerMediaConversions(Media $media = null): void
    {
        if (env('RESIZE_IMAGE') === true) {

            $this->addMediaConversion('resized-image')
                ->width(env('IMAGE_WIDTH', 300))
                ->height(env('IMAGE_HEIGHT', 300));
        }
    }
}
