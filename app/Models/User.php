<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Carbon\Carbon;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'nickname',
        'email',
        'password',
        'avatar_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if the user can access the Filament admin panel.
     *
     * @param Panel $panel
     * @return bool
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Check if the user has the 'SuperAdmin', 'Admin' or 'Moderator' roles
        return $this->hasRole('SuperAdmin') || $this->hasRole('Admin') || $this->hasRole('Moderator');
    }

    // Relationship with Categories
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    // Relationship with Threads
    public function threads(): HasMany
    {
        return $this->hasMany(Thread::class);
    }

    // Relationship with Posts
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    // Messages sent by the user
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    // Messages received by the user
    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    // Discussions initiated by the user
    public function initiatedDiscussions()
    {
        return $this->hasMany(Discussion::class, 'initiator_id');
    }

    // Discussions where the user is a participant
    public function participatedDiscussions()
    {
        return $this->hasMany(Discussion::class, 'participant_id');
    }

    // Relationship with Reports
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    // Users this user has blocked
    public function blockedUsers()
    {
        return $this->belongsToMany(User::class, 'blocked_users', 'user_id', 'blocked_user_id')->withTimestamps();
    }

    // Users who have blocked this user
    public function blockedBy()
    {
        return $this->belongsToMany(User::class, 'blocked_users', 'blocked_user_id', 'user_id')->withTimestamps();
    }

    // Get all threads a user has read
    public function readThreads()
    {
        return $this->belongsToMany(Thread::class, 'user_thread_reads')
            ->withPivot('read_at')
            ->withTimestamps();
    }

    /**
     * Ban the user for a given number of days.
     *
     * @param int $days
     * @return void
     */
    public function ban(int $days)
    {
        if (!auth()->user()->can('ban', $this)) {
            abort(403, 'You are not authorized to ban this user.');
        }

        // Calculate the future ban expiration date
        $banUntil = Carbon::now()->addDays($days);

        // Set the is_banned timestamp
        $this->is_banned = $banUntil;
        $this->save();
    }

    /**
     * Unban the user.
     *
     * @return void
     */
    public function unban()
    {
        if (!auth()->user()->can('unban', $this)) {
            abort(403, 'You are not authorized to unban this user.');
        }

        // Set is_banned to null to unban the user
        $this->is_banned = null;
        $this->save();
    }

    /**
     * Check if the user is currently banned.
     *
     * @return bool
     */
    public function isBanned(): bool
    {
        // If is_banned is set and is in the future, the user is banned
        if ($this->is_banned) {
            return \Carbon\Carbon::parse($this->is_banned)->isFuture();
        }

        // If is_banned is NULL or in the past, the user is not banned
        return false;
    }

    public function promoteToModerator(): void
    {
        if (!auth()->user()->can('promoteToModerator', $this)) {
            abort(403, 'You are not authorized to promote this user.');
        }

        if ($this->hasRole('User')) {
            $this->removeRole('User');
            $this->assignRole('Moderator');
        }
    }

    public function promoteToAdmin(): void
    {
        if (!auth()->user()->can('promoteToAdmin', $this)) {
            abort(403, 'You are not authorized to promote this user.');
        }

        if ($this->hasRole('Moderator')) {
            $this->removeRole('Moderator');
            $this->assignRole('Admin');
        }
    }

    public function demoteToUser(): void
    {
        if (!auth()->user()->can('demoteToUser', $this)) {
            abort(403, 'You are not authorized to demote this user.');
        }

        if ($this->hasRole('Moderator')) {
            $this->removeRole('Moderator');
            $this->assignRole('User');
        }
    }

    public function demoteToModerator(): void
    {
        if (!auth()->user()->can('demoteToModerator', $this)) {
            abort(403, 'You are not authorized to demote this user.');
        }

        if ($this->hasRole('Admin')) {
            $this->removeRole('Admin');
            $this->assignRole('Moderator');
        }
    }

    /**
     * Get the remaining ban duration as a human-readable string.
     *
     * @return string|null Returns the ban expiration date and time or null if not banned.
     */
    public function getBanDuration(): ?string
    {
        if ($this->isBanned()) {
            return Carbon::parse($this->is_banned)->toDayDateTimeString();
        }

        return null; // Not banned
    }
}

