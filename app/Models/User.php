<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Carbon\Carbon;

class User extends Authenticatable implements FilamentUser
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
        // Check if the user has the 'admin' role (or any other role)
        return $this->hasRole('Admin');
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
}

