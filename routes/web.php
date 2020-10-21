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

	Route::post('/get-data-video','HomeController@getDataVideo')->name('getDataVideo');
	Route::get('/get-video','HomeController@getVideo')->name('getVideo');

	Route::get('/importSeries','HomeController@importSeries')->name('import_series');
	Route::post('/importSeries','HomeController@setImportSeries')->name('set_import_series');
	Route::post('/saveSeries','HomeController@saveSeries')->name('saveSeries');
	Route::get('/getAvailablesMovies','HomeController@getAvailablesMovies')->name('getAvailablesMovies');

	Route::resource('users','UsersController')->middleware(['can:isAdmin']);
	Route::get('/profile','UsersController@profile')->name('profile');
	Route::post('/update_profile','UsersController@update_profile')->name('update_profile');
	Route::post('/change_password','UsersController@change_password')->name('change_password');

	Route::resource('categories','CategoriesController')->middleware(['can:isAdmin']);
	Route::resource('movies','MoviesController')->middleware(['can:isAdmin']);

	Route::resource('series','SeriesController')->middleware(['can:isAdmin']);
	Route::resource('seasons','SeasonsController')->middleware(['can:isAdmin']);
	Route::resource('chapters','ChaptersController')->middleware(['can:isAdmin']);
	Route::resource('applications','ApplicationsController')->middleware(['can:isAdmin']);

	Route::resource('channels','ChannelsController')->middleware(['can:isAdmin']);
});

Route::get('/see/{secure_link}','ComercialController@see_movie')->middleware('check.client')->name('see_movie');

