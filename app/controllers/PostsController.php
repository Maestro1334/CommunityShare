<?php
  class PostsController extends Controller {
    private $postModel;
    private $userModel;

    public function __construct(){
      if(!isLoggedIn()){
        redirect('users/login');
      }

      $this->postModel = $this->model('PostModel');
      $this->userModel = $this->model('UserModel');

    }

    public function index(){
      // Get posts
      $posts = $this->postModel->getPosts();

      $data = [
        'posts' => $posts
      ];

      // Loads CSS into the view
      $this->addCSS('posts.css');

      // Loads JavaScript onto page
      $this->addJs('main.js');

      // Load the view
      $this->view('posts/index', $data);
    }

    public function search(){
      if($_SERVER['REQUEST_METHOD'] == 'GET'){
      // Get posts
      $sanitizedQuery = filter_var($_GET['s'], FILTER_SANITIZE_STRING);
      
      $posts = $this->postModel->getPost($sanitizedQuery);

      $data = [
        'posts' => $posts
      ];

      $this->view('posts/search', $data);
      } else {
        $data = [
          'title' => '',
          'body' => ''
        ];

        $this->view('posts/search', $data);
      }
    } 
    
    public function add(){
      if(isPost()){
        // Sanitize POST array
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $data = [
          'title' => trim($_POST['title']),
          'body' => trim($_POST['body']),
          'user_id' => $_SESSION['user_id'],
          'images' => $_FILES['images'],
          'title_err' => '',
          'body_err' => '',
          'image_err' => ''
        ];

        // Validate data
        if(empty($data['title'])){
          $data['title_err'] = 'Please enter title';
        }
        if(empty($data['body'])){
          $data['body_err'] = 'Please enter body text';
        }

        // Check for and display errors uploading images
        if (isset($_FILES['images']['error'])){
          $message = '';
          foreach($_FILES['images']['error'] as $err) {
            switch ($err) {
              case 1:
                $message .= "The uploaded file exceeds the upload_max_filesize directive in php.ini. ";
                break;
              case 2:
                $message .= "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form. ";
                break;
              case 3:
                $message .= "The uploaded file was only partially uploaded. ";
                break;
              case 5:
                $message .= "Missing a temporary folder. ";
                break;
              case 6:
                $message .= "Failed to write file to disk. ";
                break;
              case 7:
                $message .= "File upload stopped by extension";
                break;
            }
          }
          $data['images_err'] = $message;
        }

        // Make sure no errors
        if(empty($data['title_err']) && empty($data['body_err']) && empty($data['images_err'])){
          // Validated
          // Save and rotate uploaded images
          $data['filenames'] = saveImages($data, $data['images_err']);
          // Add post to the database
          if($this->postModel->addPost($data)){
            flash('post_message', 'Post added');
            redirect('posts');
          } else {
            die('Something went wrong');
          }
        } else {
          // Load view with errors
          $this->view('posts/add', $data);
        }

      } else {
        $data = [
          'title' => '',
          'body' => ''
        ];
  
        $this->view('posts/add', $data);
      }
    }

    public function edit($id){
      if(isPost()){
        // Sanitize POST array
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $data = [
          'id' => $id,
          'title' => trim($_POST['title']),
          'body' => trim($_POST['body']),
          'user_id' => $_SESSION['user_id'],
          'title_err' => '',
          'body_err' => ''
        ];

        // Validate data
        if(empty($data['title'])){
          $data['title_err'] = 'Please enter title';
        }
        if(empty($data['body'])){
          $data['body_err'] = 'Please enter body text';
        }

        // Make sure no errors
        if(empty($data['title_err']) && empty($data['body_err'])){
          // Validated
          if($this->postModel->updatePost($data)){
            flash('post_message', 'Post updated');
            redirect('posts');
          } else {
            die('Something went wrong');
          }
        } else {
          // Load view with errors
          $this->view('posts/edit', $data);
        }

      } else {
        // Get existing post from model
        $post = $this->postModel->getPostById($id);

        // Check for owner
        if($post->user_id != $_SESSION['user_id']){
          redirect('posts');
        }

        $data = [
          'id' => $id,
          'title' => $post->title,
          'body' => $post->body
        ];
  
        $this->view('posts/edit', $data);
      }
    }

    public function show($id){
      $post = $this->postModel->getPostById($id);
      $user = $this->userModel->getUserById($post->user_id);

      $data = [
        'post' => $post,
        'user' => $user
      ];

      $this->view('posts/show', $data);
    }

    public function delete($id){
      if(isPost()){
        
        // Get existing post from model
        $post = $this->postModel->getPostById($id);

        // Check for owner
        if($post->user_id != $_SESSION['user_id']){
          redirect('posts');
        }
        if($this->postModel->deletePost($id)){
          flash('post_message', 'Post removed');
          redirect('posts');
        } else {
          die('Something went wrong');
        }
      }
      else {
        redirect('posts');
      }
    }
  }