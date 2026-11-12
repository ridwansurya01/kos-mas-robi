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
Route::post('/api/survey/submit', [LandingPageController::class, 'submitSurvey'])->name('api.survey.submit');

// Dynamic Sitemap XML for Google Search Engine Bot
Route::get('/sitemap.xml', function () {
    $baseUrl = url('/');
    $now = now()->toAtomString();

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    $xml .= '  <url>' . "\n";
    $xml .= '    <loc>' . htmlspecialchars($baseUrl, ENT_XML1, 'UTF-8') . '</loc>' . "\n";
    $xml .= '    <lastmod>' . $now . '</lastmod>' . "\n";
    $xml .= '    <changefreq>daily</changefreq>' . "\n";
    $xml .= '    <priority>1.0</priority>' . "\n";
    $xml .= '  </url>' . "\n";
    $xml .= '  <url>' . "\n";
    $xml .= '    <loc>' . htmlspecialchars($baseUrl . '/login', ENT_XML1, 'UTF-8') . '</loc>' . "\n";
    $xml .= '    <lastmod>' . $now . '</lastmod>' . "\n";
    $xml .= '    <changefreq>monthly</changefreq>' . "\n";
    $xml .= '    <priority>0.6</priority>' . "\n";
    $xml .= '  </url>' . "\n";
    $xml .= '</urlset>';

    return response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
})->name('sitemap');

// Dynamic robots.txt with absolute sitemap URL
Route::get('/robots.txt', function () {
    $sitemapUrl = url('/sitemap.xml');
    $content = "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /portal\nDisallow: /api/\nDisallow: /storage/\n\nSitemap: {$sitemapUrl}\n";
    return response($content, 200, ['Content-Type' => 'text/plain; charset=utf-8']);
});

// =========================================================================
// AUTHENTICATION ROUTES (accessible by all, controller handles auth state)
// Rate limiting applied to prevent brute-force attacks and abuse
// Pendaftaran akun penghuni baru dikelola langsung oleh Admin & Owner
// =========================================================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.post');

// Public registration is disabled; accounts are created by Admin/Owner during tenant intake
Route::get('/register', function () {
    return redirect()->route('login')->with('info', 'Pendaftaran akun baru dilakukan langsung oleh Admin / Pengelola kos saat penempatan kamar.');
})->name('register');
Route::post('/register', function () {
    return redirect()->route('login')->with('info', 'Pendaftaran akun baru dilakukan langsung oleh Admin / Pengelola kos saat penempatan kamar.');
})->name('register.post');

// ─── Google OAuth ───
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::get('/tenant/connect-google', [AuthController::class, 'connectGoogle'])->name('tenant.google.connect')->middleware('auth');

// ─── Forgot / Reset Password ───
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->middleware('throttle:3,1')->name('password.email');
Route::post('/forgot-password/send-otp', [AuthController::class, 'sendForgotOtp'])->middleware('throttle:3,1')->name('password.send_otp');
Route::post('/forgot-password/verify-otp', [AuthController::class, 'verifyForgotOtp'])->middleware('throttle:6,1')->name('password.verify_otp');
Route::post('/forgot-password/update', [AuthController::class, 'resetPasswordWithOtp'])->middleware('throttle:5,1')->name('password.update_otp');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:5,1')->name('password.update');

