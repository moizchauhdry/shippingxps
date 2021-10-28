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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/upload_image', 'PostController@upload_image')->name('upload_image');

Route::get('/recentBlogs', 'BlogController@recentBlogs')->name('recentBlogs');

Route::get('/popularBlogs', 'BlogController@popularBlogs')->name('popularBlogs');

Route::get('/mainBlogs', 'BlogController@mainBlogs')->name('mainBlogs');

Route::get('/featuredBlogs', 'BlogController@featuredBlogs')->name('featuredBlogs');

Route::get('/{symbol}/history', 'MarketController@history')->name('featuredBlogs');

Route::get('/major-indexes', 'MarketController@majorIndexes')->name('major-indexes');

Route::get('/crypto-sidebar', 'MarketController@cryptoSidebar')->name('major-indexes');


Route::get('/stock-losers', 'StockController@losers')->name('stock-losers');

Route::get('/stock-most-active', 'StockController@mostActive')->name('stock-most-active');