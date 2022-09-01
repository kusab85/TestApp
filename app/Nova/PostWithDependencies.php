<?php

namespace App\Nova;

use App\Models\Post as PostModel;
use App\Models\User as UserModel;
use App\Nova\Metrics\PostsPerStatus;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;

class PostWithDependencies extends Resource
{
    public static $model = PostModel::class;

    public static $title = 'title';

    public static $clickAction = 'edit';

    public function fields(NovaRequest $request)
    {

        return [
            ID::make()->sortable(),

            Text::make('Title')
                ->rules('required')
                ->dependsOn(['user','status','created_at'],
                    function (Text $field, NovaRequest $request, FormData $formData) {
                        logger(get_class($this).": User changed to ".var_export($formData->user, true));
                        logger(get_class($this).": Status changed to ".var_export($formData->status, true));
                        logger(get_class($this).": Created at changed to ".var_export($formData->created_at, true));
                    }
                ),

            Textarea::make('Body')
                ->rules('required')
                ->dependsOn(['title'],
                    function (Textarea $field, NovaRequest $request, FormData $formData) {
                        logger(get_class($this).": Title changed to ".var_export($formData->title, true));
                    }
                ),

            BelongsTo::make('User'),

            Date::make('Created At')->filterable(),

            Select::make('Status')
                ->options(PostModel::availableStatuses(true))
                ->displayUsingLabels(),
        ];
    }

}
