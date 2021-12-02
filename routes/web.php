<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PostController;
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

Route::get('/', function () {
    $promotional = \App\Models\Configuration::find(1);
    return Inertia::render('Welcome', [
        'promotionalMessage' => $promotional->description ?? "Shopping and Shipping for you with Ease",
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::get('/checkAuth-user',[\App\Http\Controllers\HomeController::class,'checkAuth']);

Route::get('/test-email','EmailController@index')->name('test-email');

Route::get('/dashboard','HomeController@dashboard')->name('dashboard')->middleware(['auth','verified']);

Route::get('/shipping-calculator','HomeController@pricing')->name('shipping-calculator');
Route::get('/getServicesList','HomeController@getServicesList')->name('getServicesList');

Route::get('/pricing','HomeController@pricingTable')->name('pricing');

Route::get('/getListings','HomeController@getListings')->name('getListings');
Route::get('/getQuotes','HomeController@getQuotes')->name('getQuotes');
Route::get('/getQuote','HomeController@getQuote')->name('getQuote');

Route::get('/shopping','HomeController@shopping')->name('shopping')->middleware('auth');

Route::get('/orders/quote','OrderController@quote')->name('orders.quote')->middleware('auth');

Route::get('/orders/create','OrderController@create')->name('orders.create')->middleware('auth');
Route::post('/orders','OrderController@store')->name('order.store')->middleware('auth');
Route::get('/orders','OrderController@index')->name('orders')->middleware('auth');
Route::get('/orders/{id}/edit','OrderController@edit')->name('order.edit')->middleware('auth');
Route::post('/orders/update','OrderController@update')->name('orders.update')->middleware('auth');
Route::get('/orders/{id}','OrderController@show')->name('orders.show')->middleware('auth');
Route::delete('/orders{id}','OrderController@destroy')->name('orders.destroy')->middleware('auth');
Route::post('/orders/removeItem','OrderController@removeItem')->name('orders.removeItem')->middleware('auth');

Route::get('/packages','PackageController@index')->name('packages')->middleware('auth');
Route::get('/packages/{id}','PackageController@show')->name('packages.show')->middleware('auth');
Route::get('/packages/create/{order_id}','PackageController@create')->name('package.create')->middleware('auth');
Route::post('/packages','PackageController@store')->name('package.store')->middleware('auth');
Route::get('/packages/{id}/edit','PackageController@edit')->name('package.edit')->middleware('auth');
Route::post('/packages/update','PackageController@update')->name('package.update')->middleware('auth');
Route::get('/getStorageFee','PackageController@getStorageFee')->name('getStorageFee')->middleware('auth');

Route::post('/packages/service-request','PackageController@serviceRequest')->name('packages.service-request')->middleware('auth');
Route::post('/packages/service-handle','PackageController@serviceHandle')->name('packages.service-handle')->middleware('auth');

Route::post('/packages/ship-package','PackageController@shipPackage')->name('packages.ship-package')->middleware('auth');
Route::post('/packages/consolidate-package','PackageController@consolidatePackage')->name('packages.consolidate')->middleware('auth');
Route::post('/packages/set-shipping-service','PackageController@setShippingService')->name('packages.set-shipping-service')->middleware('auth');

Route::get('/packages/custom/{package_id}','PackageController@custom')->name('packages.custom')->middleware('auth');
Route::get('/packages/get-pdf/{order_id}','PackageController@getPdf')->name('packages.pdf')->middleware('auth');

Route::get('/services/create','ServiceController@create')->name('services.create')->middleware('auth');
Route::post('/services','ServiceController@store')->name('services.store')->middleware('auth');
Route::get('/services','ServiceController@index')->name('services')->middleware('auth');
Route::get('/services/{id}/edit','ServiceController@edit')->name('services.edit')->middleware('auth');
Route::post('/services/update','ServiceController@update')->name('services.update')->middleware('auth');
Route::get('/services/{id}','ServiceController@show')->name('services.show')->middleware('auth');
Route::delete('/services{id}','ServiceController@destroy')->name('services.destroy')->middleware('auth');

Route::get('/profile','AccountsContoller@profile')->name('profile')->middleware('auth');
Route::post('/update-profile','AccountsContoller@updateProfile')->name('update-profile')->middleware('auth');
Route::get('/orders/create','OrderController@create')->name('orders.create')->middleware('auth');
Route::post('/orders','OrderController@store')->name('order.store')->middleware('auth');
Route::get('/orders','OrderController@index')->name('orders')->middleware('auth');
Route::get('/orders/{id}/edit','OrderController@edit')->name('order.edit')->middleware('auth');
Route::post('/orders/update','OrderController@update')->name('orders.update')->middleware('auth');
Route::get('/orders/{id}','OrderController@show')->name('orders.show')->middleware('auth');
Route::post('/orders','OrderController@store')->name('orders.store')->middleware('auth');
Route::post('/orders/removeItem','OrderController@removeItem')->name('orders.removeItem')->middleware('auth');

//Route::resource('address', AddressController::class)->middleware('auth');

Route::get('/addresses','AddressController@index')->name('addresses')->middleware('auth');
Route::get('/address/create','AddressController@create')->name('address.create')->middleware('auth');
Route::post('/address','AddressController@store')->name('address.store')->middleware('auth');
Route::get('/address/{id}/edit','AddressController@edit')->name('address.edit')->middleware('auth');
Route::put('/address','AddressController@update')->name('address.update')->middleware('auth');
Route::delete('/address{id}','AddressController@destroy')->name('address.destroy')->middleware('auth');
Route::get('/address/suite','AddressController@suite')->name('address.suite')->middleware('auth');

Route::get('/warehouses','WarehouseController@index')->name('warehouses')->middleware('auth');
Route::get('/warehouses/create','WarehouseController@create')->name('warehouses.create')->middleware('auth');
Route::post('/warehouses','WarehouseController@store')->name('warehouses.store')->middleware('auth');
Route::get('/warehouses/{id}/edit','WarehouseController@edit')->name('warehouses.edit')->middleware('auth');
Route::post('/warehouses','WarehouseController@update')->name('warehouses.update')->middleware('auth');
Route::delete('/warehouses{id}','WarehouseController@destroy')->name('warehouses.destroy')->middleware('auth');

Route::get('/settings','SettingsController@index')->name('settings')->middleware('auth');
Route::post('/settings','SettingsController@update')->name('settings.update')->middleware('auth');

Route::get('/contact-customer-service','ContactUs@index')->name('contact-customer-service');

Route::get('/update-password', 'AccountsContoller@edit')->middleware(['auth', 'verified'])->name('update-password');
Route::post('/update-password', 'AccountsContoller@update')->middleware(['auth', 'verified'])->name('save-updated-password');


Route::get('/test', function () {
    return Inertia::render('test');
})->middleware(['auth', 'verified'])->name('test');

require __DIR__.'/auth.php';

Route::get('/manage-users', [CustomerController::class, 'users'])
                ->middleware(['auth', 'verified'])
                ->name('manage-users');
Route::get('/create-users', [CustomerController::class, 'createUser'])
                ->middleware(['auth', 'verified'])
                ->name('create-users');
Route::post('/save-users', [CustomerController::class, 'saveUser'])
                ->middleware(['auth', 'verified'])
                ->name('save-users');

Route::get('/edit-users/{id}', [CustomerController::class, 'editUser'])
                ->middleware(['auth', 'verified'])
                ->name('edit-users');
Route::post('/update-users', [CustomerController::class, 'updateUser'])
                ->middleware(['auth', 'verified'])
                ->name('update-users');

Route::get('/show-pdf', [CustomerController::class, 'showPDF'])
                ->name('show-pdf');

Route::delete('/delete-users/{id}', [CustomerController::class, 'deleteUser'])
                ->middleware(['auth', 'verified'])
                ->name('delete-users');


Route::get('/customers', [CustomerController::class, 'index'])
                ->middleware(['auth', 'verified'])
                ->name('customers');
Route::get('/create-customer', [CustomerController::class, 'create'])
                ->middleware(['auth', 'verified'])
                ->name('create-customer');
Route::post('/create-customer', [CustomerController::class, 'store'])
                ->middleware(['auth', 'verified'])
                ->name('create-customer');
Route::get('/customer/{id}', [CustomerController::class, 'edit'])
                ->middleware(['auth', 'verified'])
                ->name('view-customer');
Route::get('/customer/show/{id}', [CustomerController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('detail-customer');
Route::put('/customer/{id}', [CustomerController::class, 'update'])
                ->middleware(['auth', 'verified'])
                ->name('edit-customer');
Route::delete('/lead/{id}', [CustomerController::class, 'destroy'])
                ->middleware(['auth', 'verified'])
                ->name('delete-customer');

Route::get('pages/list', 'CMSPageController@index')->middleware(['auth', 'verified'])->name('pages_list');
Route::get('pages/edit/{id}', 'CMSPageController@edit')->middleware(['auth', 'verified'])->name('page_edit');
Route::post('pages/update', 'CMSPageController@update')->middleware(['auth', 'verified'])->name('page_update');

Route::get('pages/add', 'PostController@add')->middleware(['auth', 'verified'])->name('page_new');
Route::post('pages/save', 'PostController@save')->middleware(['auth', 'verified'])->name('page_save');

//Route::get('{post_url}', 'PostController@index');

Route::get('/clear-cache', function () {
    \Artisan::call('cache:clear');
    \Artisan::call('route:clear');
    \Artisan::call('view:clear');
    \Artisan::call('optimize:clear');
    return 'Cache Cleared';
});
                                
