<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Student2Khalifa\UseCaseController;

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

Route::prefix('use-case')->name('use_case.')->group(function () {

    // Display the user selection page
    Route::get('/', [UseCaseController::class, 'index'])->name('index');

    // Handle user selection and store it in a session
    Route::post('/select_user', [UseCaseController::class, 'selectUser'])->name('selectUser');

    // Generate analytics report
    Route::get('/analytics_report', [UseCaseController::class, 'analyticsReport'])->name('analytics.report');

    // Customer appointment booking page
    Route::get('/appointment/create', [UseCaseController::class, 'createAppointment'])->name('appointment.create');
});


// Customer related routes
Route::prefix('customer')->name('customer.')->group(function () {

    // Customer dashboard
    Route::get('/dashboard', function () {
        return view('student2khalifa.customer.dashboard');
    })->name('dashboard');

    // Appointment booking page (form wizard)
    Route::get('/appointment/create', [UseCaseController::class, 'createAppointment'])->name('appointment.create');

    // store the created appointment
    Route::post('/appointment/store', [UseCaseController::class, 'storeAppointment'])->name('appointment.store');


    // Customer's appointments list
    Route::get('/appointments', [UseCaseController::class, 'listAppointments'])->name('appointments');

    // Customer profile
    Route::get('/profile', [UseCaseController::class, 'customerProfile'])->name('profile');

});
