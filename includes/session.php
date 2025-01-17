<?php
require_once "db.php";
require_once "db_users.php";

function startSession() {
  if (!isset($_SESSION)) session_start();
}

function getUserId() {
  global $user;
  
  if (!$user) {
    startSession();
    $user = getUserById($_SESSION['user_id'] ?? null);
    if (!$user) {
      return null;
    }
  }
  
  return $user["id"];
}

function ensureLoggedIn() {
  if (!getUserId()) {
    header('Location: ./login.php');
    exit;
  }
}

function ensureLoggedOut() {
  if (getUserId()) {
    header('Location: ./');
    exit;
  }
}

?>
