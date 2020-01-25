<?php

class Cronjob extends Controller
{
    private $modelPage;

    public function __construct()
    {
        $this->modelPage = $this->model('Page');
        $this->index();
    }

    // Daily cronjob to remove all user uploaded images from server
    public function index()
    {
        $images = $this->modelPage->getAllImagePaths();

        foreach ($images as $image) {
            unlink($_SERVER['DOCUMENT_ROOT'] . $image->ImagePath);
        }

        $this->modelPage->removeAllImages();
    }
}