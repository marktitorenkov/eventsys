<?php
require_once "db.php";

function startSession() {
  if (!isset($_SESSION)) session_start();
}

function getUserById($user_id) {
  if ($user_id === null)
  return null;
  
  $pdo = getPDO();
  $stmt = $pdo->prepare('SELECT id, username
                        FROM users
                        WHERE id = ?');
  $stmt->execute([$user_id]);
  
  $user = $stmt->fetch();
  
  return $user;
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
