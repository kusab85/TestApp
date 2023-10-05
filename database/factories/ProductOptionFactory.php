<?php

namespace Database\Factories;

use App\Models\ProductOption;
use App\Models\ProductOptions\Boolean;
use App\Models\ProductOptions\Select;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ProductOptionFactory extends Factory
{
    protected $model = ProductOption::class;

    public function definition(): array
    {
        $settings = match (rand(1, 2)) {
            1 => Boolean::make($this->faker->boolean()),
            2 => Select::make(
                $this->faker->domainWord(),
                [
                    $this->faker->domainWord() => $this->faker->name(),
                    $this->faker->domainWord() => $this->faker->name(),
                    $this->faker->domainWord() => $this->faker->name(),
                    $this->faker->domainWord() => $this->faker->name(),
                    $this->faker->domainWord() => $this->faker->name(),
                ]
            ),
            default => null,
        };

        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'settings' => $settings,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
