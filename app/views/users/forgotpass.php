<?php require APPROOT . '/views/inc/header.php'; ?>
  <div class="row">
    <div class="col-md-6 mx-auto">
      <div class="card card-body bg-light mt-5">
        <?php flash('forgot_password_message'); ?>
        <h2>Forgot password</h2>
        <p>Please fill in your email to reset your password</p>
        <form action="<?php echo URLROOT; ?>/users/forgotpass" method="POST" class="user-form">
          <div class="form-group">
            <label class="label-text" for="email">E-mail address</label>
            <input type="text" name="email" class="form-control text-box <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['email']; ?>">
            <span class="invalid-feedback"><?php echo $data['email_err']; ?></span>
          </div>
          <div class="form-group row">
            <div class="col-md-6">
              <input type="submit" class="btn btn-block btn-success" value="Send email">
            </div>
            <div class="col-md-6">
              <a href="<?php echo URLROOT; ?>/users/login" class="btn btn-light btn-block">Back to Login</a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php require APPROOT . '/views/inc/footer.php'; ?>