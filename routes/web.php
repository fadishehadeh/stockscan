<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactInquiryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\ImportExportController;
use App\Http\Controllers\InventoryApprovalController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SettingsDashboardController;
use App\Http\Controllers\StockTransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingPageController::class, 'show'])->name('landing');

// Guest routes (public, no auth required)
Route::middleware('guest')->group(function () {
    Route::post('/contact', [LandingPageController::class, 'contact'])->middleware('throttle:5,1')->name('landing.contact');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login')->name('login.store');

    // OTP verification
    Route::get('/login/otp', [OtpController::class, 'verifyForm'])->name('otp.verify.form');
    Route::post('/login/verify-otp', [OtpController::class, 'verify'])->name('otp.verify');
    Route::post('/login/resend-otp', [OtpController::class, 'resend'])->name('otp.resend');
    Route::get('/login/otp/cancel', [OtpController::class, 'cancel'])->name('otp.cancel');

    // Password reset
    Route::post('/forgot-password', [PasswordResetController::class, 'requestReset'])->name('password.request');
    Route::get('/forgot-password', [PasswordResetController::class, 'requestReset'])->name('password.request.form');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'resetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');

    // Email verification
    Route::get('/verify-email/{token}', [EmailVerificationController::class, 'verify'])->name('verification.verify');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Account management
    Route::get('/account/settings', [AccountController::class, 'showSettings'])->name('account.settings');
    Route::get('/account/change-email', [AccountController::class, 'changeEmailForm'])->name('account.change-email');
    Route::post('/account/change-email', [AccountController::class, 'changeEmail']);
    Route::get('/account/change-password', [AccountController::class, 'changePasswordForm'])->name('account.change-password');
    Route::post('/account/change-password', [AccountController::class, 'changePassword']);

    // Session management
    Route::get('/account/sessions', [SessionController::class, 'active'])->name('sessions.active');
    Route::post('/account/sessions/{id}/terminate', [SessionController::class, 'terminate'])->name('sessions.terminate');

    // Email verification
    Route::post('/verify-email/send', [EmailVerificationController::class, 'sendVerification'])->name('verification.send');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->middleware('role:super_admin|admin')->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->middleware('role:super_admin|admin')->name('products.store');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::post('/products/{product}/threshold', [ProductController::class, 'updateThreshold'])->middleware('role:super_admin|admin')->name('products.updateThreshold');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->middleware('role:super_admin|admin')->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->middleware('role:super_admin|admin')->name('products.update');
    Route::post('/products/{product}/archive', [ProductController::class, 'archive'])->middleware('role:super_admin|admin')->name('products.archive');
    Route::post('/products/{product}/restore', [ProductController::class, 'restore'])->middleware('role:super_admin|admin')->name('products.restore');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->middleware('role:super_admin|admin')->name('products.destroy');
    Route::get('/products/{product}/label', [ProductController::class, 'label'])->name('products.label');

    Route::get('/scan', [ScanController::class, 'index'])->name('scan.index');
    Route::post('/scan', [ScanController::class, 'lookup'])->name('scan.lookup');
    Route::get('/scan/{product}', [ScanController::class, 'show'])->name('scan.show');
    Route::get('/alerts', [AlertController::class, 'index'])->name('alerts.index');

    Route::get('/transactions', [StockTransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions', [StockTransactionController::class, 'store'])->name('transactions.store');
    Route::get('/approvals', [InventoryApprovalController::class, 'index'])->middleware('role:purchase_manager|super_admin|admin')->name('approvals.index');
    Route::post('/approvals/{approval}/approve', [InventoryApprovalController::class, 'approve'])->middleware('role:purchase_manager|super_admin|admin')->name('approvals.approve');
    Route::post('/approvals/{approval}/reject', [InventoryApprovalController::class, 'reject'])->middleware('role:purchase_manager|super_admin|admin')->name('approvals.reject');

    Route::get('/reports', [ReportController::class, 'index'])->middleware('role:super_admin|admin')->name('reports.index');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->middleware('role:super_admin|admin')->name('reports.export.pdf');

    Route::get('/users', [UserController::class, 'index'])->middleware('role:super_admin|admin')->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->middleware('role:super_admin|admin')->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->middleware('role:super_admin|admin')->name('users.update');
    Route::get('/settings', [SettingsDashboardController::class, 'index'])->name('settings.dashboard');
    Route::get('/settings/general', [SettingController::class, 'edit'])->middleware('role:super_admin|admin')->name('settings.edit');
    Route::put('/settings/general', [SettingController::class, 'update'])->middleware('role:super_admin|admin')->name('settings.update');

    // Mail settings (super_admin only)
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/settings/mail', [SettingController::class, 'editMailSettings'])->name('settings.mail.edit');
        Route::put('/settings/mail', [SettingController::class, 'updateMailSettings'])->name('settings.mail.update');
        Route::post('/settings/mail/test', [SettingController::class, 'testMailConnection'])->name('settings.mail.test');
    });
    Route::get('/categories', [CategoryController::class, 'index'])->middleware('role:super_admin|admin')->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->middleware('role:super_admin|admin')->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->middleware('role:super_admin|admin')->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->middleware('role:super_admin|admin')->name('categories.destroy');
    Route::get('/activity', [ActivityLogController::class, 'index'])->middleware('role:super_admin|admin')->name('activity.index');
    Route::get('/contact-inquiries', [ContactInquiryController::class, 'index'])->middleware('role:super_admin|admin')->name('contact-inquiries.index');
    Route::get('/imports/products', [ImportExportController::class, 'showImportForm'])->middleware('role:super_admin|admin')->name('imports.products.show');
    Route::post('/imports/products', [ImportExportController::class, 'importProducts'])->middleware('role:super_admin|admin')->name('imports.products.store');
    Route::get('/exports/products', [ImportExportController::class, 'exportProducts'])->middleware('role:super_admin|admin')->name('exports.products');
    Route::get('/exports/transactions', [ImportExportController::class, 'exportTransactions'])->middleware('role:super_admin|admin')->name('exports.transactions');

    // Backup management (super_admin only)
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/admin/backups', [BackupController::class, 'index'])->name('backups.index');
        Route::post('/admin/backups', [BackupController::class, 'create'])->name('backups.create');
        Route::post('/admin/backups/{backup}/restore', [BackupController::class, 'restore'])->name('backups.restore');
        Route::delete('/admin/backups/{backup}', [BackupController::class, 'delete'])->name('backups.delete');
        Route::get('/admin/backups/{backup}/download', [BackupController::class, 'download'])->name('backups.download');
    });
});
