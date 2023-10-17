<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\OrderItem;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PackageController extends BaseController
{
    public function store(Request $request)
    {
        // dd($request->all());

        $validator = Validator::make($request->all(), [
            'items.*.description' => 'required',
            'items.*.quantity' => 'required|gt:0',
            'items.*.price' => 'required|gt:0|numeric',
            // 'items.*.origin_country' => 'required',
            'items.*.batteries' => 'nullable',
            'items.*.hs_code' => 'nullable',
            // 'shipping_total' => 'required',
            'export_reason' => 'required',
            'country' => 'required',
        ],  [
            'items.*.description.required' => 'The package items description field is required.',
            'items.*.quantity.required' => 'The package items quantity field is required.',
            'items.*.price.required' => 'The package items price field is required.',
            'items.*.price.gt' => 'The package items price must be greater than 0.',
            // 'items.*.origin_country.required' => 'The package items origin country field is required.',
        ]);

        if ($validator->fails()) {
            return $this->sendError('validation error', $validator->errors());
        }

        $data = [
            'status' => 'filled',
            'custom_form_status' => true,
            // 'shipping_total' => $validated['shipping_total'],
            // 'package_type' => $validated['package_type'],
            // 'special_instructions' => $request->special_instructions,
        ];

        $package = Package::create($data);

        OrderItem::where('package_id', $package->id)->delete();

        foreach ($request->items as $key => $item) {
            $order_item = new OrderItem();
            $order_item->package_id = $package->id;
            // $order_item->hs_code = $item['hs_code'] ?? null;
            $order_item->description = $item['description'];
            $order_item->quantity = $item['quantity'];
            $order_item->unit_price = $item['price'];
            // $order_item->origin_country = $item['origin_country'];
            // $order_item->batteries = $item['batteries'] ?? null;
            $order_item->save();
        }

        return $this->sendResponse('success', 'The custom decration form filled successfully.');
    }
}
