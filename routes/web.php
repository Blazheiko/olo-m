<?php

use App\Http\Controllers\HomeController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
//    return Inertia::render('Welcome', [
//        'canLogin' => Route::has('login'),
//        'canRegister' => Route::has('register'),
//        'laravelVersion' => Application::VERSION,
//        'phpVersion' => PHP_VERSION,
//    ]);
//});

Route::get('/', [HomeController::class,'index']);
Route::post('/save-video/{name}', [HomeController::class,'saveVideo']);
Route::get('/start-record-video', [HomeController::class,'startRecordVideo']);

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    header('Feature-Policy: camera \'self\' '.config('app.url').'/dashboard');
    return Inertia::render('Dashboard');
})->name('dashboard');

Route::middleware(['auth:sanctum', 'verified'])->get('/my-videos', [HomeController::class,'myVideo'])->name('my_videos');
