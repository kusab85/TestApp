<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';
    const STATUS_ARCHIVED = 'archived';
    const STATUS_WAITING_FOR_APPROVAL = 'waiting_for_approval';
    const STATUS_REJECTED = 'rejected';

    public static function availableStatuses(bool $withLabels = false): array
    {
        $statuses = [
            self::STATUS_DRAFT,
            self::STATUS_WAITING_FOR_APPROVAL,
            self::STATUS_REJECTED,
            self::STATUS_PUBLISHED,
            self::STATUS_ARCHIVED,
        ];

        return $withLabels ?
            array_combine($statuses, array_map(fn($status) => Str::upper(Str::headline($status)), $statuses)) :
            $statuses;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
