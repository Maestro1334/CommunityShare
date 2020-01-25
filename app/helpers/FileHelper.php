<?php
/**
 * Save image(s) and return the file name(s)
 * 
 * @param $data array that holds the image data
 * @param $error variable to store errors if they occur
 * 
 * @return array of $filenames or false if an error occured
 */
function saveImages($data, &$error)
{
  $filenames = [];
  for ($i = 0; $i < count($data['images']['tmp_name']); $i++) {
    $filename = $data['images']['name'][$i];
    $file_size = $data['images']['size'][$i];
    $file_tmp = $data['images']['tmp_name'][$i];
    $targetFile = FILE_UPLOAD . $filename;
    $imageType = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $isImage = getimagesize($data['images']['tmp_name'][$i]);

    if ($isImage !== false) {
      // It is an image
      while (file_exists($targetFile)) {
        // rename the image if there already is an image with this name
        $filename = pathinfo($data['images']['name'][$i], PATHINFO_FILENAME) . bin2hex(random_bytes(2)) . '.' . $imageType;
        $targetFile = FILE_UPLOAD . $filename;
      }

      $allowedFiles = array('jpg', 'png', 'jpeg', 'gif');
      if (in_array($imageType, $allowedFiles)) {
        // FIle type is allowed
        if ($file_size <= MAX_IMAGE_UPLOAD) {
          // Image size is allowed

          // load the image
          if($imageType == "jpg" or $imageType == "jpeg"){
            $original_image = imagecreatefromjpeg($file_tmp);
          }
          else if($imageType == "gif"){
            $original_image = imagecreatefromgif($file_tmp);
          }
          else if($imageType == "png"){
            $original_image = imagecreatefrompng($file_tmp);
          }

          // Generate rotated image
          $rotated_image = imagerotate($original_image, 45, 0);

          // Store the rotated image
          if($imageType == "jpg" or $imageType == "jpeg"){
            imagejpeg($rotated_image, $targetFile);
          }
          else if($imageType == "gif"){
            imagegif($rotated_image, $targetFile);
          }
          else if($imageType == "png"){
            imagepng($rotated_image, $targetFile);
          }

          $filenames[] = $filename;

        } else {
          $error = 'The image is too large. It has to be less than or equal to ' . (MAX_IMAGE_UPLOAD / 1000000) . 'MB';
          }
      } else {
        $error = 'This file type is not supported. Only JPG, JPEG, PNG & GIF file types are allowed';
        }
    } else {
      $error = 'This file is not an image';
      }
    }
  return $filenames;
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