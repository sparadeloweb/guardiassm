<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Shifts\ShiftTypesController;
use App\Http\Controllers\Shifts\ShiftsController;

Route::prefix('shift-types')->middleware('auth')->group(function () {
    Route::get('/', [ShiftTypesController::class, 'index'])->name('shift-types.index');

    Route::get('/create', [ShiftTypesController::class, 'create'])->name('shift-types.create');

    Route::post('/', [ShiftTypesController::class, 'store'])->name('shift-types.store');

    Route::get('/{shiftType}', [ShiftTypesController::class, 'show'])->name('shift-types.show');

    Route::get('/{shiftType}/edit', [ShiftTypesController::class, 'edit'])->name('shift-types.edit');

    Route::put('/{shiftType}', [ShiftTypesController::class, 'update'])->name('shift-types.update');

    Route::delete('/{shiftType}', [ShiftTypesController::class, 'destroy'])->name('shift-types.destroy');
});

Route::prefix('shifts')->middleware('auth')->group(function () {
    Route::get('/', [ShiftsController::class, 'index'])->name('shifts.index');

    Route::get('/create', [ShiftsController::class, 'create'])->name('shifts.create');

    Route::post('/', [ShiftsController::class, 'store'])->name('shifts.store');

    Route::get('/{shift}', [ShiftsController::class, 'show'])->name('shifts.show');

    Route::get('/{shift}/edit', [ShiftsController::class, 'edit'])->name('shifts.edit');

    Route::put('/{shift}', [ShiftsController::class, 'update'])->name('shifts.update');

    Route::delete('/{shift}', [ShiftsController::class, 'destroy'])->name('shifts.destroy');
});
