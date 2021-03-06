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

Route::get('/', 'StaticPagesController@home')->name('home');

Route::get('/help', 'StaticPagesController@help')->name('help');

Route::get('/about', 'StaticPagesController@about')->name('about');

Route::get('signup', 'UsersController@create')->name('signup');

/**
 * 资源控制器
 */
Route::resource('users', 'UsersController');
// Route::get('/users', 'UserController@index')->name('users.index');
// Route::get('/users/{user}', 'UserController@show')->name('users.show');
// Route::get('/users/create', 'UserController@create')->name('users.create');
// Route::post('/users', 'UserController@store')->name('users.store');
// Route::get('/users/{user}/edit', 'UserController@edit')->name('users.edit');
// Route::patch('/users/{user}', 'UserController@update')->name('users.update');
// Route::delete('/users/{user}', 'UserController@destroy')->name('users.destroy');

Route::get('/login', 'SessionController@create')->name('login');
Route::post('/login', 'SessionController@store')->name('login');
Route::delete('/logout', 'SessionController@destroy')->name('logout');

Route::get('signup/confirm/{token}', 'UsersController@confirmEmail')->name('confirm_email');

// 密码重置路由
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

// 微博的创建
Route::resource('statuses', 'StatusesController',
    ['only'=>['store', 'destroy']]
);