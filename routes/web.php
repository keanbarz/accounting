<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\all;
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



//antiregister
/*Route::get('/register', function () {
    return redirect('/');
});*/
Route::get('/', function () {
    return redirect('login');
});

Route::get('/register', [ProfileController::class, 'create']);


//VoucherTracker
    Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [all::class, 'dashboard'])->name('dashboard');
    Route::post('/save', [all::class, 'save'])->middleware(['auth', 'verified']);
    Route::post('/delete', [all::class, 'delete'])->middleware(['auth', 'verified']);
    Route::get('/forward', [all::class, 'forward'])->name('forward');
    Route::post('/submitforward', [all::class, 'submitforward'])->name('submitforward');});
    
    Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
