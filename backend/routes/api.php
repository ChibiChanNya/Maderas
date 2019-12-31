<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('auth')->group(function () {
    // Below mention routes are public, user can access those without any restriction.
    // Create New User
    // Route::post('register', 'AuthController@register');
    // Login User
    Route::post('login', 'AuthController@login');
    // Refresh the JWT Token
    Route::post('refresh', 'AuthController@refresh');
    
    // Below mention routes are available only for the authenticated users.
    Route::middleware('auth:api')->group(function () {
        // Get user info
        // Route::post('me', 'AuthController@me');
        // Logout user from application
        Route::post('logout', 'AuthController@logout');
    });
});

Route::prefix('user')->group(function () {
    // Route::post('register', 'UserController@register');
    // Below mention routes are available only for the authenticated users.
    Route::middleware('auth:api')->group(function () {
        // Create New User
        Route::post('register', 'UserController@register');
        // Get user info
        Route::get('me', 'UserController@me');
        // Get all users
        Route::get('list', 'UserController@users_list');
        // Update a user
        Route::post('update', 'UserController@user_update');
        // Delete a user
        Route::post('delete', 'UserController@user_delete');

        // Log part
        Route::get('all_log', 'UserController@all_log');
        Route::get('user_log/{id}', 'UserController@user_log');
    });
});

Route::prefix('providers')->group(function () {
    // Route::post('register', 'UserController@register');
    // Below mention routes are available only for the authenticated users.
    Route::middleware('auth:api')->group(function () {
        // Create New Provider
        Route::post('create', 'ProviderController@create');
        // Get all Providers
        Route::get('list', 'ProviderController@providers_list');
        // Update a Provider
        Route::post('update', 'ProviderController@provider_update');
        // Delete a Provider
        Route::post('delete', 'ProviderController@provider_delete');
    });
});

Route::prefix('materials')->group(function () {
    // Route::post('register', 'UserController@register');
    // Below mention routes are available only for the authenticated users.
    Route::middleware('auth:api')->group(function () {
        // Create New User
        Route::post('create', 'SupplyController@create');
        // Get all Supplies
        Route::get('list', 'SupplyController@supplies_list');
        // Update a Supply
        Route::post('update', 'SupplyController@supply_update');
        // Delete a Supply
        Route::post('delete', 'SupplyController@supply_delete');
    });
});

Route::prefix('products')->group(function () {
    // Route::post('register', 'UserController@register');
    // Below mention routes are available only for the authenticated users.
    Route::middleware('auth:api')->group(function () {
        // Create New User
        Route::post('create', 'ProductController@create');
        // Get all Supplies
        Route::get('list', 'ProductController@products_list');
        // Update a Supply
        Route::post('update', 'ProductController@product_update');
        // Delete a Supply
        Route::post('delete', 'ProductController@product_delete');
    });
});

Route::prefix('clients')->group(function () {
    // Route::post('register', 'UserController@register');
    // Below mention routes are available only for the authenticated users.
    Route::middleware('auth:api')->group(function () {
        // Create New User
        Route::post('create', 'ClientController@create');
        // Get all Supplies
        Route::get('list', 'ClientController@clients_list');
        // Update a Supply
        Route::post('update', 'ClientController@client_update');
        // Delete a Supply
        Route::post('delete', 'ClientController@client_delete');
    });
});

Route::prefix('orders/providers')->group(function () {
    // Route::post('register', 'UserController@register');
    // Below mention routes are available only for the authenticated users.
    Route::middleware('auth:api')->group(function () {
        // Create New Order
        Route::post('create', 'OrderToProviderController@create');
        // Get all Orders
        Route::get('list', 'OrderToProviderController@orders_list');
        // Get all Orders LITE
        Route::get('list_lite', 'OrderToProviderController@orders_list_lite');
        // Update an Order
        Route::post('update', 'OrderToProviderController@order_update');
        // Delete an Order
        Route::post('delete', 'OrderToProviderController@order_delete');
        //Make an operation to the Storage
        Route::post('make_operation', 'OrderToProviderController@make_operation');
    });
});

