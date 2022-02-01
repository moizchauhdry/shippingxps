<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Shipping;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\InsuranceRequest;
use Inertia\Inertia;

class InsuranceController extends Controller
{
    //

    public function index()
    {
        $insurance = InsuranceRequest::all();

        return Inertia::render('Insurance/Index',[
            'coupons' => $insurance,
        ]);

    }


    public function create(Request $request)
    {
        $user = \Auth::user();

        if ($request->isMethod('POST')) {


            $validatedData = $request->validate([
                'package_id' => 'required',
                'shipping_service' => 'required',
                'insurance_amount' => 'required',
                'message' => 'required',
            ]);

            $insuranceRequest = InsuranceRequest::create([
                'package_id' => $validatedData['package_id'],
                'user_id' => $user->id,
                'shipping_service' => $validatedData['shipping_service'],
                'insurance_amount' => $validatedData['insurance_amount'],
                'message' => $validatedData['message'],
            ]);
            /*$url = \URL::route('additional-request.edit', $insuranceRequest->id);
            $data = [
                'url' => \URL::route('additional-request.edit', $insuranceRequest->id),
                'message' => 'Customer has created an additional request. <a href="' . $url . '">Click Here</a>',
            ];
            $admins = User::where('type', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new AdditionalRequestNotification($data));
            }*/
            return redirect()->route('insurance.index')->with('success', 'Successfully Requested');
        }

        if ($user->type == 'admin') {
            $packages = Package::all();
        } else {
            $packages = Package::where('customer_id', $user->id)->get();
        }

        $shippingServices  = collect(Shipping::getShippingServices())->pluck('serviceLabel');
        return Inertia::render('Insurance/Create',[
            'shipping_services' => $shippingServices,
            'packages' => $packages,
        ]);
    }
}
