<?php

use App\Http\Controllers\CarController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [CarController::class, 'index'])->name('home');
Route::get('/cars', [CarController::class, 'index'])->name('cars.index');

Route::middleware('auth')->group(function () {
    Route::get('/cars/create/step-1', [CarController::class, 'createStepOne'])->name('cars.create.step1');
    Route::post('/cars/create/step-1', [CarController::class, 'storeStepOne'])->name('cars.create.step1.store');

    Route::get('/cars/create/step-2', [CarController::class, 'createStepTwo'])->name('cars.create.step2');
    Route::post('/cars/create/step-2', [CarController::class, 'storeStepTwo'])->name('cars.create.step2.store');

    Route::get('/my-offers', [CarController::class, 'myOffers'])->name('cars.my-offers');
});

require __DIR__.'/auth.php';
