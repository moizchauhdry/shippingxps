<?php

namespace App\Http\Controllers;

use App\Events\PaymentEventHandler;
use App\Models\AdditionalRequest;
use App\Models\Address;
use App\Models\Coupon;
use App\Models\CustomerCoupon;
use App\Models\GiftCard;
use App\Models\InsuranceRequest;
use App\Models\Order;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use PDF;
use Mpdf\Mpdf;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use SebastianBergmann\LinesOfCode\LinesOfCode;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('package_id')) {
            \Session::forget(['order_id', 'additional_request_id', 'insurance_id']);
        }
        if (\Session::has('order_id')) {
            \Session::forget(['package_id', 'additional_request_id', 'insurance_id']);
        }
        if ($request->has('package_id') || \Session::has('order_id') || \Session::has('additional_request_id') || \Session::has('insurance_id') || \Session::has('gift_card_id')) {
            \Session::put('amount', $request->amount);
            if ($request->has('status')) {
                $status = $request->status;
            } else {
                $status = null;
            }

            if ($request->has('package_id')) {
                \Session::put('package_id', $request->package_id);
            }

            return Inertia::render(
                'Payment/OrderPayment',
                [
                    'amount' => $request->amount,
                    'status' => $status,
                    'hasInsurance' => \Session::has('insurance_id') ? 1 : 0,
                    'hasRequest' => \Session::has('additional_request_id') ? 1 : 0,
                    'hasPackage' => $request->has('package_id') ? 1 : 0,
                ]
            );
        } else {
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    // AUTHORIZE NET - PAYMENT SUCCESS
    public function pay(Request $request)
    {
        $cardNos = [
            '5610591081018250',
            '6011111111111117',
            '6011000990139424',
            '3530111333300000',
            '3566002020360505',
            '5555555555554444',
            '5105105105105100',
            '4111111111111111',
            '4012888888881881',
            '5019717010103742',
            '6331101999990016',
            '4242424242424242',
            '6011000000000012',
            '3088000000000017',
            '5424000000000015',
            '2223000010309703',
            '2223000010309711'
        ];


        /*if (in_array($request->card_no, $cardNos)) {
            return response()->json([
               'status' => 0,
               'message' => 'The Card No is Invalid'
            ]);
            // return redirect()->route('payment.index', ['amount' => \Session::get('amount')])->with('error', 'invalid Card No');
        }*/

        $discount = 0.00;
        if ($request->has('discount')) {
            if ($request->discount > 0) {
                $amt = $request->amount;
                $discount = $amt * ($request->discount / 100);
                $request->amount = $amt - $discount;
            }
        }

        $date = $request->year . "-" . $request->month . "-1 00:00:00";
        $checkDate = new Carbon($date);

        if (strtotime($checkDate) < strtotime(Carbon::now())) {
            return response()->json([
                'status' => 0,
                'message' => 'Please Check card Expiry'
            ]);
        }

        $amount = doubleval($request->amount);
        $discount = (float)number_format($discount, 2);
        \Log::info('amount = ' . $request->amount);
        \Log::info('amount = ' . $amount . ', discount = ' . $discount);




        /* Create a merchantAuthenticationType object with authentication details
           retrieved from the constants file */
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName(config('services.authorizeAnet.merchant_login_id'));
        $merchantAuthentication->setTransactionKey(config('services.authorizeAnet.merchant_transaction_key'));

        // Set the transaction's refId
        $refId = 'ref' . time();

        // Create the payment data for a credit card
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($request->card_no);
        $creditCard->setExpirationDate($request->year . "-" . $request->month);
        $creditCard->setCardCode($request->cvv);

        // Add the payment data to a paymentType object
        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setCreditCard($creditCard);

        $user = \Auth::user();
        $address = Address::where('user_id', $user->id)->first();
        $nameExplode = explode(' ', $user->name);

        $lastPayment = Payment::latest()->first();
        $invoiceID = sprintf("%05d", ++$lastPayment->id);
        $invoiceDescription = \Session::has('order_id') ? "Payment Of Order" : (\Session::has('package_id') ? "Payment of Package" : 'Request');
        // Create order information
        $order = new AnetAPI\OrderType();
        $order->setInvoiceNumber($invoiceID);
        $order->setDescription($invoiceDescription);

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

        $billing = [
            'email' => $request->email ?? $user->email ?? '',
            'fullname' => $request->first_name . ' ' . $request->last_name ?? '',
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
        //$response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
        /*For Production use the below line */
        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
        if ($response != null) {
            // Check to see if the API request was successfully received and acted upon
            if ($response->getMessages()->getResultCode() == "Ok") {
                // Since the API request was successful, look for a transaction response
                // and parse it to display the results of authorizing the card
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getMessages() != null) {
                    /*echo " Successfully created transaction with Transaction ID: " . $tresponse->getTransId() . "\n";
                    echo " Transaction Response Code: " . $tresponse->getResponseCode() . "\n";
                    echo " Message Code: " . $tresponse->getMessages()[0]->getCode() . "\n";
                    echo " Auth Code: " . $tresponse->getAuthCode() . "\n";
                    echo " Description: " . $tresponse->getMessages()[0]->getDescription() . "\n";*/

                    /*  On Successful Transaction Show Status =>   */

                    $payment = new Payment();
                    $payment->customer_id = $user->id;
                    $payment->order_id = \Session::has('order_id') ? \Session::get('order_id') : null;
                    $payment->package_id = \Session::has('package_id') ? \Session::get('package_id') : null;
                    $payment->additional_request_id = \Session::has('additional_request_id') ? \Session::get('additional_request_id') : null;
                    $payment->insurance_id = \Session::has('insurance_id') ? \Session::get('insurance_id') : null;
                    $payment->gift_card_id = \Session::has('gift_card_id') ? \Session::get('gift_card_id') : null;
                    $payment->transaction_id = $response->getTransactionResponse()->getTransId();
                    $payment->charged_amount = $amount;
                    $payment->discount = $discount;
                    $payment->charged_at = Carbon::now()->format('Y-m-d H:i:s');
                    $payment->save();
                    $payment->invoice_id = $invoiceID;
                    $payment->save();
                    /*Dont make it live */

                    /*Dont make it live end */

                    if (\Session::has('order_id')) {
                        $id = \Session::get('order_id');
                        $order = Order::find($id);
                        $order->payment_status = "Paid";
                        $order->save();
                    }

                    if (\Session::has('additional_request_id')) {
                        $id = \Session::get('additional_request_id');
                        $additionalRequest = AdditionalRequest::find($id);
                        $additionalRequest->payment_status = "Paid";
                        $additionalRequest->save();
                    }

                    if (\Session::has('insurance_id')) {
                        $id = \Session::get('insurance_id');
                        $insuranceRequest = InsuranceRequest::find($id);
                        $insuranceRequest->payment_status = "Paid";
                        $insuranceRequest->save();
                        $package = Package::find($insuranceRequest->package_id);
                        $package->payment_status = "Paid";
                        $package->save();
                    }

                    if (\Session::has('gift_card_id')) {
                        $id = \Session::get('gift_card_id');
                        $gift_card = GiftCard::find($id);
                        $gift_card->payment_status = "Paid";
                        $gift_card->save();
                    }


                    if (\Session::has('package_id')) {
                        $id = \Session::get('package_id');
                        $package = Package::find($id);
                        $package->payment_status = "Paid";
                        $package->save();

                        if ($request->has('coupon_code_id') && $request->has('coupon_code')) {
                            if ($request->coupon_code_id != null) {
                                $customerCoupon = new CustomerCoupon();
                                $customerCoupon->customer_id = $user->id;
                                $customerCoupon->coupon_id = $request->coupon_code_id;
                                $customerCoupon->save();
                            }
                        }
                    }

                    \Log::info('b4 invoice');
                    $this->buildInvoice($payment->id, $billing);
                    \Log::info('after invoice');

                    \Session::forget(['order_id', 'package_id', 'amount', 'additional_request_id', 'insurance_id', 'gift_card_id']);
                    return response()->json([
                        'status' => 1,
                        'message' => 'Please Check card Expiry',
                        'payment_id' => $payment->id,
                    ]);
                } else {
                    //  echo "Transaction Failed \n";
                    if ($tresponse->getErrors() != null) {
                        //  echo " Error Code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
                        //  echo " Error Message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
                        return response()->json([
                            'status' => 0,
                            'error_code' => $tresponse->getErrors()[0]->getErrorCode(),
                            'message' => $tresponse->getErrors()[0]->getErrorText()
                        ]);
                    }
                }
                // Or, print errors if the API request wasn't successful
            } else {
                //  echo "Transaction Failed \n";
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getErrors() != null) {
                    //  echo " Error Code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
                    //  echo " Error Message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
                    return response()->json([
                        'status' => 0,
                        'error_code' => $tresponse->getErrors()[0]->getErrorCode(),
                        'message' => $tresponse->getErrors()[0]->getErrorText()
                    ]);
                } else {
                    // echo " Error Code  : " . $response->getMessages()->getMessage()[0]->getCode() . "\n";
                    // echo " Error Message : " . $response->getMessages()->getMessage()[0]->getText() . "\n";
                    return response()->json([
                        'status' => 0,
                        'error_code' => $response->getMessages()->getMessage()[0]->getCode(),
                        'message' => $response->getMessages()->getMessage()[0]->getText()
                    ]);
                }
            }
        } else {
            // echo "No response returned \n";
            return response()->json([
                'status' => 0,
                // 'error_code' => $response->getMessages()->getMessage()[0]->getCode(),
                'message' => 'No response returned'
            ]);
        }

        return response()->json([
            'status' => 0,
            // 'error_code' => $response->getMessages()->getMessage()[0]->getCode(),
            'message' => 'No response returned'
        ]);

        /*if ($response->getMessages()->getResultCode() == "Ok") {
            // return redirect()->route('payments.PaymentSuccess')->with(['payment' => $payment, 'status']);
        } else {
            return response()->json([
                'status' => 0,
                'message' => $response->getMessages()->getMessage()[0]->getText()
            ]);
            // return redirect()->back()->with(['error' => ]);
        }*/
    }

    // PAYPAL - PAYMENT SUCCESS
    public function payPalSuccess(Request $request)
    {
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
        $user = Auth::user();
        $discount = 0.00;
        if ($request->has('discount')) {
            if ($request->discount > 0) {
                $amt = $request->amount;
                $discount = $amt * ($request->discount / 100);
                $request->amount = $amt - $discount;
            }
        }
        $amount = doubleval($request->amount);
        $discount = (float)number_format($discount, 2);
        $lastPayment = Payment::latest()->first();
        $invoiceID = sprintf("%05d", ++$lastPayment->id);
        $payment = new Payment();
        $payment->customer_id = $user->id;
        $payment->order_id = \Session::has('order_id') ? \Session::get('order_id') : null;
        $payment->package_id = \Session::has('package_id') ? \Session::get('package_id') : null;
        $payment->additional_request_id = \Session::has('additional_request_id') ? \Session::get('additional_request_id') : null;
        $payment->insurance_id = \Session::has('insurance_id') ? \Session::get('insurance_id') : null;
        $payment->gift_card_id = \Session::has('gift_card_id') ? \Session::get('gift_card_id') : null;
        $payment->transaction_id = $request->transaction_id ?? $invoiceID;
        $payment->payment_type = 'PayPal';
        $payment->charged_amount = $amount;
        $payment->discount = $discount;
        $payment->charged_at = Carbon::now()->format('Y-m-d H:i:s');
        $payment->save();
        $invoiceID =  sprintf("%05d", $payment->id);
        $payment->invoice_id = $invoiceID;
        $payment->save();
        if (\Session::has('order_id')) {
            $id = \Session::get('order_id');
            $order = Order::find($id);
            $order->payment_status = "Paid";
            $order->save();
        }

        if (\Session::has('additional_request_id')) {
            $id = \Session::get('additional_request_id');
            $additionalRequest = AdditionalRequest::find($id);
            $additionalRequest->payment_status = "Paid";
            $additionalRequest->save();
        }

        if (\Session::has('insurance_id')) {
            $id = \Session::get('insurance_id');
            $insuranceRequest = InsuranceRequest::find($id);
            $insuranceRequest->payment_status = "Paid";
            $insuranceRequest->save();
            $package = Package::find($insuranceRequest->package_id);
            $package->payment_status = "Paid";
            $package->save();
        }

        if (\Session::has('gift_card_id')) {
            $id = \Session::get('gift_card_id');
            $gift_card = GiftCard::find($id);
            $gift_card->payment_status = "Paid";
            $gift_card->save();
        }

        if (\Session::has('package_id')) {
            $id = \Session::get('package_id');
            $package = Package::find($id);
            $package->payment_status = "Paid";
            $package->save();

            if ($request->has('coupon_code_id') && $request->has('coupon_code')) {
                if ($request->coupon_code_id != null) {
                    $customerCoupon = new CustomerCoupon();
                    $customerCoupon->customer_id = $user->id;
                    $customerCoupon->coupon_id = $request->coupon_code_id;
                    $customerCoupon->save();
                }
            }
        }

        \Log::info('b4 invoice');
        $this->buildInvoice($payment->id, $billing);
        \Log::info('after invoice');

        \Session::forget(['order_id', 'package_id', 'amount', 'additional_request_id', 'insurance_id', 'gift_card_id']);
        return response()->json([
            'status' => 1,
            'message' => 'Please Check card Expiry',
            'payment_id' => $payment->id,
        ]);
    }

    public function buildInvoice($id, $billing = [])
    {
        $payment = Payment::find($id);
        $customer = $payment->customer;
        $warehouse = Warehouse::first();
        $address = Address::where('user_id', $customer->id)->first();

        if ($payment->package_id != null) {
            \Log::info('On Package');
            $package = $payment->package;
            // return view('pdfs.payment-invoice',compact('payment','package','customer'));
            $html = view('pdfs.invoice-payment', [
                'payment' => $payment,
                'package' => $package,
                'customer' => $customer,
                'address' => $address,
                'warehouse' => $warehouse,
                'billing' => $billing
            ])->render();

            // return $html;

        }

        if ($payment->order_id != null) {
            $order = $payment->order;
            // return view('pdfs.payment-invoice',compact('payment','order','customer'));

            $html = view('pdfs.invoice-payment', [
                'payment' => $payment,
                'order' => $order,
                'customer' => $customer,
                'address' => $address,
                'warehouse' => $warehouse,
                'billing' => $billing
            ])->render();
        }

        if ($payment->additional_request_id != null) {
            $additional_request_id = $payment->additionalRequest;
            // return view('pdfs.payment-invoice',compact('payment','order','customer'));

            $html = view('pdfs.invoice-payment', [
                'payment' => $payment,
                'additionalRequest' => $additional_request_id,
                'customer' => $customer,
                'address' => $address,
                'warehouse' => $warehouse,
                'billing' => $billing
            ])->render();
        }

        if ($payment->insurance_id != null) {
            $insurance = $payment->insuranceRequest;
            $warehouse = Warehouse::find($insurance->package->warehouse_id);
            // return view('pdfs.payment-invoice',compact('payment','order','customer'));

            $html = view('pdfs.invoice-payment', [
                'payment' => $payment,
                'insuranceRequest' => $insurance,
                'customer' => $customer,
                'address' => $address,
                'warehouse' => $warehouse,
                'billing' => $billing
            ])->render();
        }

        if ($payment->gift_card_id != null) {
            $gift_card = $payment->giftCard;

            $html = view('pdfs.invoice-payment', [
                'payment' => $payment,
                'warehouse' => $warehouse,
                'gift_card' => $gift_card,
                'customer' => $customer,
                'address' => $address,
                'billing' => $billing
            ])->render();
        }

        \Log::info('b4 writing');
        try {
            $mpdf = new \Mpdf\Mpdf();
            $mpdf->WriteHTML($html);
            $mpdf->SetHTMLFooter('<table style="position: absolute;width: 100%;bottom: 0px">
                <tr>
                    <th colspan="3" style="text-align: center">
                        THANK YOU FOR YOUR BUSINESS
                        <br><br>
                    </th>   
                </tr>
                <tr>
                    <th style="text-align: center;font-size: 12px;font-weight: normal">+1-657-210-1801</th>
                    <th style="text-align: center;font-size: 12px;font-weight: normal">shippingxps.com</th>
                    <th style="text-align: center;font-size: 12px;font-weight: normal">info@shippingxps.com</th>
                </tr>
            </table>');
            $mpdf->Output('public/invoices/pdf/' . $payment->invoice_id . '.pdf', \Mpdf\Output\Destination::FILE);
        } catch (\Throwable $e) {
            \Log::info($e);
        }
        \Log::info('on saving record');
        $payment->invoice_url = 'invoices/pdf/' . $payment->invoice_id . '.pdf';
        $payment->save();

        event(new PaymentEventHandler($payment));
    }

    public function getPayments(Request $request)
    {
        $user = \Auth::user();

        if ($user->type == 'admin') {
            $payments = Payment::with(['customer', 'package' => function ($query) {
                $query->with('address', function ($qry) {
                    $qry->with('country');
                });
            }, 'order'])->orderByDesc('id');
        } else {
            $payments = Payment::with(['customer', 'package' => function ($query) {
                $query->with('address', function ($qry) {
                    $qry->with('country');
                });
            }, 'order'])->where('customer_id', $user->id)->orderByDesc('id');
        }

        if ($request->isMethod('post')) {

            $payments = $this->searchPayments($request, $payments);

            $perPage = 10;

            if ($request->has('per_page') && $request->get('per_page') != NULL) {
                $perPage = $request->get('per_page');
            }

            return response([
                'payments' => $payments->paginate($perPage),
            ]);
        }
        return Inertia::render('Payment/Index', ['payments' => $payments->paginate(10)]);
    }

    // PAYMENT SUCCESS PAGE FOR BOTH PAYPAL & AUTHORIZE 
    public function paymentSuccess($id)
    {
        $payment = Payment::find($id);

        if ($payment != null) {
            return Inertia::render('Payment/PaymentSuccess', [
                'payment' => $payment,
            ]);
        } else {
            return redirect()->back();
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
        $payment = Payment::where('id', $paymentID)->with(['customer', 'package', 'order'])->first();


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
            // $mpdf->SetWatermarkImage('https://shippingxps.com/theme/img/logo.png','0.2','50%');
            // $mpdf->showWatermarkImage = true;
            $mpdf->SetFooter('ShippingXPS|Payment Report|{PAGENO}');
            $mpdf->WriteHTML($html);
            $mpdf->Output('Payment_Report_' . Carbon::now()->format('Ymdhis') . '.pdf', \Mpdf\Output\Destination::INLINE);
        } catch (\Throwable $e) {
            \Log::info($e);
        }
    }

    public function searchPayments(Request $request, $payments)
    {
        if ($request->has('search') && $request->search != null) {
            $search = $request->search;
            $payments->where(function ($query) use ($search) {
                $query->where('id', 'LIKE', "%$search%")
                    ->orWhere('transaction_id', 'LIKE', "%$search%")
                    ->orWhere('package_id', 'LIKE', "%$search%")
                    ->orWhere('invoice_id', 'LIKE', "%$search%")
                    ->orWhere('charged_amount', 'LIKE', "%$search%");
            })
                ->orWhereHas('customer', function ($qry) use ($search) {
                    $qry->where('name', 'LIKE', '%' . $search . '%');
                    if (is_numeric($search)) {
                        $s = (int)$search;
                        $s = $s - 4000;
                        $qry->orWhere('id', 'LIKE', '%' . $s . '%');
                    }
                })
                ->orWhereHas('package', function ($qry) use ($search) {
                    $qry->where('payment_status', 'LIKE', "%$search%")
                        ->orWhere('service_label', 'LIKE', "%$search%")
                        ->orWhere('shipping_charges', 'LIKE', "%$search%");
                })
                ->orWhereHas('order', function ($qry) use ($search) {
                    $qry->where('id', 'LIKE', '%' . $search . '%');
                });
        }

        if ($request->has('date_selection') && $request->get('date_selection') != NULL) {
            if ($request->get('date_selection') == '1') {
                $payments->whereDate('created_at', Carbon::today());
            }
            if ($request->get('date_selection') == '2') {
                $payments->whereDate('created_at', Carbon::yesterday());
            }
            if ($request->get('date_selection') == '3') {
                $date = Carbon::now()->subDays(7);
                $payments->where('created_at', '>=', $date);
            }
            if ($request->get('date_selection') == '4') {
                $date = Carbon::now()->subDays(30);
                $payments->where('created_at', '>=', $date);
            }
            if ($request->date_selection == 5) {
                if ($request->get('date_range')) {
                    $dateRange = explode(' - ', $request->date_range);
                    $from = date("Y-m-d", strtotime($dateRange[0]));
                    $to = date("Y-m-d", strtotime($dateRange[1]));
                    $payments->whereBetween('created_at', [$from, $to]);
                }
            }
        }

        return $payments;
    }

    public function invoice($id)
    {
        $billing = [];
        $package = null;
        $order = null;
        $additionalRequest = null;
        $insurance = null;
        $giftCard = null;

        $payment = Payment::findOrFail($id);
        $customer = $payment->customer;
        $warehouse = Warehouse::first();
        $address = Address::where('user_id', $customer->id)->first();

        if (isset($payment->package_id)) {
            $package = $payment->package;
        }

        if (isset($payment->order_id)) {
            $order = $payment->order;
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
            'address' => $address,
            'order' => $order,
            'additionalRequest' => $additionalRequest,
            'insuranceRequest' => $insurance,
            'giftCard' => $giftCard,
        ]);

        $pdf = PDF::loadView('pdfs.invoice-payment');
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('SHIPPING-XPS-INVOICE.pdf', array("Attachment" => false));
    }
}
