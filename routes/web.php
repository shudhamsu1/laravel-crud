<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DemoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Gate;
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

//this is the using gate with only controller method
//Route::get('/admins-only', function(){
//    if(Gate::allows('visitAdminPages')){
//        return 'Only admins should be able to see this page';
//    };
//    return 'You cannot use this page';
//});
//here in this route we have defined gatea action in AuthServiceProvider
Route::get('/admins-only', function() {
    return 'Only admins should be able to see this page';
})->middleware('can:visitAdminsPages');

Route::get('/',[UserController::class, 'showCorrectHomepage'])->name('login');

Route::get('/about',[DemoController::class, 'aboutPage']);

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout']);
Route::get('/manage-avatar',[UserController::class, 'showAvatarForm'])->middleware('mustBeloggedIn');
Route::post('/manage-avatar',[UserController::class, 'storeAvatar'])->middleware('mustBeloggedIn');

//Blog post related routes

Route::get('/create-post', [PostController::class, 'showCreateForm'])->name('createPost')->middleware('mustBeloggedIn');
Route::post('/create-post', [PostController::class, 'storeNewPost'])->name('storePost')->middleware('mustBeloggedIn');
Route::get('/post/{post}', [PostController::class, 'viewSinglePost'])->name('postPage');
Route::delete('/post/{post}', [PostController::class, 'delete'])->middleware('can:delete,post');

Route::get('/post/{post}/edit', [PostController::class, 'showEditForm'])->middleware('can:update,post');
Route::put('post/{post}',[PostController::class, 'actuallyUpdate'])->middleware('can:update,post');

//Profile related routes
Route::get('profile/{user:username}', [UserController::class, 'profile']);
//in the above route we want the model to be using username instead of id
