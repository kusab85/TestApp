<?php

namespace App\Nova;

use App\Models\Post as PostModel;
use App\Nova\Metrics\PostsPerStatus;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;

class Post extends Resource
{
    public static $model = PostModel::class;

    public static $title = 'title';

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('Title')->rules('required'),
            Trix::make('Body')->rules('required')->alwaysShow(),
            BelongsTo::make('User'),
            Date::make('Created At')->filterable(),
            Select::make('Status')
                ->options(PostModel::availableStatuses(true))
                ->displayUsingLabels(),
            HasMany::make(__('Comments'), 'comments', Comment::class),
        ];
    }

    public function cards(NovaRequest $request)
    {
        return [
            PostsPerStatus::make()->refreshWhenFiltersChange(),
        ];
    }
}
