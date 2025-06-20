<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Student1Mustafa\PaymentController;
use App\Http\Controllers\NoSQL\MongoMigrationController;
use App\Http\Controllers\NoSQL\Student1Mustafa\MustafaNoSQLController;
use App\Http\Controllers\Student2Khalifa\UseCaseController as SqlUseCaseController;
use App\Http\Controllers\NoSQL\Student2khalifa\UseCaseController as NoSqlUseCaseController;


Route::get('/', function () {
    return view('admin.tools.import');
});

// Admin routes group with prefix and namespace 
Route::prefix('admin')->name('admin.')->group(function () {

    // Admin home page route
    Route::get('/', [App\Http\Controllers\Admin\HomeController::class, 'home'])->name('home');

    // Employee routes (resourceful)
    Route::resource('employees', App\Http\Controllers\Admin\EmployeeController::class);

    // Customer routes
    Route::resource('customers', App\Http\Controllers\Admin\CustomerController::class);

    // Repair orders routes
    Route::resource('repair-orders', App\Http\Controllers\Admin\RepairOrderController::class);


    // Analytics report route (assuming single page)
    Route::get('analytics', [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics.index');
});

// Device routes group with prefix and namespace 
Route::prefix('device')->name('device.')->group(function () {

    // Resourceful routes
    Route::resource('types', App\Http\Controllers\Device\TypeController::class);
    Route::resource('brands', App\Http\Controllers\Device\BrandController::class);
    Route::resource('models', App\Http\Controllers\Device\ModelController::class);

    // Custom route: Show brands related to a specific device type
    Route::get('types/{id}/brands', [App\Http\Controllers\Device\TypeController::class, 'showBrands'])->name('types.brands');
    Route::get('brands/{brand_id}/device-type/{device_type_id}/models', [App\Http\Controllers\Device\BrandController::class, 'showModels'])
    ->name('brands.models');

});

Route::post('/import-random-data', [ImportController::class, 'import']);


// =======================================================
// Student 2 Khalifa Specific Par

// SQL routes
Route::prefix('use-case')->name('use_case.')->group(function () {
    Route::get('/', [SqlUseCaseController::class, 'index'])->name('index');
    Route::post('/select_user', [SqlUseCaseController::class, 'selectUser'])->name('selectUser');
    Route::get('/analytics_report', [SqlUseCaseController::class, 'analyticsReport'])->name('analytics.report');
    Route::get('/appointment/create', [SqlUseCaseController::class, 'createAppointment'])->name('appointment.create');
});

// NoSQL routes
Route::prefix('nosql/use-case')->name('nosql.use_case.')->group(function () {
    Route::get('/', [NoSqlUseCaseController::class, 'index'])->name('index');
    Route::post('/select_user', [NoSqlUseCaseController::class, 'selectUser'])->name('selectUser');
    Route::get('/analytics_report', [NoSqlUseCaseController::class, 'analyticsReport'])->name('analytics.report');
    Route::get('/appointment/create', [NoSqlUseCaseController::class, 'createAppointment'])->name('appointment.create');
    Route::post('/appointment/store', [NoSqlUseCaseController::class, 'storeAppointment'])->name('appointment.store');
    Route::get('/appointments', [NoSqlUseCaseController::class, 'listAppointments'])->name('appointments');
});


// Customer related routes sql part
Route::prefix('customer')->name('customer.')->group(function () {
    // Customer dashboard
    Route::get('/dashboard', function () {
        return view('student2khalifa.SQL.customer.dashboard');
    })->name('dashboard');
    // Appointment booking page (form wizard)
    Route::get('/appointment/create', [SqlUseCaseController::class, 'createAppointment'])->name('appointment.create');
    // store the created appointment
    Route::post('/appointment/store', [SqlUseCaseController::class, 'storeAppointment'])->name('appointment.store');
    // Customer's appointments list
    Route::get('/appointments', [SqlUseCaseController::class, 'listAppointments'])->name('appointments');
    // Customer profile
    Route::get('/profile', [SqlUseCaseController::class, 'customerProfile'])->name('profile');

});

// =======================================================
// Student 1 Mustafa Specific Part

Route::get('/mustafa/analytics-report', [PaymentController::class, 'analyticsReport'])->name('mustafa.analytics.report');
Route::get('/mustafa/use-case', [PaymentController::class, 'useCasePage'])->name('mustafa.use_case.page');
Route::post('/mustafa/use-case/pay', [PaymentController::class, 'processUserAppointmentPayment'])->name('mustafa.use_case.pay');
Route::get('/mustafa/use-case/pay/{appointment_id}', [PaymentController::class, 'showPaymentForm'])->name('mustafa.use_case.pay_form');
Route::post('/mustafa/use-case/pay', [PaymentController::class, 'processUserAppointmentPayment'])->name('mustafa.use_case.pay');
Route::get('/mustafa/pending-payments', [PaymentController::class, 'pendingPaymentsPage'])->name('mustafa.pendingPayments');
Route::post('/mustafa/confirm-payment', [PaymentController::class, 'confirmPayment'])->name('mustafa.confirmPayment');

//+++++++++++nosql mustafa

Route::get('/mustafa-nosql/analytics', [MustafaNoSQLController::class, 'analyticsReport'])->name('mustafa.nosql.analytics');
Route::get('/mustafa-nosql/use-case', [MustafaNoSQLController::class, 'useCasePage'])->name('mustafa.nosql.use_case');
Route::post('/mustafa-nosql/use-case/pay', [MustafaNoSQLController::class, 'processPayment'])->name('mustafa.nosql.use_case.pay');
Route::get('/mustafa-nosql/pay/{appointment_id}', [MustafaNoSQLController::class, 'showPaymentForm'])->name('mustafa.nosql.pay.form');
Route::post('/mustafa-nosql/pay', [MustafaNoSQLController::class, 'processPayment'])->name('mustafa.nosql.pay.submit');
Route::get('/mustafa/nosql/pending-payments', [MustafaNoSQLController::class, 'pendingPaymentsPage'])->name('mustafa.nosql.pendingPayments');
Route::post('/mustafa/nosql/confirm-payment', [MustafaNoSQLController::class, 'confirmPayment'])->name('mustafa.nosql.confirm_payment');


//====================================================noSQL routes
Route::post('/mongo-migrate', [MongoMigrationController::class, 'migrateAll'])->name('mongo-migrate');
