<?php

namespace App\Nova;

use App\Models\User as UserModel;
use App\Nova\Filters\TimeFrame;
use App\Nova\Lenses\MostActiveCommentators;
use App\Nova\Lenses\MostActivePublishers;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rules;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Line;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class User extends Resource
{
    public static string $model = UserModel::class;

    public static $title = 'name';

    public static $search = [
        'id', 'name', 'email',
    ];

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Gravatar::make()->maxWidth(50),

            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}'),

            Stack::make('Created And Verified', [
                Line::make('Created at')->asSmall()->displayUsing(fn (
                    $value
                ) => Carbon::parse($value)->format('Y-m-d H:i:s')),
                Line::make('Email verified at')->asSmall()->displayUsing(fn (
                    $value
                ) => Carbon::parse($value)->format('Y-m-d H:i:s')),
            ]),

            Password::make('Password')
                ->onlyOnForms()
                ->creationRules('required', Rules\Password::defaults())
                ->updateRules('nullable', Rules\Password::defaults()),
        ];
    }

    public function cards(NovaRequest $request): array
    {
        return [];
    }

    public function filters(NovaRequest $request): array
    {
        return [
            TimeFrame::make('users.created_at')
                ->withName('Created'),

            TimeFrame::make('users.email_verified_at')
                ->withName('Verified'),
        ];
    }

    public function lenses(NovaRequest $request): array
    {
        return [
            MostActivePublishers::make(),
            MostActiveCommentators::make(),
        ];
    }

    public function actions(NovaRequest $request): array
    {
        return [];
    }
}
