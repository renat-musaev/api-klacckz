<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Classroom extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Classroom::class;

    // Убираем из панели навигации
    public static $displayInNavigation = false;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name',
    ];

    public static $indexDefaultOrder = [
        'id' => 'asc'
    ];

    public static function indexQuery(NovaRequest $request, $query)
    {
        if (empty($request->get('orderBy'))) {
            $query->getQuery()->orders = [];
            return $query->orderBy(key(static::$indexDefaultOrder), reset(static::$indexDefaultOrder));
        }
        return $query;
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
            ID::make(__('ID'), 'id')->sortable(),
            Text::make(__('Name'), 'name')->sortable(),
            Textarea::make(__('Платежная информация для ГДЗ'), 'info_payment_1_page')->hideFromIndex(),
            Textarea::make(__('Дополнительная платежная информация для ГДЗ'), 'info_payment_2_page')->hideFromIndex(),
            Textarea::make(__('Платежная информация для видеорешений'), 'info_payment_1_exercise')->hideFromIndex(),
            Textarea::make(__('Дополнительная платежная информация для видеорешений'), 'info_payment_2_exercise')->hideFromIndex(),
            Textarea::make(__('Платежная информация для видеоуроков'), 'info_payment_1_lessons')->hideFromIndex(),
            Textarea::make(__('Дополнительная платежная информация для видеоуроков'), 'info_payment_2_lessons')->hideFromIndex(),
            Textarea::make(__('Платежная информация для комбо'), 'info_payment_combo')->hideFromIndex(),
            HasMany::make(__('Subjects'), 'subjects', 'App\Nova\Subject'),
            Boolean::make(__('For pages'), 'show_pages'),
            Boolean::make(__('For video'), 'show_video'),
            Boolean::make(__('For lessons'), 'show_lessons'),
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
