<?php
// Simple page redirect
function redirect($page = 'page')
{
  header('Location: ' . URLROOT . '/' . $page);
}

/**
 * Check if the method is a POST
 * 
 * @return bool true if is POST
 */
function isPost(){
  return ($_SERVER['REQUEST_METHOD'] == 'POST');
}