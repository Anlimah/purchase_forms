<?php
//Password: RMULiveUSSDForms

if ($_SERVER["REQUEST"] != "POST") die("END Invalid request!");

//Reads the variables sent via POST
$session_id     = $_POST["session_id"];     // Session ID
$service_code   = $_POST["service_code"];   // Service code
$phone_number   = $_POST["msisdn"];         // Phone number
$ussd_body      = $_POST["ussd_body"];      // response text
$nw_code      = $_POST["nw_code"];      // response text

if ($nw_code  == "03" && $nw_code  == "04") {
    $response  = "END Option not available for your network";
} else {

    $level = explode("*", $ussd_body);
    if (isset($ussd_body)) {

        if ($service_code == 0 && $ussd_body == "") {
            $response  = "CON Welcome to RMU Online Forms Purchse \n";
            $response .= "Choose an option:\n";
            //
        }

        if (isset($level[0]) && $level[0] != "" && $level[0] >= 1 && $level[0] <= 5 && !isset($level[1])) {
            $response = "CON Enter your first name\n";
        } else if (isset($level[1]) && $level[1] != "" && !isset($level[2])) {
            $response = "CON Please enter your ward name\n";
        } else if (isset($level[2]) && $level[2] != "" && !isset($level[3])) {
            $response = "CON Provide the Mobile Money Number to buy the forms\n";
        } else if (isset($level[3]) && $level[3] != "" && !isset($level[4])) {
            //Save data to database
            $data = array(
                'form_category' => $level[0],
                'first_name' => $level[1],
                'last_name' => $level[2],
                'phone_number' => $level[3],
                'national_id' => $level[2]
            );



            $response = "END Thank you " . $level[0] . " for registering.\nWe will keep you updated";
        }
    }
}

header('Content-type: application/json');
echo $response;
