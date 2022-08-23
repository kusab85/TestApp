<?php

namespace App\Nova;

use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;

class Post extends Resource
{
    public static $model = \App\Models\Post::class;
    public static $title = 'id';

    public static function redirectAfterCreate(NovaRequest $request, $resource)
    {
        logger(__METHOD__.': '.var_export(['query' => $request->query(), 'input' => $request->input()], true));
        return parent::redirectAfterCreate($request, $resource);
    }


    public static function redirectAfterUpdate(NovaRequest $request, $resource)
    {
        logger(__METHOD__.': '.var_export(['query' => $request->query(), 'input' => $request->input()], true));
        return parent::redirectAfterUpdate($request, $resource);
    }

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('Title')->rules('required'),
            Trix::make('Body')->rules('required'),
            BelongsTo::make('User'),
        ];
    }

}
