<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\FeedbackController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login',[UserController::class,'login']);
Route::post('register',[UserController::class,'register']);

Route::group(['middleware' => 'auth:sanctum'],function(){
    //Auth Routes
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('logout',[UserController::class,'logout']);

    //Feedback Routes
    Route::resource('feedback', FeedbackController::class);
    Route::post('feedback/{id}/comments', [FeedbackController::class, 'addComment']);
    Route::get('feedback/{id}/linked-users', [FeedbackController::class, 'findUsersLinkedToFeedback']);

    //Category Routes
    Route::resource('category', CategoryController::class);
    
});