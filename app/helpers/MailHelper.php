<?php
function sendEmail($to, $from, $from_name, $subject, $body, $attachment = null) {
  global $error;
  $mail = new PHPMailer();

  //Server settings
  $mail->SMTPDebug = 0;                                       // Enable verbose debug output
  $mail->isSMTP();                                            // Send using SMTP
  $mail->Host       = 'smtp.gmail.com';                        // Set the SMTP server to send through
  $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
  $mail->Username   = GUSER;                                   // SMTP username
  $mail->Password   = GPWD;                                    // SMTP password
  $mail->Port       = 587;                                    // TCP port to connect to

  //Recipients
  try {
    $mail->setFrom($from, $from_name);                        // Add sender with optional name parameter
  } catch (phpmailerException $e) {
    var_dump($e->getMessage());
  }
  $mail->addAddress($to);                                     // Add a recipient with optional name parameter

  // Attachments
  if (isset($attachment)) {
    try {
      $mail->addAttachment($attachment);                      // Add attachment
    } catch (phpmailerException $e) {
      var_dump($e->getMessage());
    }
  }

//  $mail->addAttachment('/tmp/image.jpg', 'new.jpg');           // Optional name

  // Content
  $mail->isHTML(true);                                     // Set email format to HTML
  $mail->Subject = $subject;
  $mail->Body    = $body;

  try {
    if (!$mail->send()) {
      $error = 'Mail error: ' . $mail->ErrorInfo;
      return false;
    } else {
      $error = 'Message sent!';
      return true;
    }
  } catch (phpmailerException $e) {
    var_dump($e->getMessage());
  }
  return false;
}
