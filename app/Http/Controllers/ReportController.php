<?php

namespace App\Http\Controllers;

use App\Imports\CarrierCostImport;
use App\Models\Expense;
use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request, $slug)
    {

        $user = Auth::user();

        if ($user->type != 'admin') {
            abort('403', 'Access to reports is restricted to administrators only');
        }

        // $search_payment_module = $request->search_payment_module;
        $search_invoice_no = $request->search_invoice_no;
        $search_suit_no = $request->search_suit_no;
        $search_tracking_out = $request->search_tracking_out;
        $search_service_type = $request->search_service_type;

        $month = $request->month ?? Carbon::now()->format('m');
        $year = $request->year ?? Carbon::now()->format('Y');

        $query = Payment::query();

        $query->select(
            'u.id as u_id',
            'u.name as u_name',

            'payments.id as p_id',
            'payments.transaction_id as t_id',
            'payments.payment_type as p_method',
            'payments.charged_amount as charged_amount',
            'payments.charged_at as charged_at',

            'pkg.id as pkg_id',
            'pkg.carrier_code as carrier_code',
            'pkg.service_label as pkg_service_label',
            'pkg.tracking_number_out as pkg_tracking_out',
            'pkg.shipping_charges_gross',
            'pkg.shipping_markup_percentage',
            'pkg.shipping_markup_fee',
            'pkg.xls_carrier_cost',
            'pkg.shipping_charges as shipping_with_markup',

            'orders.service_charges as order_service_charges',

            DB::raw('CASE 
                WHEN payments.package_id IS NOT NULL THEN "package"
                WHEN payments.order_id IS NOT NULL THEN "order"
                WHEN payments.gift_card_id IS NOT NULL THEN "gift"
                ELSE "unknown"
            END AS p_type'),

            DB::raw('CASE 
                WHEN payments.package_id IS NOT NULL THEN payments.package_id
                WHEN payments.order_id IS NOT NULL THEN payments.order_id
                WHEN payments.gift_card_id IS NOT NULL THEN payments.gift_card_id
                ELSE "unknown"
            END AS p_type_id')
        );

        $query->join('users as u', 'u.id', 'payments.customer_id');
        $query->leftJoin('packages as pkg', 'pkg.id', 'payments.package_id');
        $query->leftJoin('orders', 'orders.id', 'payments.order_id');

        $query->when($slug == 'packages', function ($qry) use ($user) {
            $qry->where('payments.package_id', '!=', NULL);
        });

        $query->when($slug == 'orders', function ($qry) use ($user) {
            $qry->where('payments.order_id', '!=', NULL);
        });

        $query->when($user->type === 'customer', function ($qry) use ($user) {
            $qry->where('payments.customer_id', $user->id);
        });

        $query->when($search_invoice_no && !empty($search_invoice_no), function ($qry) use ($search_invoice_no) {
            $qry->where('payments.id', $search_invoice_no);
        });

        $query->when($search_service_type && !empty($search_service_type), function ($qry) use ($search_service_type) {
            $qry->where('pkg.carrier_code', $search_service_type);
        });

        $query->when($search_tracking_out && !empty($search_tracking_out), function ($qry) use ($search_tracking_out) {
            $qry->where('pkg.tracking_number_out', $search_tracking_out);
        });

        $query->when($search_suit_no && !empty($search_suit_no), function ($qry) use ($search_suit_no) {
            $suit_no = (int) $search_suit_no;
            $suit_no = $suit_no - 4000;
            $qry->where('u.id', $suit_no);
        });

        // $query->when($request->date_range && !empty($request->date_range), function ($qry) use ($request) {
        //     $range = explode(' - ', $request->date_range);
        //     $from = date("Y-m-d", strtotime($range[0]));
        //     $to = date("Y-m-d", strtotime($range[1]));
        //     $qry->whereDate('charged_at', '>=', $from)->whereDate('charged_at', '<=', $to);
        // });


        $query->whereYear('charged_at', $year);
        $query->whereMonth('charged_at', $month);

        $payments = $query->orderBy('payments.id', 'desc')->paginate(25)->withQueryString();

        $total_expense = Expense::whereYear('expense_at', $year)->whereMonth('expense_at', $month)->sum('amount');

        return Inertia::render('Reports/ReportList', [
            'payments' => $payments,
            'stats' => [
                'total' => $query->sum('payments.charged_amount'),
                'profit' => $query->sum('pkg.shipping_markup_fee'),
                'gross_shipping' => $query->sum('pkg.shipping_charges_gross'),
                'shipping_with_markup' => $query->sum('pkg.shipping_charges'),
                'xls_carrier_cost' => $query->sum('pkg.xls_carrier_cost'),
                'carrier_profit' => $query->sum('payments.charged_amount') - $query->sum('pkg.xls_carrier_cost'),
                'service_charges' => $query->sum('orders.service_charges'),
                'total_expense' => $total_expense,
                'net_profit' => ($query->sum('payments.charged_amount') - $query->sum('pkg.xls_carrier_cost')) - $total_expense,
            ],
            'filters' => [
                'slug' => $slug,
                'search_service_type' => $search_service_type ?? "",
                'search_invoice_no' => $search_invoice_no ?? "",
                'search_suit_no' => $search_suit_no ?? "",
                'search_tracking_out' => $request->search_tracking_out ?? "",
                // 'date_range' => $request->date_range ?? "",
                'year' => $year,
                'month' => $month,
            ]
        ]);
    }

    public function importCarrierCost(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx',
            'type' => 'required',
        ]);

        try {
            $file = $request->file('file');
            $type = $request->type;

            if ($type == "1") {
                $import = new CarrierCostImport();
                Excel::import($import, $file);
            }

            return redirect()->route('report.index', 'packages');
        } catch (\Throwable $th) {
            throw $th;
            // abort(403, $th->getMessage());
        }
    }
}
