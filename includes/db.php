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

  $user = $stmt->fetch();

  // check if user is missing in db, but session cache still has a user_id set
  if ($user === false) {
    session_unset();
    header('Location: /login.php');
    exit;
  }

  return $user;
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

function getEventById($eventId) {
  if ($eventId === null) return null;

  // TODO: read from DB; needs event table
  return getEvents()[$eventId - 1];
}

function createGroup($creator_id, $group_name, $money_goal, $time, $place, $description, $password) {
  $pdo = getPDO();

  $meeting_time = date('G:i:s', strtotime($time));
  $meeting_place = empty($place) ? null : $place;
  $group_description = empty($description) ? null : $description;
  $hashed_password = empty($password) ? null: password_hash($password, PASSWORD_BCRYPT);

  // insert new group into table 'groups'
  $stmt1 = $pdo->prepare('INSERT INTO groups (creator_id, group_name, money_goal, meeting_time, meeting_place, group_description, hashed_password) VALUES (?, ?, ?, ?, ?, ?, ?)');
  $stmt1->execute([$creator_id, $group_name, $money_goal, $meeting_time, $meeting_place, $group_description, $hashed_password]);

  // get id of last inserted group by current user
  $stmt2 = $pdo->prepare('SELECT MAX(group_id) FROM groups WHERE creator_id = ?;');
  $stmt2->execute([$creator_id]);

  return $stmt2->fetch()['MAX(group_id)'];
}

function attachGroupToEvent($group_id, $event_id) {
  $pdo = getPDO();

  $stmt = $pdo->prepare('INSERT INTO event_to_group VALUES (?, ?)');
  $stmt->execute([$event_id, $group_id]);

  return $stmt->fetchAll();
}

function getGroupById($group_id) {
  $pdo = getPDO();

  $stmt = $pdo->prepare('SELECT * FROM groups WHERE group_id = ?;');
  $stmt->execute([$group_id]);

  return $stmt->fetch();
}

function getGroupsByEventId($event_id) {
  $pdo = getPDO();

  $stmt = $pdo->prepare('SELECT groups.group_id, group_name, money_goal, meeting_time, meeting_place, group_description, hashed_password FROM event_to_group AS eg JOIN groups ON eg.group_id = groups.group_id WHERE event_id = ?');
  $stmt->execute([$event_id]);

  return $stmt->fetchAll();
}

?>
