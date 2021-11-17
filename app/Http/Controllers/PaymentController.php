<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

class PaymentController extends Controller
{
    //



    public function     index(Request $request){
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

        return Inertia::render('Payment/OrderPayment',['amount' => $request->amount,'status'=>$status]);
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
        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setTransactionRequest($transactionRequestType);

        // Create the controller and get the response
        $controller = new AnetController\CreateTransactionController($request);
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
            \Session::forget(['order_id','package_id','amount']);
            return redirect()->route('payments.PaymentSuccess')->with(['payment'=>$payment,'status']);
        }
//        dd($response);

        return redirect()->route('payment.index',['amount'=>\Session::get('amount'),'status'=>$response->getMessages()]);

        /*return Inertia::render('Payment/OrderPayment',
            [
                'amount' => \Session::has('amount')? \Session::get('amount'): 0,
                'status'=>$response->getMessages()
            ]);*/


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
}
