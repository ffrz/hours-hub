<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\TimeEntryController;
use App\Http\Controllers\Admin\TimeTrackerController;
use App\Http\Middleware\Auth;
use App\Http\Middleware\NonAuthenticated;
use App\Http\Middleware\OnlyAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()) {
        return redirect()->route('admin.dashboard');
    }
    return inertia('Welcome');
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
        Route::get('', function() { return redirect()->route('admin.dashboard'); });
        Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('dashboard/data', [DashboardController::class, 'data'])->name('admin.dashboard.data');

        Route::prefix('reports')->group(function () {
            Route::get('', [ReportController::class, 'index'])->name('admin.report.index');
        });

        Route::get('projects/list', [ProjectController::class, 'list'])->name('admin.project.list');

        Route::middleware([OnlyAdmin::class])->group(function () {
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

            Route::prefix('time-entries')->group(function () {
                Route::get('', [TimeEntryController::class, 'index'])->name('admin.time-entry.index');
                Route::get('data', [TimeEntryController::class, 'data'])->name('admin.time-entry.data');
                Route::get('add', [TimeEntryController::class, 'editor'])->name('admin.time-entry.add');
                Route::get('edit/{id}', [TimeEntryController::class, 'editor'])->name('admin.time-entry.edit');
                Route::post('save', [TimeEntryController::class, 'save'])->name('admin.time-entry.save');
                Route::post('delete/{id}', [TimeEntryController::class, 'delete'])->name('admin.time-entry.delete');
            });
        });

        Route::prefix('time-tracker')->group(function () {
            Route::get('', [TimeTrackerController::class, 'index'])->name('admin.time-tracker.index');
            Route::get('data', [TimeTrackerController::class, 'data'])->name('admin.time-tracker.data');
            Route::post('start', [TimeTrackerController::class, 'start'])->name('admin.time-tracker.start');
            Route::post('stop', [TimeTrackerController::class, 'stop'])->name('admin.time-tracker.stop');
            Route::post('update', [TimeTrackerController::class, 'update'])->name('admin.time-tracker.update');
            Route::post('sync', [TimeTrackerController::class, 'sync'])->name('admin.time-tracker.sync');
            Route::post('cancel', [TimeTrackerController::class, 'cancel'])->name('admin.time-tracker.cancel');
            Route::post('check-last-session', [TimeTrackerController::class, 'checkLastSession'])->name('admin.time-tracker.check-last-session');
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
            })->middleware(OnlyAdmin::class);
        });
    });
});
