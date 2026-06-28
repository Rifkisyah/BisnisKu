<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ServiceRepairController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\ProductPurchaseController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\BusinessPerformanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesRevenueController;
use App\Http\Controllers\ServiceAnalysisController;
use App\Http\Controllers\SettingController;

// Public Landing Page
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
    Route::get('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('register.submit');
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Locale switch
Route::post('/locale', [SettingController::class, 'switchLocale'])->name('locale.switch');

// Authenticated Routes
Route::middleware(['auth', \App\Http\Middleware\SetLocale::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/export-pdf', [DashboardController::class, 'exportPdf'])->name('dashboard.export_pdf');

    // Settings (all roles can view and edit their own profile)
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings/profile', [SettingController::class, 'updateProfile'])->name('settings.profile');
    Route::post('/settings/password', [SettingController::class, 'updatePassword'])->name('settings.password');

    // Products & Categories (all roles can view)
    Route::resource('products', ProductController::class);
    Route::post('/products/quick-store', [ProductController::class, 'quickStore'])->name('products.quick-store');
    Route::resource('categories', CategoryController::class);

    // POS (owner + kasir)
    Route::middleware(\App\Http\Middleware\RoleMiddleware::class . ':owner,kasir')->group(function () {
        Route::get('/cashier', [CashierController::class, 'index'])->name('cashier.index');
        Route::get('/cashier/search', [CashierController::class, 'searchProducts'])->name('cashier.search');
        Route::post('/cashier/checkout', [\App\Http\Controllers\CashierController::class, 'checkout'])->name('cashier.checkout');
        
        // POS Payment Actions
        Route::post('/payments/{paymentCode}/confirm-manual', [\App\Http\Controllers\PosPaymentController::class, 'confirmManualQris'])->name('payments.confirm_manual');
        Route::post('/payments/{paymentCode}/cancel', [\App\Http\Controllers\PosPaymentController::class, 'cancelPayment'])->name('payments.cancel');
        Route::get('/payments/{paymentCode}/check-status', [\App\Http\Controllers\PosPaymentController::class, 'checkDynamicQrisStatus'])->name('payments.check_status');
        Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
        Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
        Route::get('/transactions/{transaction}/receipt', [TransactionController::class, 'receipt'])->name('transactions.receipt');
        Route::get('/transactions/{transaction}/receipt-pdf', [TransactionController::class, 'receiptPdf'])->name('transactions.receipt.pdf');
    });

    // Service Repairs (owner + teknisi + kasir)
    Route::middleware(\App\Http\Middleware\RoleMiddleware::class . ':owner,teknisi,kasir')->group(function () {
        Route::resource('service-repairs', ServiceRepairController::class);
        Route::post('/service-repairs/{serviceRepair}/add-part', [ServiceRepairController::class, 'addPart'])->name('service-repairs.add-part');
        Route::post('/service-repairs/{serviceRepair}/request-part', [ServiceRepairController::class, 'requestPart'])->name('service-repairs.request-part');
        Route::patch('/service-repairs/{serviceRepair}/status', [ServiceRepairController::class, 'updateStatus'])->name('service-repairs.update-status');
        Route::get('/service-repairs/{serviceRepair}/receipt', [ServiceRepairController::class, 'receipt'])->name('service-repairs.receipt');
        Route::get('/service-repairs/{serviceRepair}/receipt-pdf', [ServiceRepairController::class, 'receiptPdf'])->name('service-repairs.receipt.pdf');
    });

    // Owner only
    Route::middleware(\App\Http\Middleware\RoleMiddleware::class . ':owner')->group(function () {
        Route::resource('suppliers', SupplierController::class);
        Route::resource('employees', EmployeeController::class);
        Route::resource('product-purchases', ProductPurchaseController::class)->except(['edit', 'update']);
        Route::patch('/product-purchases/{productPurchase}/status', [ProductPurchaseController::class, 'updateStatus'])->name('product-purchases.update-status');
        Route::post('/product-purchases/{productPurchase}/send-whatsapp', [ProductPurchaseController::class, 'sendWhatsApp'])->name('product-purchases.send-whatsapp');


        Route::resource('debts', DebtController::class);
        Route::post('/debts/{debt}/payment', [DebtController::class, 'addPayment'])->name('debts.add-payment');
        Route::post('/transactions/{transaction}/cancel', [TransactionController::class, 'cancel'])->name('transactions.cancel');
        Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');

        // Business Performance (formerly BI)
        Route::get('/reports/business-performance', [BusinessPerformanceController::class, 'index'])->name('reports.business_performance');
        Route::get('/reports/business-performance/clusters', [BusinessPerformanceController::class, 'clusters'])->name('reports.business_performance.clusters');
        Route::get('/reports/business-performance/sma', [BusinessPerformanceController::class, 'sma'])->name('reports.business_performance.sma');
        Route::get('/reports/business-performance/export-pdf', [BusinessPerformanceController::class, 'exportPdf'])->name('reports.business_performance.export_pdf');

        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/sales-revenue', [SalesRevenueController::class, 'index'])->name('reports.sales_revenue');
        Route::get('/reports/sales-revenue/transactions', [SalesRevenueController::class, 'transactions'])->name('reports.sales_revenue.transactions');
        Route::get('/reports/sales-revenue/debts', [SalesRevenueController::class, 'debts'])->name('reports.sales_revenue.debts');
        Route::get('/reports/sales-revenue/products', [SalesRevenueController::class, 'products'])->name('reports.sales_revenue.products');
        Route::get('/reports/sales-revenue/categories', [SalesRevenueController::class, 'categories'])->name('reports.sales_revenue.categories');
        Route::get('/reports/service-analysis', [ServiceAnalysisController::class, 'index'])->name('reports.service_analysis');
        Route::get('/reports/service-analysis/trends', [ServiceAnalysisController::class, 'trends'])->name('reports.service_analysis.trends');
        Route::get('/reports/service-analysis/spareparts', [ServiceAnalysisController::class, 'spareparts'])->name('reports.service_analysis.spareparts');
        
        // Store Settings (Owner only)
        Route::post('/settings/store', [\App\Http\Controllers\SettingController::class, 'updateStoreProfile'])->name('settings.store');
        Route::get('/settings/payment', [\App\Http\Controllers\PaymentSettingController::class, 'index'])->name('settings.payment');
        Route::post('/settings/payment', [\App\Http\Controllers\PaymentSettingController::class, 'update'])->name('settings.payment.update');
    });
});
