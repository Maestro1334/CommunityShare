<?php
  /*
   * App Core Class
   * Creates URL & loads core controller
   * URL FORMAT - /controller/method/params
   */
  class Core {
    // Set Defaults
    protected $currentController = 'PagesController'; // Default controller
    protected $currentMethod = 'index'; // Default method
    protected $params = []; // Set initial empty params array

    public function __construct(){

      $url = $this->getUrl();

      // Look in controllers folder for controller
      if (isset($url)) {
        if (file_exists('../app/controllers/' . ucwords($url[0]) . 'Controller.php')) {
          // If exists, set as controller
          $this->currentController = ucwords($url[0]) . 'Controller';
          // Unset 0 index
          unset($url[0]);
        }
      }

      // Require the current controller
      require_once('../app/controllers/' . $this->currentController . '.php');

      // Instantiate the current controller
      $this->currentController = new $this->currentController;

      // Check if second part of url is set (method)
      if (isset($url[1])) {
        // Check if method/function exists in current controller class
        if (method_exists($this->currentController, $url[1])) {
          // Set current method if it exists
          $this->currentMethod = $url[1];
          // Unset 1 index
          unset($url[1]);
        }
      }

      // Get params
      $this->params = $url ? array_values($url) : [];

      // Call a callback with array of params
      call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
    }

    public function getUrl(){
      if(isset($_GET['url'])){
        $url = rtrim($_GET['url'], '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $url = explode('/', $url);
        return $url;
      }
    }
  } 
  
  