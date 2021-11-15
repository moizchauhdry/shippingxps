<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

class PaymentController extends Controller
{
    //



    public function index(Request $request){
        return Inertia::render('Payment/OrderPayment',['amount' => $request->amount]);
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

        // Create order information
//        $order = new AnetAPI\OrderType();
//        $order->setInvoiceNumber("10102");
//        $order->setDescription("Golf Shirts");

        // Set the customer's Bill To address
        $customerAddress = new AnetAPI\CustomerAddressType();
        $customerAddress->setFirstName("Ellen");
        $customerAddress->setLastName("Johnson");
        $customerAddress->setCompany("Souveniropolis");
        $customerAddress->setAddress("14 Main Street");
        $customerAddress->setCity("Pecan Springs");
        $customerAddress->setState("TX");
        $customerAddress->setZip("44628");
        $customerAddress->setCountry("USA");

        // Set the customer's identifying information
        $customerData = new AnetAPI\CustomerDataType();
        $customerData->setType("individual");
        $customerData->setId("99999456654");
        $customerData->setEmail("EllenJohnson@example.com");

        // Add values for transaction settings
        $duplicateWindowSetting = new AnetAPI\SettingType();
        $duplicateWindowSetting->setSettingName("duplicateWindow");
        $duplicateWindowSetting->setSettingValue("60");

        // Add some merchant defined fields. These fields won't be stored with the transaction,
        // but will be echoed back in the response.
        $merchantDefinedField1 = new AnetAPI\UserFieldType();
        $merchantDefinedField1->setName("customerLoyaltyNum");
        $merchantDefinedField1->setValue("1128836273");

        $merchantDefinedField2 = new AnetAPI\UserFieldType();
        $merchantDefinedField2->setName("favoriteColor");
        $merchantDefinedField2->setValue("blue");

        // Create a TransactionRequestType object and add the previous objects to it
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount($request->amount);
//        $transactionRequestType->setOrder($order);
        $transactionRequestType->setPayment($paymentOne);
        $transactionRequestType->setBillTo($customerAddress);
        $transactionRequestType->setCustomer($customerData);
        $transactionRequestType->addToTransactionSettings($duplicateWindowSetting);
        $transactionRequestType->addToUserFields($merchantDefinedField1);
        $transactionRequestType->addToUserFields($merchantDefinedField2);

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
        /*dd($response);*/
        return redirect()->route('payments.PaymentSuccess')->with(['response'=>$response]);
    }

    public function getPayments()
    {
        /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName(config('services.authorizeAnet.merchant_login_id'));
        $merchantAuthentication->setTransactionKey(config('services.authorizeAnet.merchant_transaction_key'));

        // Set the transaction's refId
        $refId = 'ref' . time();

        $request = new AnetAPI\GetSettledBatchListRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setIncludeStatistics(true);

        // Both the first and last dates must be in the same time zone
        // The time between first and last dates, inclusively, cannot exceed 31 days.
//        $request->setFirstSettlementDate($firstSettlementDate);
//        $request->setLastSettlementDate($lastSettlementDate);

        $controller = new AnetController\GetSettledBatchListController ($request);

        $payments[] = [];

        $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);

        /*if (($response != null) && ($response->getMessages()->getResultCode() == "Ok"))
        {
            foreach($response->getBatchList() as $batch)
            {

                echo "\n\n";
                echo "Batch ID: " . $batch->getBatchId() . "\n";
                echo "Batch settled on (UTC): " . $batch->getSettlementTimeUTC()->format('r') . "\n";
                echo "Batch settled on (Local): " . $batch->getSettlementTimeLocal()->format('D, d M Y H:i:s') . "\n";
                echo "Batch settlement state: " . $batch->getSettlementState() . "\n";
                echo "Batch market type: " . $batch->getMarketType() . "\n";
                echo "Batch product: " . $batch->getProduct() . "\n";
                foreach($batch->getStatistics() as $statistics)
                {
                    echo "Account type: ".$statistics->getAccountType()."\n";
                    echo "Total charge amount: ".$statistics->getChargeAmount()."\n";
                    echo "Charge count: ".$statistics->getChargeCount()."\n";
                    echo "Refund amount: ".$statistics->getRefundAmount()."\n";
                    echo "Refund count: ".$statistics->getRefundCount()."\n";
                    echo "Void count: ".$statistics->getVoidCount()."\n";
                    echo "Decline count: ".$statistics->getDeclineCount()."\n";
                    echo "Error amount: ".$statistics->getErrorCount()."\n";
                }
            }
        }
        else
        {
            echo "ERROR :  Invalid response\n";
            $errorMessages = $response->getMessages()->getMessage();
            echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
        }*/

        // Set the request's refId



        dump($response);
        return Inertia::render('Payment/BatchList',['batchLists'=>$response->getBatchList()]);
    }

    public function paymentSuccess()
    {
        if(\Session::has('response')){
            $response = \Session::get('response');
        }else{
            $response = null;
        }

        return Inertia::render('Payment/PaymentSuccess',[
            'response' => $response,
        ]);
    }
}
