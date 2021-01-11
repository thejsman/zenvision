<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();
Route::get('user/stores', 'ShopifyStoreController@getStores');
Route::get('/logout', 'Auth\LoginController@logout');
Route::get('/', 'HomeController@index')->name('home');
Route::get('/mastersheet', 'MastersheetController@index');
Route::get('/shopify/auth/', 'ShopifyStoreController@getResponse');

Route::get('/{group}/{component}', 'HomeController@show');



Route::group(['middleware' => ['auth']], function () {
    //Shopify Connect APIs
    Route::get('validateShopifyStoreUrl', 'ShopifyStoreController@validateUrl');
    //Dashboard - Shopify Data
    Route::get('shopifystoredata', 'DashboardController@index');
    Route::get('mastersheetdata', 'DashboardController@mastersheet');
    Route::get('dailynetequity', 'MastersheetController@getNetEquityData');
    Route::get('msdebts', 'DashboardController@msdebts');
    Route::get('abandonedcart', 'DashboardController@getAbandonedCartCount');
    Route::get('shopifybalance', 'DashboardController@getShopifyStoreBalance');


    Route::patch('shopifystore', 'ShopifyStoreController@toggleStore');
    Route::patch('shopifystoredelete', 'ShopifyStoreController@destroy');

    //Subscription Cost APIs
    Route::get('subscriptioncost', 'SubscriptionCostController@index');
    Route::post('subscriptioncost', 'SubscriptionCostController@store');
    Route::delete('subscriptioncost/{id}', 'SubscriptionCostController@destroy');
    Route::patch('subscriptioncost/{id}', 'SubscriptionCostController@update');
    Route::patch('endSubscriptioncost/{id}', 'SubscriptionCostController@endSubscription');
});
