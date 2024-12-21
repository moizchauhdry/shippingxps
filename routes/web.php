<?php

use App\Http\Controllers\AuctionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomFormController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\GiftCardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ShippingCalculatorController;
use App\Http\Controllers\ShippingRatesController;
use Inertia\Inertia;

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

require __DIR__ . '/auth.php';
Route::any('shipping-rates', [ShippingRatesController::class, 'index'])->name('shipping-rates.index');
Route::get('/shipping-calculator', 'HomeController@pricing')->name('shipping-calculator');
Route::get('dashboard/shipping-calculator', [ShippingCalculatorController::class, 'index'])->name('dashboard.shipping-calculator.index');

Route::group(['middleware' => ['verified', 'auth']], function () {

    require __DIR__ . '/package.php';

    Route::get('/', function () {
        return redirect()->route('dashboard');
    })->name('homePage');

    Route::get('/checkAuth-user', [HomeController::class, 'checkAuth']);

    Route::any('/dashboard', 'HomeController@dashboard')->name('dashboard')->middleware(['auth', 'verified']);

    Route::any('/dashboard-success', 'HomeController@dashboard')->name('dashboard-success')->middleware(['auth', 'verified']);
    Route::get('/getServicesList', 'HomeController@getServicesList')->name('getServicesList');
    Route::get('/pricing', 'HomeController@pricingTable')->name('pricing');
    Route::get('/getListings', 'HomeController@getListings')->name('getListings');
    Route::get('/getQuotes', 'HomeController@getQuotes')->name('getQuotes');
    Route::get('/getQuote', 'HomeController@getQuote')->name('getQuote');
    Route::get('/getQuoteByOrders', 'HomeController@getQuoteByOrders')->name('getQuoteByOrders');
    Route::get('/test-order-xps', 'HomeController@putTestOrder')->name('putTestOrder');
    Route::get('/shopping', 'HomeController@shopping')->name('shopping')->middleware('auth');
    // Route::get('/test-email', 'EmailController@index')->name('test-email');
    Route::any('announcement', [HomeController::class, 'announcement'])->name('announcement');

    Route::any('/orders/index', 'OrderController@index')->name('orders')->middleware('auth');
    Route::get('/orders/create', 'OrderController@create')->name('orders.create')->middleware('auth');
    Route::post('/orders/store', 'OrderController@store')->name('order.store')->middleware('auth');
    Route::get('/orders/edit/{id}', 'OrderController@edit')->name('order.edit')->middleware('auth');
    Route::post('/orders/update', 'OrderController@update')->name('orders.update')->middleware('auth');
    Route::get('/orders/show/{id}', 'OrderController@show')->name('orders.show')->middleware('auth');
    Route::post('/orders/removeItem', 'OrderController@removeItem')->name('orders.removeItem')->middleware('auth');
    // Route::get('/orders/quote', 'OrderController@quote')->name('orders.quote')->middleware('auth');

    Route::get('/services/create', 'ServiceController@create')->name('services.create')->middleware('auth');
    Route::post('/services', 'ServiceController@store')->name('services.store')->middleware('auth');
    Route::get('/services', 'ServiceController@index')->name('services')->middleware('auth');
    Route::get('/services/{id}/edit', 'ServiceController@edit')->name('services.edit')->middleware('auth');
    Route::post('/services/update', 'ServiceController@update')->name('services.update')->middleware('auth');
    Route::get('/services/{id}', 'ServiceController@show')->name('services.show')->middleware('auth');
    Route::delete('/services{id}', 'ServiceController@destroy')->name('services.destroy')->middleware('auth');

    Route::get('/profile', 'AccountsContoller@profile')->name('profile')->middleware('auth');
    Route::post('/update-profile', 'AccountsContoller@updateProfile')->name('update-profile')->middleware('auth');

    Route::get('/addresses', 'AddressController@index')->name('addresses')->middleware('auth');
    Route::get('/address/create', 'AddressController@create')->name('address.create')->middleware('auth');
    Route::post('/address', 'AddressController@store')->name('address.store')->middleware('auth');
    Route::get('/address/{id}/edit', 'AddressController@edit')->name('address.edit')->middleware('auth');
    Route::put('/address', 'AddressController@update')->name('address.update')->middleware('auth');
    Route::delete('/address{id}', 'AddressController@destroy')->name('address.destroy')->middleware('auth');
    Route::get('/address/suite', 'AddressController@suite')->name('address.suite')->middleware('auth');

    Route::get('/warehouses', 'WarehouseController@index')->name('warehouses')->middleware('auth');
    Route::get('/warehouses/create', 'WarehouseController@create')->name('warehouses.create')->middleware('auth');
    Route::post('/warehouses', 'WarehouseController@store')->name('warehouses.store')->middleware('auth');
    Route::get('/warehouses/{id}/edit', 'WarehouseController@edit')->name('warehouses.edit')->middleware('auth');
    Route::post('/warehouses', 'WarehouseController@update')->name('warehouses.update')->middleware('auth');
    Route::delete('/warehouses{id}', 'WarehouseController@destroy')->name('warehouses.destroy')->middleware('auth');

    Route::group(['prefix' => 'auctions-a', 'middleware' => 'auth', 'as' => 'auctions.'], function () {
        Route::get('listing', [AuctionController::class, 'listing'])->name('listing');
        Route::get('create', [AuctionController::class, 'create'])->name('create');
        Route::post('store', [AuctionController::class, 'store'])->name('store');
        Route::get('show/{id}', [AuctionController::class, 'show'])->name('show');
        Route::get('edit/{id}', [AuctionController::class, 'edit'])->name('edit');
        Route::post('update/{id}', [AuctionController::class, 'update'])->name('update');
        Route::post('delete-image', [AuctionController::class, 'deleteImage'])->name('delete-image');
        Route::post('select-bid', [AuctionController::class, 'selectBidder'])->name('select-bid');
        Route::post('update-status', [AuctionController::class, 'updateStatus'])->name('update-status');
    });

    Route::get('/settings', 'SettingsController@index')->name('settings')->middleware('auth');
    Route::post('/settings', 'SettingsController@update')->name('settings.update')->middleware('auth');

    Route::get('/contact-customer-service', 'ContactUs@index')->name('contact-customer-service');

    Route::get('/update-password', 'AccountsContoller@edit')->middleware(['auth', 'verified'])->name('update-password');
    Route::post('/update-password', 'AccountsContoller@update')->middleware(['auth', 'verified'])->name('save-updated-password');

    Route::get('/manage-users', [CustomerController::class, 'users'])->middleware(['auth', 'verified'])->name('manage-users');
    Route::get('/create-users', [CustomerController::class, 'createUser'])->middleware(['auth', 'verified'])->name('create-users');
    Route::post('/save-users', [CustomerController::class, 'saveUser'])->middleware(['auth', 'verified'])->name('save-users');
    Route::get('/edit-users/{id}', [CustomerController::class, 'editUser'])->middleware(['auth', 'verified'])->name('edit-users');
    Route::post('/update-users', [CustomerController::class, 'updateUser'])->middleware(['auth', 'verified'])->name('update-users');
    Route::get('/show-pdf', [CustomerController::class, 'showPDF'])->name('show-pdf');
    Route::delete('/delete-users/{id}', [CustomerController::class, 'deleteUser'])->middleware(['auth', 'verified'])->name('delete-users');

    Route::prefix('customer')->group(function () {
        Route::any('/', [CustomerController::class, 'index'])->name('customers.index');
        // Route::get('create', [CustomerController::class, 'create'])->name('customers.create');
        // Route::post('store', [CustomerController::class, 'store'])->name('customers.store');
        Route::get('edit/{id}', [CustomerController::class, 'edit'])->name('customers.edit');
        Route::post('update/{id}', [CustomerController::class, 'update'])->name('customers.update');
        Route::get('show/{id}', [CustomerController::class, 'show'])->name('customers.show');
        // Route::delete('destroy/{id}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    });

    Route::get('pages/list', 'CMSPageController@index')->middleware(['auth', 'verified'])->name('pages_list');
    Route::get('pages/edit/{id}', 'CMSPageController@edit')->middleware(['auth', 'verified'])->name('page_edit');
    Route::post('pages/update', 'CMSPageController@update')->middleware(['auth', 'verified'])->name('page_update');
    Route::get('pages/add', 'PostController@add')->middleware(['auth', 'verified'])->name('page_new');
    Route::post('pages/save', 'PostController@save')->middleware(['auth', 'verified'])->name('page_save');
    Route::get('page/{slug}', 'CMSPageController@show')->name('page-show');
    Route::get('packages-to-dash/{id}', 'PackageController@pushPackage')->name('pushPackage');

    Route::get('auctions', [AuctionController::class, 'index'])->name('auctions.index');
    Route::get('auctions/{id}', [AuctionController::class, 'detail'])->name('auctions.detail');
    Route::post('bid-auction', [AuctionController::class, 'bid'])->name('auctions.bid');
    Route::get('decode-pdf', [HomeController::class, 'decodePdf']);
    Route::get('square', [HomeController::class, 'square']);


    // ************** WEB 02 **************

    Route::prefix('shop-for-me')->group(function () {
        Route::any('index', [\App\Http\Controllers\ShopController::class, 'index'])->name('shop-for-me.index');
        Route::get('/create', [\App\Http\Controllers\ShopController::class, 'create'])->name('shop-for-me.create');
        Route::get('/{id}/edit', [\App\Http\Controllers\ShopController::class, 'edit'])->name('shop-for-me.edit');
        Route::get('/show/{id}', [\App\Http\Controllers\ShopController::class, 'show'])->name('shop-for-me.show');
        Route::get('/filter-stores/{id}', [\App\Http\Controllers\ShopController::class, 'filterStores'])->name('shop-for-me.filter-stores');
        Route::post('shop-for-me', [\App\Http\Controllers\ShopController::class, 'store'])->name('shop-for-me.store');
        Route::post('/update', [\App\Http\Controllers\ShopController::class, 'updateOrder'])->name('shop-for-me.update');
        Route::post('order/delete-image', [\App\Http\Controllers\OrderController::class, 'deleteImage'])->name('orders.removeImage');
        Route::delete('/{id}', [\App\Http\Controllers\ShopController::class, 'destroy'])->name('shop-for-me.delete');
        Route::any('/storeComment/{id}', [\App\Http\Controllers\ShopController::class, 'storeComment'])->name('shop-for-me.storeComment');
        Route::any('/changeStatus', [\App\Http\Controllers\ShopController::class, 'changeStatus'])->name('shop-for-me.changeStatus');
        Route::post('/update-invoice', [\App\Http\Controllers\ShopController::class, 'updateInvoice'])->name('shop-for-me.update-invoice');
    });

    Route::any('notifications', [\App\Http\Controllers\HomeController::class, 'notifications'])->name('notifications');
    Route::get('mark-all-read', [\App\Http\Controllers\HomeController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::post('mark-read', [\App\Http\Controllers\HomeController::class, 'markRead'])->name('notifications.mark-read');

    Route::prefix('store')->group(function () {
        Route::get('/', [\App\Http\Controllers\StoreController::class, 'index'])->name('store.index');
        Route::get('/create', [\App\Http\Controllers\StoreController::class, 'create'])->name('store.create');
        Route::get('/edit/{store}', [\App\Http\Controllers\StoreController::class, 'edit'])->name('store.edit');
        Route::post('/store', [\App\Http\Controllers\StoreController::class, 'store'])->name('store.store');
        Route::post('/update', [\App\Http\Controllers\StoreController::class, 'update'])->name('store.update');
    });

    Route::prefix('coupon')->group(function () {
        Route::get('/', [\App\Http\Controllers\CouponController::class, 'index'])->name('coupon.index');
        Route::get('/create', [\App\Http\Controllers\CouponController::class, 'create'])->name('coupon.create');
        Route::get('/edit/{store}', [\App\Http\Controllers\CouponController::class, 'edit'])->name('coupon.edit');
        Route::post('/store', [\App\Http\Controllers\CouponController::class, 'store'])->name('coupon.store');
        Route::post('/update', [\App\Http\Controllers\CouponController::class, 'update'])->name('coupon.update');
        Route::post('/changeStatus', [\App\Http\Controllers\CouponController::class, 'changeStatus'])->name('coupon.changeStatus');
    });

    Route::prefix('promotional')->group(function () {
        Route::get('/', [\App\Http\Controllers\ConfigurationController::class, 'index'])->name('promotional.index');
        Route::get('/edit/{id}', [\App\Http\Controllers\ConfigurationController::class, 'edit'])->name('promotional.edit');
        Route::post('/update', [\App\Http\Controllers\ConfigurationController::class, 'update'])->name('promotional.update');
    });

    Route::prefix('service-page')->group(function () {
        Route::get('/edit/', [\App\Http\Controllers\ServicePageController::class, 'edit'])->name('service-page.edit');
        Route::post('/update', [\App\Http\Controllers\ServicePageController::class, 'update'])->name('service-page.update');
    });

    Route::prefix('additional-request')->group(function () {
        Route::get('/', [\App\Http\Controllers\AdditionalRequestController::class, 'index'])->name('additional-request.index');
        Route::any('/create', [\App\Http\Controllers\AdditionalRequestController::class, 'create'])->name('additional-request.create');
        Route::any('/{id}/edit', [\App\Http\Controllers\AdditionalRequestController::class, 'edit'])->name('additional-request.edit');
        Route::any('/update/{id}', [\App\Http\Controllers\AdditionalRequestController::class, 'update'])->name('additional-request.update');
        Route::any('/changeStatus', [\App\Http\Controllers\AdditionalRequestController::class, 'changeStatus'])->name('additional-request.changeStatus');
        Route::any('/storeComment/{id}', [\App\Http\Controllers\AdditionalRequestController::class, 'storeComment'])->name('additional-request.storeComment');
    });

    Route::group(['prefix' => 'gift-card', 'as' => 'gift-card.'], function () {
        Route::get('/', [GiftCardController::class, 'index'])->name('index');
        Route::any('/create', [GiftCardController::class, 'create'])->name('create');
        Route::any('/{id}/edit', [GiftCardController::class, 'edit'])->name('edit');
        Route::any('/update/{id}', [GiftCardController::class, 'update'])->name('update');
        Route::any('/changeStatus', [GiftCardController::class, 'changeStatus'])->name('changeStatus');
        Route::any('/storeComment/{id}', [GiftCardController::class, 'storeComment'])->name('storeComment');
        Route::post('/delete-image', [GiftCardController::class, 'deleteImage'])->name('removeImage');
    });

    Route::prefix('insurance')->group(function () {
        Route::get('/', [\App\Http\Controllers\InsuranceController::class, 'index'])->name('insurance.index');
        Route::any('/create', [\App\Http\Controllers\InsuranceController::class, 'create'])->name('insurance.create');
        Route::any('/{id}/edit', [\App\Http\Controllers\InsuranceController::class, 'edit'])->name('insurance.edit');
        Route::any('/update/{id}', [\App\Http\Controllers\InsuranceController::class, 'update'])->name('insurance.update');
        Route::any('/changeStatus', [\App\Http\Controllers\InsuranceController::class, 'changeStatus'])->name('insurance.changeStatus');
        Route::any('/storeComment/{id}', [\App\Http\Controllers\InsuranceController::class, 'storeComment'])->name('insurance.storeComment');
    });

    Route::prefix('payment')->group(function () {
        Route::any('list', [\App\Http\Controllers\PaymentController::class,  'getPayments'])->name('payments.getPayments');
        Route::post('check/coupon', [\App\Http\Controllers\PaymentController::class, 'checkCoupon'])->name('checkCoupon');
        Route::get('invoice/{id}', [\App\Http\Controllers\PaymentController::class, 'invoice'])->name('payment.invoice');
        Route::get('generateReport/{id}', [\App\Http\Controllers\PaymentController::class, 'generateReport'])->name('generateReport');
        Route::any('generateReportList', [\App\Http\Controllers\PaymentController::class, 'generateReportList'])->name('generateReportList');
        Route::post('add-payment', [PaymentController::class, 'addPayment'])->middleware(['auth'])->name('payment.add');

        Route::middleware(['auth', 'check.shipping.address'])->group(function () {
            Route::any('setup', [\App\Http\Controllers\PaymentController::class, 'index'])->name('payment.index');
            Route::post('pay', [\App\Http\Controllers\PaymentController::class,  'pay'])->name('payment.pay');
            Route::any('paypal/init', [\App\Http\Controllers\PaymentController::class,  'payPalInit'])->name('payment.payPalInit');
            Route::any('payPalSuccess', [\App\Http\Controllers\PaymentController::class,  'payPalSuccess'])->name('payment.payPalSuccess');
            Route::get('PaymentSuccess/{id}', [\App\Http\Controllers\PaymentController::class,  'PaymentSuccess'])->name('payments.PaymentSuccess');
            Route::post('square-success', [PaymentController::class, 'squareSuccess'])->name('payment.square-success');
        });
    });

    Route::prefix('reports')->group(function () {
        Route::any('report/{slug}', [ReportController::class, 'index'])->name('report.index');
        Route::any('import-carrier-cost', [ReportController::class, 'importCarrierCost'])->name('report.import-carrier-cost');
    });

    Route::prefix('expenses')->group(function () {
        Route::any('list', [ExpenseController::class, 'index'])->name('expense.index');
        Route::get('create', [ExpenseController::class, 'create'])->name('expense.create');
        Route::post('store', [ExpenseController::class, 'store'])->name('expense.store');
        Route::post('destroy', [ExpenseController::class, 'destroy'])->name('expense.destroy');
    });

    Route::get('getShippingAddress/{id}', [\App\Http\Controllers\AddressController::class, 'getShippingAddress'])->name('getShippingAddress');
    Route::get('getMailingAddress', [\App\Http\Controllers\HomeController::class, 'getMailingAddress'])->name('getMailingAddress');

    Route::get('custom-form/index', [CustomFormController::class, 'index'])->name('custom-form.index');
    Route::get('custom-form/create', [CustomFormController::class, 'create'])->name('custom-form.create');
    Route::post('custom-form/store', [CustomFormController::class, 'store'])->name('custom-form.store');
    Route::get('custom-form/print/{id}', [CustomFormController::class, 'print'])->name('custom-form.print');

    Route::post('getAddressByID', [\App\Http\Controllers\AddressController::class, 'getAddressByID'])->name('getAddressByID');
});
