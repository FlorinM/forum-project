<?php

namespace App\Observers;

use App\Models\Thread;

class ThreadObserver
{
    /**
     * Handle the Thread "updated" event.
     *
     * This method checks if the "approved" field was changed to `true`.
     * If so, it ensures the first post of the thread is also approved.
     *
     * @param Thread $thread The updated Thread model instance.
     * @return void
     */
    public function updated(Thread $thread)
    {
        // Check if the "approved" column was changed to true
        if ($thread->wasChanged('approved') && $thread->approved) {
            // Find the first post of the thread and approve it
            $firstPost = $thread->posts()->first();
            if ($firstPost && !$firstPost->approved) {
                $firstPost->update(['approved' => true]);
            }
        }
    }
}
