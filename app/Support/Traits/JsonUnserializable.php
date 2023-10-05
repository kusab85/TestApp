<?php

namespace App\Support\Traits;

trait JsonUnserializable
{
    public function jsonUnserialize(mixed $data): static
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $this->$key = $value;
            }
        }

        return $this;
    }
}
