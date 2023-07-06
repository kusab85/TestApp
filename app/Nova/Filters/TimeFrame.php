<?php

namespace App\Nova\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class TimeFrame extends Filter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

    /**
     * The column that should be filtered on.
     */
    protected string $column;

    /**
     * Create a new filter instance.
     */
    public function __construct(string $column)
    {
        $this->column = $column;
    }

    public function withName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Apply the filter to the given query.
     *
     * @param  NovaRequest  $request
     * @param  Builder  $query
     * @param  mixed  $value
     * @return Builder
     */
    public function apply(NovaRequest $request, $query, $value): Builder
    {
        $since = [
            'TW' => Carbon::now()->startOfWeek(),
            'SW' => Carbon::now()->subWeek(),
            'TM' => Carbon::now()->startOfMonth(),
            'SM' => Carbon::now()->subMonth(),
            'TY' => Carbon::now()->startOfYear(),
            'SY' => Carbon::now()->subYear(),
        ];

        return isset($since[$value]) ? $query->where($this->column, '>=', $since[$value]) : $query;
    }

    /**
     * Get the filter's available options.
     */
    public function options(NovaRequest $request): array
    {
        return [
            'This week' => 'TW',
            'Since week ago' => 'SW',
            'This month' => 'TM',
            'Since month ago' => 'SM',
            'This year' => 'TY',
            'Since year ago' => 'SY',
        ];
    }

    public function key(): string
    {
        return Str::snake(Str::replace('.', '_', $this->column)).'_not_older_than';
    }
}
