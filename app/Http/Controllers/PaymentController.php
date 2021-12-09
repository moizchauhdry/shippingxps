<?php

namespace App\Http\Controllers;

use App\Events\PaymentEventHandler;
use App\Models\Address;
use App\Models\Coupon;
use App\Models\CustomerCoupon;
use App\Models\Order;
use App\Models\Package;
use App\Models\Payment;
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
    //


    public function index(Request $request)
    {
        if ($request->has('package_id') || \Session::has('order_id')) {
            \Session::put('amount', $request->amount);
            if ($request->has('status')) {
                $status = $request->status;
            } else {
                $status = null;
            }

            if ($request->has('package_id')) {
                \Session::put('package_id', $request->package_id);
            }

            return Inertia::render('Payment/OrderPayment',
                [
                    'amount' => $request->amount,
                    'status' => $status,
                    'hasPackage' => $request->has('package_id') ? 1 : 0,
                ]);
        } else {
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

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
            '2223000010309711'];


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

        $amount = (double)number_format($request->amount, 2);
        $discount = (double)number_format($discount,2);
        \Log::info('amount = '. $amount . ', discount = '.$discount);


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
        $invoiceDescription = \Session::has('order_id') ? "Payment Of Order" : (\Session::has('package_id') ? "Payment of Package" : 'Void');
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
                    $this->buildInvoice($payment->id);
                    \Log::info('after invoice');

                    \Session::forget(['order_id', 'package_id', 'amount']);
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

    public function buildInvoice($id)
    {
        $payment = Payment::find($id);
        $customer = $payment->customer;
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
        <th style="text-align: center;font-size: 12px;font-weight: normal">+1 657-201-7881</th>
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

    public function getPayments()
    {
        $user = \Auth::user();

        if ($user->type == 'admin') {
            $payments = Payment::with(['customer', 'package', 'order'])->orderByDesc('id')->get();
        } else {
            $payments = Payment::with(['customer', 'package', 'order'])->where('customer_id', $user->id)->orderByDesc('id')->get();
        }
        return Inertia::render('Payment/Index', ['payments' => $payments]);
    }

    /* Coupon Mangement */

    public function paymentSuccess($id)
    {
        /*if (\Session::has('payment')) {
            $payment = \Session::get('payment');
        } else {
            $payment = null;
        }*/
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
}
