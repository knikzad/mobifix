<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\EmployeeController;

Route::get('/', function () {
    return view('import');
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
