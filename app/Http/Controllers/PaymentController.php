<?php

namespace App\Http\Controllers;

use App\Events\PaymentEventHandler;
use App\Models\AdditionalRequest;
use App\Models\Address;
use App\Models\Auction;
use App\Models\Coupon;
use App\Models\CustomerCoupon;
use App\Models\GiftCard;
use App\Models\InsuranceRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Package;
use App\Models\Payment;
use App\Models\ServiceRequest;
use App\Models\SiteSetting;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use PDF;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payment_module = $request->payment_module;

        if (!in_array($payment_module, ['package', 'gift_card', 'order', 'auction'])) {
            return redirect()->back()->with('error', 'PAYMENT DENIED!');
        }

        $shipping_address = [];
        $status = NULL;
        $user = Auth::user();

        if ($payment_module == 'package') {
            $package = Package::query()
                ->where('payment_status', 'Pending')
                ->where('id', $request->package_id)
                ->where('customer_id', $user->id)
                // ->where('grand_total', '>', 0)
                ->firstOrFail();

            if ($package->grand_total <= 0) {
                abort(403, 'The amount is less then 0');
            }

            $amount = $package->grand_total;
            $payment_module_id = $package->id;


            $sr_count = ServiceRequest::where('package_id', $package->id)->where('status', 'pending')->count();
            if ($sr_count > 0) {
                abort(403, "The service request is pending on the admin side, so you can't proceed with checkout.");
            }
        }

        if ($payment_module == 'gift_card') {
            $gift_card = GiftCard::query()
                ->where('payment_status', 'Pending')
                ->where('id', $request->payment_module_id)
                ->where('user_id', $user->id)
                ->where('final_amount', '>', 0)
                ->firstOrFail();
            $amount = $gift_card->final_amount;
            $payment_module_id = $gift_card->id;
        }

        if ($payment_module == 'order') {
            $order = Order::query()
                ->where('payment_status', 'Pending')
                ->where('id', $request->payment_module_id)
                ->where('customer_id', $user->id)
                ->where('grand_total', '>', 0)
                ->firstOrFail();
            $amount = $order->grand_total;
            $payment_module_id = $order->id;
        }

        if ($payment_module == 'auction') {
            $auction = Auction::query()
                ->where('bought_at', NULL)
                ->where('id', $request->auction_id)
                ->where('winner_id', $user->id)
                ->firstOrFail();

            $amount = $auction->final_price;
            $payment_module_id = $auction->id;
        }

        if ($amount <= 0) {
            abort(403, 'The amount is less then 0');
        }


        $address_list = Address::where('user_id', $user->id)->get();
        foreach ($address_list as $address) {
            $billing_addresses[$address->id] = [
                'id' => $address->id,
                'label' => $address->fullname . ", " . $address->city . ", " . $address->state . ", " . $address->zip_code,
            ];
        }


        $paypal_processing_fee = SiteSetting::where('name', 'paypal_processing_percentage')->first()->value ?? 0.00;
        $paypal_charged_amount = ($amount * $paypal_processing_fee / 100) + $amount;
        $paypal_charged_amount =  number_format((float)$paypal_charged_amount, 2, '.', '');

        $square_application_id = config('services.square.application_id');
        $square_location_id = config('services.square.location_id');

        return Inertia::render(
            'Payment/OrderPayment',
            [
                'status' => $status,
                'amount' => $amount,
                'payment_module' => $payment_module,
                'payment_module_id' => $payment_module_id,
                'billing_addresses' => $billing_addresses,
                'paypal_processing_fee' => $paypal_processing_fee,
                'paypal_charged_amount' => $paypal_charged_amount,
                'square_application_id' => $square_application_id,
                'square_location_id' => $square_location_id,
            ]
        );
    }

    // AUTHORIZE NET - PAYMENT SUCCESS
    public function pay(Request $request)
    {
        // dd($request->all());
        if (!in_array($request->payment_module_type, ['package', 'gift_card', 'order'])) {
            return redirect()->back()->with('error', 'PAYMENT DENIED!');
        }

        $date = $request->year . "-" . $request->month . "-1 00:00:00";
        $checkDate = new Carbon($date);

        if (strtotime($checkDate) < strtotime(Carbon::now())) {
            return response()->json([
                'status' => 0,
                'message' => 'CARD EXPIRED!'
            ]);
        }

        $amount = doubleval($request->amount);
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName(config('services.authorizeAnet.merchant_login_id'));
        $merchantAuthentication->setTransactionKey(config('services.authorizeAnet.merchant_transaction_key'));
        $refId = 'ref' . time();
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($request->card_no);
        $creditCard->setExpirationDate($request->year . "-" . $request->month);
        $creditCard->setCardCode($request->cvv);
        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setCreditCard($creditCard);

        $user = Auth::user();
        $lastPayment = Payment::latest()->first();
        $invoiceID = sprintf("%05d", ++$lastPayment->id);

        // Create order information
        $order = new AnetAPI\OrderType();
        $order->setInvoiceNumber($invoiceID);
        $order->setDescription('SHIPPINGXPS_PAYMENT');

        // Set the customer's Bill To address
        $customerAddress = new AnetAPI\CustomerAddressType();
        $customerAddress->setFirstName($request->first_name ?? '');
        $customerAddress->setLastName($request->last_name ?? '');
        $customerAddress->setCompany("");
        $customerAddress->setAddress($request->address ?? '');
        $customerAddress->setCity($request->city ?? '');
        $customerAddress->setState($request->state ?? '');
        $customerAddress->setZip($request->zip ?? 'None');
        $customerAddress->setCountry($request->country ?? '');

        $billing_address = [
            'email' => $request->email ?? $user->email ?? '',
            'fullname' => $request->first_name . ' ' . $request->last_name ?? '',
            'phone' => $request->phone_no ?? '',
            'address' => $request->address . ', ' . $request->city . ', ' . $request->zip . ', ' . $request->country ?? '',
        ];


        // Set the customer's identifying information
        $customerData = new AnetAPI\CustomerDataType();
        $customerData->setType("individual");
        $customerData->setId($user->id);
        $customerData->setEmail($request->email);

        // Add values for transaction settings
        $duplicateWindowSetting = new AnetAPI\SettingType();
        $duplicateWindowSetting->setSettingName("duplicateWindow");
        $duplicateWindowSetting->setSettingValue("60");

        // Create a TransactionRequestType object and add the previous objects to it
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount($amount);
        $transactionRequestType->setOrder($order);
        $transactionRequestType->setPayment($paymentOne);
        $transactionRequestType->setBillTo($customerAddress);
        $transactionRequestType->setCustomer($customerData);
        $transactionRequestType->addToTransactionSettings($duplicateWindowSetting);

        // Assemble the complete transaction request
        $transaction = new AnetAPI\CreateTransactionRequest();
        $transaction->setMerchantAuthentication($merchantAuthentication);
        $transaction->setRefId($refId);
        $transaction->setTransactionRequest($transactionRequestType);


        // Create the controller and get the response
        $controller = new AnetController\CreateTransactionController($transaction);
        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
        // $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);

        // Check to see if the API request was successfully received and acted upon
        if ($response->getMessages()->getResultCode() == "Ok") {
            // Since the API request was successful, look for a transaction response
            // and parse it to display the results of authorizing the card
            $tresponse = $response->getTransactionResponse();

            if ($tresponse != null && $tresponse->getMessages() != null) {

                $payment = new Payment();
                $payment->customer_id = $user->id;

                $payment->order_id = $request->payment_module_type == 'order' ? $request->payment_module_id : null;
                $payment->package_id = $request->payment_module_type == 'package' ? $request->payment_module_id : null;
                $payment->additional_request_id = $request->payment_module_type == 'additional_request' ? $request->payment_module_id : null;
                $payment->insurance_id = $request->payment_module_type == 'insurance' ? $request->payment_module_id : null;
                $payment->gift_card_id = $request->payment_module_type == 'gift_card' ? $request->payment_module_id : null;

                $payment->transaction_id = $response->getTransactionResponse()->getTransId();
                $payment->charged_amount = $amount;
                $payment->discount = 0;
                $payment->charged_at = Carbon::now()->format('Y-m-d H:i:s');
                $payment->save();
                $payment->invoice_id = $invoiceID;
                $payment->billing_address = $billing_address;
                $payment->save();

                $shipping = [];
                if ($request->has('shipping_address_id') && $request->get('shipping_address_id') != null) {
                    $shippingAddress = Address::where('id', $request->shipping_address_id)->first();
                    $shipping['email'] = $user->email ?? '';
                    $shipping['fullname'] = $shippingAddress->fullname ?? '';
                    $shipping['phone'] = $shippingAddress->phone ?? '';
                    $shipping['address'] = $shippingAddress->address . ', ' . $shippingAddress->city . ', ' . $shippingAddress->state . ', ' . $shippingAddress->country_name ?? '';
                } else {
                    $shippingAddress = Address::where('user_id', $user->id)->first();
                    $shipping['email'] = $user->email ?? '';
                    $shipping['fullname'] = $shippingAddress->fullname ?? '';
                    $shipping['phone'] = $shippingAddress->phone ?? '';
                    $shipping['address'] = $shippingAddress->address . ', ' . $shippingAddress->city . ', ' . $shippingAddress->state . ', ' . $shippingAddress->country_name ?? '';
                }

                $payment_module_id = $request->payment_module_id;
                if ($request->payment_module_type == 'order') {
                    $order = Order::find($payment_module_id);
                    $order->payment_status = "Paid";
                    $order->save();
                }

                if ($request->payment_module_type == 'additional_request') {
                    $additionalRequest = AdditionalRequest::find($payment_module_id);
                    $additionalRequest->payment_status = "Paid";
                    $additionalRequest->save();
                }

                if ($request->payment_module_type == 'insurance') {
                    $insuranceRequest = InsuranceRequest::find($payment_module_id);
                    $insuranceRequest->payment_status = "Paid";
                    $insuranceRequest->save();
                    $package = Package::find($insuranceRequest->package_id);
                    $package->payment_status = "Paid";
                    $package->save();
                }

                if ($request->payment_module_type == 'gift_card') {
                    $gift_card = GiftCard::find($payment_module_id);
                    $gift_card->payment_status = "Paid";
                    $gift_card->save();
                }

                if ($request->payment_module_type == 'auction') {
                    $auction = Auction::find($payment_module_id);
                    $auction->payment_status = "Paid";
                    $auction->save();
                }

                if ($request->payment_module_type == 'package') {
                    $package = Package::find($payment_module_id);
                    $package->payment_status = "Paid";
                    $package->save();

                    if ($package->address_book_id != null) {
                        $shippingAddress = Address::where('id', $package->address_book_id)->first();
                        $shipping['email'] = $user->email ?? '';
                        $shipping['fullname'] = $shippingAddress->fullname ?? '';
                        $shipping['phone'] = $shippingAddress->phone ?? '';
                        $shipping['address'] = $shippingAddress->address . ', ' . $shippingAddress->city . ', ' . $shippingAddress->state . ', ' . $shippingAddress->country_name ?? '';
                    } else {
                        $shippingAddress = Address::where('user_id', $user->id)->first();
                        $shipping['email'] = $user->email ?? '';
                        $shipping['fullname'] = $shippingAddress->fullname ?? '';
                        $shipping['phone'] = $shippingAddress->phone ?? '';
                        $shipping['address'] = $shippingAddress->address . ', ' . $shippingAddress->city . ', ' . $shippingAddress->state . ', ' . $shippingAddress->country_name ?? '';
                    }

                    if ($request->has('coupon_code_id') && $request->has('coupon_code')) {
                        if ($request->coupon_code_id != null) {
                            $customerCoupon = new CustomerCoupon();
                            $customerCoupon->customer_id = $user->id;
                            $customerCoupon->coupon_id = $request->coupon_code_id;
                            $customerCoupon->save();
                        }
                    }
                }


                try {
                    event(new PaymentEventHandler($payment));
                } catch (\Throwable $th) {
                    //throw $th;
                }

                return response()->json([
                    'status' => 1,
                    'message' => 'PAYMENT_SUCCESS',
                    'payment_id' => $payment->id,
                ]);
            } else {
                if ($tresponse->getErrors() != null) {
                    return response()->json([
                        'status' => 0,
                        'error_code' => $tresponse->getErrors()[0]->getErrorCode(),
                        'message' => $tresponse->getErrors()[0]->getErrorText()
                    ]);
                }
            }
        } else {
            $tresponse = $response->getTransactionResponse();

            if ($tresponse != null && $tresponse->getErrors() != null) {
                return response()->json([
                    'status' => 0,
                    'error_code' => $tresponse->getErrors()[0]->getErrorCode(),
                    'message' => $tresponse->getErrors()[0]->getErrorText()
                ]);
            } else {
                return response()->json([
                    'status' => 0,
                    'error_code' => $response->getMessages()->getMessage()[0]->getCode(),
                    'message' => $response->getMessages()->getMessage()[0]->getText()
                ]);
            }
        }
    }

    //  PayPal Payment Page
    public function payPalInit(Request $request)
    {
        $data = $request->all();
        if (isset($data['amount']) && $data['amount'] > 0) {
            return Inertia::render(
                'Payment/PayPalPayment',
                [
                    'dataResponse' => $data
                ]
            );
        } else {
            return redirect()->route('dashboard');
        }
    }

    // PAYPAL - PAYMENT SUCCESS
    public function payPalSuccess(Request $request)
    {
        $user = Auth::user();
        $description = json_decode($request->payment_detail, true);
        $shipping = $description['purchase_units'][0]['shipping'];
        $billing['email'] = $description['payer']['email_address'] ?? '';
        $billing['fullname'] = $shipping['name']['full_name'] ?? '';

        $billing['address'] = null;
        if (!empty($shipping['address'])) {
            foreach ($shipping['address'] as $key => $address) {
                $billing['address'] .= $address . (($key) == 'country_code' ? '' : ', ');
            }
        }

        $shipping = [];
        if ($request->has('shipping_address_id') && $request->get('shipping_address_id') != null) {
            $shippingAddress = Address::where('id', $request->shipping_address_id)->first();
            $shipping['email'] = $description['payer']['email_address'] ?? '';
            $shipping['fullname'] = $shippingAddress->fullname ?? '';
            $shipping['phone'] = $shippingAddress->phone ?? '';
            $shipping['address'] = $shippingAddress->address . ', ' . $shippingAddress->city . ', ' . $shippingAddress->state . ', ' . $shippingAddress->country_name ?? '';
        } else {
            $shippingAddress = Address::where('user_id', $user->id)->first();

            $shipping['email'] = $description['payer']['email_address'] ?? '';
            $shipping['fullname'] = $shippingAddress->fullname ?? '';
            $shipping['phone'] = $shippingAddress->phone ?? '';
            $shipping['address'] = $shippingAddress->address . ', ' . $shippingAddress->city . ', ' . $shippingAddress->state . ', ' . $shippingAddress->country_name ?? '';
        }

        $discount = 0.00;
        $amount = doubleval($request->amount);
        $discount = (float)number_format($discount, 2);
        $lastPayment = Payment::latest()->first();
        $invoiceID = sprintf("%05d", ++$lastPayment->id);
        $payment = new Payment();
        $payment->customer_id = $user->id;

        $payment->order_id = $request->payment_module_type == 'order' ? $request->payment_module_id : NULL;
        $payment->package_id = $request->payment_module_type == 'package' ? $request->payment_module_id : NULL;
        $payment->additional_request_id = $request->payment_module_type == 'additional-request' ? $request->payment_module_id : NULL;
        $payment->insurance_id = $request->payment_module_type == 'insurance' ? $request->payment_module_id : NULL;
        $payment->gift_card_id = $request->payment_module_type == 'gift-card' ? $request->payment_module_id : NULL;

        $payment->transaction_id = $request->transaction_id ?? $invoiceID;
        $payment->payment_type = 'PayPal';
        $paypal_pecentage = SiteSetting::where('name', 'paypal_processing_percentage')->first()->value ?? 0;
        $paypal_fee = ($amount * $paypal_pecentage / 100);
        $payment->paypal_fee = $paypal_fee;
        $payment->charged_amount = $amount;
        $payment->discount = $discount;
        $payment->charged_at = Carbon::now()->format('Y-m-d H:i:s');
        $payment->save();
        $invoiceID =  sprintf("%05d", $payment->id);
        $payment->invoice_id = $invoiceID;
        $payment->billing_address = $billing;
        $payment->save();

        $payment_module_id = $request->payment_module_id;
        if ($request->payment_module_type == 'order') {
            $order = Order::find($payment_module_id);
            $order->payment_status = "Paid";
            $order->save();
        }

        if ($request->payment_module_type == 'additional_request') {
            $additionalRequest = AdditionalRequest::find($payment_module_id);
            $additionalRequest->payment_status = "Paid";
            $additionalRequest->save();
        }

        if ($request->payment_module_type == 'insurance') {
            $insuranceRequest = InsuranceRequest::find($payment_module_id);
            $insuranceRequest->payment_status = "Paid";
            $insuranceRequest->save();
            $package = Package::find($insuranceRequest->package_id);
            $package->payment_status = "Paid";
            $package->save();
        }

        if ($request->payment_module_type == 'gift_card') {
            $gift_card = GiftCard::find($payment_module_id);
            $gift_card->payment_status = "Paid";
            $gift_card->save();
        }

        if ($request->payment_module_type == 'auction') {
            $auction = Auction::find($payment_module_id);
            $auction->payment_status = "Paid";
            $auction->save();
        }

        if ($request->payment_module_type == 'package') {
            $package = Package::find($payment_module_id);
            $package->payment_status = "Paid";
            $package->save();

            if ($package->address_book_id != null) {
                $shippingAddress = Address::where('id', $package->address_book_id)->first();
                $shipping['email'] = $description['payer']['email_address'] ?? '';
                $shipping['fullname'] = $shippingAddress->fullname ?? '';
                $shipping['phone'] = $shippingAddress->phone ?? '';
                $shipping['address'] = $shippingAddress->address . ', ' . $shippingAddress->city . ', ' . $shippingAddress->state . ', ' . $shippingAddress->country_name ?? '';
            } else {
                $shippingAddress = Address::where('user_id', $user->id)->first();
                $shipping['email'] = $description['payer']['email_address'] ?? '';
                $shipping['fullname'] = $shippingAddress->fullname ?? '';
                $shipping['phone'] = $shippingAddress->phone ?? '';
                $shipping['address'] = $shippingAddress->address . ', ' . $shippingAddress->city . ', ' . $shippingAddress->state . ', ' . $shippingAddress->country_name ?? '';
            }

            if ($request->has('coupon_code_id') && $request->has('coupon_code')) {
                if ($request->coupon_code_id != null) {
                    $customerCoupon = new CustomerCoupon();
                    $customerCoupon->customer_id = $user->id;
                    $customerCoupon->coupon_id = $request->coupon_code_id;
                    $customerCoupon->save();
                }
            }
        }


        try {
            event(new PaymentEventHandler($payment));
        } catch (\Throwable $th) {
            //throw $th;
        }

        return response()->json([
            'status' => 1,
            'message' => 'Paypal',
            'payment_id' => $payment->id,
        ]);
    }

    // PAYMENT SUCCESS PAGE FOR BOTH PAYPAL & AUTHORIZE 
    public function paymentSuccess($id)
    {
        try {
            $payment = Payment::find($id);

            return Inertia::render('Payment/PaymentSuccess', [
                'payment' => $payment,
            ]);
        } catch (\Throwable $th) {
            return redirect()->route('dashboard')->with('something went wrong');
        }
    }

    public function checkCoupon(Request $request)
    {
        // dd($request->code);
        $customer = Auth::user();
        $code = $request->code;

        $coupon = Coupon::where('code', $code)->first();

        if ($coupon != null) {
            $strCheck = strcmp($code, $coupon->code);
            if ($strCheck == 0) {
                $checkCode = CustomerCoupon::where('coupon_id', $coupon->id)->where('customer_id', $customer->id)->get();
                if ($checkCode->count() > 0) {
                    return response()->json([
                        'status' => 0,
                        'message' => 'Coupon already used',
                    ]);
                } else {
                    if (\Session::has('amount')) {
                        $amount = \Session::get('amount');
                        $discount = $amount * ($coupon->discount / 100);
                        $newAmount = $amount - $discount;
                        \Session::put('amount', $newAmount);
                    }
                    return response()->json([
                        'status' => 1,
                        'message' => 'Coupon Applied',
                        'discount' => $coupon->discount,
                        'coupon_id' => $coupon->id,
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'Coupon is invalid',
                ]);
            }
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Coupon is invalid',
            ]);
        }
    }

    public function generateReport($paymentID)
    {
        $payment = Payment::where('id', $paymentID)
            ->with(['customer', 'package', 'order'])
            ->when(Auth::user()->type == 'customer', function ($qry) {
                $qry->where('customer_id', Auth::user()->id);
            })->firstOrFail();


        $html = view('pdfs.report', [
            'payment' => $payment,

        ])->render();


        try {
            $mpdf = new \Mpdf\Mpdf();
            $mpdf->SetFooter('ShippingXPS||Payment Report');
            $mpdf->WriteHTML($html);
            $mpdf->Output($payment->customer->name . '_' . Carbon::now()->format('Ymdhis') . '.pdf', \Mpdf\Output\Destination::INLINE);
        } catch (\Throwable $e) {
            \Log::info($e);
        }
    }

    public function generateReportList()
    {
        $payments = Payment::all();


        $html = view('pdfs.reports', [
            'payments' => $payments,
        ])->render();

        try {
            $mpdf = new \Mpdf\Mpdf();
            // $mpdf->SetWatermarkImage('http://shippingxps.com/wp-content/uploads/2022/09/logo-1.png','0.2','50%');
            // $mpdf->showWatermarkImage = true;
            $mpdf->SetFooter('ShippingXPS|Payment Report|{PAGENO}');
            $mpdf->WriteHTML($html);
            $mpdf->Output('Payment_Report_' . Carbon::now()->format('Ymdhis') . '.pdf', \Mpdf\Output\Destination::INLINE);
        } catch (\Throwable $e) {
            \Log::info($e);
        }
    }

    public function invoice($id)
    {
        $billing = [];
        $package = null;
        $order = null;
        $additionalRequest = null;
        $insurance = null;
        $giftCard = null;
        $service_requests = [];
        $order_items = [];

        $payment = Payment::when(Auth::user()->type == 'customer', function ($qry) {
            $qry->where('customer_id', Auth::user()->id);
        })->findOrFail($id);

        $customer = $payment->customer;
        $billing = json_decode($payment->billing_address) ?? [];
        $warehouse = Warehouse::first();
        $address = json_decode($payment->shipping_address) ?? Address::where('user_id', $customer->id)->first();
        $mailout_fee = SiteSetting::where('id', 2)->first()->value;

        if (isset($payment->package_id)) {
            $package = $payment->package;
            $service_requests = $package->serviceRequests->where('status', 'served');
        }

        if (isset($payment->order_id)) {
            $order = $payment->order;
            $order_items = OrderItem::where('order_id', $order->id)->get();
        }

        if (isset($payment->additional_request_id)) {
            $additionalRequest = $payment->additionalRequest;
        }

        if (isset($payment->insurance_id)) {
            $insurance = $payment->insuranceRequest;
            $warehouse = Warehouse::find($insurance->package->warehouse_id);
        }

        if (isset($payment->gift_card_id)) {
            $giftCard = $payment->giftCard;
        }

        view()->share([
            'payment' => $payment,
            'package' => $package,
            'customer' => $customer,
            'address' => $address,
            'warehouse' => $warehouse,
            'billing' => $billing,
            'order' => $order,
            'order_items' => $order_items,
            'additionalRequest' => $additionalRequest,
            'insuranceRequest' => $insurance,
            'giftCard' => $giftCard,
            'service_requests' => $service_requests,
            'mailout_fee' => $mailout_fee,
        ]);

        $pdf = PDF::loadView('pdfs.payment-invoice');
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('payment-invoice.pdf', array("Attachment" => false));
    }

    public function addPayment(Request $request)
    {

        // dd($request->all());

        $data = $request->validate([
            'transaction_id' => 'required',
            'payment_type' => 'required',
            'payment_module_id' => 'required',
            'payment_module' => 'required|in:package,order',
        ]);

        $pm = $request->payment_module;
        $pmi = $request->payment_module_id;

        if ($pm == 'package') {
            $package = Package::where('id', $pmi)->first();

            if ($package->payment_status != 'Paid') {
                Payment::create([
                    'customer_id' => $package->customer_id,
                    'package_id' => $package->id,
                    'transaction_id' => $data['transaction_id'],
                    'payment_type' => $data['payment_type'],
                    'charged_amount' => $package->grand_total,
                    'charged_at' => Carbon::now()
                ]);
            }

            $package->update(['payment_status' => 'Paid']);
        }

        if ($pm == 'order') {
            $order = Order::where('id', $pmi)->first();

            if ($order->payment_status != 'Paid') {
                Payment::create([
                    'customer_id' => $order->customer_id,
                    'order_id' => $order->id,
                    'transaction_id' => $data['transaction_id'],
                    'payment_type' => $data['payment_type'],
                    'charged_amount' => $order->grand_total,
                    'charged_at' => Carbon::now()
                ]);
            }

            $order->update(['payment_status' => 'Paid']);
        }

        return redirect()->back();
    }

    public function getPayments(Request $request)
    {
        $user = Auth::user();
        $search_invoice_no = $request->search_invoice_no;
        $search_suit_no = $request->search_suit_no;
        $search_tracking_out = $request->search_tracking_out;

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
            'pkg.service_label as pkg_service_label',
            'pkg.tracking_number_out as pkg_tracking_out',
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

        $query->where('payments.charged_at', '!=', NULL);

        $query->when($user->type === 'customer', function ($qry) use ($user) {
            $qry->where('payments.customer_id', $user->id);
        });

        $query->when($search_invoice_no && !empty($search_invoice_no), function ($qry) use ($search_invoice_no) {
            $qry->where('payments.id', $search_invoice_no);
        });

        $query->when($search_tracking_out && !empty($search_tracking_out), function ($qry) use ($search_tracking_out) {
            $qry->where('pkg.tracking_number_out', $search_tracking_out);
        });

        $query->when($search_suit_no && !empty($search_suit_no), function ($qry) use ($search_suit_no) {
            $suit_no = (int) $search_suit_no;
            $suit_no = $suit_no - 4000;
            $qry->where('u.id', $suit_no);
        });

        $query->when($request->date_range && !empty($request->date_range), function ($qry) use ($request) {
            $range = explode(' - ', $request->date_range);
            $from = date("Y-m-d", strtotime($range[0]));
            $to = date("Y-m-d", strtotime($range[1]));
            $qry->whereDate('charged_at', '>=', $from)->whereDate('charged_at', '<=', $to);
        });

        $payments = $query->orderBy('payments.id', 'desc')->paginate(10)->withQueryString();

        return Inertia::render('Payment/Index', [
            'payments' => $payments,
            'filters' => [
                'search_invoice_no' => $search_invoice_no ?? "",
                'search_suit_no' => $search_suit_no ?? "",
                'search_tracking_out' => $request->search_tracking_out ?? "",
                'date_range' => $request->date_range ?? "",
            ]
        ]);
    }

    public function squareSuccess(Request $request)
    {
        // dd($request->all());

        try {

            $SQUARE_API_URL = config('services.square.api_url');
            $user = Auth::user();

            $payment_module = $request->payment_module;

            $package = NULL;
            if ($payment_module == 'package') {
                $package = Package::query()
                    ->where('id', $request->payment_module_id)
                    ->where('customer_id', $user->id)
                    ->where('payment_status', 'Pending')
                    ->firstOrFail();

                if ($package->grand_total <= 0) {
                    abort(403, 'AMOUNT MUST BE GREATER THEN 0');
                }

                $amount = $package->grand_total;
                $payment_module_id = $package->id;
            }

            if ($payment_module == 'order') {
                $order = Order::query()
                    ->where('id', $request->payment_module_id)
                    ->where('customer_id', $user->id)
                    ->where('payment_status', 'Pending')
                    ->firstOrFail();;

                if ($order->grand_total <= 0) {
                    abort(403, 'AMOUNT MUST BE GREATER THEN 0');
                }

                $amount = $order->grand_total;
                $payment_module_id = $order->id;
            }

            if ($payment_module == 'gift_card') {
                $gift_card = GiftCard::query()
                    ->where('id', $request->payment_module_id)
                    ->where('customer_id', $user->id)
                    ->where('payment_status', 'Pending')
                    ->firstOrFail();;

                if ($gift_card->final_amount <= 0) {
                    abort(403, 'AMOUNT MUST BE GREATER THEN 0');
                }

                $amount = $gift_card->final_amount;
                $payment_module_id = $gift_card->id;
            }

            if ($payment_module == 'auction') {
                $auction = Auction::query()
                    ->where('payment_status', 'Pending')
                    ->where('id', $request->payment_module_id)
                    ->where('customer_id', $user->id)
                    ->firstOrFail();;

                if ($auction->final_price <= 0) {
                    abort(403, 'AMOUNT MUST BE GREATER THEN 0');
                }

                $amount = $auction->final_price;
                $payment_module_id = $auction->id;
            }

            $grand_total = $amount * 100;
            $amount = (int) $grand_total;

            $headers = [
                'Authorization' => 'Bearer ' . config('services.square.access_token')
            ];

            // CREATE CUSTOMER
            $customer_url = $SQUARE_API_URL . '/customers';
            $customer_body = [
                "company_name" => $user->name . ' - ' . $user->id + 4000,
                'idempotency_key' => (string) Str::uuid(),
            ];

            $customer_response = Http::withHeaders($headers)->post($customer_url, $customer_body);
            $customer_response = json_decode($customer_response->getBody(), true);

            // CREATE CARD
            // $card_url =  $SQUARE_API_URL . '/cards';
            // $card_body = [
            //     "card" => [
            //         "cardholder_name" => $user->name,
            //         "customer_id" => $customer_response['customer']['id']
            //     ],
            //     'idempotency_key' => (string) Str::uuid(),
            //     'source_id' => $request->payment_token,
            //     'verification_token' => $request->verification_token,
            // ];

            // $card_response = Http::withHeaders($headers)->post($card_url, $card_body);
            // $card_response = json_decode($card_response->getBody(), true);
            
            $billing_address = Address::find($request->billing_address_id);

            // CREATE PAYMENT
            $payment_url =  $SQUARE_API_URL . '/payments';
            $payment_body = [
                'amount_money' => [
                    'amount' => $amount,
                    'currency' => 'USD',
                ],
                'idempotency_key' => (string) Str::uuid(),
                'customer_id' => $customer_response['customer']['id'],
                // 'source_id' => $card_response['card']['id'],
                'source_id' => $request->payment_token,
                'verification_token' => $request->verification_token,
                'billing_address' => [
                    'address_line_1' => $billing_address->address,
                    'address_line_2' => $billing_address->address_2,
                    'address_line_3' => $billing_address->address_3,
                    'first_name' => $billing_address->fullname,
                    'country' => $billing_address->country_code,
                    'postal_code' => $billing_address->zip_code,
                ],
            ];

            $payment_response = Http::withHeaders($headers)->post($payment_url, $payment_body);
            $payment_response = json_decode($payment_response->getBody(), true);

            $data = [
                $payment_module . '_id' => $payment_module_id,
                'customer_id' => $user->id,
                'transaction_id' => $payment_response['payment']['id'],
                'payment_type' => 'square',
                'sq_customer_id' => $customer_response['customer']['id'],
                'sq_customer_response' => json_encode($customer_response),
                // 'sq_card_id' => $card_response['card']['id'],
                // 'sq_card_response' => json_encode($card_response),
                'sq_payment_id' => $payment_response['payment']['id'],
                'sq_payment_response' => json_encode($payment_response), 
                'billing_address' => json_encode($billing_address),
            ];

            $payment = Payment::updateOrCreate([$payment_module . '_id' => $payment_module_id], $data);

            if ($payment_response['payment']['status'] === 'COMPLETED') {

                $payment->update([
                    'charged_amount' => $payment_response['payment']['amount_money']['amount'] / 100,
                    'charged_at' => Carbon::now(),
                ]);

                if ($payment_module == 'package') {
                    $package->update(['payment_status' => 'Paid']);
                    $shipping_address = Address::find($package->address_book_id);
                    $payment->update([
                        'shipping_address' => json_encode($shipping_address),
                    ]);
                }

                if ($payment_module == 'order') {
                    $order->update(['payment_status' => 'Paid']);
                }

                if ($payment_module == 'gift_card') {
                    $gift_card->update(['payment_status' => 'Paid']);
                }

                if ($payment_module == 'auction') {
                    $auction->update(['payment_status' => 'Paid']);
                }

                return redirect()->route('payments.getPayments')->with('success', 'The payment have been completed successfully.');
            } else {

                return redirect()->back()->with('error', $payment_response['errors'][0]['code']);
            }
        } catch (\Throwable $th) {
            Log::info($th);
            return redirect()->back()->with('error', "Payment Error!");
        }
    }
}
