<?php

/*
 *  CORE CONTROLLER CLASS
 *  Loads Models & Views
 */

class Controller
{
    protected $scripts = [];
    protected $css = [];

    // Method to add custom JS files when loading the page
    protected function addJs($path, $internal = true)
    {
        array_push($this->scripts, ['path' => $path, 'internal' => $internal]);
    }

    // Method to add custom CSS files when loading the page
    protected function addCSS($path)
    {
        array_push($this->css, $path);
    }

    // Lets us load model from controllers
    public function model($model, $folder = null)
    {
        // Require model file
        if ($folder != null) {
            require_once '../app/models/' . $folder . '/' . $model . '.php';
        } else {
            require_once '../app/models/' .  $model . '.php';
        }

        // Instantiate model
        return new $model();
    }

    // Lets us load view from controllers
    public function view($url, $data = [])
    {
        // Check for view file
        $scripts = $this->scripts;
        $styling = $this->css;

        if (file_exists('../app/views/' . $url . '.php')) {
            // Require view file
            require_once '../app/views/' . $url . '.php';
        } else {
            // No view exists
            die('View does not exist');
        }
    }
}
