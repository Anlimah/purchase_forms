<?php

$headers = 'From: ' . 'addmissions@rmuictonline.com';
$subject = '<b>RMU Admissions Form Purchase: Verification Code</b>';
$message = 'Hi, <br> Your verification code is <b>12345</b>';

//send mail
$success = mail($recipient_email, $subject, $message, $headers);
if (!$success) {
    $errorMessage = error_get_last()['message'];
    echo $errorMessage;
} else {
    echo "Success";
}
