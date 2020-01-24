<?php
/**
 * Save an image and return the file name
 * 
 * @param $image The image opbject from the posted form
 * @param $error A place to store errors if they occur
 * 
 * @return $filename or false if an error occured
 */
function saveImage($image, &$error)
{
  $filename = basename($image['name']);
  $targetFile = FILE_UPLOAD . $filename;
  $fileSize = $image['size'];
  $imageType = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
  $check = getimagesize($image['tmp_name']);
  if ($check !== false) {
    // It's a real image
    while(file_exists($targetFile)){
      // rename the image if there already is an image with this name
      $filename = pathinfo($image['name'], PATHINFO_FILENAME) . bin2hex(random_bytes(2)) . '.'. $imageType;
      $targetFile = FILE_UPLOAD . $filename;
    }

    $allowedFiles = array('jpg', 'png', 'jpeg', 'gif');
    if (in_array($imageType, $allowedFiles)) {
      if($fileSize <= MAX_IMAGE_UPLOAD){
        if(move_uploaded_file($image['tmp_name'], $targetFile)){
          return $filename;
        }
        $error = 'There went something wrong while updating';
      } else {
        $error = 'The file is to large. It has to be less than or equal to ' . (MAX_IMAGE_UPLOAD / 1000000) . 'MB';
      }
    } else {
      $error = 'This file type is not supported. Only JPG, JPEG, PNG & GIF files are allowed';
    }
  } else {
    $error = 'This file is not an image';
  }
  die($error);
  return false;
}

/**
 * Delete the image from the file upload
 * 
 * @param string $filename Filename of the to delete image
 * @return bool $success True if delete was succesfull
 */
function deleteImage($filename){
  $file = FILE_UPLOAD . $filename;
  return unlink($file);
}