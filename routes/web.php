<?php

use App\Http\Controllers\Api\DetectingFaceController;
use App\Http\Controllers\PoliceController;
use App\Http\Controllers\PoliceUnitController;
use App\Models\PoliceUnit;
use Illuminate\Support\Facades\Auth;
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
/*
Route::post('/prueba1', [DetectingFaceController::class, 'detectedFaceImage'])->name('prueba.detectedFace');
Route::post('/prueba2', [DetectingFaceController::class, 'detectedFaceImage']);
*/
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('/police', PoliceController::class);
Route::resource('/police-unit', PoliceUnitController::class);
Route::post('/police',[PoliceController::class,'store2'])->name('police.store2');

Route::get('/prueba', [DetectingFaceController::class, 'index'])->name('prueba.index');
