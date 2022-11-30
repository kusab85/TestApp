<?php

namespace App\Nova\Metrics;

use App\Models\Post;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;

class PostsPerStatus extends Partition
{
    public function calculate(NovaRequest $request)
    {
        $statuses = Post::availableStatuses(true);

        logger(print_r([get_class($request), $this->filters], true));

        return $this
            ->count($request, Post::class, 'status')
            ->label(function ($v) use ($statuses) {
                return $statuses[$v] ?? 'UNKNOWN';
            });
    }

    public function uriKey()
    {
        return 'posts-per-status';
    }
}
