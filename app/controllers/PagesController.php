<?php
  class PagesController extends Controller {
    public function __construct(){
     
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
          "value" => $data['amount'],
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

        $amount = 0;
        if(isset($_POST['custom_amount'])){
          $amount = $_POST['custom_amount'];
        } else {
          $amount = $_POST['button_amount'];
        }
        print_r($_POST);
        // Create mollie payment
        try {
          $data = [
            'method' => \Mollie\Api\Types\PaymentMethod::IDEAL,
            'description' => "CommunityShare donation",
            'id' => 6,
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
      // Perform pre payment checks
      $this->beforePaymentChecks();

      try {
        // Insert the order in the database and create a invoice PDF
        $payment_id = $this->createOrder(3);
      } catch (Exception $e) {
        flash('alert', 'Sorry, your order could not be created at this time. Try again in a couple of minutes.', 'alert');
        redirect('checkout');
      }

      // Create mollie payment
      try {
        $data = [
          'method' => \Mollie\Api\Types\PaymentMethod::PAYPAL,
          'description' => "Haarlem Festival Paypal Order #" . $payment_id,
          'id' => $payment_id,
          "issuer" => null
        ];
        $this->pay($data);
      } catch (IncompatiblePlatform $e) {
        echo "Incompatible platform: " . $e->getMessage();
      } catch (ApiException $e) {
        echo "API call failed: " . \htmlspecialchars($e->getMessage());
      }
    }

    public function webhook()
    {
      try {
        $mollie = new \Mollie\Api\MollieApiClient();
        $mollie->setApiKey("test_S5bvrqm4abfQh6efPP94eWV5PxkfGv");
        $payment = $mollie->payments->get($_POST["id"]);

        $payment_id = $payment->metadata->order_id;
        // Update order status
        $this->checkoutModel->updateStatus($payment_id, $payment->status);
        if ($payment->isPaid() && !$payment->hasRefunds() && !$payment->hasChargebacks()) {
          // Fetch order from the database where the payment id matches
          $order = $this->checkoutModel->getOrderPayment($payment_id);

          // Path to the invoice PDF on the server
          $pdfWithInvoicePath = URLROOT . '/' . $order->invoice;

          // Send the confirmation mail to the user
          sendOrderConfirmationEmail($this->checkoutModel->getUserByPaymentId($payment_id), $pdfWithInvoicePath);
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
      // Payment failed, or refunded. Returning tickets to ticket pool
      $this->checkoutModel->addStock($this->checkoutModel->getTicketsByPaymentId($payment_id));
    }

    // Load order complete page
    public function complete()
    {
      // Require 'order complete' view php file
      $this->view('pages/complete');
    }
  }