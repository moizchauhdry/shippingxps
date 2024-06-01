<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $suit_no = intval($request->suit_no) - 4000;

        $query = Package::with('customer', 'warehouse', 'child_packages', 'boxes')
            ->when(Auth::user()->type == 'customer', function ($qry) {
                $qry->where('customer_id', Auth::user()->id);
            })
            ->when($request->pkg_id && !empty($request->pkg_id), function ($qry) use ($request) {
                $qry->where('id', $request->pkg_id);
            })
            ->when($request->suit_no && !empty($request->suit_no), function ($qry) use ($suit_no) {
                $qry->where('customer_id', $suit_no);
            })
            ->when($request->pkg_type && !empty($request->pkg_type), function ($qry) use ($request) {
                $qry->where('pkg_type', $request->pkg_type);
            })
            ->when($request->pkg_carrier && !empty($request->pkg_carrier), function ($qry) use ($request) {
                $qry->where('carrier_code', $request->pkg_carrier);
            })
            ->when($request->pkg_status && !empty($request->pkg_status), function ($qry) use ($request) {
                if ($request->pkg_status == 'shipped') {
                    $qry->where('tracking_number_out', '!=', NULL);
                } else {
                    $qry->where('status', $request->pkg_status);
                }
            })
            ->when($request->payment_status && !empty($request->payment_status), function ($qry) use ($request) {
                $qry->where('payment_status', $request->payment_status);
            })
            ->when($request->tracking_out && !empty($request->tracking_out), function ($qry) use ($request) {
                $qry->where('tracking_number_out', $request->tracking_out);
            })
            ->when($request->auctioned && !empty($request->auctioned), function ($qry) use ($request) {
                $qry->where('auctioned', $request->auctioned);
            })
            ->when($request->date_range && !empty($request->date_range), function ($qry) use ($request) {
                $range = explode(' - ', $request->date_range);
                $from = date("Y-m-d", strtotime($range[0]));
                $to = date("Y-m-d", strtotime($range[1]));
                $qry->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
            });

        $packages_count = $query->count();
        $packages = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        $open_pkgs_count = Package::where('status', 'open')->where('pkg_type', 'single')->count();

        return Inertia::render('Reports/PackageReport', [
            'pkgs' => $packages,
            'open_pkgs_count' => $open_pkgs_count,
            'packages_count' => $packages_count,
            'filters' => [
                'pkg_id' => $request->pkg_id ?? "",
                'suit_no' => $request->suit_no ?? "",
                'pkg_status' => $request->pkg_status ?? "",
                'pkg_type' => $request->pkg_type ?? "",
                'pkg_carrier' => $request->pkg_carrier ?? "",
                'payment_status' => $request->payment_status ?? "",
                'auctioned' => $request->auctioned ?? "",
                'date_range' => $request->date_range ?? "",
                'tracking_out' => $request->tracking_out ?? "",
            ]
        ]);
    }
}
