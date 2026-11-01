<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TenantPortalController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\EmailReminderController;

// Public Landing Page inspired by modern property reference
Route::get('/', [LandingPageController::class, 'index'])->name('home');

// =========================================================================
// AUTHENTICATION ROUTES (accessible by all, controller handles auth state)
// =========================================================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// ─── Google OAuth ───
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// ─── Forgot / Reset Password ───
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Quick demo switcher (dev-only convenience, requires existing auth)
Route::get('/quick-login/{role?}', [AuthController::class, 'quickLogin'])->name('quick-login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// =========================================================================
// 1. OWNER / PENGELOLA DASHBOARD ROUTES — Owner only
// =========================================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', function (\Illuminate\Http\Request $request) {
        if (!Auth::user()->isOwner()) {
            return redirect()->route('tenant.portal');
        }
        return app(DashboardController::class)->index($request);
    })->name('admin.dashboard');

    Route::get('/dashboard', function () {
        if (!Auth::user()->isOwner()) {
            return redirect()->route('tenant.portal');
        }
        return redirect()->route('admin.dashboard');
    });

    // API routes — owner only
    Route::prefix('api')->group(function () {
        Route::get('/data', function (\Illuminate\Http\Request $req) {
            if (!Auth::user()->isOwner()) abort(403);
            return app(DashboardController::class)->getJsonData($req);
        })->name('api.data');

        Route::post('/rooms', function (\Illuminate\Http\Request $req) {
            if (!Auth::user()->isOwner()) abort(403);
            return app(DashboardController::class)->saveRoom($req);
        })->name('api.rooms.save');

        Route::post('/rooms/{id}/status', function (\Illuminate\Http\Request $req, $id) {
            if (!Auth::user()->isOwner()) abort(403);
            return app(DashboardController::class)->updateRoomStatus($req, $id);
        })->name('api.rooms.status');

        Route::post('/rooms/{id}/custom-interior', function (\Illuminate\Http\Request $req, $id) {
            if (!Auth::user()->isOwner()) abort(403);
            return app(DashboardController::class)->updateCustomInterior($req, $id);
        })->name('api.rooms.custom_interior');

        Route::post('/tenants', function (\Illuminate\Http\Request $req) {
            if (!Auth::user()->isOwner()) abort(403);
            return app(DashboardController::class)->saveTenant($req);
        })->name('api.tenants.save');

        Route::post('/tenants/{id}/checkout', function (\Illuminate\Http\Request $req, $id) {
            if (!Auth::user()->isOwner()) abort(403);
            return app(DashboardController::class)->checkoutTenant($req, $id);
        })->name('api.tenants.checkout');

        Route::post('/finances', function (\Illuminate\Http\Request $req) {
            if (!Auth::user()->isOwner()) abort(403);
            return app(DashboardController::class)->saveFinance($req);
        })->name('api.finances.save');

        Route::get('/finances/stats', function (\Illuminate\Http\Request $req) {
            if (!Auth::user()->isOwner()) abort(403);
            return app(DashboardController::class)->getFinanceStats($req);
        })->name('api.finances.stats');

        Route::post('/maintenance', function (\Illuminate\Http\Request $req) {
            if (!Auth::user()->isOwner()) abort(403);
            return app(DashboardController::class)->saveMaintenance($req);
        })->name('api.maintenance.save');

        Route::post('/maintenance/{id}/status', function (\Illuminate\Http\Request $req, $id) {
            if (!Auth::user()->isOwner()) abort(403);
            return app(DashboardController::class)->updateMaintenanceStatus($req, $id);
        })->name('api.maintenance.status');

        Route::post('/settings', function (\Illuminate\Http\Request $req) {
            if (!Auth::user()->isOwner()) abort(403);
            return app(DashboardController::class)->saveSettings($req);
        })->name('api.settings.save');

        Route::post('/settings/landing', function (\Illuminate\Http\Request $req) {
            if (!Auth::user()->isOwner()) abort(403);
            return app(DashboardController::class)->saveLandingPage($req);
        })->name('api.settings.landing');

        Route::get('/export', function (\Illuminate\Http\Request $req) {
            if (!Auth::user()->isOwner()) abort(403);
            return app(DashboardController::class)->exportData($req);
        })->name('api.export');

        Route::post('/reset', function (\Illuminate\Http\Request $req) {
            if (!Auth::user()->isOwner()) abort(403);
            return app(DashboardController::class)->resetData($req);
        })->name('api.reset');

        // Email Reminders API
        Route::post('/reminders/dispatch', [EmailReminderController::class, 'dispatchReminders'])->name('api.reminders.dispatch');
        Route::post('/reminders/test', [EmailReminderController::class, 'sendTestReminder'])->name('api.reminders.test');

        // Room Transfers Approval API
        Route::post('/room-transfers/{id}/status', function (\Illuminate\Http\Request $req, $id) {
            if (!Auth::user()->isOwner()) abort(403);
            return app(DashboardController::class)->updateRoomTransferStatus($req, $id);
        })->name('api.room_transfers.status');
    });
});

// =========================================================================
// 2. TENANT / PELANGGAN PORTAL ROUTES — Tenant only (auth required)
// =========================================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/portal', [TenantPortalController::class, 'index'])->name('tenant.portal');
    Route::post('/portal/payment', [TenantPortalController::class, 'submitPayment'])->name('tenant.payment.submit');
    Route::post('/portal/complaint', [TenantPortalController::class, 'submitComplaint'])->name('tenant.complaint.submit');
    Route::post('/portal/profile', [TenantPortalController::class, 'updateProfile'])->name('tenant.profile.update');
    Route::post('/portal/profile/send-new-password', [TenantPortalController::class, 'sendNewPasswordToGmail'])->name('tenant.profile.send_new_password');
    Route::post('/portal/room/{id}/custom-interior', [TenantPortalController::class, 'requestCustomInterior'])->name('tenant.room.custom_interior');
    Route::post('/portal/room-transfer', [TenantPortalController::class, 'submitRoomTransfer'])->name('tenant.room.transfer');
});
