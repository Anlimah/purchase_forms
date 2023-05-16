<?php
session_start();
/*if (isset($_SESSION['step5Done']) && $_SESSION['step5Done'] == true && isset($_SESSION["vendor_id"]) && !empty($_SESSION["vendor_id"]) && $_SESSION["vendor_type"] == "ONLINE") {
    if (!isset($_SESSION["_step6Token"])) {
        $rstrong = true;
        $_SESSION["_step6Token"] = hash('sha256', bin2hex(openssl_random_pseudo_bytes(64, $rstrong)));
    }
} else {
    header('Location: step5.php');
}*/

require_once('../bootstrap.php');

use Src\Controller\ExposeDataController;

$expose = new ExposeDataController();

//Password: RMULiveUSSDForms

//if ($_SERVER["REQUEST_METHOD"] != "POST") die("END Invalid request!");

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
            $response .= "{$form['id']}" . " {$form['name']}";
        }
    } elseif (isset($level[0]) && $level[0] != "" && $level[0] >= 1 && $level[0] <= count($forms) && !isset($level[1])) {
        $response = "CON Enter your first name\n";
    } else if (isset($level[1]) && $level[1] != "" && !isset($level[2])) {
        $response = "CON Please enter your ward name\n";
    } else if (isset($level[2]) && $level[2] != "" && !isset($level[3])) {
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
        /*$data = array(
            'form_category' => $level[0],
            'first_name' => $level[1],
            'last_name' => $level[2],
            'phone_number' => $level[3],
            'national_id' => $level[2]
        );*/
        $response = "END Thank you " . $level[0] . " for registering.\n";
    }
}

header('Content-type: text/plain');
echo $response;
/*}

header('Content-type: application/json');
echo $response;*/
