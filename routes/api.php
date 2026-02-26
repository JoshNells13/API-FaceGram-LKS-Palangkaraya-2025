<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function(){
    Route::post('auth/register',[AuthController::class,'register']);
    Route::post('auth/login',[AuthController::class,'login']);

    Route::middleware('auth:sanctum')->group(function(){
        Route::post('auth/logout',[AuthController::class,'logout']);


        Route::post('posts', [PostController::class,'createPost']);
        Route::delete('posts/{postId}',[PostController::class,'deletepost']);
        Route::get('posts',[PostController::class,'getPost']);


        Route::post('users/{username}/follow',[FollowController::class,'followUser']);
        Route::delete('users/{username}/unfollow', [FollowController::class,'unfollowUser']);
        Route::get('users/{username}/following', [FollowController::class, 'getFollowing']);

        Route::put('users/{username}/accept', [FollowController::class, 'acceptFollow']);
        Route::get('users/{username}/followers', [FollowController::class, 'getFollower']);

        Route::get('users', [UserController::class, 'index']);
        Route::get('users/{username}', [UserController::class, 'show']);

    });



});
