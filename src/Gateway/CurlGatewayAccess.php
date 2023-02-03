<?php

namespace Src\Gateway;

class CurlGatewayAccess
{
    private $url = null;
    private $payload = null;
    private $httpHeader = null;
    private $curl_array = array();


    public function __construct($url, $httpHeader, $payload)
    {
        $this->url = $url;
        $this->payload = $payload;
        $this->httpHeader = $httpHeader;
    }

    private function setCURL_Array()
    {
        $this->curl_array = array(
            CURLOPT_URL => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $this->payload,
            CURLOPT_HTTPHEADER => $this->httpHeader,
        );
    }

    public function initiateProcess()
    {
        //$this->setCURL_Array();
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://sms.hubtel.com/v1/messages/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
          "From":"RMU",
          "To": "233555351068",
          "Content": "The Job is the JOB"
          }',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ZWlxamp4bnc6aXdjcHpudGY=',
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        echo $response;
    }
}
