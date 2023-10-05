<?php

namespace App\Models\ProductOptions;

use App\Support\Contracts\JsonUnserializable;
use App\Support\Traits\JsonUnserializable as JsonUnserializableTrait;
use Laravel\Nova\Makeable;

class Boolean implements JsonUnserializable
{
    use Makeable, JsonUnserializableTrait;

    public bool $defaultValue;

    public function __construct(bool $defaultValue = false)
    {
        $this->defaultValue = $defaultValue;
    }
}
