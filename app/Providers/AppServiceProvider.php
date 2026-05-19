<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            $types = [
                'B' => 'Business',
                'T' => 'Technology',
                'C' => 'Creative',
            ];

            $sidebarTopicGroups = collect($types)->map(function (string $label, string $type) {
                $topics = DB::table('categories')
                    ->select(
                        'categories.id',
                        'categories.name',
                        'categories.slug',
                        DB::raw('COUNT(courses.id) as total')
                    )
                    ->join('courses', 'courses.category_id', '=', 'categories.id')
                    ->where('courses.is_published', true)
                    ->where('courses.id', 'like', '%'.$type)
                    ->groupBy('categories.id', 'categories.name', 'categories.slug')
                    ->orderByDesc('total')
                    ->orderBy('categories.name')
                    ->limit(8)
                    ->get();

                return [
                    'type' => $type,
                    'label' => $label,
                    'title' => $label.' Topics',
                    'topics' => $topics,
                ];
            })->values();

            $view->with('sidebarTopicGroups', $sidebarTopicGroups);
        });
    }
}
