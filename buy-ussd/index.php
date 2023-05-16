<?php

require_once('../bootstrap.php');

use Src\Controller\ExposeDataController;

$expose = new ExposeDataController();

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

if (isset($text)) {

    $formChosen = array();

    if ($text == "") {
        $response  = "CON Welcome to RMU Online Forms Purchase paltform. Select a form to buy.\n";
        // Fetch and display all available forms
        $underAndPostFprms = $expose->getUndergradAndPostgradForms();
        $i = 1;
        foreach ($underAndPostFprms as $form) {
            $response .= "{$i}. " . ucwords(strtolower($form['name'])) . "\n";
            array_push($formChosen, array($i => $form['name']));
            $i += 1;
        }
        $response .= "99." . " More";
    } elseif ((int) $level[0] >= 1 && (int) $level[0] <= count($underAndPostFprms) && !$level[1]) {
        $formInfo = $expose->getFormPriceB($level[0]);
        $response = "CON " . $formInfo[0]["name"] . " forms cost GHc " . $formInfo[0]["amount"] . ". Select an option.";
        $response = "1. Buy\n";
        $response = "2. Cancel";
    } elseif ($level[0] >= 1 && $level[0] <= count($underAndPostFprms) && !$level[1]) {
        $formInfo = $expose->getFormPriceB($level[0]);
        $response = "CON Enter your first name.";
    } else if ($level[1] != "" && !$level[2]) {
        $response = "CON Enter your last name.";
    } else if ($level[2] != "" && !$level[3]) {
        $response = "CON Enter the Mobile Money number to buy the form.";
    } else if ($level[3] != "" && !$level[4]) {
        //Save data to database
        $response = "END Thank you " . $level[3] . " for registering.";
    }
}

header('Content-type: text/plain');
echo $response;
/*}

header('Content-type: application/json');
echo $response;*/
