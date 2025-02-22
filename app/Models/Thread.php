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

    /**
     * Mark the thread as read for the authenticated user.
     *
     * This function checks if the user has already interacted with the thread
     * via the pivot table. If a record exists, it updates the `read_at` timestamp.
     * Otherwise, it creates a new record marking the thread as read.
     */
    public function markAsRead()
    {
        $user = auth()->user();

        // Check if the pivot record exists (i.e., user has already interacted with the thread)
        if ($user->readThreads()->where('thread_id', $this->id)->exists()) {
            // If the pivot record exists, update the read_at timestamp
            $user->readThreads()->updateExistingPivot($this->id, [
                'read_at' => now(),
            ]);
        } else {
            // If the pivot record doesn't exist, create a new one with the current timestamp
            $user->readThreads()->attach($this->id, [
                'read_at' => now(),
            ]);
        }
    }
}

