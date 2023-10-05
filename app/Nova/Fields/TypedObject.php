<?php

namespace App\Nova\Fields;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Makeable;
use Laravel\Nova\Resource;

/**
 * @method static make(string $name, string $attribute = 'settings'): static
 */
class TypedObject
{
    use Makeable;

    public string $name;

    public string $attribute;

    public function __construct(string $name, string $attribute = 'settings')
    {
        $this->name = $name;

        $this->attribute = $attribute;
    }

    public function morphsToOneOf(array $canMorphTo, NovaRequest $request, Resource $resource): array
    {
        $class_attribute = "{$this->attribute}_class";

        $canMorphTo = collect($canMorphTo);

        $this->validateMorphTo($canMorphTo);

        return [
            Select::make($this->name, $class_attribute)
                ->displayUsingLabels()
                ->options(
                    $canMorphTo
                        ->mapWithKeys(fn (TypedObjectForm $formOfMorphTo) => [
                            $formOfMorphTo::$modelClass => $formOfMorphTo::label(),
                        ])
                        ->toArray()
                )
                ->required(),

            ...$canMorphTo
                ->map(
                    fn (TypedObjectForm $formOfMorphTo) => $formOfMorphTo::make($this->attribute)
                        ->resolveFields($request, $resource->$class_attribute)
                )
                ->toArray(),
        ];
    }

    protected function validateMorphTo(Collection $canMorphTo): void
    {
        collect($canMorphTo)
            ->each(function ($formOfMorphTo) {
                if (! is_subclass_of($formOfMorphTo, TypedObjectForm::class)) {
                    throw new InvalidArgumentException(get_class($formOfMorphTo).' is not a subclass of '.TypedObjectForm::class);
                }
            });
    }
}
