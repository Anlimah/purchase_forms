<?php
require_once('../bootstrap.php');

use Src\Controller\ExposeDataController;
use Src\Controller\PaymentController;

$expose = new ExposeDataController();
$pay = new PaymentController();

//Password: RMULiveUSSDForms

if ($_SERVER["REQUEST_METHOD"] != "POST") die("Invalid request!");

//Reads the variables sent via POST
$sessionId      = $_POST["session_id"];     // Session ID
$serviceCode    = $_POST["service_code"];   // Service code
$phoneNumber    = $_POST["msisdn"];         // Phone number
$text           = $_POST["ussd_body"];      // response text
$networkCode    = $_POST["nw_code"];        // network code 01 > MTN, 02 > VODA, 
$msgType        = $_POST["msg_type"];       // Message Type 0, 1, 2

if ($networkCode  == "03" && $networkCode  == "04") {
    $response  = "END Form purchase via USSD not available for your network.\nYou can visit https://forms.rmuictonline.com/buy-online/ to buy a form.";
} else {

    $level = explode("*", $text);

    if (isset($text)) {

        if ($text == "") {
            $response  = "CON Welcome to RMU Online Forms Purchase paltform. Select a form to buy.\n";
            // Fetch and display all available forms
            //$underAndPostFprms = $expose->getUndergradAndPostgradForms();
            $allForms = $expose->getAvailableForms();
            foreach ($allForms as $form) {
                $response .= $form['id'] . ". " . ucwords(strtolower($form['name'])) . "\n";
            }
        } elseif ($level[0] != "" && !$level[1]) {
            $formInfo = $expose->getFormPriceA($level[0]);
            $response = "CON " . $formInfo[0]["name"] . " forms cost GHc " . $formInfo[0]["amount"] . ".  Enter 1 to continue.\n";
            $response .= "1. Buy";
        } elseif ($level[1] == "1" && !$level[2]) {
            $response = "CON Enter your first name.";
        } else if ($level[2] != "" && !$level[3]) {
            $response = "CON Enter your last name.";
        } else if ($level[3] != "" && !$level[4]) {
            $response = "CON Enter the Mobile Money number to buy the form. eg 024XXXXXX";
        } else if ($level[4] != "" && !$level[5]) {

            $phlen = strlen($level[4]);
            $networks_codes = array(
                "24" => "MTN", "25" => "MTN", "53" => "MTN", "54" => "MTN", "55" => "MTN", "59" => "MTN", "20" => "VOD", "50" => "VOD",
            );
            $phone_number = "";

            if ($phlen == 9) {
                $net_code = substr($level[4], 0, 2); // 555351068 /55
                $phone_number_start = 0;
            } elseif ($phlen == 10) {
                $net_code = substr($level[4], 1, 2); // 0555351068 /55
                $phone_number_start = 1;
            } elseif ($phlen == 13) {
                $net_code = substr($level[4], 4, 2); // +233555351068 /55
                $phone_number_start = 4;
            } elseif ($phlen == 14) {
                $net_code = substr($level[4], 5, 2); //+2330555351068 /55
                $phone_number_start = 5;
            }

            $network = $networks_codes[$net_code];

            if (!$network) {
                $response = "END This service is only available for MTN and VODAFONE users. To buy RMU forms with all networks, visit https://forms.rmuictonline.com";
            } else {
                $vendor_id = "1665605087";
                $phone_number = "0" . substr($level[4], $phone_number_start, 9);
                $formInfo = $expose->getFormPriceA($level[0]);
                $admin_period = $expose->getCurrentAdmissionPeriodID();

                $data = array(
                    "first_name" => $level[2],
                    "last_name" => $level[3],
                    "email_address" => "",
                    "country_name" => "Ghana",
                    "country_code" => '+233',
                    "phone_number" => $phone_number,
                    "form_id" => $level[0],
                    "pay_method" => "USSD",
                    "network" => $network,
                    "amount" => $formInfo[0]["amount"],
                    "vendor_id" => $vendor_id,
                    "admin_period" => $admin_period
                );

                $result = $pay->orchardPaymentControllerB($data);
                if (!$result["success"]) {
                    $response = "END Process failed! {$result["status"]} {$result["message"]}";
                } else {
                    $response = "END Thank you! Payment prompt will be sent to {$level[4]} shortly.";
                }
            }
        } else {
            $response = "END Sorry, the input is not valid.";
        }
    }
}

header('Content-type: text/plain');
echo $response;
