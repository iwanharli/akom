<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientRegistrationController;
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

// Public report view via UUID (no auth required)
Route::get('/reports/{uuid}', [ReportController::class, 'show'])->name('reports.show');

// Public client registration
Route::get('/register-client', [ClientRegistrationController::class, 'create'])->name('client.register');
Route::post('/register-client', [ClientRegistrationController::class, 'store'])->name('client.register.store');

Route::middleware(['auth', 'role:superadmin,admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('clients', ClientController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::put('/clients/{client}/toggle-status', [ClientController::class, 'toggleStatus'])->name('clients.toggle-status');

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

// Superadmin exclusive routes
Route::middleware(['auth', 'role:superadmin'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
});

require __DIR__.'/auth.php';
