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
    Route::patch('/my-offers/{car}/sold', [CarController::class, 'markAsSold'])->name('cars.mark-sold');
    Route::patch('/my-offers/{car}/activate', [CarController::class, 'markAsActive'])->name('cars.mark-active');
    Route::delete('/my-offers/{car}', [CarController::class, 'destroy'])->name('cars.destroy');
    Route::get('/my-offers/{car}/edit', [CarController::class, 'edit'])->name('cars.edit');
    Route::match(['put','patch'], '/my-offers/{car}', [CarController::class, 'update'])->name('cars.update');
});

require __DIR__.'/auth.php';
