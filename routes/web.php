<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ParkingController;
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

Route::get('/', function () {
    return view('welcome');
})->name('homepage');


Route::get('/parking-lot-empty-spaces', [ParkingController::class, 'getEmptySpaces'])->name('parking.empty-spaces');
Route::get('/check-vehicle-expense', [ParkingController::class, 'checkVehicleExpense'])->name('check-vehicle-expense');

Route::get('/enter-parking', [ParkingController::class, 'showEntryForm'])->name('enter-parking');
Route::post('/enter-parking', [ParkingController::class, 'enterParking'])->name('process-entry');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
