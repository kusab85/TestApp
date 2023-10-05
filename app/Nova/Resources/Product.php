<?php

namespace App\Nova\Resources;

use App\Models\Product as ProductModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Line;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;

class Product extends Resource
{
    public static string $model = ProductModel::class;

    public static $title = 'name';

    public static $search = [
        'id', 'name', 'tagline', 'description',
    ];

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),

            Stack::make('Product', [
                Line::make('Name')
                    ->asHeading(),

                Line::make('Tagline')
                    ->sortable()
                    ->displayUsing(fn ($value) => Str::limit($value, 120))
                    ->asSmall(),
            ])
                ->onlyOnIndex(),

            Text::make('Name')
                ->sortable()
                ->rules('required')
                ->hideFromIndex(),

            Text::make('Tagline')
                ->sortable()
                ->rules('required')
                ->hideFromIndex(),

            Textarea::make('Description')
                ->alwaysShow()
                ->sortable()
                ->rules('required'),

            Currency::make('Price')
                ->sortable()
                ->rules('required', 'numeric'),

            HasMany::make('Options', 'options', ProductOption::class),
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