// Quick demo switcher (dev-only convenience, requires existing auth)
Route::get('/quick-login/{role?}', [AuthController::class, 'quickLogin'])->name('quick-login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// =========================================================================
// 1. OWNER & ADMIN DASHBOARD ROUTES
// =========================================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', function (\Illuminate\Http\Request $request) {
        if (!Auth::user()->isStaffOrOwner()) {
            return redirect()->route('tenant.portal');
        }
        return app(DashboardController::class)->index($request);
    })->name('admin.dashboard');

    Route::get('/dashboard', function () {
        if (!Auth::user()->isStaffOrOwner()) {
            return redirect()->route('tenant.portal');
        }
        return redirect()->route('admin.dashboard');
    });

    Route::get('/admin/dashboard', function () {
        if (!Auth::user()->isStaffOrOwner()) {
            return redirect()->route('tenant.portal');
        }
        return redirect()->route('admin.dashboard');
    });

    // Laporan Keuangan Stats: Khusus Role Owner
    Route::get('/finances/stats', function (\Illuminate\Http\Request $req) {
        if (!Auth::user()->isOwner()) {
            abort(403, 'Akses Laporan Keuangan khusus Role Owner.');
        }
        return app(DashboardController::class)->getFinanceStats($req);
    })->name('finances.stats.direct');

    // Live Notifications Route (Direct & API)
    Route::get('/notifications/live', [DashboardController::class, 'getLiveNotifications'])->name('notifications.live');

    // API routes — Owner & Admin
    Route::prefix('api')->group(function () {
        Route::get('/data', function (\Illuminate\Http\Request $req) {
            if (!Auth::user()->isStaffOrOwner()) abort(403);
            return app(DashboardController::class)->getJsonData($req);
        })->name('api.data');

        Route::post('/rooms', function (\Illuminate\Http\Request $req) {
            if (!Auth::user()->isStaffOrOwner()) abort(403);
            return app(DashboardController::class)->saveRoom($req);
        })->name('api.rooms.save');

        Route::post('/rooms/upload-photo', function (\Illuminate\Http\Request $req) {
            if (!Auth::user()->isStaffOrOwner()) abort(403);
            return app(DashboardController::class)->uploadRoomPhoto($req);
        })->name('api.rooms.upload_photo');

        Route::post('/rooms/{id}/status', function (\Illuminate\Http\Request $req, $id) {
            if (!Auth::user()->isStaffOrOwner()) abort(403);
            return app(DashboardController::class)->updateRoomStatus($req, $id);
        })->name('api.rooms.status');

        Route::post('/rooms/{id}/custom-interior', function (\Illuminate\Http\Request $req, $id) {
            if (!Auth::user()->isStaffOrOwner()) abort(403);
            return app(DashboardController::class)->updateCustomInterior($req, $id);
        })->name('api.rooms.custom_interior');

        Route::post('/rooms/{id}/delete', function (\Illuminate\Http\Request $req, $id) {
            if (!Auth::user()->isStaffOrOwner()) abort(403);
            return app(DashboardController::class)->deleteRoom($id);
        })->name('api.rooms.delete');

        Route::post('/floors/{floor}/delete', function (\Illuminate\Http\Request $req, $floor) {
            if (!Auth::user()->isStaffOrOwner()) abort(403);
            return app(DashboardController::class)->deleteFloor($req, $floor);
        })->name('api.floors.delete');

        Route::post('/tenants', function (\Illuminate\Http\Request $req) {
            if (!Auth::user()->isStaffOrOwner()) abort(403);
            return app(DashboardController::class)->saveTenant($req);
        })->name('api.tenants.save');

        Route::get('/tenants/{id}', function ($id) {
            if (!Auth::user()->isStaffOrOwner()) abort(403);
            return app(DashboardController::class)->getTenant($id);
        })->name('api.tenants.get');

        Route::post('/tenants/{id}/update', function (\Illuminate\Http\Request $req, $id) {
            if (!Auth::user()->isStaffOrOwner()) abort(403);
            return app(DashboardController::class)->updateTenant($req, $id);
        })->name('api.tenants.update');

        Route::post('/tenants/{id}/checkout', function (\Illuminate\Http\Request $req, $id) {
            if (!Auth::user()->isStaffOrOwner()) abort(403);
            return app(DashboardController::class)->checkoutTenant($req, $id);
        })->name('api.tenants.checkout');

        Route::post('/tenants/{id}/due-day', function (\Illuminate\Http\Request $req, $id) {
            if (!Auth::user()->isStaffOrOwner()) abort(403);
            return app(DashboardController::class)->updateTenantDueDay($req, $id);
        })->name('api.tenants.due_day');

        Route::post('/finances', function (\Illuminate\Http\Request $req) {
            if (!Auth::user()->isStaffOrOwner()) abort(403);
            return app(DashboardController::class)->saveFinance($req);
        })->name('api.finances.save');

        Route::post('/finances/{id}/send-receipt', function (\Illuminate\Http\Request $req, $id) {
            if (!Auth::user()->isStaffOrOwner()) abort(403);
            return app(DashboardController::class)->sendReceiptToTenant($req, $id);
        })->name('api.finances.send-receipt');

        // Laporan Keuangan: Khusus Role Owner
        Route::get('/finances/stats', function (\Illuminate\Http\Request $req) {
            if (!Auth::user()->isOwner()) {
                abort(403, 'Akses Laporan Keuangan khusus Role Owner.');
            }
            return app(DashboardController::class)->getFinanceStats($req);
        })->name('api.finances.stats');

        Route::post('/maintenance', function (\Illuminate\Http\Request $req) {
            if (!Auth::user()->isStaffOrOwner()) abort(403);
            return app(DashboardController::class)->saveMaintenance($req);
        })->name('api.maintenance.save');

        Route::post('/maintenance/{id}/status', function (\Illuminate\Http\Request $req, $id) {
            if (!Auth::user()->isStaffOrOwner()) abort(403);
            return app(DashboardController::class)->updateMaintenanceStatus($req, $id);
        })->name('api.maintenance.status');

        Route::post('/settings', function (\Illuminate\Http\Request $req) {
            if (!Auth::user()->isStaffOrOwner()) abort(403);
            return app(DashboardController::class)->saveSettings($req);
        })->name('api.settings.save');

        Route::post('/settings/landing', function (\Illuminate\Http\Request $req) {
            if (!Auth::user()->isStaffOrOwner()) abort(403);
            return app(DashboardController::class)->saveLandingPage($req);
        })->name('api.settings.landing');

        Route::post('/settings/login-page', function (\Illuminate\Http\Request $req) {
            if (!Auth::user()->isStaffOrOwner()) abort(403);
            return app(DashboardController::class)->saveLoginPage($req);
        })->name('api.settings.login_page');

        Route::post('/settings/email', function (\Illuminate\Http\Request $req) {
            if (!Auth::user()->isStaffOrOwner()) abort(403);
            return app(DashboardController::class)->saveEmailSettings($req);
        })->name('api.settings.email');

        Route::post('/settings/email-test', function (\Illuminate\Http\Request $req) {
            if (!Auth::user()->isStaffOrOwner()) abort(403);
            return app(DashboardController::class)->testSendEmail($req);
        })->name('api.settings.email_test');

        Route::get('/export', function (\Illuminate\Http\Request $req) {
            if (!Auth::user()->isStaffOrOwner()) abort(403);
            return app(DashboardController::class)->exportData($req);
        })->name('api.export');

        Route::post('/reset', function (\Illuminate\Http\Request $req) {
            if (!Auth::user()->isOwner()) abort(403, 'Reset data hanya dapat dilakukan oleh Owner.');
            return app(DashboardController::class)->resetData($req);
        })->name('api.reset');

        // Email Reminders API
        Route::post('/reminders/dispatch', [EmailReminderController::class, 'dispatchReminders'])->name('api.reminders.dispatch');
        Route::post('/reminders/test', [EmailReminderController::class, 'sendTestReminder'])->name('api.reminders.test');

        // Live Notifications AJAX API
        Route::get('/notifications/live', [DashboardController::class, 'getLiveNotifications'])->name('api.notifications.live');

        // Location Survey Status & Delete API
        Route::post('/surveys/{id}/status', function (\Illuminate\Http\Request $req, $id) {
            if (!Auth::user()->isStaffOrOwner()) abort(403);
            return app(DashboardController::class)->updateSurveyStatus($req, $id);
        })->name('api.surveys.status');

        Route::post('/surveys/{id}/delete', function (\Illuminate\Http\Request $req, $id) {
            if (!Auth::user()->isStaffOrOwner()) abort(403);
            return app(DashboardController::class)->deleteSurvey($req, $id);
        })->name('api.surveys.delete');

        Route::post('/surveys/{id}/calendar', function (\Illuminate\Http\Request $req, $id) {
            if (!Auth::user()->isStaffOrOwner()) abort(403);
            return app(DashboardController::class)->addToCalendar($req, $id);
        })->name('api.surveys.calendar');

        // Room Transfers Approval API
        Route::post('/room-transfers/{id}/status', function (\Illuminate\Http\Request $req, $id) {
            if (!Auth::user()->isStaffOrOwner()) abort(403);
            return app(DashboardController::class)->updateRoomTransferStatus($req, $id);
        })->name('api.room_transfers.status');

        // Manajemen Admin (Khusus Role Owner & Admin)
        Route::post('/admins', function (\Illuminate\Http\Request $req) {
            if (!Auth::user()->isStaffOrOwner()) abort(403, 'Akses khusus Pengelola Kos.');
            return app(DashboardController::class)->saveAdmin($req);
        })->name('api.admins.save');

        Route::delete('/admins/{id}', function (\Illuminate\Http\Request $req, $id) {
            if (!Auth::user()->isStaffOrOwner()) abort(403, 'Akses khusus Pengelola Kos.');
            return app(DashboardController::class)->deleteAdmin($req, $id);
        })->name('api.admins.delete');

        Route::post('/admins/{id}/delete', function (\Illuminate\Http\Request $req, $id) {
            if (!Auth::user()->isStaffOrOwner()) abort(403, 'Akses khusus Pengelola Kos.');
            return app(DashboardController::class)->deleteAdmin($req, $id);
        })->name('api.admins.delete_post');

        Route::post('/admins/{id}/branch', function (\Illuminate\Http\Request $req, $id) {
            if (!Auth::user()->isStaffOrOwner()) abort(403, 'Akses khusus Pengelola Kos.');
            return app(DashboardController::class)->updateAdminBranch($req, $id);
        })->name('api.admins.branch');
    });
});

