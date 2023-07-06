<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Actions\Response;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\FilterDecoder;
use Laravel\Nova\Http\Requests\ActionRequest;
use Laravel\Nova\Http\Requests\LensActionRequest;
use Laravel\Nova\Query\ApplyFilter;
use Rap2hpoutre\FastExcel\FastExcel;

class ExportToCsv extends ExportAsCsv
{
    use InteractsWithQueue, Queueable;

    protected string $fileBasename;

    public function withFileBasename(string $fileBasename): static
    {
        $this->fileBasename = $fileBasename;

        return $this;
    }

    /**
     * I can not use $request->filters() directly because
     * LensActionRequest->availableFilters() returns filters of its assigned resource
     * not of the lens itself, so I need decode filters manually.
     */
    protected function requestFilters(ActionRequest $request): Collection
    {
        return ($request instanceof LensActionRequest)
            ? (new FilterDecoder($request->filters, $request->lens()->availableFilters($request)))->filters()
            : $request->filters();
    }

    protected function resolveFileName(ActionRequest $request): string
    {
        // I can not use $request->filters() directly because
        // LensActionRequest->availableFilters() returns filters of its assigned resource
        // not of the lens itself, so I need decode filters manually
        $filtersInfo = $this->requestFilters($request)
            ->mapWithKeys(function (ApplyFilter $filter) use ($request) {
                return [
                    $filter->filter->key() => [
                        'filterName' => $filter->filter->name(),
                        'valueLabel' => array_flip($filter->filter->options($request))[$filter->value] ?? null,
                        'value' => $filter->value,
                    ],
                ];
            })
            ->map(fn ($info) => $info['filterName'].' '.$info['valueLabel']);

        $fileBasename = Str::kebab($filtersInfo->prepend($this->fileBasename ?? $this->uriKey())->implode(' '));

        return sprintf('%s-%s.csv', $fileBasename, now()->format('Ymd-His'));
    }

    /**
     * Method almost same as Laravel\Nova\Actions\ExportAsCsv::dispatchRequestUsing
     * except $filename resolving line:
     * $filename = $fields->get('filename') ?? $this->resolveFileName($request);.
     */
    protected function dispatchRequestUsing(ActionRequest $request, Response $response, ActionFields $fields): Response
    {
        $this->then(function ($results) {
            return $results->first();
        });

        $query = $request->toSelectedResourceQuery();

        $query->when($this->withQueryCallback instanceof Closure, function ($query) use ($fields) {
            return call_user_func($this->withQueryCallback, $query, $fields);
        });

        $eloquentGenerator = function () use ($query) {
            foreach ($query->cursor() as $model) {
                yield $model;
            }
        };

        $filename = $fields->get('filename') ?? $this->resolveFileName($request);

        $extension = 'csv';

        if (Str::contains($filename, '.')) {
            [$filename, $extension] = explode('.', $filename);
        }

        $exportFilename = sprintf(
            '%s.%s',
            $filename,
            $fields->get('writerType') ?? $extension
        );

        return $response->successful([
            (new FastExcel($eloquentGenerator()))->download($exportFilename, $this->withFormatCallback),
        ]);
    }
}
