<?php

class cronjob extends Controller
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
    // Get all images
    $images = $this->postModel->getAllImages();
    // Remove all images from file upload
    foreach ($images as $image) {
      unlink($_SERVER['DOCUMENT_ROOT'] . '/communityshare/public/' . FILE_UPLOAD . $image->filename);
    }
    // Remove all image filenames from database
    $this->modelPage->removeAllImages();

    // Get all filenames from files in PDF folder
    $files = glob($_SERVER['DOCUMENT_ROOT'] . '/communityshare/public/pdf/*');
    if (isset($files)) {
      // If there are files in the PDF folder
      foreach($files as $file){
        // Delete all files
        if(is_file($file))
          unlink($file);
      }
    }
  }
}