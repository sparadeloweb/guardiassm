<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Doctors\DoctorsController;

Route::prefix('doctors')->middleware('auth')->group(function () {
    Route::get('/', [DoctorsController::class, 'index'])->name('doctors.index');

    Route::get('/create', [DoctorsController::class, 'create'])->name('doctors.create');

    Route::post('/', [DoctorsController::class, 'store'])->name('doctors.store');

    Route::get('/{doctor}', [DoctorsController::class, 'show'])->name('doctors.show');

    Route::get('/{doctor}/edit', [DoctorsController::class, 'edit'])->name('doctors.edit');

    Route::put('/{doctor}', [DoctorsController::class, 'update'])->name('doctors.update');

    Route::delete('/{doctor}', [DoctorsController::class, 'destroy'])->name('doctors.destroy');
});
