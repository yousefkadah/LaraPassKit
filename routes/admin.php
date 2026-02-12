<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminApprovalController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin dashboard
    Route::get('/', [AdminController::class, 'index'])->name('index');

    // User management
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');

    // Account approval queue
    Route::get('/approvals', [AdminApprovalController::class, 'index'])->name('approvals.index');
    Route::post('/approvals/{user}/approve', [AdminApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('/approvals/{user}/reject', [AdminApprovalController::class, 'reject'])->name('approvals.reject');
    Route::get('/approvals/approved', [AdminApprovalController::class, 'approved'])->name('approvals.approved');
    Route::get('/approvals/rejected', [AdminApprovalController::class, 'rejected'])->name('approvals.rejected');
});
