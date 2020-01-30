<?php require APPROOT . '/views/inc/header.php'; ?>
<main>
  <?php flash('register_success'); ?>
  <div class="row mb-3">
    <div class="col-md-6">
      <h1>Profile</h1>
    </div>
    <div class="col-md-6">
      <a href="<?php echo URLROOT; ?>/users/editprofile" class="btn btn-primary pull-right">
        <i class="fa fa-pencil"></i> Edit Profile
      </a>
    </div>
  </div>
  <div class="card card-body mb-3">
    <h4 class="card-title">Name: <?php echo $data['user']->name; ?></h4>
    <h4 class="card-title">Email: <?php echo $data['user']->email; ?></h4>
    <h4 class="card-title">Created At: <?php echo $data['user']->created_at; ?></h4>
  </div>
</main>
<?php require APPROOT . '/views/inc/footer.php'; ?>