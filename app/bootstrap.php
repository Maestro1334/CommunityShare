<?php
  // Load Config
  require_once 'config/config.php';

  // Load Helpers
  require_once 'helpers/SessionHelper.php';
  require_once 'helpers/UrlHelper.php';
  require_once 'helpers/FileHelper.php';
  require_once 'helpers/PDFHelper.php';
  require_once 'helpers/MailHelper.php';

  // Load Libraries
  require_once ("libraries/mollie/vendor/autoload.php");
  require_once ("libraries/mollie/examples/functions.php");
  require_once 'libraries/FPDF/fpdf.php';
  require_once 'libraries/qrcode/qrcode.class.php';
  require_once 'libraries/PHPMailer/class.smtp.php';
  require_once 'libraries/PHPMailer/class.phpmaileroauthgoogle.php';

  // Autoload Core Classes
  spl_autoload_register(function ($className) {
    require_once 'libraries/'. $className . '.php';
  });
  
