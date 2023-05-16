<?php
//Password: RMULiveUSSDForms

//if ($_SERVER["REQUEST_METHOD"] != "POST") die("END Invalid request!");

//Reads the variables sent via POST
$sessionId      = $_POST["sessionId"];      // Session ID
$serviceCode    = $_POST["serviceCode"];    // Service code
$phoneNumber    = $_POST["phoneNumber"];    // Phone number
$text           = $_POST["text"];           // response text
$networkCode    = $_POST["networkCode"];    // network code

if ($text == "") {
    $response  = "CON Welcome to RMU Online Forms Purchse \n";
    $response .= "Choose an option:\n";
    $response .= "1. {$sessionId}\n";
    $response .= "2. {$serviceCode}\n";
    $response .= "3. {$phoneNumber}\n";
    $response .= "4. {$text}\n";
    $response .= "5. {$networkCode}\n";
    //
} elseif ($text == "1") {
    $response  = "CON Welcome to RMU Online Forms Purchse \n";
    $response .= "Choose an option:\n";
    $response .= "1. {$sessionId}\n";
    $response .= "2. {$serviceCode}\n";
    $response .= "3. {$phoneNumber}\n";
    $response .= "4. {$text}\n";
    $response .= "5. {$networkCode}\n";
    //
} elseif ($text == "2") {
    $response  = "CON Welcome to RMU Online Forms Purchse \n";
    $response .= "Choose an option:\n";
    $response .= "1. {$sessionId}\n";
    $response .= "2. {$serviceCode}\n";
    $response .= "3. {$phoneNumber}\n";
    $response .= "4. {$text}\n";
    $response .= "5. {$networkCode}\n";
    //
} elseif ($text == "3") {
    $response  = "CON Welcome to RMU Online Forms Purchse \n";
    $response .= "Choose an option:\n";
    $response .= "1. {$sessionId}\n";
    $response .= "2. {$serviceCode}\n";
    $response .= "3. {$phoneNumber}\n";
    $response .= "4. {$text}\n";
    $response .= "5. {$networkCode}\n";
    //
}
header('Content-type: text/plain');
echo $response;

/*if ($networkCode  == "03" && $networkCode  == "04") {
    $response  = "END Option not available for your network";
} else {

    $level = explode("*", $text);
    if (isset($text)) {

        if ($service_code == 0 && $text == "") {
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
echo $response;*/
