<?php
session_start();

require_once('../bootstrap.php');
require_once('../src/Gateway/OrchardPaymentGateway.php');
require_once('../src/Controller/VoucherPurchase.php');

use Src\Controller\OrchardPaymentGateway;
use Src\Controller\VoucherPurchase;

if (isset($_GET['status']) && !empty($_GET['status']) && isset($_GET['transaction_id']) && !empty($_GET['transaction_id']) && $_GET['status'] == '015') {
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
} else {
    //echo 'Payment processing failed!';
    header('Location: purchase_step1.php?status=cancelled');
}

//OrchardPaymentGateway::destroyAllSessions(); //Kill all sessions


?>
<script src="js/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        //get variable(parameters) from url
        function getUrlVars() {
            var vars = {};
            var parts = window.location.href.replace(
                /[?&]+([^=&]+)=([^&]*)/gi,
                function(m, key, value) {
                    vars[key] = value;
                }
            );
            return vars;
        }

        //Use a default value when param is missing
        function getUrlParam(parameter, defaultvalue) {
            var urlparameter = defaultvalue;
            if (window.location.href.indexOf(parameter) > -1) {
                urlparameter = getUrlVars()[parameter];
            }
            return urlparameter;
        }

        alert(getUrlVars()["status"]);
    });
</script>