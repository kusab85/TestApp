<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Comment;
use App\Models\Post;
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
    public function run()
    {
        // create users with posts
        User::factory()
            ->count(15)
            ->sequence(function (Sequence $sequence) {
                return 0 === $sequence->index
                    ? ['name' => 'Test User', 'email' => 'test@example.com']
                    : [];
            })
            ->has(
                Post::factory()
                    ->count(rand(10, 20))
                    ->state(function (array $attributes, User $user) {
                        return [
                            'created_at' => fake()->dateTimeBetween($user->email_verified_at ?? now()),
                        ];
                    })
            )
            ->create();

        // create comments for all posts
        Comment::factory()
            ->count(1000)
            ->sequence(function (Sequence $sequence) {
                $post = Cache::get('posts', fn() => Post::all(['id', 'created_at']))->random();
                $user = Cache::get('users', fn() => User::all(['id', 'created_at']))->random();
                /*
                                dump([
                                    $post->created_at->format('Y.m.d H.i.s'),
                                    $user->created_at->format('Y.m.d H.i.s'),
                                    max($post->created_at, $user->created_at)->format('Y.m.d H.i.s'),
                                ]);
                */
                return [
                    'user_id'    => $user->id,
                    'post_id'    => $post->id,
                    'created_at' => fake()->dateTimeBetween(max($post->created_at, $user->created_at)),
                ];
            })
            ->create();
    }
}
