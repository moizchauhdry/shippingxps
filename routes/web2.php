<?php

use Illuminate\Support\Facades\Route;


Route::middleware('auth')->group(function () {

    /*
     * Shop for me routes
     */
    Route::prefix('shop-for-me')->group(function () {
        Route::get('', [\App\Http\Controllers\ShopController::class, 'index'])->name('shop-for-me.index');
        Route::get('/create', [\App\Http\Controllers\ShopController::class, 'create'])->name('shop-for-me.create');
        Route::get('/{id}/edit', [\App\Http\Controllers\ShopController::class, 'edit'])->name('shop-for-me.edit');
        Route::get('/show/{id}', [\App\Http\Controllers\ShopController::class, 'show'])->name('shop-for-me.show');
        Route::get('/filter-stores/{id}', [\App\Http\Controllers\ShopController::class, 'filterStores'])->name('shop-for-me.filter-stores');

        Route::post('shop-for-me', [\App\Http\Controllers\ShopController::class, 'store'])->name('shop-for-me.store');
        Route::post('/update', [\App\Http\Controllers\ShopController::class, 'updateOrder'])->name('shop-for-me.update');
        Route::post('order/delete-image', [\App\Http\Controllers\OrderController::class, 'deleteImage'])->name('orders.removeImage');
        Route::delete('/{id}', [\App\Http\Controllers\ShopController::class, 'destroy'])->name('shop-for-me.delete');
    });

    /*
     * Notification Routes
     */
    Route::get('notifications', [\App\Http\Controllers\HomeController::class, 'notifications'])->name('notifications');
    Route::get('mark-all-read', [\App\Http\Controllers\HomeController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::post('mark-read', [\App\Http\Controllers\HomeController::class, 'markRead'])->name('notifications.mark-read');

    /*
     * Store Routes
     */
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

    /*
     * Shop for me routes
     */
    Route::prefix('payment')->group(function () {
        Route::any('/setup', [\App\Http\Controllers\PaymentController::class, 'index'])->name('payment.index');
        Route::post('pay', [\App\Http\Controllers\PaymentController::class,  'pay'])->name('payment.pay');
        Route::get('list', [\App\Http\Controllers\PaymentController::class,  'getPayments'])->name('payments.getPayments');
        Route::get('PaymentSuccess/{id}', [\App\Http\Controllers\PaymentController::class,  'PaymentSuccess'])->name('payments.PaymentSuccess');
        Route::post('check/coupon', [\App\Http\Controllers\PaymentController::class, 'checkCoupon'])->name('checkCoupon');
        Route::get('invoice/{id}', [\App\Http\Controllers\PaymentController::class, 'buildInvoice'])->name('buildInvoice');
        Route::get('generateReport/{id}', [\App\Http\Controllers\PaymentController::class, 'generateReport'])->name('generateReport');
        Route::get('generateReportList', [\App\Http\Controllers\PaymentController::class, 'generateReportList'])->name('generateReportList');
    });

    Route::get('getShippingAddress/{id}', [\App\Http\Controllers\AddressController::class, 'getShippingAddress'])->name('getShippingAddress');

});


Route::get('migrate', function () {
    $m = \Illuminate\Support\Facades\Artisan::call('migrate', [
        '--force' => true
    ]);
    dd($m);
});
