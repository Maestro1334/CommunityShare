<?php
  class UsersController extends Controller {
    private $userModel;

    public function __construct(){
      $this->userModel = $this->model('UserModel');
    }

    public function register(){
      // Check for POST
      if(isPost()){
        // Process form
  
        // Sanitize POST data
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        // Init data
        $data =[
          'name' => trim($_POST['name']),
          'email' => trim($_POST['email']),
          'password' => trim($_POST['password']),
          'confirm_password' => trim($_POST['confirm_password']),
          'name_err' => '',
          'email_err' => '',
          'password_err' => '',
          'confirm_password_err' => ''
        ];

        // Validate Email
        if(empty($data['email'])){
          $data['email_err'] = 'Please enter email';
        } else {
          // Check email
          if($this->userModel->findUserByEmail($data['email'])){
            $data['email_err'] = 'Email is already taken';
          }
        }

        // Validate Name
        if(empty($data['name'])){
          $data['name_err'] = 'Please enter name';
        }

        // Validate Password
        if(empty($data['password'])){
          $data['password_err'] = 'Please enter password';
        } elseif(strlen($data['password']) < 6){
          $data['password_err'] = 'Password must be at least 6 characters';
        }

        // Validate Confirm Password
        if(empty($data['confirm_password'])){
          $data['confirm_password_err'] = 'Please confirm password';
        } else {
          if($data['password'] != $data['confirm_password']){
            $data['confirm_password_err'] = 'Passwords do not match';
          }
        }

        // Make sure errors are empty
        if(empty($data['email_err']) && empty($data['name_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])){
          // Validated
          
          // Hash Password
          $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

          // Register UserModel
          if($this->userModel->register($data)){
            flash('register_success', 'You are registered and can log in');
            redirect('users/login');
          } else {
            die('Something went wrong');
          }

        } else {
          // Load view with errors
          $this->view('users/register', $data);
        }

      } else {
        // Init data
        $data =[
          'name' => '',
          'email' => '',
          'password' => '',
          'confirm_password' => '',
          'name_err' => '',
          'email_err' => '',
          'password_err' => '',
          'confirm_password_err' => ''
        ];

        // Load view
        $this->view('users/register', $data);
      }
    }

    public function login(){
      // Check for POST
      if(isPost()){
        // Process form
        // Sanitize POST data
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        
        // Init data
        $data =[
          'email' => trim($_POST['email']),
          'password' => trim($_POST['password']),
          'email_err' => '',
          'password_err' => '',      
        ];

        // Validate Email
        if(empty($data['email'])){
          $data['email_err'] = 'Please enter email';
        }

        // Validate Password
        if(empty($data['password'])){
          $data['password_err'] = 'Please enter password';
        }

        // Check for user/email
        if($this->userModel->findUserByEmail($data['email'])){
          // UserModel found
        } else {
          // UserModel not found
          $data['email_err'] = 'No user found';
        }

        // Make sure errors are empty
        if(empty($data['email_err']) && empty($data['password_err'])){
          // Validated
          // Check and set logged in user
          $loggedInUser = $this->userModel->login($data['email'], $data['password']);

          if($loggedInUser){
            // Create Session
            $this->createUserSession($loggedInUser);
          } else {
            $data['password_err'] = 'Password incorrect';

            $this->view('users/login', $data);
          }
        } else {
          // Load view with errors
          $this->view('users/login', $data);
        }


      } else {
        // Init data
        $data =[    
          'email' => '',
          'password' => '',
          'email_err' => '',
          'password_err' => '',        
        ];

        // Load view
        $this->view('users/login', $data);
      }
    }

    public function createUserSession($user){
      $_SESSION['user_id'] = $user->id;
      $_SESSION['user_email'] = $user->email;
      $_SESSION['user_name'] = $user->name;
      redirect('posts');
    }

    // public function refreshUserSession($id){
    //   $user = getUserById($id);
    //   $_SESSION['user_email'] = $user->email;
    //   $_SESSION['user_name'] = $user->name;
    // }

    public function logout(){
      unset($_SESSION['user_id']);
      unset($_SESSION['user_email']);
      unset($_SESSION['user_name']);
      session_destroy();
      redirect('users/login');
    }


    public function forgotPass()
    {
      $data = [
        'email' => '',
        'email_err' => ''
      ];

      if (isPost()) {
        // Post request
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        // Validate email
        if (empty($_POST['email'])) {
          $data['email_err'] = 'Please enter an email address';
        } else {
          $data['email'] = $_POST['email'];
          // Check if email is valid
          if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $data['email_err'] = 'This is not a valid email address';
          } else {
            // Check if there is a user with this email
            if ($this->userModel->findUserByEmail($data['email'])) {
              // There is a user
              // Get the user id
              $userId = $this->userModel->getUserIdByEmail($data['email']);
              if (isset($userId)) {
                // Generate token
                $resetToken = bin2hex(random_bytes(8));
                try {
                  // Store token to database
                  $this->userModel->createPasswordReset($userId, $resetToken);
                  try {
                    // Send password reset mail
                    sendEmail($data['email'],
                      'basbrak123@gmail.com',
                      'CommunityShare',
                      'Reset password',
                      'Hi, here is a link to reset your password.<br/><a href="' . URLROOT . '/user/resetpass/' . $resetToken . '">Click here</a> to continue.');
                    flash('forgot_password_message', 'A reset password email has been sent');
                  } catch (\Throwable $th) {
                    flash('forgot_password_message', $th, 'alert alert-danger');
                  }
                  $this->view('users/forgotpass', $data);
                } catch (\Throwable $th) {
                  //throw $th;
                  flash('forgot_password_message', $th->getMessage(), 'alert alert-danger');
                }
              } else {
                $data['email_err'] = 'Something went wrong';
              }
            } else {
              // There is no user
              $data['email_err'] = 'No user found with this email address';
            }
          }
        }
      }
      $this->view('users/forgotpass', $data);
    }

//    public function forgotPass(){
//      // Check for POST
//      if(isPost()){
//        // Process form
//        // Sanitize POST data
//        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
//
//        // Init data
//        $data =[
//          'email' => trim($_POST['email']),
//          'password' => trim($_POST['password']),
//          'confirm_password' => trim($_POST['confirm_password']),
//          'email_err' => '',
//          'password_err' => '',
//          'confirm_password_err' => ''
//        ];
//
//        // Validate Email
//        if(empty($data['email'])){
//          $data['email_err'] = 'Please enter email';
//        } else {
//          // Check for existing email
//          if(!$this->userModel->findUserByEmail($data['email'])){
//            $data['email_err'] = 'No user found';
//          }
//        }
//
//        // Validate Password
//        if(empty($data['password'])){
//          $data['password_err'] = 'Please enter password';
//        } elseif(strlen($data['password']) < 6){
//          $data['password_err'] = 'Password must be at least 6 characters';
//        }
//
//        // Validate Confirm Password
//        if(empty($data['confirm_password'])){
//          $data['confirm_password_err'] = 'Please confirm password';
//        } else {
//          if($data['password'] != $data['confirm_password']){
//            $data['confirm_password_err'] = 'Passwords do not match';
//          }
//        }
//
//        // Make sure errors are empty
//        if(empty($data['email_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])){
//          // Validated
//
//          // Hash Password
//          $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
//
//          // Reset password
//          if($this->userModel->resetPass($data)){
//            flash('register_success', 'Your password has been changed and you can log in');
//            redirect('users/login');
//          } else {
//            die('Something went wrong');
//          }
//
//        } else {
//          // Load view with errors
//          $this->view('users/resetpass', $data);
//        }
//      } else {
//        // Init data
//        $data =[
//          'email' => '',
//          'password' => '',
//          'confirm_password' => '',
//          'email_err' => '',
//          'password_err' => '',
//          'confirm_password_err' => ''
//        ];
//
//        // Load view
//        $this->view('users/resetpass', $data);
//      }
//    }

    public function profile(){
      $user = $this->userModel->getUserById($_SESSION['user_id']);

      $data = [
        'user' => $user
      ];

      $this->view('users/profile', $data);
    }

    public function editProfile(){
      // Check for POST
      if(isPost()){
        // Process form
        // Sanitize POST data
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        
        // Init data
        $data =[
          'name' => trim($_POST['name']),
          'email' => trim($_POST['email']),
          'password' => trim($_POST['password']),
          'confirm_password' => trim($_POST['confirm_password']),
          'name_err' => '',
          'email_err' => '',
          'password_err' => '',
          'confirm_password_err' => ''
        ];

        // Validate Name
        if(empty($data['name'])){
          $data['name_err'] = 'Please enter name';
        }

        // Validate Email
        if(empty($data['email'])){
          $data['email_err'] = 'Please enter email';
        } 
        
        // Validate Password
        if(empty($data['password'])){
          $data['password_err'] = 'Please enter password';
        } elseif(strlen($data['password']) < 6){
          $data['password_err'] = 'Password must be at least 6 characters';
        }

        // Validate Confirm Password
        if(empty($data['confirm_password'])){
          $data['confirm_password_err'] = 'Please confirm password';
        } else {
          if($data['password'] != $data['confirm_password']){
            $data['confirm_password_err'] = 'Passwords do not match';
          }
        }

        // Make sure errors are empty
        if(empty($data['email_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])){
          // Validated
          
          // Hash Password
          $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

          // Update user session
          $_SESSION['user_name'] = $data['name'];
          $_SESSION['user_email'] = $data['email'];

          // Edit profile
          if($this->userModel->editProfile($data)){
            flash('register_success', 'Your profile has been updated');
            redirect('users/profile');
          } else {
            die('Something went wrong');
          }

        } else {
          // Load view with errors
          $this->view('users/editprofile', $data);
        }
      } else {
        // Init data
        $data =[
          'name' => '',
          'email' => '',
          'password' => '',
          'confirm_password' => '',
          'name_err' => '',
          'email_err' => '',
          'password_err' => '',
          'confirm_password_err' => ''
        ];

        // Load view
        $this->view('users/editprofile', $data);
      }
    }
  }