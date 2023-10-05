<?php

namespace App\Nova\Resources\ProductOptions;

use App\Nova\Fields\TypedObjectForm;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Select extends TypedObjectForm
{
    public static string $modelClass = \App\Models\ProductOptions\Select::class;

    protected function fields(NovaRequest $request): array
    {
        return [
            Text::make('Default Value', 'defaultValue')
                ->rules('nullable'),

            KeyValue::make('Options', 'options')
                ->keyLabel('code')
                ->valueLabel('label')
                ->required(),
        ];
    }
}
