<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;

class User extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\User::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'phone';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name', 'phone',
    ];

    public static function label()
    {
        return __('Пользователи');
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            //Gravatar::make()->maxWidth(50),

            Text::make(__('Имя пользователя'), 'name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make(__('Номер мобильного'), 'phone')
                ->sortable()
                ->rules('required', 'digits:10')
                ->creationRules('unique:users,phone')
                ->updateRules('unique:users,phone,{{resourceId}}'),

            Text::make(__('Email'), 'email')
                ->sortable()
                ->hideFromIndex(),

            Number::make(__('Авторизованных устройств'), 'device_count')
                ->sortable()
                ->hideWhenCreating(),

            Number::make(__('Разрешенное количество устройств'), 'device_limit')
                ->hideFromIndex()
                ->sortable()
                ->hideWhenCreating(),

            Password::make(__('Пароль'), 'password')
                ->onlyOnForms()
                ->creationRules('required', 'string', 'min:6')
                ->updateRules('nullable', 'string', 'min:6'),

            HasMany::make(__('Оплата за ГДЗ'), 'pagePayments', PagePayment::class),
            HasMany::make(__('Оплата за видеоуроки'), 'lessonPayments', LessonPayment::class),
            HasMany::make(__('Оплата за видеорешений'), 'videoPayments', VideoPayment::class),
            HasMany::make(__('Оплата за видеокурсы'), 'coursePayments', CoursePayment::class),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
