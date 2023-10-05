<?php

namespace App\Support\Contracts;

interface JsonUnserializable
{
    public function jsonUnserialize(mixed $data): static;
}
