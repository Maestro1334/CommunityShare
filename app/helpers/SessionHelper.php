<?php
session_start();

/**
 * Create or flash a message 
 * Send only a name if you want the message to be shown on the page
 * 
 * @param string $name The name of the flash message
 * @param string $message The message for the flash message
 * @param string $class Optional class for message. Standard is 'alert alert-success'
 * @return nothing or an div with message
 */
function flash($name = '', $message = '', $class = 'alert alert-success')
{
  if (!empty($name)) {
    //No message, create it
    if (!empty($message) && empty($_SESSION[$name])) {
      if (!empty($_SESSION[$name])) {
        unset($_SESSION[$name]);
      }
      if (!empty($_SESSION[$name . '_class'])) {
        unset($_SESSION[$name . '_class']);
      }
      $_SESSION[$name] = $message;
      $_SESSION[$name . '_class'] = $class;
    }
    //Message exists, display it
    elseif (!empty($_SESSION[$name]) && empty($message)) {
      $class = !empty($_SESSION[$name . '_class']) ? $_SESSION[$name . '_class'] : 'success';
      echo '<div class="' . $class . '" id="msg-flash">' . $_SESSION[$name] . '</div>';
      unset($_SESSION[$name]);
      unset($_SESSION[$name . '_class']);
    }
  }
}

/**
 * Check id a user is logged in
 * @return bool true if logged in
 */
function isLoggedIn()
{
  return isset($_SESSION['user_id']);
}

/**
 * Set some userinformation in the session
 * 
 * @param $user UserModel object of logged in user
 */
function createUserSession($user)
{
  $_SESSION['user_id'] = $user->id;
  $_SESSION['user_type'] = $user->type;
  $_SESSION['user_email'] = $user->email;
}

/**
 * Unset all user information from the session
 */
function removeUserSession()
{ 
  unset($_SESSION['user_id']);
  session_destroy();
}

/**
 * Get the Id from the user
 * 
 * @return Id or false if no user_id is set
 */
function getUserId(){
  return (isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : false;
}

/**
 * Set the password reset token in the session
 *
 * @param string $token Token to set
 */
function createResetTokenSession($token)
{
  $_SESSION['reset_token'] = $token;
}

/**
 * Get the reset token from session
 *
 * @return token or bool false if none is set
 */
function getResetToken()
{
  return (isset($_SESSION['reset_token'])) ? $_SESSION['reset_token'] : false;
}

/**
 * Remove the reset token from the session
 */
function removeResetTokenSession()
{
  unset($_SESSION['reset_token']);
}