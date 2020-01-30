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

function sendDonateThankYou(){
  // Create thank you PDF
  $attachmentLocation = 'pdf/pdf-' . $_SESSION['user_id'] . '-' . bin2hex(random_bytes(8)) . '.pdf';
  $qrMessage = generateRandomString(10) . '?id=' . $_SESSION['user_id'];
  createPDFThankYou($_SESSION['user_name'], $qrMessage, $attachmentLocation);

  // Send the confirmation mail with PDF to the user
  $subject = 'Your donation to CommunityShare';
  sendEmail($_SESSION['user_email'],
    GUSER,
    SITENAME,
    $subject,
    'Thank you for your donation to CommunityShare '. $_SESSION['user_name']. '! <br> Your donation will help our free website to continue to thrive :)',
    $attachmentLocation
  );
}
