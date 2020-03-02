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

use Illuminate\Http\Request;

Route::domain('chatapp.keinher.com')->group(function () {

    Route::get('/contacts','UserController@get')->name('get.contacts');

    Route::get('/conversation/{id}','MessageController@get')->name('get.messages');

    Route::post('/send','MessageController@save')->name('save.message');

    Route::get('/home', 'HomeController@index')->name('home');


    /******************
     * Auth::routes();
     */
    Route::any('/login', 'UserController@login')->middleware('ajax')->name('ulog');

    Route::get('/', function () {
        return view('auth.login');
    })->middleware('guest')->name('login');

    Route::any('/register', 'Auth\RegisterController@register')->middleware('ajax','guest')->name('registrar');

    Route::get('/logout', function(){
        \Auth::logout();
        return redirect('/');
    })->middleware('auth')->name('logout');

    Route::get('/registro', function(){
        return view('auth.register');
    })->middleware('guest')->name('registro');

    Route::get('/auth', function(){
        return response()->json([ 'user' => auth()->user() ]);
    })->middleware('auth')->name('get.user');

    /**************************
     * Functionalities routes
     */
    Route::post('/read', 'MessageController@read')->name('mark.read');

    Route::post('/upload', 'UserController@upload')->name('upload');

    Route::post('/update', 'UserController@update')->name('user.update');

});