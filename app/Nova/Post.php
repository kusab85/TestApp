<?php

namespace App\Nova;

use App\Models\Post as PostModel;
use App\Nova\Metrics\PostsPerStatus;
use Laravel\Nova\Exceptions\HelperNotSupported;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;

class Post extends Resource
{
    public static string $model = PostModel::class;

    public static $title = 'title';

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            Text::make('Title')->rules('required'),
            Trix::make('Body')->rules('required')->alwaysShow(),
            BelongsTo::make('User')
                ->dependsOn(
                    ['created_at'],
                    function (BelongsTo $field, NovaRequest $request, FormData $formData) {
                        $field->help(
                            '$formData = '.var_export($formData->getAttributes(), true)."</br>\n".
                            '$field->value = '.var_export($field->value, true)
                        );
                    }
                ),
            Date::make('Created At')->filterable(),
            Select::make('Status')
                ->options(PostModel::availableStatuses(true))
                ->displayUsingLabels(),
            HasMany::make(__('Comments'), 'comments', Comment::class),
        ];
    }

    /**
     * @throws HelperNotSupported
     */
    public function cards(NovaRequest $request): array
    {
        return [
            PostsPerStatus::make()->refreshWhenFiltersChange(),
        ];
    }
}
