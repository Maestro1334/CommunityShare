<?php require APPROOT . '/views/inc/header.php'; ?>
<?php flash('post_message'); ?>
<div class="row mb-3">
  <div class="col-md-6">
    <h1>Posts</h1>
  </div>
  <div class="col-md-6">
    <a href="<?php echo URLROOT; ?>/posts/add" class="btn btn-primary pull-right">
      <i class="fa fa-pencil"></i> Add Post
    </a>
  </div>
</div>
<div class="container">
  <br />
  <div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-8">
      <form class="card card-sm" action="<?php echo URLROOT; ?>/posts/search" method="get">
        <div class="card-body row no-gutters align-items-center">
          <div class="col-auto">
          </div>
          <div class="col">
            <input class="form-control form-control-lg form-control-borderless" type="search" name="s" placeholder="Search posts by username">
          </div>
          <div class="col-auto">
            <button class="btn btn-lg btn-success" type="submit">Search</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<?php foreach ($data['posts'] as $post) :
  $images = explode(',', $post->filenames); ?>
  <div class="card card-body mb-3">
    <h4 class="card-title"><?php echo $post->title; ?></h4>
    <div class="bg-light p-2 mb-3">
      Written by <?php echo $post->name; ?> on <?php echo $post->postCreated; ?>
    </div>
    <p class="card-text"><?php echo $post->body; ?></p>
    <?php if (isset($post->filenames)) {
    foreach ($images as $image) { ?>
      <img class="card-img-bottom post-image" src="<?php echo URLROOT; ?>/public/<?php echo FILE_UPLOAD . $image; ?>" alt="Card image cap">
    <?php }}; ?>
    <a href="<?php echo URLROOT; ?>/posts/show/<?php echo $post->postId; ?>" class="btn btn-dark">More</a>
  </div>
<?php endforeach; ?>
<?php require APPROOT . '/views/inc/footer.php'; ?>