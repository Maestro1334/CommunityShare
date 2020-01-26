<?php

class Cronjob extends Controller
{
  private $postModel;

  public function __construct()
  {
    $this->modelPage = $this->model('Page');
    $this->index();
  }

  // Daily cron job to remove all generated pdf documents and images from server
  public function index()
  {
    $images = $this->postModel->getAllImages();

    foreach ($images as $image) {
      unlink($_SERVER['DOCUMENT_ROOT'] . '/CommunityShare/public/' . FILE_UPLOAD . $image);
    }

    // get all file names
    $files = glob($_SERVER['DOCUMENT_ROOT'] . '/CommunityShare/public/pdf/*');

    if (isset($files)) {
      foreach($files as $file){ // iterate files
        if(is_file($file))
          unlink($file); // delete file
      }
    }

    $this->postModel->removeAllImages();
  }
}