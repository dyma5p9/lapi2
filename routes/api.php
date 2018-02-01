<?php

use Illuminate\Http\Request;

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

Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');

Route::group(['middleware' => 'auth:api'], function(){

    Route::get('/notes', ['as' => 'note.notes', 'uses' => 'API\NoteController@index']);
    Route::get('/note/{id}', ['as' => 'note.show', 'uses' => 'API\NoteController@show']);
    Route::put('/note', ['as' => 'note.store', 'uses' => 'API\NoteController@store']);
    Route::post('/note/{id}', ['as' => 'note.update', 'uses' => 'API\NoteController@update']);
    Route::delete('/note/{id}', ['as' => 'note.destroy', 'uses' => 'API\NoteController@destroy']);

    Route::get('/albums', ['as' => 'note.albums', 'uses' => 'API\AlbumController@index']);
    Route::get('/album/{id}', ['as' => 'album.show', 'uses' => 'API\AlbumController@show']);
    Route::put('/album', ['as' => 'album.store', 'uses' => 'API\AlbumController@store']);
    Route::post('/album/{id}', ['as' => 'album.update', 'uses' => 'API\AlbumController@update']);
    Route::delete('/album/{id}', ['as' => 'album.destroy', 'uses' => 'API\AlbumController@destroy']);

    Route::get('/photos/{album?}', ['as' => 'note.photos', 'uses' => 'API\PhotoController@index']);
    Route::get('/photo/{id}', ['as' => 'photo.show', 'uses' => 'API\PhotoController@show']);
    Route::post('/photo', ['as' => 'photo.store', 'uses' => 'API\PhotoController@store']);
    Route::post('/photo_update', ['as' => 'photo.update', 'uses' => 'API\PhotoController@update']);
    Route::delete('/photo/{id}', ['as' => 'photo.destroy', 'uses' => 'API\PhotoController@destroy']);

    //Route::post('details', 'API\UserController@details');

});

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/
