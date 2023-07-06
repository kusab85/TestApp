<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Actions\Response;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\ActionRequest;
use Laravel\Nova\Query\ApplyFilter;
use Rap2hpoutre\FastExcel\FastExcel;

/**
 * "Standalone'able" version of Laravel\Nova\Actions\ExportToCsv action that can automagically generate filename
 * based on fileBasename and filters applied to resources query.
 */
class ExportToCsv extends ExportAsCsv
{
    use InteractsWithQueue, Queueable;

    protected string $fileBasename;

    public function withFileBasename(string $fileBasename): static
    {
        $this->fileBasename = $fileBasename;

        return $this;
    }

    public function standalone(): static
    {
        $this->standalone = true;
        $this->sole = false;

        return $this;
    }

    /**
     * Make a filename using fileBasename and filters applied to query resources.
     */
    protected function resolveFileName(ActionRequest $request): string
    {
        $filtersInfo = $request->filters()
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
     * Method almost same as Laravel\Nova\Actions\ExportAsCsv::dispatchRequestUsing except 2 lines.
     *
     * $query resolving line:
     * $query = $this->isStandalone() ? $request->toQuery() : $request->toSelectedResourceQuery();
     *
     * $filename resolving line:
     * $filename = $fields->get('filename') ?? $this->resolveFileName($request);
     */
    protected function dispatchRequestUsing(ActionRequest $request, Response $response, ActionFields $fields): Response
    {
        $this->then(function ($results) {
            return $results->first();
        });

        $query = $this->isStandalone() ? $request->toQuery() : $request->toSelectedResourceQuery();

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
