<?php

namespace App\Nova\Lenses;

use App\Nova\Actions\ExportToCsv;
use App\Nova\Filters\TimeFrame;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Lenses\Lens;
use Laravel\Nova\Nova;

class MostActiveCommentators extends Lens
{
    public static $search = [];

    /**
     * Get the query builder / paginator for the lens.
     */
    public static function query(LensRequest $request, $query): mixed
    {
        return $request
            ->withoutTableOrderPrefix()
            ->withOrdering(
                $request->withFilters(
                    $query->select(
                        'users.id',
                        'users.name',
                        'users.email',
                        DB::raw('count(comments.id) as comments_count'),
                        DB::raw('max(comments.created_at) as latest_comment_date')
                    )
                        ->from('users')
                        ->leftJoin('comments', 'comments.user_id', '=', 'users.id')
                        ->groupBy('users.id')
                )
            )
            // add order by comments_count when is not set explicitly by user
            ->when(null === $request->orderBy, function (Builder $query) {
                $query->orderByDesc('comments_count');
            });
    }

    /**
     * Get the fields available to the lens.
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make(Nova::__('ID'), 'id')->sortable(),

            Gravatar::make()->maxWidth(50),

            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Number::make('Comments count')->sortable(),

            Text::make('Latest comment date', 'latest_comment_date')->sortable(),
        ];
    }

    /**
     * Get the cards available on the lens.
     */
    public function cards(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the filters available for the lens.
     */
    public function filters(NovaRequest $request): array
    {
        return [
            TimeFrame::make('comments.created_at'),
        ];
    }

    /**
     * Get the actions available on the lens.
     */
    public function actions(NovaRequest $request): array
    {
        return [
            ExportToCsv::make()
                //->standalone()
                ->withoutConfirmation()
                ->withFileBasename('Most Active Commentators'),
        ];
    }

    /**
     * Get the URI key for the lens.
     */
    public function uriKey(): string
    {
        return 'most-active-commentators';
    }
}
