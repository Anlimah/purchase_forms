<?php

namespace Src\Controller;

use Src\Gateway\OrchardPaymentGateway;
use Src\Controller\VoucherPurchase;

class PaymentConfirmation
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
}
