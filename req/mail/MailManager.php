<?php

require "PHPMailer_5.2.4/PHPMailer_5.2.4/class.phpmailer.php";
if (!empty($error))
    echo $error;

function smtpmailer($to, $from, $from_name, $subject, $body) {
    global $error;
    $mail->CharSet = 'UTF-8';
    $mail = new PHPMailer();  // create a new object
    $mail->IsSMTP(); // enable SMTP
    $mail->IsHTML(true);
    $mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
    $mail->SMTPAuth = true;  // authentication enabled
    $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 465;
    $mail->Username = GUSER;
    $mail->Password = GPWD;
    $mail->SetFrom($from, $from_name);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AddAddress($to);
    while(!$mail->Send())
    {
	sleep(5);
    }
    return true;
   /* if (!$mail->Send()) {
        return false;
    } else {
        return true;
    }*/
}

function smtpnotificationmailer($to, $from, $from_name, $subject, $body) {
    global $error;
    $mail->CharSet = 'UTF-8';
    $mail = new PHPMailer();  // create a new object
    $mail->IsSMTP(); // enable SMTP
    $mail->IsHTML(true);
    $mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
    $mail->SMTPAuth = true;  // authentication enabled
    $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 465;
    $mail->Username = GUSER;
    $mail->Password = GPWD;
    //$mail->AddBCC($bcc);
    $mail->SetFrom($from, $from_name);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AddAddress($to);
    if (!$mail->Send()) {
        return false;
    } else {
        return true;
    }
}

function smtpbulkmailer($to, $bcc, $from, $from_name, $subject, $body) {
    global $error;
    $mail->CharSet = 'UTF-8';
    $mail = new PHPMailer();  // create a new object
    $mail->IsSMTP(); // enable SMTP
    $mail->IsHTML(true);
    $mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
    $mail->SMTPAuth = true;  // authentication enabled
    $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 465;
    $mail->Username = GUSER;
    $mail->Password = GPWD;
    foreach ($bcc as $bccer) {
        $mail->AddBCC($bccer);
    }
    $mail->SetFrom($from, $from_name);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AddAddress($to);
    if (!$mail->Send()) {
        return false;
    } else {
        return true;
    }
}

function sendSignupEmail($email_id,$first_name,$last_name,$token){
    $from = "nemosocialnetwork@gmail.com";
    $subject = "NEMO verification token";
    $link_prefix = "http://127.0.0.1/nemo/activateaccount.php";

    $body = "<h1>Hi ".$first_name." ".$last_name.",</h1><br>You signed up for an account on NEMO. To verify and activate your account, please <a href='$link_prefix?token=$token'>CLICK ME</a>.";
    smtpmailer($email_id, $from, $first_name." ".$last_name, $subject, $body);
}
?>
