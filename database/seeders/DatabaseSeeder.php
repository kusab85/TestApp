<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $u = User::factory()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com',
        ]);

        Post::factory(10)->create(['user_id' => $u->id]);
    }
}
