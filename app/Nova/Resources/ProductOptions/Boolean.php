<?php

namespace App\Nova\Resources\ProductOptions;

use App\Nova\Fields\TypedObjectForm;
use Laravel\Nova\Fields\Boolean as BooleanField;
use Laravel\Nova\Http\Requests\NovaRequest;

class Boolean extends TypedObjectForm
{
    public static string $modelClass = \App\Models\ProductOptions\Boolean::class;

    public function fields(NovaRequest $request): array
    {
        return [
            BooleanField::make('Default Value', 'defaultValue'),
        ];
    }
}
