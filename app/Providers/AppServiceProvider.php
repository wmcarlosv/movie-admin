<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Schema;
use Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Dispatcher $events)
    {
        Schema::defaultStringLength(191);

        $events->listen(BuildingMenu::class, function(BuildingMenu $event){
            $role = Auth::user()->role;

            if($role == 'admin'){

                $event->menu->add([
                    'header' => 'MAIN MENU'
                ],[
                    'text' => 'Dashboard',
                    'icon' => 'fas fa-tachometer-alt',
                    'route' => 'dashboard'
                ],[
                    'text' => 'Profile',
                    'icon' => 'fas fa-user',
                    'route' => 'profile'
                ],[
                    'text' => 'Users',
                    'icon' => 'fas fa-users',
                    'route' => 'users.index'
                ],[
                    'text' => 'Categories',
                    'icon' => 'fas fa-tags',
                    'route' => 'categories.index'
                ],[
                    'text' => 'Applications',
                    'icon' => 'fas fa-mobile-alt',
                    'route' => 'applications.index'
                ],[
                    'text' => 'Movies Manage',
                    'icon' => 'fas fa-list',
                    'submenu' => [
                        [
                            'text' => 'Movies',
                            'icon' => 'fas fa-ticket-alt',
                            'route' => 'movies.index'
                        ],[
                            'text' => 'Import Movies',
                            'icon' => 'fas fa-file-import',
                            'route' => 'import_movies'
                        ]
                    ]
                ],[
                    'text' => 'Series Manage',
                    'icon' => 'fas fa-list',
                    'submenu' => [
                        [
                            'text' => 'Series',
                            'route' => 'series.index',
                            'icon' => 'fas fa-tv'
                        ],
                        [
                            'text' => 'Seasons',
                            'route' => 'seasons.index',
                            'icon' => 'fas fa-play'
                        ],
                        [
                            'text' => 'Chapters',
                            'route' => 'chapters.index',
                            'icon' => 'fas fa-video'
                        ],
                        [
                            'text' => 'Import Series',
                            'route' => 'import_series',
                            'icon' => 'fas fa-file-import'
                        ]
                    ]
                ]);

            }

            if(Auth::user()->role == 'client'){
                  $event->menu->add([
                        'header' => 'MAIN MENU'
                    ],[
                        'text' => 'Dashboard',
                        'icon' => 'fas fa-tachometer-alt',
                        'route' => 'dashboard'
                    ],[
                        'text' => 'Profile',
                        'icon' => 'fas fa-user',
                        'route' => 'profile'
                    ]
                );
            }
            
        });
    }
}
