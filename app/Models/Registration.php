<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Registration extends Model
{
    protected $fillable = ['event_id', 'user_id', 'status', 'motivation'];

    public const STATUSES = [
        'pending' => 'На рассмотрении',
        'approved' => 'Одобрена',
        'rejected' => 'Отклонена',
        'attended' => 'Участие подтверждено',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
