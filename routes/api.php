<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use \App\Http\Controllers\UserController;
use \App\Http\Controllers\InvoiceController;
use \App\Http\Controllers\RepairController;
use \App\Http\Controllers\CustomerController;
use \App\Http\Controllers\OperatorController;
use \App\Http\Controllers\LocationController;
use \App\Http\Controllers\EquipmentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/login', [UserController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('user/{id}', [UserController::class, 'show']);

    #region Only login for pdf generation
    Route::post('get_invoice_for_pdf', [InvoiceController::class, 'getInvoiceForPDF']);
    Route::post('get_repair_for_pdf', [RepairController::class, 'showP']);
    Route::post('get_repair_for_pdf', [RepairController::class, 'showMultiple']);
    #endregion

    #region Users CRUD routes
    Route::group(['middleware' => ['role_or_permission:super_user|access_um']], function () {
        Route::post('all_users', [UserController::class, 'getAll']);
        Route::post('user/update', [UserController::class, 'store']);
        Route::post('user/create', [UserController::class, 'store']);
        Route::post('user/delete/{id}', [UserController::class, 'destroy']);
    });
    #endregion

    #region Customers CRUD routes
    Route::group(['middleware' => ['role_or_permission:super_user|read customers']], function () {
        Route::post('all_customers', [CustomerController::class, 'getAll']);
        Route::get('customer/{id}', [CustomerController::class, 'show']);
    });
    Route::group(['middleware' => ['role_or_permission:super_user|write customers']], function () {
        Route::post('customer/update', [CustomerController::class, 'update']);
    });
    Route::group(['middleware' => ['role_or_permission:super_user|create customers']], function () {
        Route::post('customer/create', [CustomerController::class, 'store']);
    });
    Route::group(['middleware' => ['role_or_permission:super_user|delete customers']], function () {
        Route::post('customer/delete/{id}', [CustomerController::class, 'destroy']);
    });
    #endregion

    #region Operators CRUD routes
    Route::group(['middleware' => ['role_or_permission:super_user|read operators']], function () {
        Route::post('all_operators', [OperatorController::class, 'getAll']);
        Route::get('operator/{id}', [OperatorController::class, 'show']);
    });
    Route::group(['middleware' => ['role_or_permission:super_user|write operators']], function () {
        Route::post('operator/update', [OperatorController::class, 'update']);
    });
    Route::group(['middleware' => ['role_or_permission:super_user|create operators']], function () {
        Route::post('operator/create', [OperatorController::class, 'store']);
    });
    Route::group(['middleware' => ['role_or_permission:super_user|delete operators']], function () {
        Route::post('operator/delete/{id}', [OperatorController::class, 'destroy']);
    });
    #endregion

    #region Locations CRUD routes
    Route::group(['middleware' => ['role_or_permission:super_user|read locations']], function () {
        Route::post('all_locations', [LocationController::class, 'getAll']);
        Route::get('location/{id}', [LocationController::class, 'show']);
    });
    Route::group(['middleware' => ['role_or_permission:super_user|write locations']], function () {
        Route::post('location/update', [LocationController::class, 'update']);
    });
    Route::group(['middleware' => ['role_or_permission:super_user|create locations']], function () {
        Route::post('location/create', [LocationController::class, 'store']);
    });
    Route::group(['middleware' => ['role_or_permission:super_user|delete locations']], function () {
        Route::post('location/delete/{id}', [LocationController::class, 'destroy']);
    });
    #endregion

    #region Equipment CRUD routes
    Route::group(['middleware' => ['role_or_permission:super_user|read equipment']], function () {
        Route::post('all_equipments', [EquipmentController::class, 'getAll']);
        Route::post('all_equipments_min_data', [EquipmentController::class, 'getAllMinData']);
        Route::post('search_equipment', [EquipmentController::class, 'search']);
        Route::get('equipment/{id}', [EquipmentController::class, 'show']);
    });
    Route::group(['middleware' => ['role_or_permission:super_user|write equipment']], function () {
        Route::post('equipment/update', [EquipmentController::class, 'update']);
        Route::post('equipment/update_history', [EquipmentController::class, 'updateHistory']);
        Route::post('equipment/update_maintenance_contract', [EquipmentController::class, 'updateMaintenanceContract']);
    });
    Route::group(['middleware' => ['role_or_permission:super_user|create equipment']], function () {
        Route::post('equipment/create', [EquipmentController::class, 'store']);
    });
    Route::group(['middleware' => ['role_or_permission:super_user|delete equipment']], function () {
        Route::post('equipment/delete/{id}', [EquipmentController::class, 'destroy']);
    });
    #endregion

});