Route::prefix('orders/clients')->group(function () {
    // Route::post('register', 'UserController@register');
    // Below mention routes are available only for the authenticated users.
    Route::middleware('auth:api')->group(function () {
        // Create New Order
        Route::post('create', 'ClientOrderController@create');
        // Get all Orders
        Route::get('list', 'ClientOrderController@orders_list');
        // Get all Orders LITE
        Route::get('list_lite', 'ClientOrderController@orders_list_lite');
        // Update an Order
        Route::post('update', 'ClientOrderController@order_update');
        // Delete an Order
        Route::post('delete', 'ClientOrderController@order_delete');
        //Make an operation to the Storage
        // Route::post('make_operation', 'ClientOrderController@make_operation');
    });
});

Route::prefix('shipments')->group(function () {
    // Route::post('register', 'UserController@register');
    // Below mention routes are available only for the authenticated users.
    Route::middleware('auth:api')->group(function () {
        // Create New Order
        Route::post('create', 'ShipmentController@create');
        // Get all Orders
        Route::get('list', 'ShipmentController@shipments_list');
        // Get all Orders LITE
        Route::get('list_lite', 'ShipmentController@shipments_list_lite');
        // Update an Order
        Route::post('update', 'ShipmentController@shipment_update');
        // Delete an Order
        Route::post('delete', 'ShipmentController@shipment_delete');
        //Make an operation to the Storage
        Route::post('make_operation', 'ShipmentController@make_operation');
    });
});

Route::prefix('ledger')->group(function () {
    // Route::post('register', 'UserController@register');
    // Below mention routes are available only for the authenticated users.
    Route::middleware('auth:api')->group(function () {
        // Create New Order
        Route::post('create', 'LedgerController@create');
        // Get all Orders
        Route::get('list', 'LedgerController@ledgers_list');
        // Update an Order
        Route::post('update', 'LedgerController@ledger_update');
        // Delete an Order
        Route::post('delete', 'LedgerController@ledger_delete');
    });
});

Route::prefix('invoices')->group(function () {
    // Route::post('register', 'UserController@register');
    // Below mention routes are available only for the authenticated users.
    Route::middleware('auth:api')->group(function () {
        // Create New Invoice
        //Route::post('create', 'InvoiceController@create');
        // Get all Invoices (locally)
        Route::get('list', 'InvoiceController@invoices_list');
        // Get all unit codes from API
        Route::get('unit_codes', 'InvoiceController@unit_codes');
        // Get all payment forms from API
        Route::get('payment_forms', 'InvoiceController@payment_forms');
        // Get all payment methods from API
        Route::get('payment_methods', 'InvoiceController@payment_methods');
        // Get all cfdi uses from API
        Route::get('cfdi_uses', 'InvoiceController@cfdi_uses');
        // Create CFDI
        Route::post('create_cfdi', 'InvoiceController@create_cfdi');
        Route::post('send', 'InvoiceController@send_cfdi');
        Route::post('cancel', 'InvoiceController@cancel_cfdi');
    });
});

Route::prefix('dashboard')->group(function () {
    // Route::post('register', 'UserController@register');
    // Below mention routes are available only for the authenticated users.
    Route::middleware('auth:api')->group(function () {
        // Create New Invoice
        Route::get('graph_one', 'DashBoardController@graph_one');
        Route::get('graph_two', 'DashBoardController@graph_two');
        Route::get('graph_three', 'DashBoardController@graph_three');
        Route::get('graph_four', 'DashBoardController@graph_four');
    });
});

// Route::group([
//     'prefix' => 'auth'
// ], function ($router) {

//     Route::post('login', 'AuthController@login');
//     Route::post('logout', 'AuthController@logout');
//     Route::post('refresh', 'AuthController@refresh');
//     Route::post('me', 'AuthController@me');
//     Route::post('/register', 'AuthController@register');

// });

Route::get('prueba', 'AuthController@prueba')->middleware('web');
