<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="row">
  <div class="col-md-6 mx-auto">
    <div class="card card-body bg-light mt-5">
      <h2>Edit profile</h2>
      <p>Please fill in your new personal information</p>
      <form action="<?php echo URLROOT; ?>/users/editprofile" method="post">
        <div class="form-group">
          <label for="name">Name: <sup>*</sup></label>
          <input type="text" name="name" class="form-control form-control-lg <?php echo (!empty($data['name_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $_SESSION['user_name']; ?>">
          <span class="invalid-feedback"><?php echo $data['name_err']; ?></span>
        </div>
        <div class="form-group">
          <label for="email">Email: <sup>*</sup></label>
          <input type="email" name="email" class="form-control form-control-lg <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $_SESSION['user_email']; ?>">
          <span class="invalid-feedback"><?php echo $data['email_err']; ?></span>
        </div>
        <div class="form-group">
          <label for="password">New Password: <sup>*</sup></label>
          <input type="password" name="password" class="form-control form-control-lg <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['password']; ?>">
          <span class="invalid-feedback"><?php echo $data['password_err']; ?></span>
        </div>
        <div class="form-group">
          <label for="confirm_password">Confirm Password: <sup>*</sup></label>
          <input type="password" name="confirm_password" class="form-control form-control-lg <?php echo (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['confirm_password']; ?>">
          <span class="invalid-feedback"><?php echo $data['confirm_password_err']; ?></span>
        </div>
        <div class="row">
          <div class="col">
            <input type="submit" value="Edit Profile" class="btn btn-success btn-block">
          </div>
          <div class="col">
            <a href="<?php echo URLROOT; ?>/users/profile" class="btn btn-danger btn-block">Go back</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>