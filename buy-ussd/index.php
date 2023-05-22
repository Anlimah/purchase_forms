<?php
require_once('../bootstrap.php');

use Src\Controller\USSDHandler;

//Password: RMULiveUSSDForms

if ($_SERVER["REQUEST_METHOD"] != "POST") die("Invalid request!");

//Reads the variables sent via POST
$sessionId      = $_POST["session_id"];     // Session ID
$serviceCode    = $_POST["service_code"];   // Service code
$phoneNumber    = $_POST["msisdn"];         // Phone number
$ussdBody       = $_POST["ussd_body"];      // response ussdBody
$networkCode    = $_POST["nw_code"];        // network code 01 > MTN, 02 > VODA, 
$msgType        = $_POST["msg_type"];       // Message Type 0, 1, 2

$ussd = new USSDHandler($sessionId, $serviceCode, $phoneNumber, $ussdBody, $networkCode, $msgType);
$response = $ussd->control();

header("Content-Type: application/json");
die(json_encode($response));
