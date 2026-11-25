<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone',
        'faculty', 'group_name', 'organization', 'bio',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed'];
    }

    public const ROLES = [
        'volunteer' => 'Волонтёр',
        'organizer' => 'Организатор',
        'admin' => 'Администратор',
    ];

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function organizedEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'organizer_id');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function getRoleLabelAttribute(): string
    {
        return self::ROLES[$this->role] ?? $this->role;
    }

    /** Отработанные мероприятия (участие подтверждено организатором). */
    public function attendedRegistrations()
    {
        return $this->registrations()->where('status', 'attended')->with('event.category');
    }

    /** Суммарные часы и баллы по подтверждённым участиям. */
    public function volunteerStats(): array
    {
        $done = $this->attendedRegistrations()->get();

        return [
            'events' => $done->count(),
            'hours' => $done->sum(fn ($r) => $r->event->hours),
            'points' => round($done->sum(fn ($r) => $r->event->points), 1),
        ];
    }
}
