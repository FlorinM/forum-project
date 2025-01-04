<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\BroadcastsEvents;

class Message extends Model
{
    use HasFactory;
    use BroadcastsEvents;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'discussion_id',
        'message',
        'read_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * Get the sender of the message.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the receiver of the message.
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Get the discussion that owns the message.
     */
    public function discussion()
    {
        return $this->belongsTo(Discussion::class, 'discussion_id');
    }

    /**
     * Define the private channel for broadcasting.
     */
    public function broadcastOn(): array
    {
        return [new PrivateChannel("discussion.{$this->discussion_id}")];
    }
}
