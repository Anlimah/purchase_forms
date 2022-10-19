<?php

$headers = 'From: ' . 'addmissions@rmuictonline.com';
$subject = '<b>RMU Admissions Form Purchase: Verification Code</b>';
$message = 'Hi, <br> Your verification code is <b>12345</b>';

//send mail
echo mail($recipient_email, $subject, $message, $headers);
