<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// if (App::environment('production')) {
//     URL::forceScheme('https');
// }

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware' => 'guest:api'], function () {
    /*login route*/
    Route::post('login', 'Auth\LoginController@login');

    /*register route*/
    Route::post('register', 'Auth\RegisterController@register');
});

//Order webHooks
Route::post('webhooks/create-order', 'WebhookController@createorder');
Route::post('webhooks/orders-updated', 'WebhookController@ordersUpdated');
Route::post('webhooks/orders-cancelled', 'WebhookController@ordersCancelled');
Route::post('webhooks/orders-delete', 'WebhookController@ordersDelete');


// Product Webhooks
Route::post('webhooks/product-create', 'ProductsController@store');
Route::post('webhooks/product-update', 'ProductsController@update');

//Inventory Items Webhook
Route::post('webhooks/inventory-create', 'InventoryController@store');
Route::post('webhooks/inventory-update', 'InventoryController@update');
