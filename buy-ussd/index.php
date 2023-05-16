<?php

require_once('../bootstrap.php');

use Src\Controller\ExposeDataController;

$expose = new ExposeDataController();

//Password: RMULiveUSSDForms

//if ($_SERVER["REQUEST_METHOD"] != "POST") die("END Invalid request!");

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

    if ($text == "") {
        $response  = "CON Welcome to RMU Online Forms Purchase paltform. Select a form to buy.\n";
        // Fetch and display all available forms
        $forms = $expose->getAvailableForms();
        $i = 1;
        foreach ($forms as $form) {
            $response .= "{$i}." . ucwords(strtolower($form['name'])) . "\n";
            if ($i == 5) break;
            $i += 1;
        }
        $response .= "99." . " More";
    } elseif ($level[0] != "" && !$level[1]) {
        $response = "CON Enter your first name {$level[0]}.\n";
    } else if ($level[1] != "" && !$level[2]) {
        $response = "CON Please enter your ward name {$level[1]}.\n";
    } else if ($level[2] != "" && !$level[3]) {
        $response = "CON Provide the Mobile Money Number to buy the forms {$level[2]}.\n";
    } else if ($level[3] != "" && !$level[4]) {
        //Save data to database
        $response = "END Thank you " . $level[3] . " for registering.\n";
    }
}

header('Content-type: text/plain');
echo $response;
/*}

header('Content-type: application/json');
echo $response;*/
