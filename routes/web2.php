<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GiftCardController;


Route::middleware('auth')->group(function () {
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
        Route::any('/storeComment/{id}', [\App\Http\Controllers\ShopController::class, 'storeComment'])->name('shop-for-me.storeComment');
        Route::any('/changeStatus', [\App\Http\Controllers\ShopController::class, 'changeStatus'])->name('shop-for-me.changeStatus');
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
        Route::any('/setup', [\App\Http\Controllers\PaymentController::class, 'index'])->name('payment.index');
        Route::post('pay', [\App\Http\Controllers\PaymentController::class,  'pay'])->name('payment.pay');
        Route::any('paypal/init', [\App\Http\Controllers\PaymentController::class,  'payPalInit'])->name('payment.payPalInit');
        Route::any('payPalSuccess', [\App\Http\Controllers\PaymentController::class,  'payPalSuccess'])->name('payment.payPalSuccess');
        Route::any('list', [\App\Http\Controllers\PaymentController::class,  'getPayments'])->name('payments.getPayments');
        Route::get('PaymentSuccess/{id}', [\App\Http\Controllers\PaymentController::class,  'PaymentSuccess'])->name('payments.PaymentSuccess');
        Route::post('check/coupon', [\App\Http\Controllers\PaymentController::class, 'checkCoupon'])->name('checkCoupon');
        Route::get('invoice/{id}', [\App\Http\Controllers\PaymentController::class, 'invoice'])->name('payment.invoice');
        Route::get('generateReport/{id}', [\App\Http\Controllers\PaymentController::class, 'generateReport'])->name('generateReport');
        Route::any('generateReportList', [\App\Http\Controllers\PaymentController::class, 'generateReportList'])->name('generateReportList');
    });

    Route::get('getShippingAddress/{id}', [\App\Http\Controllers\AddressController::class, 'getShippingAddress'])->name('getShippingAddress');
    Route::get('getMailingAddress', [\App\Http\Controllers\HomeController::class, 'getMailingAddress'])->name('getMailingAddress');
});
