<?php
function sendEmail($to, $from, $from_name, $subject, $body) {
  global $error;
  $mail = new PHPMailer();

  //Server settings
  $mail->SMTPDebug = 0;                      // Enable verbose debug output
  $mail->isSMTP();                                            // Send using SMTP
  $mail->Host       = 'smtp.gmail.com';                        // Set the SMTP server to send through
  $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
  $mail->Username   = GUSER;                                   // SMTP username
  $mail->Password   = GPWD;                                    // SMTP password
  $mail->Port       = 587;                                    // TCP port to connect to

  //Recipients
  $mail->setFrom($from, $from_name);                          // Add sender with optional name parameter
  $mail->addAddress($to);                                     // Add a recipient with optional name parameter

//  // Attachments
//  $mail->addAttachment('/var/tmp/file.tar.gz');               // Add attachments
//  $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

  // Content
  $mail->isHTML(true);                                       // Set email format to HTML
  $mail->Subject = $subject;
  $mail->Body    = $body;

  if (!$mail->send()){
    $error = 'Mail error: '.$mail->ErrorInfo;
    return false;
  }
  else {
    $error = 'Message sent!';
    return true;
  }
}
