<?php

use App\Models\ShippingService;
use App\Models\SiteSetting;
use Carbon\Carbon;

function format_number($number)
{
    if ($number > 0) {
        return number_format((float)$number, 2, '.', '');
    } else {
        return 0;
    }
}

function calulate_storage($package)
{
    $storage_days_exceeded = 0;

    $boxes_weight = $package->boxes->sum('weight');
    $fee = (float) SiteSetting::where('name', 'storage_fee')->first()->value;

    $createdAt = Carbon::parse($package->created_at);
    $now = Carbon::now();
    $days_exceeded = $now->diffInDays($createdAt) - 75;
    $storage_days = $now->diffInDays($createdAt);

    if ($days_exceeded > 0) {
        $storage_fee = $fee * $boxes_weight * $days_exceeded;
    } else {
        $storage_fee = 0;
    }

    if ($days_exceeded > 0) {
        $storage_days_exceeded = $days_exceeded;
    }

    $package->update([
        'storage_fee' => (float) $storage_fee,
        'storage_days' => (float) $storage_days,
        'storage_days_exceeded' => (float) $storage_days_exceeded,
    ]);

    return true;
}

function shipping_service_markup($type)
{
    $percentage = 0;
    $service = ShippingService::where('service_code', $type)->first();
    if ($service) {
        $percentage = $service->markup_percentage;
    }

    return $percentage;
}
