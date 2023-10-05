<?php

namespace App\Nova\Fields;

use Illuminate\Support\Str;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Makeable;

abstract class TypedObjectForm
{
    use Makeable;

    public string $attribute;

    public static string $modelClass;

    public function __construct(string $attribute)
    {
        $this->attribute = $attribute;
    }

    public static function label(): string
    {
        return __(Str::title(Str::snake(class_basename(get_called_class()), ' ')));
    }

    public function resolveFields(NovaRequest $request, ?string $settings_class): array
    {
        return collect($this->fields($request))
            ->filter(function (Field $field) use ($request, $settings_class) {
                $attribute_class = "{$this->attribute}_class";
                $field->attribute = "{$this->attribute}->{$field->attribute}";

                if ($request->isFormRequest()) {
                    $field->dependsOn(
                        [$attribute_class],
                        function (Field $field, NovaRequest $request, FormData $formData) use ($attribute_class) {
                            if ($formData->get($attribute_class) !== static::$modelClass) {
                                $field->hide();
                            } else {
                                $field->show();
                            }
                        }
                    );

                    return true;
                }

                return $settings_class === static::$modelClass;
            })
            ->toArray();
    }

    abstract protected function fields(NovaRequest $request): array;
}
