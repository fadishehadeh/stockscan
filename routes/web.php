<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImportExportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StockTransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login')->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->middleware('owner')->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->middleware('owner')->name('products.store');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->middleware('owner')->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->middleware('owner')->name('products.update');
    Route::post('/products/{product}/archive', [ProductController::class, 'archive'])->middleware('owner')->name('products.archive');
    Route::post('/products/{product}/restore', [ProductController::class, 'restore'])->middleware('owner')->name('products.restore');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->middleware('owner')->name('products.destroy');
    Route::get('/products/{product}/label', [ProductController::class, 'label'])->name('products.label');

    Route::get('/scan', [ScanController::class, 'index'])->name('scan.index');
    Route::post('/scan', [ScanController::class, 'lookup'])->name('scan.lookup');
    Route::get('/scan/{product}', [ScanController::class, 'show'])->name('scan.show');
    Route::get('/alerts', [AlertController::class, 'index'])->name('alerts.index');

    Route::get('/transactions', [StockTransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions', [StockTransactionController::class, 'store'])->name('transactions.store');

    Route::get('/reports', [ReportController::class, 'index'])->middleware('owner')->name('reports.index');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->middleware('owner')->name('reports.export.pdf');

    Route::get('/users', [UserController::class, 'index'])->middleware('owner')->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->middleware('owner')->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->middleware('owner')->name('users.update');
    Route::get('/settings', [SettingController::class, 'edit'])->middleware('owner')->name('settings.edit');
    Route::put('/settings', [SettingController::class, 'update'])->middleware('owner')->name('settings.update');
    Route::get('/categories', [CategoryController::class, 'index'])->middleware('owner')->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->middleware('owner')->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->middleware('owner')->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->middleware('owner')->name('categories.destroy');
    Route::get('/activity', [ActivityLogController::class, 'index'])->middleware('owner')->name('activity.index');
    Route::get('/imports/products', [ImportExportController::class, 'showImportForm'])->middleware('owner')->name('imports.products.show');
    Route::post('/imports/products', [ImportExportController::class, 'importProducts'])->middleware('owner')->name('imports.products.store');
    Route::get('/exports/products', [ImportExportController::class, 'exportProducts'])->middleware('owner')->name('exports.products');
    Route::get('/exports/transactions', [ImportExportController::class, 'exportTransactions'])->middleware('owner')->name('exports.transactions');
});
