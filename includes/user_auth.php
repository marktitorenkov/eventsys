<?php
require_once "db.php";

function registerUser($username, $password, $birthdate) {
  $pdo = getPDO();
  $hashed_password = password_hash($password, PASSWORD_BCRYPT);
  
  $stmt = $pdo->prepare('INSERT INTO users (username, birthdate, password_hash)
                        VALUES (?, ?, ?)');
  try {
    $stmt->execute([$username, $birthdate, $hashed_password]);
    return true;
  } catch (\PDOException $e) {
    // Duplicate entry (username already exists)
    if ($e->getCode() === '23000') {
      return false;
    }
    throw $e;
  }
}

function loginUser($username, $password) {
  $pdo = getPDO();
  
  $stmt = $pdo->prepare('SELECT id, password_hash
                        FROM users
                        WHERE username = ?');
  $stmt->execute([$username]);
  $user = $stmt->fetch();
  
  if ($user && password_verify($password, $user['password_hash'])) {
    return $user['id'];
  }
  return false;
}

?>