<?php

namespace App\Nova;

use App\Models\Comment as CommentModel;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Line;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;

class Comment extends Resource
{
    public static string $model = CommentModel::class;

    public static $title = 'id';

    public static $search = [
        'id', 'comment',
    ];

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Post')
                ->hideFromIndex()
                ->rules('required'),

            Line::make('Comment')
                ->onlyOnIndex()
                ->displayUsing(fn($comment) => Str::limit($comment, 120))
                ->asSmall(),

            BelongsTo::make('User')
                ->rules('required')
                ->filterable(),

            Trix::make('Comment')
                ->hideFromIndex()
                ->rules('required')
                ->alwaysShow(),

            Date::make('Created At')->filterable(),
        ];
    }

    public function cards(NovaRequest $request): array
    {
        return [];
    }

    public function filters(NovaRequest $request): array
    {
        return [];
    }

    public function lenses(NovaRequest $request): array
    {
        return [];
    }

    public function actions(NovaRequest $request): array
    {
        return [];
    }
}
