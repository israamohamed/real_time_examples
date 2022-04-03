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
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('users' , 'UserController@all')->name('users.all');

Route::get('game' , 'GameController@index')->name('game.index');

Route::get('chat' , 'ChatController@showChat')->name('chat.show');

Route::post('chat/send_message' , 'ChatController@sendMessage')->name('chat.send_message');

Route::post('chat/greet/{user}' , 'ChatController@greetRecieved')->name('chat.greet');
