<?php

namespace App\Models\ProductOptions;

use App\Support\Contracts\JsonUnserializable;
use App\Support\Traits\JsonUnserializable as JsonUnserializableTrait;
use Laravel\Nova\Makeable;

class Select implements JsonUnserializable
{
    use Makeable, JsonUnserializableTrait;

    public ?string $defaultValue;

    public array $options = [];

    public function __construct(?string $defaultValue = null, array $options = [])
    {
        $this->defaultValue = $defaultValue;
        $this->options = $options;
    }
}
