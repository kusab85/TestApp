<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Post;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        // create users
        User::factory()
            ->count(50)
            ->sequence(function (Sequence $sequence) {
                return 0 === $sequence->index
                    ? ['name' => 'Test User', 'email' => 'test@example.com']
                    : [];
            })
            ->create();

        // create posts for random users
        Post::factory()
            ->count(250)
            ->sequence(function (Sequence $sequence) {
                $user = Cache::get('users', fn () => User::all(['id', 'created_at']))->random();

                return [
                    'user_id' => $user->id,
                    'created_at' => fake()->dateTimeBetween($user->created_at),
                ];
            })
            ->create();

        // create comments for random posts
//        Comment::factory()
//            ->count(1000)
//            ->sequence(function (Sequence $sequence) {
//                $post = Cache::get('posts', fn () => Post::all(['id', 'created_at']))->random();
//                $user = Cache::get('users', fn () => User::all(['id', 'created_at']))->random();
//                /*
//                                dump([
//                                    $post->created_at->format('Y.m.d H.i.s'),
//                                    $user->created_at->format('Y.m.d H.i.s'),
//                                    max($post->created_at, $user->created_at)->format('Y.m.d H.i.s'),
//                                ]);
//                */
//                return [
//                    'user_id' => $user->id,
//                    'post_id' => $post->id,
//                    'created_at' => fake()->dateTimeBetween(max($post->created_at, $user->created_at)),
//                ];
//            })
//            ->create();

        // create products
        Product::factory()
            ->has(ProductOption::factory()->count(rand(3, 5)), 'options')
            ->count(10)
            ->create();
    }
}
