<?php

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
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('users', 'UserController@index');
    Route::get('user/info/{id}', 'UserController@info');
    Route::get('user/get-permissions-by-role', 'UserController@getPermissionsByRole');
    Route::patch('user/edit', 'UserController@edit');
    Route::delete('user/delete/{id}', 'UserController@delete');
});
