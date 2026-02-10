<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\Manage\InventoryController;
use App\Http\Controllers\Manage\OmsetController;
use App\Http\Controllers\Manage\TherapistController;
use App\Http\Controllers\Manage\TherapistNameController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/archives', [ArchiveController::class, 'index'])->name('archives.index');
    Route::post('/archives/archive-now', [ArchiveController::class, 'archiveNow'])->name('archives.archive');
    Route::get('/archives/export', [ArchiveController::class, 'export'])->name('archives.export');

    Route::prefix('manage')->group(function () {
        Route::prefix('omset')->name('manage.omset.')->group(function () {
            Route::get('/', [OmsetController::class, 'index'])->name('index');
            Route::get('/create', [OmsetController::class, 'create'])->name('create');
            Route::post('/', [OmsetController::class, 'store'])->middleware('no-admin-write')->name('store');
            Route::get('/{id}/edit', [OmsetController::class, 'edit'])->name('edit');
            Route::put('/{id}', [OmsetController::class, 'update'])->middleware('no-admin-write')->name('update');
            Route::delete('/{id}', [OmsetController::class, 'destroy'])->middleware('no-admin-write')->name('destroy');
            Route::get('/export/excel', [OmsetController::class, 'exportExcel'])
                ->middleware('role:admin')
                ->name('export.excel');
            Route::get('/export/pdf', [OmsetController::class, 'exportPdf'])
                ->middleware('role:admin')
                ->name('export.pdf');
            Route::get('/print', [OmsetController::class, 'print'])
                ->middleware('role:admin')
                ->name('print');
        });

        Route::prefix('therapist')->name('manage.therapist.')->group(function () {
            Route::get('/', [TherapistController::class, 'index'])->name('index');
            Route::get('/summary', [TherapistController::class, 'summary'])->name('summary');
            Route::get('/monthly', [TherapistController::class, 'monthly'])->name('monthly');
            Route::get('/create', [TherapistController::class, 'create'])->name('create');
            Route::post('/', [TherapistController::class, 'store'])->middleware('no-admin-write')->name('store');
            Route::post('/names', [TherapistNameController::class, 'store'])->middleware('no-admin-write')->name('names.store');
            Route::delete('/names/{id}', [TherapistNameController::class, 'destroy'])->middleware('no-admin-write')->name('names.destroy');
            Route::get('/{id}/edit', [TherapistController::class, 'edit'])->name('edit');
            Route::put('/{id}', [TherapistController::class, 'update'])->middleware('no-admin-write')->name('update');
            Route::delete('/{id}', [TherapistController::class, 'destroy'])->middleware('no-admin-write')->name('destroy');
            Route::get('/export/excel', [TherapistController::class, 'exportExcel'])
                ->middleware('role:admin')
                ->name('export.excel');
            Route::get('/export/pdf', [TherapistController::class, 'exportPdf'])
                ->middleware('role:admin')
                ->name('export.pdf');
            Route::get('/print', [TherapistController::class, 'print'])
                ->middleware('role:admin')
                ->name('print');
        });

        Route::prefix('inventory')->name('manage.inventory.')->group(function () {
            Route::get('/', [InventoryController::class, 'index'])->name('index');
            Route::get('/create', [InventoryController::class, 'create'])->name('create');
            Route::post('/', [InventoryController::class, 'store'])->middleware('no-admin-write')->name('store');
            Route::get('/{id}/edit', [InventoryController::class, 'edit'])->name('edit');
            Route::put('/{id}', [InventoryController::class, 'update'])->middleware('no-admin-write')->name('update');
            Route::delete('/{id}', [InventoryController::class, 'destroy'])->middleware('no-admin-write')->name('destroy');
            Route::get('/{id}/stock-in', [InventoryController::class, 'stockInForm'])->name('stock-in.form');
            Route::post('/{id}/stock-in', [InventoryController::class, 'stockIn'])->middleware('no-admin-write')->name('stock-in');
            Route::get('/{id}/stock-out', [InventoryController::class, 'stockOutForm'])->name('stock-out.form');
            Route::post('/{id}/stock-out', [InventoryController::class, 'stockOut'])->middleware('no-admin-write')->name('stock-out');
            Route::get('/{id}/movements/{movement}/edit', [InventoryController::class, 'editMovement'])
                ->name('movements.edit');
            Route::put('/{id}/movements/{movement}', [InventoryController::class, 'updateMovement'])
                ->middleware('no-admin-write')
                ->name('movements.update');
            Route::get('/export/excel', [InventoryController::class, 'exportExcel'])
                ->middleware('role:admin')
                ->name('export.excel');
            Route::get('/export/pdf', [InventoryController::class, 'exportPdf'])
                ->middleware('role:admin')
                ->name('export.pdf');
            Route::get('/print', [InventoryController::class, 'print'])
                ->middleware('role:admin')
                ->name('print');
        });
    });

    Route::get('/exports', [ExportController::class, 'index'])
        ->middleware('role:admin')
        ->name('exports.index');
    Route::get('/exports/{filename}', [ExportController::class, 'download'])
        ->middleware('role:admin')
        ->name('exports.download');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
