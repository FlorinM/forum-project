<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discussion extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'initiator_id',
        'participant_id',
        'subject',
        'initiator_deleted_at',
        'participant_deleted_at',
    ];

    /**
     * Get the initiator of the discussion.
     */
    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiator_id');
    }

    /**
     * Get the participant of the discussion.
     */
    public function participant()
    {
        return $this->belongsTo(User::class, 'participant_id');
    }

    /**
     * Get the messages for the discussion.
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'discussion_id');
    }
}
