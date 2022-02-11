<?php

use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register',[AuthController::class,'register']);
Route::post('login',[AuthController::class,'login']);


Route::group(['middleware'=>'auth:api'],function(){
    Route::get('profile',[AuthController::class,'profile']);
    Route::post('logout',[AuthController::class,'logout']);
    
    Route::get('/posts',[PostController::class,'index']);
    Route::get('/post/{id}',[PostController::class,'show']);
    Route::post('/posts',[PostController::class,'store']);
    Route::post('/post/{id}',[PostController::class,'update']);
    Route::post('/posts/{id}',[PostController::class,'destroy']);
});




