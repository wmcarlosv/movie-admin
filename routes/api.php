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
Route::get('serie/{id}','SeriesController@serieById');

Route::get('movie-categories/{id}','MoviesController@getCategoriesByMovie');
Route::get('serie-categories/{id}','SeriesController@getCategoriesBySerie');

Route::post('movies-by-categories','MoviesController@getMoviesByCategories');
Route::post('series-by-categories','SeriesController@getSeriesByCategories');

Route::get('categories','MoviesController@getCategories');

Route::get('search/{type}/{q}','MoviesController@searchData');
Route::get('search-series/{type}/{q}','SeriesController@searchData');

Route::get('application/{code}','ApplicationsController@getData');

Route::get('channels/{category_id?}','ChannelsController@getByPhone');

Route::get('channels/by/categories','ChannelsController@getCategories');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});