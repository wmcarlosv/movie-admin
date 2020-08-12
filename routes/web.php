<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Auth::routes();

Route::prefix('admin')->middleware(['auth'])->group(function(){

	Route::get('/dashboard', 'HomeController@index')->name('dashboard');
	Route::get('/importMovies','HomeController@importMovies')->name('import_movies');
	Route::post('/importMovies','HomeController@setImportMovies')->name('set_import_movies');
	Route::post('/saveMovies','HomeController@saveMovies')->name('save_movies');

	Route::resource('users','UsersController');
	Route::get('/profile','UsersController@profile')->name('profile');
	Route::post('/update_profile','UsersController@update_profile')->name('update_profile');
	Route::post('/change_password','UsersController@change_password')->name('change_password');

	Route::resource('categories','CategoriesController');
	Route::resource('movies','MoviesController');
});
