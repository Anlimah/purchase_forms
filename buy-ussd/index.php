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
        $response  = "CON Welcome to RMU Online Forms Purchse \n";
        $response .= "Choose an option:\n";
        // Fetch and display all available forms
        $forms = $expose->getAvailableForms();
        foreach ($forms as $form) {
            $response .= "{$form['id']}." . " {$form['name']}\n";
        }
    } elseif ($text == "1" || $text == "2" || $text == "3" || $text == "4" || $text == "5") {
        $response = "CON Enter your first name\n";
    } else if ($text != "") {
        $response = "CON Provide the Mobile Money Number to buy the forms\n";
    } else if (isset($level[3]) && $level[3] != "" && !isset($level[4])) {
        //Save data to database
        $response  = "CON Welcome to RMU Online Forms Purchse \n";
        $response .= "Choose an option:\n";
        $response .= "1. {$sessionId}\n";
        $response .= "2. {$serviceCode}\n";
        $response .= "3. {$phoneNumber}\n";
        $response .= "4. {$text}\n";
        $response .= "5. {$networkCode}\n";
        $response = "END Thank you " . $level[0] . " for registering.\n";
    }
}

header('Content-type: text/plain');
echo $response;
/*}

header('Content-type: application/json');
echo $response;*/
