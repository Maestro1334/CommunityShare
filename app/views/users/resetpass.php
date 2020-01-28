<?php require APPROOT . '/views/inc/header.php'; ?>
  <div class="card card-body bg-light mt-5 reset-password-container">
    <div class="col-md-8 mx-auto reset-password-card">
      <div class="card-heading">
        <h2 class="card-title">Forgot password</h2>
      </div>
      <?php flash('token_message'); ?>
      <div class="row card-body">
        <form action="<?php echo URLROOT; ?>/users/resetpass" method="POST" class="user-form">
          <input type="hidden" name="token" value="<?php echo $data['token']; ?>">
          <div class="form-group">
            <label class="label-text" for="password">Enter new password</label>
            <input type="password" name="password" class="form-control text-box <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['password']; ?>" <?php echo (!empty($data['token_err'])) ? 'disabled' : '' ?>>
            <span class="invalid-feedback"><?php echo $data['password_err']; ?></span>
          </div>
          <div class="form-group">
            <label class="label-text" for="confirm_password">Confirm new password</label>
            <input type="password" name="confirm_password" class="form-control text-box <?php echo (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>" <?php echo (!empty($data['token_err'])) ? 'disabled' : '' ?>>
            <span class="invalid-feedback"><?php echo $data['confirm_password_err']; ?></span>
          </div>
          <input type="submit" class="btn btn-block btn-submit" value="Reset password" <?php echo (!empty($data['token_err'])) ? 'disabled' : '' ?>>
        </form>
      </div>
    </div>
  </div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
