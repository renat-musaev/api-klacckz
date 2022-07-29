<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Book extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Book::class;

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
        'id', 'name'
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
            Textarea::make(__('Description for exercise'), 'text_exercise')->sortable(),
            Textarea::make(__('Description for lesson'), 'text_lesson')->sortable(),
            Textarea::make(__('Платежная информация для ГДЗ'), 'info_payment_1_page')->hideFromIndex(),
            Textarea::make(__('Дополнительная платежная информация для ГДЗ'), 'info_payment_2_page')->hideFromIndex(),
            Textarea::make(__('Платежная информация для Видеорешений'), 'info_payment_1_exercise')->hideFromIndex(),
            Textarea::make(__('Дополнительная платежная информация для Видеорешений'), 'info_payment_2_exercise')->hideFromIndex(),
            Textarea::make(__('Платежная информация для Видеоуроков'), 'info_payment_1_lesson')->hideFromIndex(),
            Textarea::make(__('Дополнительная платежная информация для Видеоуроков'), 'info_payment_2_lesson')->hideFromIndex(),
            Image::make(__('Cover'), 'cover')
                ->disk('public')
                ->path('books')
                ->creationRules('required')
                ->hideFromIndex(),
            Image::make(__('Cover for lesson'), 'cover_lesson')
                ->disk('public')
                ->path('books_lessons')
                ->hideFromIndex(),
            Image::make(__('Cover for video'), 'cover_video')
                ->disk('public')
                ->path('books_video')
                ->hideFromIndex(),
            Text::make(__('Preview for lesson'), 'preview_lesson')->nullable(),
            Text::make(__('Preview for exercise'), 'preview_exercise')->nullable(),
            Text::make('info_free')->nullable(),
            HasMany::make(__('Pages'), 'pages', 'App\Nova\Page'),
            HasMany::make(__('Video'), 'videos', 'App\Nova\Video'),
            HasMany::make(__('Lessosns'), 'lessons', 'App\Nova\Lesson'),
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
