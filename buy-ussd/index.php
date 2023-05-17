<?php
session_start();
$vendor_id = "1665605087";

if (!isset($_SESSION["formChosen"])) $_SESSION["formChosen"] = array();

require_once('../bootstrap.php');

use Src\Controller\ExposeDataController;
use Src\Controller\PaymentController;

$expose = new ExposeDataController();
$pay = new PaymentController();

//Password: RMULiveUSSDForms

if ($_SERVER["REQUEST_METHOD"] != "POST") die("END Invalid request!");

//Reads the variables sent via POST
$sessionId      = $_POST["sessionId"];      // Session ID
$serviceCode    = $_POST["serviceCode"];    // Service code
$phoneNumber    = $_POST["phoneNumber"];    // Phone number
$text           = $_POST["text"];           // response text
$networkCode    = $_POST["networkCode"];    // network code

/*if ($networkCode  == "03" && $networkCode  == "04") {
    $response  = "END Form purchase via USSD not available for your network.\nYou can visit https://forms.rmuictonline.com/buy-online/ to buy a form.";
} else {*/

$level = explode("*", $text);
$final = false;
$phone_number = 0;

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
        $response = "CON Enter the Mobile Money number to buy the form.";
    } else if ($level[4] != "" && !$level[5]) {
        $level[5] = true;
        $final = $level[5];
        $phone_number = $level[4];
        $response = "END Thank you! " . $level[5] . "Payment prompt will be sent to {$level[4]} shortly.";
    }
}

header('Content-type: text/plain');
echo $response;

/*if ($final) {
    $formInfo = $expose->getFormPriceA($level[0]);
    $formInfo = $expose->getCurrentAdmissionPeriodID();
    $data = array(
        "first_name" => $level[2],
        "last_name" => $level[3],
        "email_address" => "",
        "country_name" => "Ghana",
        "country_code" => '+233',
        "phone_number" => $phone_number,
        "form_id" => $level[0],
        "pay_method" => "USSD",
        "amount" => $formInfo[0]["amount"],
        "vendor_id" => $vendor_id,
        "admin_period" => $admin_period
    );
    $result = $pay->orchardPaymentControllerB($data);
    $pay->processTransaction($$result["trans_id"]);
}*/
