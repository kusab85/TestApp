<?php

namespace App\Models;

use App\Casts\AsTypedObjectCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'settings_class',
        'settings',
    ];

    protected $casts = [
        'settings' => AsTypedObjectCast::class,
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
