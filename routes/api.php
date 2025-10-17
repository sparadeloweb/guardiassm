<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    // Doctors
    Route::post('/doctors', [App\Http\Controllers\Doctors\DoctorsController::class, 'store']);

    // Shift Types
    Route::post('/shift-types', [App\Http\Controllers\Shifts\ShiftTypesController::class, 'store']);

    // Patients
    Route::post('/patients', [App\Http\Controllers\Patients\PatientsController::class, 'store']);

    // Pathologies
    Route::post('/pathologies', [App\Http\Controllers\Patients\PathologiesController::class, 'store']);
});
