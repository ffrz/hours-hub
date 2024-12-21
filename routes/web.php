<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\TimeTrackerController;
use App\Http\Middleware\Auth;
use App\Http\Middleware\NonAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return inertia('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

Route::middleware(NonAuthenticated::class)->group(function () {
    Route::prefix('/admin/auth')->group(function () {
        Route::match(['get', 'post'], 'login', [AuthController::class, 'login'])->name('admin.auth.login');
        Route::match(['get', 'post'], 'register', [AuthController::class, 'register'])->name('admin.auth.register');
        Route::match(['get', 'post'], 'forgot-password', [AuthController::class, 'forgotPassword'])->name('admin.auth.forgot-password');
    });
});

Route::middleware([Auth::class])->group(function () {
    Route::match(['get', 'post'], 'admin/auth/logout', [AuthController::class, 'logout'])->name('admin.auth.logout');

    Route::prefix('admin')->group(function () {
        Route::redirect('', 'admin/dashboard', 301);

        Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('test', [DashboardController::class, 'test'])->name('admin.test');
        Route::get('about', function () {
            return inertia('admin/About');
        })->name('admin.about');

        Route::prefix('projects')->group(function () {
            Route::get('', [ProjectController::class, 'index'])->name('admin.project.index');
            Route::get('data', [ProjectController::class, 'data'])->name('admin.project.data');
            Route::get('add', [ProjectController::class, 'editor'])->name('admin.project.add');
            Route::get('edit/{id}', [ProjectController::class, 'editor'])->name('admin.project.edit');
            Route::post('save', [ProjectController::class, 'save'])->name('admin.project.save');
            Route::post('delete/{id}', [ProjectController::class, 'delete'])->name('admin.project.delete');
        });

        Route::prefix('clients')->group(function () {
            Route::get('', [ClientController::class, 'index'])->name('admin.client.index');
            Route::get('data', [ClientController::class, 'data'])->name('admin.client.data');
            Route::get('add', [ClientController::class, 'editor'])->name('admin.client.add');
            Route::get('edit/{id}', [ClientController::class, 'editor'])->name('admin.client.edit');
            Route::post('save', [ClientController::class, 'save'])->name('admin.client.save');
            Route::post('delete/{id}', [ClientController::class, 'delete'])->name('admin.client.delete');
        });

        Route::prefix('time-tracker')->group(function () {
            Route::get('', [TimeTrackerController::class, 'index'])->name('admin.time-tracker.index');
            Route::get('data', [TimeTrackerController::class, 'data'])->name('admin.time-tracker.data');
            Route::get('add', [TimeTrackerController::class, 'editor'])->name('admin.time-tracker.add');
            Route::get('edit/{id}', [TimeTrackerController::class, 'editor'])->name('admin.time-tracker.edit');
            Route::post('save', [TimeTrackerController::class, 'save'])->name('admin.time-tracker.save');
            Route::post('delete/{id}', [TimeTrackerController::class, 'delete'])->name('admin.time-tracker.delete');
        });

        Route::prefix('settings')->group(function () {
            Route::get('profile/edit', [ProfileController::class, 'edit'])->name('admin.profile.edit');
            Route::post('profile/update', [ProfileController::class, 'update'])->name('admin.profile.update');
            Route::post('profile/update-password', [ProfileController::class, 'updatePassword'])->name('admin.profile.update-password');

            Route::prefix('users')->group(function () {
                Route::get('', [UserController::class, 'index'])->name('admin.user.index');
                Route::get('data', [UserController::class, 'data'])->name('admin.user.data');
                Route::get('add', [UserController::class, 'editor'])->name('admin.user.add');
                Route::get('edit/{id}', [UserController::class, 'editor'])->name('admin.user.edit');
                Route::post('save', [UserController::class, 'save'])->name('admin.user.save');
                Route::post('delete/{id}', [UserController::class, 'delete'])->name('admin.user.delete');
            });
        });
    });
});
