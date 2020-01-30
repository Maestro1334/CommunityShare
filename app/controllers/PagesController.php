<?php
  class PagesController extends Controller {
    private $pageModel;
    private $dataModel;

    public function __construct(){
      $this->pageModel = $this->model('PageModel');
      $this->dataModel= $this->model('DataModel');
    }
    
    public function index(){
      if(isLoggedIn()){
        redirect('posts');
      }

      $data = [
        'title' => 'CommunityShare',
        'description' => 'Simple social network built for communities'
      ];
     
      $this->view('pages/index', $data);
    }

    public function about(){
      $data = [
        'title' => 'About Us',
        'description' => 'Share posts with your community'
      ];

      $this->view('pages/about', $data);
    }

    public function donate()
    {
      if (isLoggedIn()) {
        $this->view('pages/donate');
      } else {
        redirect('users/login');
      }
    }

    private function createOrder($amount)
    {
      // Array with payment data
      $payment = [
        'amount' => $amount,
        'status' => 'open'
      ];

      // Add row to donation table in the database
      // And return payment ID
      return $this->pageModel->addDonation($payment);
    }

    /**
     * Pay the ticket with a chosen payment method
     *
     * @param $data
     * @return void
     * @throws ApiException
     * @throws IncompatiblePlatform
     */
    private function pay($data)
    {
      $mollie = new \Mollie\Api\MollieApiClient();
      $mollie->setApiKey("test_mfANzfRkqJEzHDvUSnp4pn6QsJ9HG4");
      // Create mollie payment
      $payment = $mollie->payments->create([
        "amount" => [
          "currency" => "EUR",
          "value" => strval(number_format($data['amount'], 2, ".", "")),
        ],
        "method" => $data['method'],
        "description" => $data['description'],
        "redirectUrl" => URLROOT . '/pages/complete?id=' . $data['id'],
        "webhookUrl" => URLROOT . '/pages/webhook',
        "metadata" => [
          "order_id" => $data['id'],
        ],
        "issuer" => $data['issuer']
      ]);


      // Send the customer off to complete the payment.
      header("Location: " . $payment->getCheckoutUrl(), true, 303);
    }


    public function ideal()
    {
      if (isPost()) {
        // Sanitize POST array
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        if(empty($_POST['custom_amount_ideal']) && empty($_POST['button_amount_ideal'])) {
          flash('alert', 'Please select an amount to donate');
          redirect('pages/donate');
        }

        $amount = 1;
        if(isset($_POST['custom_amount_ideal'])){
          $amount = (int)$_POST['custom_amount_ideal'];
        } else {
          $amount = (int)$_POST['button_amount_ideal'];
        }

        try {
          // Insert the order in the database and create a invoice PDF
          $payment_id = $this->createOrder($amount);
        } catch (Exception $e) {
          echo "Creating order failed: ". $e->getMessage();
        }
        // Create mollie payment
        try {
          $data = [
            'method' => \Mollie\Api\Types\PaymentMethod::IDEAL,
            'description' => "CommunityShare iDeal donation #{$payment_id}",
            'id' => $payment_id,
            "issuer" => !empty($_POST["issuer"]) ? $_POST["issuer"] : null,
            'amount' => $amount
          ];
          $this->pay($data);
        } catch (IncompatiblePlatform $e) {
          echo "Incompatible platform: " . $e->getMessage();
        } catch (ApiException $e) {
          echo "API call failed: " . \htmlspecialchars($e->getMessage());
        }
      }
    }

    public function paypal()
    {
      if (isPost()) {
        // Sanitize POST array
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        if(empty($_POST['custom_amount_paypal']) && empty($_POST['button_amount_paypal'])) {
          flash('alert', 'Please select an amount to donate');
          redirect('pages/donate');
        }

        $amount = 1;
        if(isset($_POST['custom_amount_paypal'])){
          $amount = (int)$_POST['custom_amount_paypal'];
        } else {
          $amount = (int)$_POST['button_amount_paypal'];
        }

        try {
          // Insert the order in the database and create a invoice PDF
          $payment_id = $this->createOrder($amount);
        } catch (Exception $e) {
          echo "Creating order failed: ". $e->getMessage();
        }

        // Create mollie payment
        try {
          $data = [
            'method' => \Mollie\Api\Types\PaymentMethod::PAYPAL,
            'description' => "CommunityShare paypal donation #" . $payment_id,
            'id' => $payment_id,
            "issuer" => !empty($_POST["issuer"]) ? $_POST["issuer"] : null,
            'amount' => $amount
          ];
          $this->pay($data);
        } catch (IncompatiblePlatform $e) {
          echo "Incompatible platform: " . $e->getMessage();
        } catch (ApiException $e) {
          echo "API call failed: " . \htmlspecialchars($e->getMessage());
        }
      }
    }

    public function webhook()
    {
      try {
        $mollie = new \Mollie\Api\MollieApiClient();
        $mollie->setApiKey("test_mfANzfRkqJEzHDvUSnp4pn6QsJ9HG4");
        $payment = $mollie->payments->get($_POST["id"]);

        $payment_id = $payment->metadata->order_id;
        // Update order status
        $this->pageModel->updateStatus($payment_id, $payment->status);
        if ($payment->isPaid() && !$payment->hasRefunds() && !$payment->hasChargebacks()) {
          // Donation complete
          die('payment complete');
        } elseif ($payment->isOpen()) {
          $this->failedPayment($payment_id);
        } elseif ($payment->isPending()) {
          // The payment is pending.
        } elseif ($payment->isFailed()) {
          $this->failedPayment($payment_id);
        } elseif ($payment->isExpired()) {
          $this->failedPayment($payment_id);
        } elseif ($payment->isCanceled()) {
          $this->failedPayment($payment_id);
        } elseif ($payment->hasRefunds()) {
          $this->failedPayment($payment_id);
        } elseif ($payment->hasChargebacks()) {
          $this->failedPayment($payment_id);
        }
      } catch (ApiException $e) {
        echo "API call failed: " . \htmlspecialchars($e->getMessage());
      } catch (Exception $e) {
        echo 'Random Bytes failed: ' . $e->getMessage();
      }
    }

    private function failedPayment($payment_id)
    {
      // Send payment failed email
      $subject = 'Something went wrong with your donation to CommunityShare';
      sendEmail($_SESSION['user_email'],
        GUSER,
        SITENAME,
        $subject,
        'Something went wrong processing your donation to CommunityShare '. $_SESSION['user_name']. '! <br> Please try again or contact me at mattismeeuwesse@gmail.com if you need any help! <br> If you cancelled your transaction on purpose, you can ignore this email.'
      );
    }

    // Load order complete page
    public function complete()
    {
      // Require 'order complete' view php file
      $this->view('pages/complete');
    }


    public function data() {
      if(isLoggedIn()){
        $data = $this->dataModel->getAllInformation();

        $this->view('pages/data', $data);
      } else {
        redirect('users/login');
      }
    }

    public function import()
    {
      if (isPost()) {
        $this->dataModel->clearCSV();
        // Get file name
        $filename = $_FILES["filename"]["tmp_name"];

        // Check if the file is a CVS
        $cvs = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');
        if (in_array($_FILES['filename']['type'], $cvs)){
          // Check if file size is bigger than 0
          if ($_FILES["filename"]["size"] > 0)
          {
            // Open file in read mode
            $file = fopen($filename, "r");

            // While there is data in the file upload the data to the database
            while (($readData = fgetcsv($file, 10000, ",")) !== FALSE)
            {
              $this->dataModel->importCVS($readData[0], $readData[1]);
            }
            fclose($file);

            flash('alert', 'Importing successful');
            redirect('pages/data');
          } else {
            flash('alert', 'File does not contain any data');
            redirect('pages/data');
          }
        } else {
          flash('alert', 'Sorry, only cvs file format is allowed');
          redirect('pages/data');
        }
      }
      else {
        redirect('pages/data');
      }
    }

    public function export()
    {
      if (isPost()) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=data.csv');

        $output = fopen('php://output', 'w+');
        $information = $this->dataModel->getAllInformation();

        foreach($information as $info){
          $row['name'] = $info->name;
          $row['data'] = $info->data;

          fputcsv($output, $row);
        }

        fclose($output);
      }
      else {
        redirect('pages/data');
      }
    }
  }