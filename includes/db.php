<?php
require_once "config.php";

function getPDO() {
  global $config;

  $dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4";
  $options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ];

  static $pdo = null;
  if ($pdo === null) {
    try {
        $pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], $options);
    } catch (\PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
  }
  return $pdo;
}

function registerUser($username, $password) {
  $pdo = getPDO();
  $hashed_password = password_hash($password, PASSWORD_BCRYPT);

  $stmt = $pdo->prepare('INSERT INTO users (username, password_hash) VALUES (?, ?)');
  try {
    $stmt->execute([$username, $hashed_password]);
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

  $stmt = $pdo->prepare('SELECT id, password_hash FROM users WHERE username = ?');
  $stmt->execute([$username]);
  $user = $stmt->fetch();

  if ($user && password_verify($password, $user['password_hash'])) {
    return $user['id'];
  }
  return false;
}

function getUserById($userId) {
  if ($userId === null) return null;

  $pdo = getPDO();
  $stmt = $pdo->prepare('SELECT id, username FROM users WHERE id = ?');
  $stmt->execute([$userId]);
  return $stmt->fetch();
}

function getEvents() {
  // TODO: read from DB
  return [
    [
      'id' => 1,
      'name' => 'D Birthday',
      'date' => strtotime('2025-02-04'),
      'recurring' => true,
    ],
    [
      'id' => 2,
      'name' => 'M Birthday',
      'date' => strtotime('2025-05-05'),
      'recurring' => true,
    ],
    [
      'id' => 3,
      'name' => 'O Birthday',
      'date' => strtotime('2025-05-24'),
      'recurring' => true,
    ],
    [
      'id' => 4,
      'name' => 'I Nameday',
      'date' => strtotime('2025-05-05'),
      'recurring' => true,
    ],
    [
      'id' => 5,
      'name' => 'X+Y Wedding',
      'date' => strtotime('2025-08-15'),
      'recurring' => false,
    ],
  ];
}

?>
