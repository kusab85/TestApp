<?php

namespace App\Nova\Lenses;

use App\Models\Post as PostModel;
use App\Nova\Metrics\PostsPerStatus;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Lenses\Lens;

class PostsViaLens extends Lens
{
    /**
     * Get the query builder / paginator for the lens.
     *
     * @param \Laravel\Nova\Http\Requests\LensRequest $request
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return mixed
     */
    public static function query(LensRequest $request, $query): mixed
    {
        return $request->withOrdering($request->withFilters(
            $query
        ));
    }

    /**
     * Get the fields available to the lens.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     *
     * @return array
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            Text::make('Title')->rules('required'),
            Trix::make('Body')->rules('required'),
            BelongsTo::make('User'),
            Date::make('Created At')->filterable()->sortable(),
            Select::make('Status')
                ->options(PostModel::availableStatuses(true))
                ->displayUsingLabels(),
        ];
    }

    /**
     * Get the cards available on the lens.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     *
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [
            PostsPerStatus::make()->refreshWhenFiltersChange(),
        ];
    }

    /**
     * Get the filters available for the lens.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     *
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available on the lens.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     *
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return parent::actions($request);
    }

    /**
     * Get the URI key for the lens.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'posts-via-lens';
    }
}
