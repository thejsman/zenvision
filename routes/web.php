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
Route::delete('/user', 'UserController@destroy');
Route::get('/shopify/auth/', 'ShopifyStoreController@getResponse');



Route::get('/{group}/{component}', 'HomeController@show');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/mastersheet', 'MastersheetController@index')->name('mastersheet');

    Route::get('/profile', 'UserController@index');
    Route::get('/user', 'UserController@getUser');
    Route::patch('/user', 'UserController@edit');

    Route::patch('/changepassword', 'UserController@changePassword');

    //Shopify Connect APIs
    Route::get('validateShopifyStoreUrl', 'ShopifyStoreController@validateUrl');

    //Dashboard - Shopify Data
    Route::get('shopifystoredata', 'DashboardController@index');
    Route::get('mastersheetdata', 'DashboardController@mastersheet');
    Route::get('dailynetequity', 'MastersheetController@getNetEquityData');
    Route::get('msdebts', 'DashboardController@msdebts');
    Route::get('abandonedcart', 'DashboardController@getAbandonedCartCount');

    Route::get('shopify-balance', 'ShopifyStoreController@getShopifyStoreBalance');
    Route::get('shopify-reserves', 'ShopifyStoreController@getShopifyStoreReserves');

    Route::get('getavgunitperorder', 'DashboardController@getAvgUnitsPerOrder');

    Route::get('shopify-orders', 'DashboardController@getShopifyStoreOrders');
    Route::get('shopify-allorders', 'DashboardController@getShopifyStoreAllOrders');


    Route::patch('shopifystore', 'ShopifyStoreController@toggleStore');
    Route::patch('shopifystoredelete', 'ShopifyStoreController@destroy');
    Route::get('getshopifydisputes', 'ShopifyStoreController@getDisputes');
    Route::get('shopify-refunds', 'ShopifyStoreController@getRefundTotal');
    //Subscription Cost APIs
    Route::get('subscriptioncost', 'SubscriptionCostController@index');
    Route::post('subscriptioncost', 'SubscriptionCostController@store');
    Route::delete('subscriptioncost/{id}', 'SubscriptionCostController@destroy');
    Route::patch('subscriptioncost/{id}', 'SubscriptionCostController@update');
    Route::patch('endSubscriptioncost/{id}', 'SubscriptionCostController@endSubscription');

    //Paypal
    Route::get('getpaypalaccounts', 'PaypalController@index');
    Route::get('paypal', 'PaypalController@store');
    Route::patch('paypal', 'PaypalController@toogleAccount');
    Route::patch('paypaldelete', 'PaypalController@destroy');
    Route::get('paypaltransactions', 'PaypalController@getPaypalTransactions');

    Route::get('paypaldisputes', 'PaypalController@getPaypalDisputes');

    //Stripe
    Route::get('getstripeaccounts', 'StripeController@index');
    Route::get('stripe-accounts', 'StripeController@getStripeAccountsForPA');
    Route::get('stripeconnect', 'StripeController@store');

    Route::get('stripeconnect-chargeback', 'StripeController@getStripeChargebacks');
    Route::get('stripeconnect-merchantfee', 'StripeController@getMerchantFees');

    Route::get('stripeaccount-balance', 'StripeController@getAccountBalance');
    Route::get('getStripeTransactions', 'StripeController@getStripeTransactionsFromDb');
    Route::patch('stripeconnect', 'StripeController@toogleAccount');
    Route::patch('stripeconnectdelete', 'StripeController@destroy');
    Route::get('stripeaccount-transactions', 'StripeController@getStripeTransactionsDateWise');

    Route::post('stripeaccount-merchantfee2', 'StripeController@merchantFeeReportRun');

    //Bank Account
    Route::post('bankaccount', 'BankAccountController@store');
    Route::get('bankaccounts', 'BankAccountController@getBankAccounts');
    Route::patch('bankaccountdelete', 'BankAccountController@destroy');
    Route::get('bankaccount-balance', 'BankAccountController@getAccountBalance');
    Route::get('bankaccount-transactions', 'BankAccountController@getAccountTransactions');
    Route::get('bankaccount-link-token', 'BankAccountController@generateLinkToken');

    //Credit Card
    Route::get('creditcard-liabilities', 'CreditCardController@getCreditCardliabilities');

    // Route::get('getStripeTransactions', 'StripeController@getStripeTransactionsSdk');

    Route::get('stripe-report-balancetransaction', 'StripeController@createBalanceTransactionReport');
    Route::get('stripe-report-report', 'StripeController@getReport');

    // COGS Data
    Route::get('cogs', 'CogsController@index');
    Route::post('cogs', 'CogsController@update');
    Route::get('cogsicon', 'CogsController@showCogsIcon');

    //Inventory 
    Route::get('inventory', 'InventoryController@index');
    Route::patch('inventory', 'InventoryController@deleteInventory');
    //Facebook APIs
    Route::get('getfacebookaccounts', 'FacebookController@getAdAccounts');
    Route::get('getfacebookadsdata', 'FacebookController@getFacebookAdsData');

    Route::get('fbconnect', 'FacebookController@index');

    Route::get('facebook-connect', 'FacebookController@facebookConnect');
    Route::get('facebook-listadaccounts', 'FacebookController@listAdAccounts');
    Route::post('facebook-addadaccounts', 'FacebookController@store');

    Route::patch('fbconnect', 'FacebookController@toogleAdAccount');
    Route::patch('fbconnectdelete', 'FacebookController@destroy');

    //Snapchat
    Route::get('snapchat-connect', 'SnapchatController@store');
    Route::get('getsnapchataccounts', 'SnapchatController@getAdAccounts');
    Route::get('snapchat-listadaccounts', 'SnapchatController@listAdAccounts');

    //Snapchat Ad account
    Route::post('snapchatadaccount', 'SnapchatAdAccountController@store');
    Route::get('updateaccesstoken', 'SnapchatAdAccountController@updateAccessToken');
    Route::patch('snapchatadaccount', 'SnapchatAdAccountController@toogleAdAccount');
    Route::patch('snapchatadaccount-delete', 'SnapchatAdAccountController@destroy');

    Route::get('snapchat-adspend', 'SnapchatAdAccountController@getAdSpend');

    //TikTok Ad account

    Route::get('tiktok-connect', 'TiktokAdController@tiktokConnect');
    Route::get('tiktokaccount', 'TiktokAdController@index');
    Route::get('tiktokaccount-listaccount', 'TiktokAdController@getTiktokAccountInfo');
    Route::get('tiktokaccount-adspend', 'TiktokAdController@getTiktokAdSpend');
    Route::post('tiktokaccount', 'TiktokAdController@store');
    Route::patch('tiktokaccount', 'TiktokAdController@toogleAccount');
    Route::patch('tiktokaccount-delete', 'TiktokAdController@destroy');

    //Google APIs
    Route::get('google-connect', 'GoogleAdController@index');
    Route::post('google-connect', 'GoogleAdController@store');
    Route::patch('google-connect', 'GoogleAdController@toogleAdAccount');
    Route::patch('google-connect-delete', 'GoogleAdController@destroy');
    Route::get('google-connect-listaccounts', 'GoogleAdController@listAdAccounts');
    Route::get('google-connect-test', 'GoogleAdController@store');
    Route::get('google-connect-getaccounts', 'GoogleAdController@getGoogleAdAccounts');
    Route::get('google-adspend', 'GoogleAdController@getAdSpends');

    //supplierPayable
    Route::get('supplierpayable', 'SupplierPayableController@get');
    Route::post('supplierpayable', 'SupplierPayableController@store');
    Route::patch('supplierpayable', 'SupplierPayableController@edit');
    Route::delete('supplierpayable/{id}', 'SupplierPayableController@destroy');
    Route::delete('supplierpayable-txn/{id}', 'SupplierPayableController@destroyByTransactionId');
});

Route::get('/shopify-register', 'ShopifyRegisterController@index')->name('shopify-register');
Route::post('/shopify-register', 'ShopifyStoreController@ShopifyInstallCreateUser');
Route::get('/shopify-register-temp', 'ShopifyStoreController@getShopifyStoreInfo');


//Stripe Webhooks
Route::post('stripe/webhook/report', 'StripeController@reportWebhookHandler');

Route::post('stripe/webhook/charge', 'StripeController@chargeWebookHandler');

Route::get('stripe-reportrun', 'StripeController@createReportRun');
Route::get('stripe-report-content', 'StripeController@getReportContent');
Route::get('stripe-report-status', 'StripeController@getReportStatus');
Route::get('stripe-report-status2', 'StripeController@getReportStatus2');

Route::get('shopifyinstall', 'ShopifyStoreController@shopifyInstall');
