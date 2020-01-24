<?php

/**
 * Send a mail for resetting the password
 *
 * @param string $to Email addres of the user
 * @param string $token Reset token to be send
 *
 * @throws Exception when mail sending failed
 */
function sendPasswordResetMail($to, $token)
{
    $subject = 'Reset your password';
    $msg = 'Hi, here is a link to reset your password.<br/><a href="' . URLROOT . '/user/reset_password/' . $token . '">Click here</a> to continue.';
    $msg = wordwrap($msg, 70);

    // To send HTML mail, the Content-type header must be set
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
    $headers .= 'From: noreply@haarlemfestival.com';

    try {
        mail($to, $subject, $msg, $headers);
    } catch (\Throwable $th) {
        throw new Exception('Sending a mail failed:' . $th->getMessage() . '<br/>Here is the link: ' . URLROOT . '/user/reset_password/' . $token);
    }
}

/**
 * Send mail for order confirmation
 *
 * @param object $user The user to send the mail
 * @param string $fileUrl The file to be linked
 *
 * @return null
 */
function sendOrderConfirmationEmail($user, $fileUrl)
{
    $subject = 'Order Confirmation';
    $myHaarlemUrl = URLROOT . '/user/account';

    $msg = 'Dear Customer, <br><br>';
    $msg .= "You can almost go wild on the dance floor or enjoy a course dinner because<br>";
    $msg .= "your order is ready! <br><br>";
    $msg .= "You can find your unique tickets in <a href='$myHaarlemUrl'>My Haarlem Festival</a> <br>";
    $msg .= "or download it <a href='$fileUrl'>here</a>.<br><br>";
    $msg .= "Have fun with your purchase!<br><br>";
    $msg .= "Kind regards,<br>";
    $msg .= "Team Haarlem festival";


    // To send HTML mail, the Content-type header must be set
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
    $headers .= 'From: Haarlem Festival <noreply@haarlemfestival.com>';

    try {
        mail($user->email, $subject, $msg, $headers);
    } catch (\Throwable $th) {
        return null;
    }
}

/**
 * Send mail for order confirmation
 *
 * @param object $user The user to send the mail
 * @param string $invoiceUrl The invoice to be linked
 *
 * @return null
 */
function sendInvoiceConfirmationEmail($user, $invoiceUrl)
{
    $subject = 'Order Confirmation (Invoice)';
    $myHaarlemUrl = URLROOT . '/user/account';

    $msg = 'Dear Customer, <br><br>';
    $msg .= "You can almost go wild on the dance floor or enjoy a course dinner because<br>";
    $msg .= "your order is ready! <br><br>";
    $msg .= "You can find your unique tickets in <a href='$myHaarlemUrl'>My Haarlem Festival</a> <br>";
    $msg .= "Download your invoice <a href='$invoiceUrl'>here</a>. <strong>Don't forget to pay at the counter by showing your ticket!</strong><br><br>";
    $msg .= "Have fun with your purchase!<br><br>";
    $msg .= "Kind regards,<br>";
    $msg .= "Team Haarlem festival";


    // To send HTML mail, the Content-type header must be set
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
    $headers .= 'From: Haarlem Festival <noreply@haarlemfestival.com>';

    try {
        mail($user->email, $subject, $msg, $headers);
    } catch (\Throwable $th) {
        return null;
    }
}

/**
 * Send mail for account details to a new user
 *
 * @param object $data The user's email and password
 *
 * @return null
 */
function sendAccountDetails($data)
{
    $subject = 'Welcome to Haarlem Festival!';
    $myHaarlemUrl = URLROOT . '/user/account';

    $msg = 'Dear Customer, <br><br>';
    if($data['type'] == UserType::VISITOR){
        $msg .= "Someone made an account for you.<br>";
    } else {
        $msg .= "You have been invited to the Content Management System of Haarlem Festival.<br>";
    }
    $msg .= "Here are your account details: <br>";
    $msg .= "Username: " . $data['email'] . "<br>";
    $msg .= "Password: " . $data['password'] . "<br><br>";
    $msg .= "You can change your password at your <a href='$myHaarlemUrl'>account</a> <br><br>";
    $msg .= "Kind regards,<br>";
    $msg .= "Team Haarlem festival";


    // To send HTML mail, the Content-type header must be set
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
    $headers .= 'From: Haarlem Festival <noreply@haarlemfestival.com>';

    try {
        mail($data['email'], $subject, $msg, $headers);
    } catch (\Throwable $th) {
        return null;
    }
}