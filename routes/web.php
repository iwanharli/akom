<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/reports/{uuid}', [ReportController::class, 'show'])->name('reports.show');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [ReportController::class, 'index'])->name('dashboard');
    Route::resource('reports-admin', ReportController::class, [
        'names' => [
            'index' => 'reports.index',
            'create' => 'reports.create',
            'store' => 'reports.store',
            'edit' => 'reports.edit',
            'update' => 'reports.update',
            'destroy' => 'reports.destroy',
        ]
    ]);
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
