<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DemoController;
use App\Http\Controllers\UserController;
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

//Route::get('/', function () {
//    return view('homepage');
//});

Route::get('/',[DemoController::class, 'homepage']);

Route::get('/about',[DemoController::class, 'aboutPage']);

Route::post('/register', [UserController::class, 'register']);

//Route::controller(DemoController::class)->group(function (){
//    Route::get('/about', 'Index')->name('about.page');
//    Route::get('/contact',  'ContactMethod');
//});

//Route::get('/about', [DemoController::class, 'Index']);
//Route::get('/contact', [DemoController::class, 'ContactMethod']);

//
//Route::get('/contact', function(){
//    return view('contact');
//});
//Route::get('/contactme',[DemoController::class,'show']);
.//Route::get('user/{$user}', [UserController::class, 'loadUserView']);
