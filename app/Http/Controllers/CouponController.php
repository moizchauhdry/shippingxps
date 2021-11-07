<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CouponController extends Controller
{
    //
    public function index(){
        $coupons = Coupon::all();
        return Inertia::render('Coupons/Index',[
            'coupons' => $coupons,
        ]);
    }

    public function create()
    {
        return Inertia::render('Coupons/Create');
    }

    public function store(Request $request)
    {
//        dd($request->all());
        $validate = $request->validate([
            'name' => 'required',
            'discount' => 'required',
        ]);

        $code = \Str::random(6);

        $coupon = new Coupon();
        $coupon->name = $request->input('name');
        $coupon->discount = $request->input('discount');
        $coupon->code = $code;
        $coupon->save();

        return redirect()->route('coupon.index')->with('success', 'Coupon Added!');
    }

    public function show($id)
    {

    }

    public function edit($id)
    {

    }

    public function update(Request $request)
    {

    }

    public function changeStatus(Request $request)
    {
        $id = $request->id;
        $status = $request->status;

        $coupon = Coupon::find($id);
        $coupon->status = $status;
        $coupon->save();

        return response()->json(['status'=>'1','coupon'=>$coupon]);
    }

    public function destroy(Request $request)
    {

    }
}
