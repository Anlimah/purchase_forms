<?php

namespace Src\Controller;

use Src\Gateway\OrchardPaymentGateway;
use Src\Controller\VoucherPurchase;

class PaymentController
{
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
                    return $voucher->createApplicant($_SESSION);
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

    public function orchardPaymentController($amount, $number, $method, $network = "MTN")
    {
        if (!empty($amount) && !empty($number) && !empty($method) && !empty($network)) {
            $callback_url = "https://forms.purchase.rmuictonline.com/confirm.php";
            $trans_id = time();
            $service_id = getenv('ORCHARD_SERVID');

            $landing_page = "https://forms.purchase.rmuictonline.com/confirm.php";

            $payload = array();
            $payUrl = "";

            if ($method == "Mobile Money") {
                $payload = json_encode(array(
                    "customer_number" => $number,
                    "amount" => $amount,
                    "exttrid" => $trans_id,
                    "reference" => "Test payment",
                    "trans_type" => "CTM",
                    "nw" => $network,
                    "callback_url" => "$callback_url",
                    "service_id" => $service_id,
                    "ts" => date("Y-m-d H:i:s"),
                    "nickname" => "RMU Admissions"
                ));
                $payUrl = "https://orchard-api.anmgw.com/sendRequest";
            } else if ($method == "Credit Card") {
                $payload = json_encode(array(
                    "amount" => $amount,
                    "callback_url" => $callback_url,
                    "exttrid" => $trans_id,
                    "reference" => "Pay for RMU admissions form",
                    "service_id" => $service_id,
                    "trans_type" => "CTM",
                    "nickname" => "RMU",
                    "landing_page" => $landing_page,
                    "ts" => date("Y-m-d H:i:s"),
                    "payment_mode" => "MOM",
                    "currency_code" => "GHS",
                    "currency_val" => "233"
                ));
                $payUrl = "https://payments.anmgw.com/third_party_request";
            }

            $client_id = getenv('ORCHARD_CLIENT');
            $client_secret = getenv('ORCHARD_SECRET');
            $signature = hash_hmac("sha256", $payload, $client_secret);

            $secretKey = $client_id . ":" . $signature;
            $request_verb = 'POST';

            $pay = new OrchardPaymentGateway($secretKey, $payUrl, $request_verb, $payload);
            $response = json_decode($pay->initiatePayment());

            if ($method == "Mobile Money") {
                if ($response->resp_code == "015") {
                    return array("success" => true, "message" => "?status=" . $response->resp_code . "&transaction_id=" . $trans_id);
                }
            } else if ($method == "Credit Card") {
                if ($response->resp_code == "000" && $response->resp_desc == "Passed") {
                    return array("success" => true, "message" => $response->redirect_url);
                }
            }

            //echo $response->resp_desc;
            return array("success" => false, "message" => $response->resp_desc);
        }
    }
}
