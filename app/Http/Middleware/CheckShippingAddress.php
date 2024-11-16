<?php

namespace App\Http\Middleware;

use App\Models\Address;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckShippingAddress
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if ($user) {
            $shipping_address_count = Address::where('user_id', $user->id)->count();
            if ($shipping_address_count == 0) {
                return \redirect()->route('addresses')->with('error', 'Please add a shipping address to proceed with the payment.');
            }
        }

        return $next($request);
    }
}
