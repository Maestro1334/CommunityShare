<?php require APPROOT . '/views/inc/header.php'; ?>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/donate.css">
  <script type = "text/javascript" src ="<?php echo URLROOT; ?>/js/donate.js"></script>
  <main id="donate-main">
    <div class="donate-container">
      <div class="row">
        <div class="col-auto">
          <?php flash('alert'); ?>
          <div class="row" id="donate-block">
            <ul class="nav flex-column nav-pills nav-justified col-3 bg-light" id="v-pills-tab"
                role="tablist" aria-orientation="vertical">
              <li class="nav-item">
                <a class="nav-link active" href="#ideal" data-toggle="tab" role="tab" aria-controls="choice-ideal" aria-selected="true">
                <img id="ideal-button" src="<?php echo URLROOT; ?>/public/img/payment/ideal.png" alt="ideal-image"></a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#paypal" data-toggle="tab" role="tab" aria-controls="choice-paypal" aria-selected="false">
                <img id="paypal-button" src="<?php echo URLROOT; ?>/public/img/payment/paypal.png" alt="paypal-image"></a>
              </li>
            </ul>

            <div class="tab-content col-8">
              <div class="tab-pane fade show active" id="ideal" role="tabpanel"
                   aria-labelledby="choice-ideal">
                <form action="<?php echo URLROOT; ?>/pages/ideal" method="post" class="donation-form">
                  <div id="ideal-block">
                    <div class="amount-select">
                      <h2 class="amount-select-title">Please choose donation amount</h2>
                      <div class="btn-group-lg amount-select-buttons <?php echo (!empty($data['button_amount_err'])) ? 'is-invalid' : ''; ?>" role="group" aria-label="amount-select">
                        <button type="button" class="btn btn-primary button-amount-select" value="5" onclick="saveButtonAmountIdeal(this)">&euro;5,-</button>
                        <button type="button" class="btn btn-primary button-amount-select" value="10" onclick="saveButtonAmountIdeal(this)">&euro;10,-</button>
                        <button type="button" class="btn btn-primary button-amount-select" value="15" onclick="saveButtonAmountIdeal(this)">&euro;15,-</button>
                        <span class="invalid-feedback"><?php echo $data['button_amount_err']; ?></span>
                        <input name="button_amount_ideal" type="hidden" id="button-amount-ideal" value="">
                      </div>
                    </div>
                    <div class="form-group custom-amount">
                      <label for="custom_amount_ideal">Enter a custom amount (optional)</label>
                      <input type="number" min="1" step="any" name="custom_amount_ideal" placeholder="Amount in euro's" class="form-control form-control-lg <?php echo (!empty($data['custom_amount_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['custom_amount']; ?>">
                      <span class="invalid-feedback"><?php echo $data['custom_amount_err']; ?></span>
                    </div>
                    <div class="bank-select-block">
                      <b>Choose your bank: </b><br>
                      <?php try {
                        $mollie = new \Mollie\Api\MollieApiClient();
                        $mollie->setApiKey("test_mfANzfRkqJEzHDvUSnp4pn6QsJ9HG4");
                        $method = $mollie->methods->get(\Mollie\Api\Types\PaymentMethod::IDEAL, ["include" => "issuers"]);
                        echo '<select name="issuer">';
                        foreach ($method->issuers() as $issuer) {
                          echo '<option value=' . htmlspecialchars($issuer->id) . '>' . htmlspecialchars($issuer->name) . '</option>';
                        }
                        echo '</select>';
                      } catch (\Mollie\Api\Exceptions\ApiException $e) {
                        echo "API call failed: " . htmlspecialchars($e->getMessage());
                      } ?>
                    </div>
                    <button type="submit" class="btn button-pay" id="button-pay-ideal">Donate</button>
                  </div>
                </form>
              </div>
              <div class="tab-pane fade" id="paypal" role="tabpanel" aria-labelledby="choice-paypal">
                <form action="<?php echo URLROOT; ?>/pages/paypal" method=post>
                  <div id="paypal-block">
                    <div class="amount-select">
                      <h2 class="amount-select-title">Please choose donation amount</h2>
                      <div class="btn-group-lg amount-select-buttons <?php echo (!empty($data['button_amount_err'])) ? 'is-invalid' : ''; ?>" role="group" aria-label="amount-select">
                        <button type="button" class="btn btn-primary button-amount-select" value="5" onclick="saveButtonAmountPayPal(this)">&euro;5,-</button>
                        <button type="button" class="btn btn-primary button-amount-select" value="10" onclick="saveButtonAmountPayPal(this)">&euro;10,-</button>
                        <button type="button" class="btn btn-primary button-amount-select" value="15" onclick="saveButtonAmountPayPal(this)">&euro;15,-</button>
                        <span class="invalid-feedback"><?php echo $data['button_amount_err']; ?></span>
                        <input name="button_amount_paypal" type="hidden" id="button-amount-paypal" value="">
                      </div>
                    </div>
                    <div class="form-group custom-amount">
                      <label for="custom_amount_paypal">Enter a custom amount (optional)</label>
                      <input type="number" min="1" step="any" name="custom_amount_paypal" placeholder="Amount in euro's" class="form-control form-control-lg <?php echo (!empty($data['custom_amount_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['custom_amount']; ?>">
                      <span class="invalid-feedback"><?php echo $data['custom_amount_err']; ?></span>
                    </div>
                    <button type="submit" class="btn button-pay" id="button-pay-paypal">Donate</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    </div>
  </main>
<?php require APPROOT . '/views/inc/footer.php'; ?>