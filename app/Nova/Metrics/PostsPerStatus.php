<?php

namespace App\Nova\Metrics;

use App\Models\Post;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;

class PostsPerStatus extends Partition
{

    public function calculate(NovaRequest $request)
    {
        return $this->count($request, Post::class, 'status');
    }


    public function uriKey()
    {
        return 'posts-per-status';
    }
}
