<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Course extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Course::class;

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
            Boolean::make(__('Show'), 'show'),
            Text::make(__('Name'), 'name')->sortable()->rules('required'),
            Textarea::make(__('Description'), 'text')->sortable(),
            Number::make(__('Price for 1 month'), 'price_1')->rules('required')->default(0),
            Number::make(__('Price for 3 months'), 'price_2')->rules('required')->default(0),
            Number::make(__('Price for 6 months'), 'price_3')->rules('required')->default(0),
            Number::make(__('Price for 1 year'), 'price_4')->rules('required')->default(0),
            Textarea::make(__('Платежная информация'), 'info_payment_1')->hideFromIndex(),
            Textarea::make(__('Дополнительная платежная информация'), 'info_payment_2')->hideFromIndex(),
            Image::make(__('Cover'), 'cover')
                ->disk('public')
                ->path('courses')
                ->creationRules('required')
                ->hideFromIndex(),
            Text::make(__('Preview'), 'preview')->hideFromIndex()->nullable(),
            HasMany::make(__('Video'), 'videos', 'App\Nova\CourseVideo'),
            // Textarea::make(__('Платежная нформация'), 'info')->hideFromIndex(),
            // Textarea::make(__('Дополнительная платежная информация'), 'info_payment')->hideFromIndex(),
            // //BelongsTo::make(__('Parent'), 'parent', 'App\Nova\Course')->nullable(),
            // HasMany::make(__('Children'), 'children', 'App\Nova\Course'),
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
