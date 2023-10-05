<?php

namespace App\Casts;

use App\Support\Contracts\JsonUnserializable;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Database\Eloquent\Model;

class AsTypedObjectCast implements Castable
{
    public static function castUsing(array $arguments): CastsAttributes
    {
        return new class implements CastsAttributes
        {
            public function get(Model $model, string $key, mixed $value, array $attributes): mixed
            {
                $class = $attributes["{$key}_class"] ?? null;
                $data = $attributes[$key] ?? null;

                if (is_null($data) or ! $this->validateClass($class)) {
                    return null;
                }

                $data = Json::decode($data);

                return is_array($data) ? ((new $class())->jsonUnserialize($data)) : null;
            }

            public function set(Model $model, string $key, mixed $value, array $attributes): mixed
            {
                $class = get_class($value);

                $this->validateClass($class);

                return [
                    "{$key}_class" => $class,
                    $key => Json::encode($value),
                ];
            }

            private function validateClass(string $className): bool
            {
                if (! class_exists($className)) {
                    throw  new \InvalidArgumentException("Class {$className} does not exists.");
                }

                if (! class_implements($className, JsonUnserializable::class)) {
                    throw  new \InvalidArgumentException("Class {$className} does not implement ".JsonUnserializable::class.' interface.');
                }

                return true;
            }
        };
    }
}
