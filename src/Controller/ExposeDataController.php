<?php

namespace Src\Controller;

use Twilio\Rest\Client;
use Src\System\DatabaseMethods;
use Src\Controller\PaymentConfirmation;

class ExposeDataController extends DatabaseMethods
{
    public function verifyEmailAddress($email, $code)
    {
        $sql = "SELECT `id` FROM `verify_email_address` WHERE `email_address`=:e AND `code`=:c";
        return $this->getID($sql, array(':e' => $email, ':c' => $code));
    }

    public function verifyPhoneNumber($number, $code)
    {
        $sql = "SELECT `id` FROM `verify_phone_number` WHERE `phone_number`=:p AND `code`=:c";
        return $this->getID($sql, array(':p' => $number, ':c' => $code));
    }

    public function validateEmail($input)
    {
        if (empty($input)) {
            die("Invalid email address!");
        }
        $user_email = htmlentities(htmlspecialchars($input));
        $sanitized_email = filter_var($user_email, FILTER_SANITIZE_EMAIL);
        if (!filter_var($sanitized_email, FILTER_VALIDATE_EMAIL)) {
            die("Invalid email address!" . $sanitized_email);
        }
        return $user_email;
    }

    public function validateInput($input)
    {
        if (empty($input)) {
            die("Invalid input!");
        }
        $user_input = htmlentities(htmlspecialchars($input));
        $validated_input = (bool) preg_match('/^[A-Za-z0-9]/', $user_input);
        if ($validated_input) {
            return $user_input;
        }
        die("Invalid input!");
    }

    public function validatePhone($input)
    {
        if (empty($input)) {
            die("Provide a phone number!");
        }
        $user_input = htmlentities(htmlspecialchars($input));
        $validated_input = (bool) preg_match('/^[0-9]/', $user_input);
        if ($validated_input) {
            return $user_input;
        }
        die("Invalid phone number!");
    }

    public function validateDate($date)
    {
        if (strtotime($date) === false) {
            die("Invalid date!");
        }
        list($year, $month, $day) = explode('-', $date);
        if (checkdate($month, $day, $year)) {
            return $date;
        }
    }

    public function validateImage($files)
    {
        if (!isset($files['file']['error']) || !empty($files["pics"]["name"])) {
            $allowedFileType = ['image/jpeg', 'image/png', 'image/jpg'];
            for ($i = 0; $i < count($files["pics"]["name"]); $i++) {
                $check = getimagesize($files["pics"]["tmp_name"][$i]);
                if ($check !== false && in_array($files["pics"]["type"][$i], $allowedFileType)) {
                    return $files;
                }
            }
        }
        die("Invalid file uploaded!");
    }

    public function validateInputTextOnly($input)
    {
        if (empty($input)) {
            return array("status" => "error", "message" => "required");
        }

        $user_input = htmlentities(htmlspecialchars($input));
        $validated_input = (bool) preg_match('/^[A-Za-z]/', $user_input);

        if ($validated_input) {
            return array("status" => "success", "message" => $user_input);
        }

        return array("status" => "error", "message" => "invalid");
    }

    public function validateInputTextNumber($input)
    {
        if (empty($input)) {
            return array("status" => "error", "message" => "required");
        }

        $user_input = htmlentities(htmlspecialchars($input));
        $validated_input = (bool) preg_match('/^[A-Za-z0-9]/', $user_input);

        if ($validated_input) {
            return array("status" => "success", "message" => $user_input);
        }

        return array("status" => "error", "message" => "invalid");
    }

    public function validateYearData($input)
    {
        if (empty($input) || strtoupper($input) == "YEAR") {
            return array("status" => "error", "message" => "required");
        }

        if ($input < 1990 || $input > 2022) {
            return array("status" => "error", "message" => "invalid");
        }

        $user_input = htmlentities(htmlspecialchars($input));
        $validated_input = (bool) preg_match('/^[0-9]/', $user_input);

        if ($validated_input) {
            return array("status" => "success", "message" => $user_input);
        }

        return array("status" => "error", "message" => "invalid");
    }

    public function validateGrade($input)
    {
        if (empty($input) || strtoupper($input) == "GRADE") {
            return array("status" => "error", "message" => "required");
        }

        if (strlen($input) < 1 || strlen($input) > 2) {
            return array("status" => "error", "message" => "invalid");
        }

        $user_input = htmlentities(htmlspecialchars($input));
        return array("status" => "success", "message" => $user_input);
    }

    public function getIPAddress()
    {
        //whether ip is from the share internet  
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        //whether ip is from the proxy  
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        //whether ip is from the remote address  
        else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public function getFormPrice(string $form_type)
    {
        return $this->getData("SELECT `amount` FROM `form_type` WHERE `name` LIKE '%$form_type%'");
    }

    public function getAdminYearCode()
    {
        $sql = "SELECT EXTRACT(YEAR FROM (SELECT `start_date` FROM admission_period)) AS 'year'";
        $year = (string) $this->getData($sql)[0]['year'];
        return (int) substr($year, 2, 2);
    }

    public function getFormTypes()
    {
        return $this->getData("SELECT * FROM `form_type`");
    }

    public function getPaymentMethods()
    {
        return $this->getData("SELECT * FROM `payment_method`");
    }

    public function getPrograms($type)
    {
        $sql = "SELECT * FROM `programs` WHERE `type` = :t";
        $param = array(":t" => $type);
        return $this->getData($sql, $param);
    }

    public function getHalls()
    {
        return $this->getData("SELECT * FROM `halls`");
    }

    public function sendEmail($recipient_email, $user_id)
    {
        //generate code and store hash version of code
        $v_code = $this->genCode($user_id);
        if ($v_code) {
            //prepare mail info
            $headers = 'From: ' . 'y.m.ratty7@gmail.com';
            $subject = 'RMU Admmisions Form Purchase: Code Verification';
            $message = 'Hi, <br> your verification code is <b>' . $v_code . '</b>';

            //send mail
            return mail($recipient_email, $subject, $message, $headers);
        }
        return 0;
    }

    public function sendSMS($recipient_number, $otp_code, $message, $ISD = '+233')
    {
        $sid = getenv('TWILIO_SID');
        $token = getenv('TWILIO_TKN');
        $client = new Client($sid, $token);

        //prepare SMS message
        $to = $ISD . $recipient_number;
        $account_phone = getenv('TWILIO_PNM');
        $from = array('from' => $account_phone, 'body' => $message . ' ' . $otp_code);

        //send SMS
        $response = $client->messages->create($to, $from);
        if ($response->sid) {
            $_SESSION['sms_code'] = $otp_code;
            $_SESSION['sms_sid'] = $response->sid;
            if (isset($_SESSION['sms_code']) && !empty($_SESSION['sms_code']) && isset($_SESSION['sms_sid']) && !empty($_SESSION['sms_sid'])) return 1;
        } else {
            return 0;
        }
    }

    public function sendOTP($phone_number)
    {
        $otp_code = $this->genCode(4);
        $message = 'Your OTP verification code is';
        return $this->sendSMS($phone_number, $otp_code, $message);
    }
    /**
     * @param int transaction_id //transaction_id
     */
    public function callOrchardGateway($amount, $network, $number)
    {
        $payConfirm = new PaymentConfirmation();
        return $payConfirm->orchardPaymentController($amount, $network, $number);
    }

    /**
     * @param int transaction_id //transaction_id
     */
    public function confirmPurchase(int $transaction_id)
    {
        $payConfirm = new PaymentConfirmation();
        return $payConfirm->processTransaction($transaction_id);
    }
}
