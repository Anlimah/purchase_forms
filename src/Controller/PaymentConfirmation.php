<?php

namespace Src\Controller;

use Src\System\DatabaseMethods;

$trans_id = $_GET['transaction_id'];
$service_id = getenv('ORCHARD_SERVID');

$payload = json_encode(array(
    "exttrid" => $trans_id,
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
    $response = json_decode($pay->initiatePayment());
    if (isset($response->trans_status)) {
        if ($response->trans_status == '000/01') {
            $voucher = new VoucherPurchase();
            if ($voucher->createApplicant($_SESSION)) {
                echo 'Payment was successful!<br><hr><br>';
                echo '<span style="color:red;"><b>Please do not close this page yet.</b></span><br><br>';
                echo 'An email with your <b>Application Number</b> and <b>PIN Code</b> and has been sent to you!<br>';
                echo 'Please confirm and proceed to the <a href="../apply"><b>online applicatioin portal</b></a> to complete your application process.<br>';
            } else {
                echo 'Server error!<br>';
            }
        } else {
            echo 'Payment processing failed!<br>';
        }
    }

    if (isset($response->resp_code)) {
        if ($response->resp_code == '084') {
            echo 'Transaction is still pending. Complete payment process!<br>';
            echo 'Process will be cancelled within 30 seconds.<br><br>';
            $response = json_decode($pay->initiatePayment());
            if (isset($response->trans_status)) {
                if ($response->trans_status == '000/01') {
                    $voucher = new VoucherPurchase();
                    if ($voucher->createApplicant($_SESSION)) {
                        echo 'Payment was successful!<br><hr><br>';
                        echo '<span style="color:red;"><b>Please do not close this page yet.</b></span><br><br>';
                        echo 'An email with your <b>Application Number</b> and <b>PIN Code</b> and has been sent to you!<br>';
                        echo 'Please confirm and proceed to the <a href="../apply"><b>online applicatioin portal</b></a> to complete your application process.<br>';
                    } else {
                        echo 'Server error!<br>';
                    }
                } else {
                    echo 'Payment processing failed! 1<br>';
                }
            } else {
                echo 'Payment processing failed! 2<br>';
            }
        } else {
            echo "Failed";
        }
    }
} catch (\Exception $e) {
    throw $e;
}
