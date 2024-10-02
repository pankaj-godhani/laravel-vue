<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\StudentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [HomeController::class, 'login']);
Route::post('register', [HomeController::class, 'register']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->post('logout', [HomeController::class, 'logout'])->name('logout');

Route::middleware('auth:sanctum')->group(function(){
    Route::get('students', [StudentController::class, 'index']);
    Route::post('students', [StudentController::class, 'store']);
});
