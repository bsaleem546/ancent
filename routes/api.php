<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use \App\Http\Controllers\UserController;
use \App\Http\Controllers\InvoiceController;
use \App\Http\Controllers\RepairController;
use \App\Http\Controllers\CustomerController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/login', [UserController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('user/{id}', [UserController::class, 'show']);

    // Only login for pdf generation
    Route::post('get_invoice_for_pdf', [InvoiceController::class, 'getInvoiceForPDF']);
    Route::post('get_repair_for_pdf', [RepairController::class, 'showP']);
    Route::post('get_repair_for_pdf', [RepairController::class, 'showMultiple']);

    //Users CRUD routes
    Route::group(['middleware' => ['role_or_permission:super_user|access_um']], function () {
        Route::post('all_users', [UserController::class, 'getAll']);
        Route::post('user/update', [UserController::class, 'store']);
        Route::post('user/create', [UserController::class, 'store']);
        Route::post('user/delete/{id}', [UserController::class, 'destroy']);
    });

    //Customers CRUD routes
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
});
