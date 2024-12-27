<?php

function startSession() {
  if (!isset($_SESSION)) session_start();
}

function getUserId() {
  startSession();
  if (isset($_SESSION['user_id'])) {
    return $_SESSION['user_id'];
  } else {
    return null;
  }
}

function ensureLoggedIn() {
  startSession();
  if (!isset($_SESSION['user_id'])) {
    header('Location: ./login.php');
    exit;
  }
}

function ensureLoggedOut() {
  startSession();
  if (isset($_SESSION['user_id'])) {
    header('Location: ./');
    exit;
  }
}

?>
