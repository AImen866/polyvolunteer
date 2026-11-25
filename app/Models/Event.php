<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'category_id', 'title', 'description', 'requirements',
        'location', 'starts_at', 'ends_at', 'capacity', 'status',
    ];

    protected $casts = ['starts_at' => 'datetime', 'ends_at' => 'datetime'];

    public const STATUSES = [
        'draft' => 'Черновик',
        'published' => 'Опубликовано',
        'cancelled' => 'Отменено',
    ];

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    /** Только опубликованные мероприятия. */
    public function scopePublished(Builder $q): Builder
    {
        return $q->where('status', 'published');
    }

    /** Мероприятия, которые ещё не закончились. */
    public function scopeUpcoming(Builder $q): Builder
    {
        return $q->where('ends_at', '>=', now());
    }

    /** Фильтры афиши: поиск, категория, дата, только со свободными местами. */
    public function scopeFilter(Builder $q, array $f): Builder
    {
        return $q
            ->when($f['q'] ?? null, fn ($q, $v) => $q->where(fn ($q) => $q
                ->where('title', 'like', "%{$v}%")
                ->orWhere('description', 'like', "%{$v}%")
                ->orWhere('location', 'like', "%{$v}%")))
            ->when($f['category'] ?? null, fn ($q, $v) => $q->where('category_id', $v))
            ->when($f['date_from'] ?? null, fn ($q, $v) => $q->whereDate('starts_at', '>=', $v))
            ->when($f['date_to'] ?? null, fn ($q, $v) => $q->whereDate('starts_at', '<=', $v))
            ->when($f['free'] ?? null, fn ($q) => $q->whereRaw(
                'capacity > (select count(*) from registrations r where r.event_id = events.id and r.status in (?, ?, ?))',
                ['pending', 'approved', 'attended']
            ));
    }

    /** Занятые места: заявки в ожидании, одобренные и отработанные. */
    public function getTakenAttribute(): int
    {
        return $this->registrations->whereIn('status', ['pending', 'approved', 'attended'])->count();
    }

    public function getFreeSeatsAttribute(): int
    {
        return max(0, $this->capacity - $this->taken);
    }

    public function getFillPercentAttribute(): int
    {
        return $this->capacity > 0 ? min(100, (int) round($this->taken / $this->capacity * 100)) : 0;
    }

    /** Длительность в часах (с точностью до 0,5). */
    public function getHoursAttribute(): float
    {
        return round($this->starts_at->diffInMinutes($this->ends_at) / 60 * 2) / 2;
    }

    /** Баллы волонтёра: часы × коэффициент категории. */
    public function getPointsAttribute(): float
    {
        return round($this->hours * (float) $this->category->hours_coefficient, 1);
    }

    public function getIsOverAttribute(): bool
    {
        return $this->ends_at->isPast();
    }
}
