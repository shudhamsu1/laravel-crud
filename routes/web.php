<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DemoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
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
//Route::get('/',[DemoController::class, 'homepage']);
//User related routes$
Route::get('/',[UserController::class, 'showCorrectHomepage'])->name('login');

Route::get('/about',[DemoController::class, 'aboutPage']);

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout']);

//Blog post related routes

Route::get('/create-post', [PostController::class, 'showCreateForm'])->name('createPost')->middleware('mustBeloggedIn');
Route::post('/create-post', [PostController::class, 'storeNewPost'])->name('storePost')->middleware('mustBeloggedIn');
Route::get('/post/{post}', [PostController::class, 'viewSinglePost']);
Route::delete('/post/{post}', [PostController::class, 'delete']);

//Profile related routes
Route::get('profile/{user:username}', [UserController::class, 'profile']);
//in the above route we want the model to be using username instead of id
