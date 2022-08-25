<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence(),
            'body' => $this->faker->paragraphs(5,true),
            'created_at' => $this->faker->dateTimeBetween('-2 years'),
            'status' => $this->faker->randomElement( [
                Post::STATUS_DRAFT,
                Post::STATUS_WAITING_FOR_APPROVAL,
                Post::STATUS_REJECTED,
                Post::STATUS_PUBLISHED,
                Post::STATUS_ARCHIVED,
            ])
        ];
    }
}
