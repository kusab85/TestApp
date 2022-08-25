<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';
    const STATUS_ARCHIVED = 'archived';
    const STATUS_WAITING_FOR_APPROVAL = 'waiting_for_approval';
    const STATUS_REJECTED = 'rejected';


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
