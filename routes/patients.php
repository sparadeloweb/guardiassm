<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Patients\PatientsController;
use App\Http\Controllers\Patients\PathologiesController;

Route::prefix('patients')->middleware('auth')->group(function () {
    Route::get('/', [PatientsController::class, 'index'])->name('patients.index');

    Route::get('/create', [PatientsController::class, 'create'])->name('patients.create');

    Route::post('/', [PatientsController::class, 'store'])->name('patients.store');

    Route::get('/{patient}', [PatientsController::class, 'show'])->name('patients.show');

    Route::get('/{patient}/edit', [PatientsController::class, 'edit'])->name('patients.edit');

    Route::put('/{patient}', [PatientsController::class, 'update'])->name('patients.update');

    Route::delete('/{patient}', [PatientsController::class, 'destroy'])->name('patients.destroy');
});

Route::prefix('pathologies')->middleware('auth')->group(function () {
    Route::get('/', [PathologiesController::class, 'index'])->name('pathologies.index');

    Route::get('/create', [PathologiesController::class, 'create'])->name('pathologies.create');

    Route::post('/', [PathologiesController::class, 'store'])->name('pathologies.store');

    Route::get('/{pathology}', [PathologiesController::class, 'show'])->name('pathologies.show');

    Route::get('/{pathology}/edit', [PathologiesController::class, 'edit'])->name('pathologies.edit');

    Route::put('/{pathology}', [PathologiesController::class, 'update'])->name('pathologies.update');

    Route::delete('/{pathology}', [PathologiesController::class, 'destroy'])->name('pathologies.destroy');
});
