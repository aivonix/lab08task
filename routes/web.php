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

Route::get('/parking-lot-empty-spaces', [ParkingController::class, 'showEmptySpacesForm'])->name('empty-spaces-form');
Route::get('/check-vehicle-expense', [ParkingController::class, 'checkVehicleExpenseForm'])->name('check-expense-form');

Route::get('/enter-parking', [ParkingController::class, 'showEntryForm'])->name('enter-form');

Route::get('/exit-parking', [ParkingController::class, 'showExitForm'])->name('exit-form');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
