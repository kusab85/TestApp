<?php

namespace App\Nova;

use App\Models\Post as PostModel;
use App\Nova\Metrics\PostsPerStatus;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\FormData;
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
            Trix::make('Body')->rules('required'),
            BelongsTo::make('User')
                ->dependsOn(
                    ['user'],
                    function (BelongsTo $field, NovaRequest $request, FormData $formData) {
                        $field->help(
                            '$formData->user = '.var_export($formData->user, true).' '.
                            '$field->value = '.var_export($field->value, true)
                        );
                    }),
            Date::make('Created At')->filterable(),
            Select::make('Status')
                ->options(PostModel::availableStatuses(true))
                ->displayUsingLabels(),
        ];
    }

    public function cards(NovaRequest $request)
    {
        return [
            PostsPerStatus::make()->refreshWhenFiltersChange(),
        ];
    }
}
