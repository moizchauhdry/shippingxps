<?php

namespace App\Http\Controllers;

use App\Events\PaymentEventHandler;
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
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

class PaymentController extends Controller
{
    //



    public function index(Request $request){
        if($request->has('package_id') || \Session::has('order_id')){
            \Session::put('amount',$request->amount);
            if($request->has('status')){
                $status = $request->status;
            }
            else{
                $status = null;
            }

            if($request->has('package_id')){
                \Session::put('package_id',$request->package_id);
            }

            return Inertia::render('Payment/OrderPayment',
                [
                    'amount' => $request->amount,
                    'status'=>$status,
                    'hasPackage'=> $request->has('package_id') ? 1 : 0,
                ]);
        }else{
            return redirect()->back()->with('error','Something went wrong');
        }
    }

    public function pay(Request $request)
    {
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
        $creditCard->setExpirationDate($request->year."-".$request->month);
        $creditCard->setCardCode($request->cvv);

        // Add the payment data to a paymentType object
        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setCreditCard($creditCard);

        $user = \Auth::user();

        $nameExplode = explode(' ',$user->name);

        // Set the customer's Bill To address
        $customerAddress = new AnetAPI\CustomerAddressType();
        $customerAddress->setFirstName($nameExplode[0] ?? '');
        $customerAddress->setLastName($nameExplode[0] ?? '');
        $customerAddress->setCompany("");
        $customerAddress->setAddress($user->address->address ?? '');
        $customerAddress->setCity($user->address->city ?? '');
        $customerAddress->setState($user->address->state ?? '');
        $customerAddress->setZip($user->address->zip ?? '');
        $customerAddress->setCountry($user->address->country->name ?? '');

        // Set the customer's identifying information
        $customerData = new AnetAPI\CustomerDataType();
        $customerData->setType("individual");
        $customerData->setId($user->id);
        $customerData->setEmail($user->email);

        // Add values for transaction settings
        $duplicateWindowSetting = new AnetAPI\SettingType();
        $duplicateWindowSetting->setSettingName("duplicateWindow");
        $duplicateWindowSetting->setSettingValue("60");

        // Create a TransactionRequestType object and add the previous objects to it
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount($request->amount);
//        $transactionRequestType->setOrder($order);
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
        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
        /*if ($response != null) {
            // Check to see if the API request was successfully received and acted upon
            if ($response->getMessages()->getResultCode() == "Ok") {
                // Since the API request was successful, look for a transaction response
                // and parse it to display the results of authorizing the card
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getMessages() != null) {
                    echo " Successfully created transaction with Transaction ID: " . $tresponse->getTransId() . "\n";
                    echo " Transaction Response Code: " . $tresponse->getResponseCode() . "\n";
                    echo " Message Code: " . $tresponse->getMessages()[0]->getCode() . "\n";
                    echo " Auth Code: " . $tresponse->getAuthCode() . "\n";
                    echo " Description: " . $tresponse->getMessages()[0]->getDescription() . "\n";
                } else {
                    echo "Transaction Failed \n";
                    if ($tresponse->getErrors() != null) {
                        echo " Error Code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
                        echo " Error Message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
                    }
                }
                // Or, print errors if the API request wasn't successful
            } else {
                echo "Transaction Failed \n";
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getErrors() != null) {
                    echo " Error Code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
                    echo " Error Message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
                } else {
                    echo " Error Code  : " . $response->getMessages()->getMessage()[0]->getCode() . "\n";
                    echo " Error Message : " . $response->getMessages()->getMessage()[0]->getText() . "\n";
                }
            }
        } else {
            echo  "No response returned \n";
        }*/

        if ($response->getMessages()->getResultCode() == "Ok"){
            $payment = new Payment();
            $payment->customer_id = $user->id;
            $payment->order_id = \Session::has('order_id')? \Session::get('order_id'): null;
            $payment->package_id = \Session::has('package_id')? \Session::get('package_id'): null;
            $payment->invoice_id = Str::uuid();
            $payment->transaction_id = $response->getTransactionResponse()->getTransId();
            $payment->charged_amount = \Session::get('amount');
            $payment->charged_at = Carbon::now()->format('Y-m-d H:i:s');
            $payment->save();

            /*Dont make it live */
                event(new PaymentEventHandler($payment));
            /*Dont make it live end */

            if(\Session::has('order_id')){
                $id = \Session::get('order_id');
                $order = Order::find($id);
                $order->payment_status = "Paid";
                $order->save();
            }

            if(\Session::has('package_id')){
                $id = \Session::get('package_id');
                $package = Package::find($id);
                $package->payment_status = "Paid";
                $package->save();

                /*if($request->has('coupon_id') && $request->coupon_code != null){
                    $customerCoupon = new CustomerCoupon();
                    $customerCoupon->customer_id = $user->id;
                    $customerCoupon->coupon_id = $request->coupon_id;
                    $customerCoupon->save();
                }*/



            }

            \Session::forget(['order_id','package_id','amount']);
            return redirect()->route('payments.PaymentSuccess')->with(['payment'=>$payment,'status']);
        }

        return redirect()->route('payment.index',['amount'=>\Session::get('amount'),'status'=>$response->getMessages()]);


    }

    public function getPayments()
    {
        $user = \Auth::user();

        if($user->type == 'admin'){
            $payments = Payment::with(['customer','package','order'])->orderByDesc('id')->get();
        }else{
            $payments = Payment::with(['customer','package','order'])->where('customer_id',$user->id)->orderByDesc('id')->get();
        }
        return Inertia::render('Payment/Index',['payments'=> $payments]);
    }

    public function paymentSuccess()
    {
        if(\Session::has('payment')){
            $payment = \Session::get('payment');
        }else{
            $payment = null;
        }

        return Inertia::render('Payment/PaymentSuccess',[
            'payment' => $payment,
        ]);
    }

    /* Coupon Mangement */
    public function checkCoupon(Request $request)
    {
        // dd($request->code);
        $customer = Auth::user();
        $code = $request->code;

        $coupon = Coupon::where('code',$code)->first();

        if($coupon != null){
            $strCheck = strcmp($code,$coupon->code);
            if($strCheck == 0){
                $checkCode = CustomerCoupon::where('coupon_id',$coupon->id)->where('customer_id',$customer->id)->get();
                if($checkCode->count() > 0){
                    return response()->json([
                        'status' => 0,
                        'message' => 'Coupon already used',
                    ]);
                }else{
                    if(\Session::has('amount')){
                        $amount = \Session::get('amount');
                        $discount = $amount * ($coupon->discount / 100);
                        $newAmount = $amount - $discount;
                        \Session::put('amount',$newAmount);
                    }
                    return response()->json([
                        'status' => 1,
                        'message' => 'Coupon Applied',
                        'discount' => $coupon->discount,
                        'coupon_id' => $coupon->id,
                    ]);
                }

            }else{
                return response()->json([
                    'status' => 0,
                    'message' => 'Coupon is invalid',
                ]);
            }
        }else{
            return response()->json([
                'status' => 0,
                'message' => 'Coupon is invalid',
            ]);
        }

    }

    public function buildInvoice()
    {
        $package = Package::find(2);


        return view('pdfs.payment-invoice',compact('package'));
    }
}
