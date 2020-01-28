<?php require APPROOT . '/views/inc/header.php'; ?>
  <div class="card card-body bg-light mt-5">
    <div class="row">
      <div class="col-md-4 mx-auto">
        <div class="card-heading">
          <h2 class="card-title">Forgot password</h2>
        </div>
        <?php flash('forgot_password_message'); ?>
        <div class="row card-body">
          <form action="<?php echo URLROOT; ?>/users/forgotpass" method="POST" class="user-form">
            <div class="form-group">
              <label class="label-text" for="email">E-mail address</label>
              <input type="text" name="email" class="form-control text-box <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['email']; ?>">
              <span class="invalid-feedback"><?php echo $data['email_err']; ?></span>
            </div>
            <input type="submit" class="btn btn-block btn-submit" value="Send email">
          </form>
        </div>
      </div>
    </div>
  </div>
<?php require APPROOT . '/views/inc/footer.php'; ?>