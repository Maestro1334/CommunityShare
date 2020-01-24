<?php

/**
 * Validate given emailaddress
 * 
 * @param string $email The email to be validated
 * @param string $error The error location to be filled if something is wrong
 */
function validateEmail($email, &$error, $newUser = true ,$folder = '')
{
  if(!empty($folder)){
    $folder .= '/';
  }
  require_once '../app/models/'.$folder.'UserModel.php';
  $userModel = new UserModel;
  if (empty($email)) {
    $error = 'Please enter an email address';
  } else {
    // Check if email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $error = 'This is not a valid email address';
    } else {
      // Check if there is a user with this email
      $check = $userModel->isUser($email);
      if ((int)$check->exists == 1) {
        // There is a user
        if ($newUser) {
          $error = 'Email is already taken';
        }
      } else {
        // There is no user
        if (!$newUser) {
          $error = 'No user found with this email address';
        }
      }
    }
  }
}

/**
 * Validate if password is strong enough
 * Increase strenght later
 * 
 * @param string $password Password to check
 * @return boolean True if strong enough
 */
function strengthCheckPassword($password)
{
  $uppercase = preg_match('@[A-Z]@', $password);
  $lowercase = preg_match('@[a-z]@', $password);
  $number = preg_match('@[0-9]@', $password);
  $specialChars = preg_match('@[^\w]@', $password);

  return ($uppercase && $lowercase && $number && $specialChars && strlen($password) >= 8);
}

/**
 * Compare new password to the current password
 * 
 * @param string $password New password to compare
 * @return boolean if is different password
 */
function isDifferenThanCurrentPassword($password, $user = null)
{
  require_once '../app/models/UserModel.php';
  $userModel = new UserModel;
  if(!$user)
  {
    //$user = $userModel->getLoggedInUser();
    die('Getting logged in user needs to be added');
  }
  return (!password_verify($password, $user->password));
}

/**
 * Validate the user given password and confirmation password
 * 
 * @param string $password Password to validate
 * @param string $confirmPassword Password to be identical to $password
 * @param string $pError Field to might contain password error
 * @param string $cpError Field to might contain confirmPassword error
 * @param boolean $toCompare Compare with current password
 */
function validatePasswordInputs($password, $confirmPassword, &$pError, &$cpError, $toCompare = false)
{
  if (empty($password)) {
    $pError = 'Please enter a password';
  } else {
    if ($toCompare) {
      if (!isDifferenThanCurrentPassword($password)) {
        $pError = 'You cannot use an old password';
      } else {
        if (!strengthCheckPassword($password)) {
          $pError = 'Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character';
        }
      }
    } else {
      if (!strengthCheckPassword($password)) {
        $pError = 'Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character';
      }
    }
  }

  // Validate confirm password
  if (empty($confirmPassword)) {
    $cpError = 'Please confirm password';
  } else {
    if ($password != $confirmPassword) {
      $cpError = 'The password do not match';
    }
  }
}

/**
 * Filter the POST data
 */
function filterPost()
{ 
  $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
}

/**
 * Validate if string contains no special characters and is filled in
 *
 * @param string $value what needs to be validated
 * @param string $subject that the validation is for
 * @return string with error message or empty string
 */
function validateString(string $value, string $subject){
    // Check if the input field is filled
    if (empty($value)){
        // Give the user 'empty field' error
        return 'Please enter your ' . $subject;
    }  // Check if name only contains a-z, A-Z and 0-9 symbols
    else if (!preg_match("/^[a-zA-Z0-9 \s]*$/", $value))
    {
        // Give the user 'forbidden characters' error
        return 'Your ' . $subject . ' cannot contain any special characters';
    }

    return '';
}

/**
 * Validate if phone number contains only and is filled in
 *
 * @param string $phonenumber
 * @return string with error message or empty string
 */
function validatePhoneNumber(string $phonenumber){
    // Check if the input field is filled
    if (empty($phonenumber)){
        // Give the user 'empty field' error
        return 'Please enter your phone number';
    }  // Check if name only contains 0-9 symbols
    else if (!preg_match("/^[0-9 \s]*$/", $phonenumber))
    {
        // Give the user 'forbidden characters' error
        return 'Your phone number can only contain numbers';
    }

    return '';
}