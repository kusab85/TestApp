<?php

namespace App\Nova\Resources;

use App\Models\ProductOption as ProductOptionModel;
use App\Nova\Resources\ProductOptions\Boolean as BooleanOption;
use App\Nova\Resources\ProductOptions\Select as SelectOption;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class ProductOption extends Resource
{
    public static string $model = ProductOptionModel::class;

    public static $title = 'name';

    public static $search = [
        'id', 'name', 'description',
    ];

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Product')
                ->readonly(),

            Text::make('Name')
                ->sortable()
                ->showOnPreview()
                ->rules('required'),

            Text::make('Description')
                ->hideFromIndex()
                ->rules('required'),

            Select::make('Type', 'settings_class')
                ->displayUsingLabels()
                ->options([
                    BooleanOption::$modelClass => BooleanOption::label(),
                    SelectOption::$modelClass => SelectOption::label(),
                ])
                ->required(),

            ...BooleanOption::make('settings')->resolveFields($request, $this->settings_class),

            ...SelectOption::make('settings')->resolveFields($request, $this->settings_class),

        ];
    }

    public function cards(Request $request): array
    {
        return [];
    }

    public function filters(Request $request): array
    {
        return [];
    }

    public function lenses(Request $request): array
    {
        return [];
    }

    public function actions(Request $request): array
    {
        return [];
    }
}
