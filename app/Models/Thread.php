<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'user_id', 'title', 'content', 'approved', 'reported'];

    /**
     * Define the relationship between the thread and the category it belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Define the relationship between the thread and the user who created it.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define the relationship between the thread and its posts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Define a many-to-many relationship between the Thread and User models.
     * This relationship is represented by the 'user_thread_reads' pivot table,
     * which tracks when a user has read a specific thread.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function readers()
    {
        return $this->belongsToMany(User::class, 'user_thread_reads')
            ->withPivot('read_at')
            ->withTimestamps();
    }
}

