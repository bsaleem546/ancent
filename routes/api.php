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
use \App\Http\Controllers\CompanyController;
use \App\Http\Controllers\ReplacementController;
use \App\Http\Controllers\EmployeeController;
use \App\Http\Controllers\TextBlockController;
use \App\Http\Controllers\UnitsController;
use \App\Http\Controllers\VatController;

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
    Route::group(['middleware' => []], function () {
//        Route::group(['middleware' => ['role_or_permission:super_user|access_um']], function () {
        Route::post('all_users', [UserController::class, 'getAll']);
        Route::post('user/update', [UserController::class, 'store']);
        Route::post('user/create', [UserController::class, 'store']);
        Route::post('user/delete/{id}', [UserController::class, 'destroy']);
    });
    #endregion

    #region Customers CRUD routes
    Route::group(['middleware' => []], function () {
//        Route::group(['middleware' => ['role_or_permission:super_user|read customers']], function () {
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
    Route::group(['middleware' => []], function () {
//        Route::group(['middleware' => ['role_or_permission:super_user|read operators']], function () {
        Route::post('all_operators', [OperatorController::class, 'getAll']);
        Route::get('operator/{id}', [OperatorController::class, 'show']);
    });
    Route::group(['middleware' => []], function () {
//        Route::group(['middleware' => ['role_or_permission:super_user|write operators']], function () {
        Route::post('operator/update', [OperatorController::class, 'update']);
    });
    Route::group(['middleware' => []], function () {
//        Route::group(['middleware' => ['role_or_permission:super_user|create operators']], function () {
        Route::post('operator/create', [OperatorController::class, 'store']);
    });
    Route::group(['middleware' => []], function () {
//        Route::group(['middleware' => ['role_or_permission:super_user|delete operators']], function () {
        Route::post('operator/delete/{id}', [OperatorController::class, 'destroy']);
    });
    #endregion

    #region Locations CRUD routes
    Route::group(['middleware' => []], function () {
//        Route::group(['middleware' => ['role_or_permission:super_user|read locations']], function () {
        Route::post('all_locations', [LocationController::class, 'getAll']);
        Route::get('location/{id}', [LocationController::class, 'show']);
    });
    Route::group(['middleware' => []], function () {
//    Route::group(['middleware' => ['role_or_permission:super_user|write locations']], function () {
        Route::post('location/update', [LocationController::class, 'update']);
    });
    Route::group(['middleware' => []], function () {
//    Route::group(['middleware' => ['role_or_permission:super_user|create locations']], function () {
        Route::post('location/create', [LocationController::class, 'store']);
    });
    Route::group(['middleware' => []], function () {
//    Route::group(['middleware' => ['role_or_permission:super_user|delete locations']], function () {
        Route::post('location/delete/{id}', [LocationController::class, 'destroy']);
    });
    #endregion

    #region Equipment CRUD routes
    Route::group(['middleware' => []], function () {
//    Route::group(['middleware' => ['role_or_permission:super_user|read equipment']], function () {
        Route::post('all_equipments', [EquipmentController::class, 'getAll']);
        Route::post('all_equipments_min_data', [EquipmentController::class, 'getAllMinData']);
        Route::post('search_equipment', [EquipmentController::class, 'search']);
        Route::get('equipment/{id}', [EquipmentController::class, 'show']);
    });
    Route::group(['middleware' => []], function () {
//    Route::group(['middleware' => ['role_or_permission:super_user|write equipment']], function () {
        Route::post('equipment/update', [EquipmentController::class, 'update']);
        Route::post('equipment/update_history', [EquipmentController::class, 'updateHistory']);
        Route::post('equipment/update_maintenance_contract', [EquipmentController::class, 'updateMaintenanceContract']);
    });
    Route::group(['middleware' => []], function () {
//    Route::group(['middleware' => ['role_or_permission:super_user|create equipment']], function () {
        Route::post('equipment/create', [EquipmentController::class, 'store']);
    });
    Route::group(['middleware' => []], function () {
//    Route::group(['middleware' => ['role_or_permission:super_user|delete equipment']], function () {
        Route::post('equipment/delete/{id}', [EquipmentController::class, 'destroy']);
    });
    #endregion

    #region Repairs CRUD routes
    Route::group(['middleware' => []], function () {
//        Route::group(['middleware' => ['role_or_permission:super_user|read repairs']], function () {
        Route::post('all_repairs', [RepairController::class, 'getAll']);
        Route::post('all_repairs_min_data', [RepairController::class, 'getAllMinData']);
        Route::get('repair/{id}', [RepairController::class, 'show']);
        Route::post('get_repair', [RepairController::class, 'showP']);
        Route::post('get_repairs_by_ids', [RepairController::class, 'getRepairsByIds']);
    });
    Route::group(['middleware' => []], function () {
//    Route::group(['middleware' => ['role_or_permission:super_user|write repairs|create repair_details|write repair_details']], function () {
        Route::post('repair/update', [RepairController::class, 'update']);
        Route::post('repair/generate_invoice', [RepairController::class, 'generateInvoice']);
        Route::post('repair/generate_offer', [RepairController::class, 'generateOffer']);
    });
    Route::group(['middleware' => []], function () {
//    Route::group(['middleware' => ['role_or_permission:super_user|create repairs']], function () {
        Route::post('repair/create', [RepairController::class, 'store']);
        Route::post('repair/create_related_repair', [RepairController::class, 'createRelatedRepair']);
    });
    Route::group(['middleware' => []], function () {
//    Route::group(['middleware' => ['role_or_permission:super_user|delete repairs']], function () {
        Route::post('repair/delete/{id}', [RepairController::class, 'destroy']);
    });
    Route::group(['middleware' => []], function () {
//    Route::group(['middleware' => ['role_or_permission:super_user|create repair_details|write repair_details']], function () {
        Route::post('update_repair_schedule', [RepairController::class, 'updateSchedule']);
        Route::post('delete_repair_schedule', [RepairController::class, 'deleteSchedule']);
        Route::post('update_scheduled_employees', [RepairController::class, 'updateRepairScheduledEmployees']);
        Route::post('update_estimation', [RepairController::class, 'updateEstimation']);
    });
    #endregion

    #region Invoices CRUD routes
    Route::group(['middleware' => []], function () {
//    Route::group(['middleware' => ['role_or_permission:super_user|read repairs']], function () {
        Route::post('all_invoices', [InvoiceController::class, 'getAll']);
        Route::get('invoice/{id}', [InvoiceController::class, 'show']);
        Route::post('get_invoice', [InvoiceController::class, 'showP']);
        Route::post('get_invoices_by_ids', [InvoiceController::class, 'getInvoicesByIds']);
        Route::post('get_invoices_for_pdf_by_ids', [InvoiceController::class, 'getInvoicesForPdfByIds']);
    });
    Route::group(['middleware' => []], function () {
//    Route::group(['middleware' => ['role_or_permission:super_user|write repairs|create repair_details|write repair_details']], function () {
        Route::post('invoice/update', [InvoiceController::class, 'update']);
        Route::post('invoice/generate_invoices_for_ids', [InvoiceController::class, 'generateInvoicesForIds']);
        //fixme this route controller is not available
        Route::post('invoice/generate_offer', [InvoiceController::class, 'generateOffer']);
    });
    Route::group(['middleware' => []], function () {
//    Route::group(['middleware' => ['role_or_permission:super_user|create repairs']], function () {
        Route::post('invoice/store', [InvoiceController::class, 'store']);
    });
    Route::group(['middleware' => []], function () {
//    Route::group(['middleware' => ['role_or_permission:super_user|delete repairs']], function () {
        Route::post('invoice/delete/{id}', [InvoiceController::class, 'destroy']);
    });
    Route::group(['middleware' => []], function () {
//    Route::group(['middleware' => ['role_or_permission:super_user|create repair_details|write repair_details']], function () {
        Route::post('invoice/update_payment_date', [InvoiceController::class, 'updatePaymentDate']);
    });
    #endregion

    #region Equipment Model/Type/Manufacturer
    // fixme this route is also not available
    Route::get('equipment_models', [EquipmentController::class, 'getEquipmentModels']);

    Route::get('equipment_types', [EquipmentController::class, 'getEquipmentTypes']);
    Route::get('equipment_manufacturers', [EquipmentController::class, 'getEquipmentManufacturers']);
    Route::get('all_equipment_manufacture_years', [EquipmentController::class, 'getAllEquipmentManufactureYears']);
    #endregion

    #region Companies routes
    Route::get('company/{id}', [CompanyController::class, 'show']);
    Route::get('all_companies', [CompanyController::class, 'index']);
    Route::post('all_companies', [CompanyController::class, 'index']);
    #endregion

    #region replacements routes
    Route::get('replacement/{id}', [ReplacementController::class, 'show']);
    Route::get('all_replacements', [ReplacementController::class, 'index']);
    Route::post('all_replacements', [ReplacementController::class, 'index']);
    Route::get('all_replacement_groups', [ReplacementController::class, 'getAllReplacementGroups']);
    #endregion

    #region Employees routes
    Route::post('all_employees', [EmployeeController::class, 'getAll']);
    #endregion

    #region Text blocks routes
    Route::get('all_textblocks', [TextBlockController::class, 'getAll']);
    Route::post('all_textblocks', [TextBlockController::class, 'getAll']);
    #endregion

    #region Measure units routes
    Route::post('all_units', [UnitsController::class, 'getAll']);
    #endregion

    #region Vat routes
    // fixme this route is also not available
    Route::post('vat/get_vat_by_date', [VatController::class, 'getByDate']);

    Route::post('vat/get_all', [VatController::class, 'getAll']);
    Route::post('vat/create', [VatController::class, 'store']);
    Route::post('vat/get', [VatController::class, 'show']);
    Route::post('vat/update', [VatController::class, 'update']);
    Route::post('vat/delete/{id}', [VatController::class, 'destroy']);
    #endregion
});