// =========================================================================
// 2. TENANT / PELANGGAN PORTAL ROUTES — Tenant only (auth required)
// =========================================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/portal', [TenantPortalController::class, 'index'])->name('tenant.portal');
    Route::post('/portal/complete-profile', [TenantPortalController::class, 'completeProfile'])->name('tenant.profile.complete');
    Route::post('/portal/payment', [TenantPortalController::class, 'submitPayment'])->name('tenant.payment.submit');
    Route::post('/portal/complaint', [TenantPortalController::class, 'submitComplaint'])->name('tenant.complaint.submit');
    Route::post('/portal/profile', [TenantPortalController::class, 'updateProfile'])->name('tenant.profile.update');
    Route::post('/portal/profile/send-otp', [TenantPortalController::class, 'sendPasswordOtp'])->middleware('throttle:3,1')->name('tenant.profile.send_otp');
    Route::post('/portal/profile/verify-otp', [TenantPortalController::class, 'verifyPasswordOtp'])->middleware('throttle:6,1')->name('tenant.profile.verify_otp');
    Route::post('/portal/profile/update-password', [TenantPortalController::class, 'updatePasswordWithOtp'])->middleware('throttle:5,1')->name('tenant.profile.update_password');
    Route::post('/portal/profile/send-new-password', [TenantPortalController::class, 'sendNewPasswordToGmail'])->middleware('throttle:3,1')->name('tenant.profile.send_new_password');
    Route::post('/portal/room/{id}/custom-interior', [TenantPortalController::class, 'requestCustomInterior'])->name('tenant.room.custom_interior');
    Route::post('/portal/room-transfer', [TenantPortalController::class, 'submitRoomTransfer'])->name('tenant.room.transfer');
});
