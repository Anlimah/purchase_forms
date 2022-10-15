<?php

namespace Src\Controller;

use Src\Gateway\OrchardPaymentGateway;
use Src\Controller\VoucherPurchase;

class PaymentController
{
    private $voucher;

    public function __construct()
    {
        $this->voucher = new VoucherPurchase();
    }

    public function vendorPaymentProcess($data)
    {
        if (!empty($data)) {
            $trans_id = time();
            if ($trans_id) {
                return $this->voucher->SaveFormPurchaseData($data, $trans_id);
            } else {
                return array("success" => true, "message" => "Transaction ID failed!");
            }
        }
    }

    public function verifyVendorPurchase(int $vendor_id, int $transaction_id)
    {
        
    }

    private function prepareTransaction($secretKey, $payUrl, $payload)
    {
    }
    /**
     * @param int transaction_id //transaction_id
     */
    private function getTransactionStatus(int $transaction_id)
    {
        $service_id = getenv('ORCHARD_SERVID');

        $payload = json_encode(array(
            "exttrid" => $transaction_id,
            "trans_type" => "TSC",
            "service_id" => $service_id
        ));

        $client_id = getenv('ORCHARD_CLIENT');
        $client_secret = getenv('ORCHARD_SECRET');
        $signature = hash_hmac("sha256", $payload, $client_secret);

        $secretKey = $client_id . ":" . $signature;
        $payUrl = "https://orchard-api.anmgw.com/checkTransaction";
        $request_verb = 'POST';
        try {
            $pay = new OrchardPaymentGateway($secretKey, $payUrl, $request_verb, $payload);
            return $pay->initiatePayment();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function processTransaction(int $transaction_id)
    {
        $response = json_decode($this->getTransactionStatus($transaction_id));
        if (!empty($response)) {
            if (isset($response->trans_status)) {
                if ($response->trans_status == '000/01') {
                    $voucher = new VoucherPurchase();
                    return $voucher->SaveFormPurchaseData($_SESSION, $transaction_id);
                } else {
                    return array("success" => false, "message" => "Payment failed! Code: " . $response->trans_status);
                }
            }

            if (isset($response->resp_code)) {
                if ($response->resp_code == '084') {
                    return array(
                        "success" => false,
                        "message" => "Payment pending! Might be due to inssuficient fund in your account or your payment session expired. Code: " . $response->resp_code
                    );
                } else {
                    return array("success" => false, "message" => "Payment process failed! Code: " . $response->resp_code);
                }
            }
        }
        return array("success" => false, "message" => "Payment failed! Code: 0");
    }

    public function orchardPaymentController($amount)
    {
        if (!empty($amount)) {
            $callback_url = "https://forms.purchase.rmuictonline.com/confirm.php";
            $landing_page = "https://forms.purchase.rmuictonline.com/confirm.php";
            $trans_id = time();
            $service_id = getenv('ORCHARD_SERVID');

            $payload = json_encode(array(
                "amount" => $amount,
                "callback_url" => $callback_url,
                "exttrid" => $trans_id,
                "reference" => "RMU Forms Purchase",
                "service_id" => $service_id,
                "trans_type" => "CTM",
                "nickname" => "RMU",
                "landing_page" => $landing_page,
                "ts" => date("Y-m-d H:i:s"),
                "payment_mode" => "CRM",
                "currency_code" => "GHS",
                "currency_val" => $amount
            ));

            $client_id = getenv('ORCHARD_CLIENT');
            $client_secret = getenv('ORCHARD_SECRET');
            $signature = hash_hmac("sha256", $payload, $client_secret);

            $secretKey = $client_id . ":" . $signature;
            $request_verb = 'POST';
            $payUrl = "https://payments.anmgw.com/third_party_request";

            $pay = new OrchardPaymentGateway($secretKey, $payUrl, $request_verb, $payload);
            $response = json_decode($pay->initiatePayment());

            if ($response->resp_code == "000" && $response->resp_desc == "Passed") {
                //Save user data

                return array("success" => true, "status" => $response->resp_code, "message" => $response->redirect_url);
            }
            //echo $response->resp_desc;
            return array("success" => false, "status" => $response->resp_code, "message" => $response->resp_desc);
        }
    }
}
