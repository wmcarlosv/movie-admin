<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('movies/{order?}','MoviesController@getByPhone');
Route::get('series/{order?}','SeriesController@getByPhone');

Route::get('movie/{id}','MoviesController@movieById');
Route::get('movie-categories/{id}','MoviesController@getCategoriesByMovie');
Route::post('movies-by-categories','MoviesController@getMoviesByCategories');

Route::get('categories','MoviesController@getCategories');

Route::get('search/{type}/{q}','MoviesController@searchData');

Route::get('application/{code}','ApplicationsController@getData');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});