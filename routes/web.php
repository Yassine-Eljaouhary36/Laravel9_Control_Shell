<?php

use App\Http\Controllers\HomeController;
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

Route::get('/', [HomeController::class ,'index'])->name("Home");
Route::post('/proc', [HomeController::class ,'runCommand'])->name("run");

Route::post('/runProc', [HomeController::class ,'run'])->name("runProc");

Route::post('/check', [HomeController::class ,'CheckProc'])->name("CheckProc");

Route::post('/Kill', [HomeController::class ,'KillPross'])->name("KillPross");
