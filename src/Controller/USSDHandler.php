<?php

namespace Src\Controller;

session_start();

use Src\Controller\ExposeDataController;
use Src\Controller\PaymentController;

$expose = new ExposeDataController();

class USSDHandler
{
    private $sessionId      = null;
    private $serviceCode    = null;
    private $phoneNumber    = null;
    private $msgType        = null;
    private $text           = null;
    private $networkCode    = null;
    private $payload = array();

    public function __construct($sessionId, $serviceCode, $phoneNumber, $msgType, $text, $networkCode)
    {
        $this->sessionId    = $sessionId;
        $this->serviceCode  = $serviceCode;
        $this->phoneNumber  = $phoneNumber;
        $this->msgType      = $msgType;
        $this->text         = $text;
        $this->networkCode  = $networkCode;
    }

    public function control()
    {

        if (!isset($this->sessionId) || !isset($this->serviceCode) || !isset($phoneNumber) || !isset($text) || !isset($networkCode))
            $this->text = "[01] Invalid request!";
        if (empty($this->sessionId) || empty($this->serviceCode) || empty($phoneNumber) || empty($text) || empty($networkCode))
            $this->text = "[02] Invalid request!";

        if ($this->networkCode  == "03" && $this->networkCode  == "04") $this->uSupportedNetworksResponse();

        if (!isset($_SESSION["ussd_start"]) || $this->msgType == "0") {
            $_SESSION["ussd_start"] = base64_encode($this->sessionId . $this->phoneNumber);
            $this->mainMenuResponse();
        }

        if ($this);

        $this->payload = array(
            "session_id" => $this->sessionId,
            "service_code" => $this->serviceCode,
            "msisdn" => $this->phoneNumber,
            "msg_type" => $this->msgType,
            "ussd_body" => $this->text,
            "nw_code" => $this->networkCode,
        );
        return $this->payload;
    }

    private function uSupportedNetworksResponse()
    {
        $this->text = "This service is available for only MTN and VODAFONE users. Please visit https://forms.rmuictonline.com to buy a forms on all networks";
        $this->msgType = '2';
    }

    private function mainMenuResponse()
    {
        $response  = "Welcome to RMU Online Forms Purchase paltform. Select a form to buy.\n";
        // Fetch and display all available forms
        $expose = new ExposeDataController();
        $allForms = $expose->getAvailableForms();

        foreach ($allForms as $form) {
            $response .= $form['id'] . ". " . ucwords(strtolower($form['name'])) . "\n";
        }

        $this->text = $response;
        $this->msgType = '1';
    }
}
