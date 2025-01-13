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

  $stmt = $pdo->prepare('INSERT INTO users (username, password_hash)
                         VALUES (?, ?)');
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

function getUserById($userId) {
  if ($userId === null) return null;

  $pdo = getPDO();
  $stmt = $pdo->prepare('SELECT id, username
                         FROM users
                         WHERE id = ?');
  $stmt->execute([$userId]);

  $user = $stmt->fetch();

  return $user;
}

function getEvents() {
  $pdo = getPDO();

  $stmt = $pdo->prepare('SELECT *
                         FROM events');
  $stmt->execute();

  return $stmt->fetchAll();
}

function getEventById($eventId) {
  if ($eventId === null) return null;

  $pdo = getPDO();
  $stmt = $pdo->prepare('SELECT *
                         FROM events
                         WHERE event_id = ?');
  $stmt->execute([$eventId]);

  return $stmt->fetch();
}

function generate_random_string($length = 8) {
  $allowed_characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $ch_length = strlen($allowed_characters);
  $random_string = '';

  for ($i = 0; $i < $length; $i++) {
    $random_string .= $allowed_characters[random_int(0, $ch_length - 1)];
  }

  return $random_string;
}

function createGroup($creator_id, $group_name, $money_goal, $time, $place, $description, $is_private) {
  $pdo = getPDO();

  $meeting_time = date('G:i:s', strtotime($time));
  $meeting_place = empty($place) ? null : $place;
  $group_description = empty($description) ? null : $description;
  $group_pass = $is_private ? generate_random_string() : null;

  // insert new group into table 'groups'
  $stmt1 = $pdo->prepare('INSERT INTO groups (creator_id, group_name, money_goal, meeting_time, meeting_place, group_description, group_pass)
                          VALUES (?, ?, ?, ?, ?, ?, ?)');
  $stmt1->execute([$creator_id, $group_name, $money_goal, $meeting_time, $meeting_place, $group_description, $group_pass]);

  // get id of last inserted group by current user
  $stmt2 = $pdo->prepare('SELECT MAX(group_id)
                          FROM groups
                          WHERE creator_id = ?');
  $stmt2->execute([$creator_id]);

  return $stmt2->fetch()['MAX(group_id)'];
}

function attachGroupToEvent($group_id, $event_id, $year) {
  $pdo = getPDO();

  $stmt = $pdo->prepare('INSERT INTO event_to_group
                         VALUES (?, ?, ?)');
  $stmt->execute([$event_id, $group_id, $year]);

  return $stmt->fetchAll();
}

function getGroupById($group_id) {
  $pdo = getPDO();

  $stmt = $pdo->prepare('SELECT *
                         FROM groups
                         WHERE group_id = ?');
  $stmt->execute([$group_id]);

  return $stmt->fetch();
}

function getGroupsByEventIdYear($event_id, $year) {
  $pdo = getPDO();

  $stmt = $pdo->prepare('SELECT groups.group_id, group_name, money_goal, meeting_time, meeting_place, group_description, group_pass
                         FROM event_to_group AS eg
                         JOIN groups ON eg.group_id = groups.group_id
                         WHERE event_id = ? AND year = ?');
  $stmt->execute([$event_id, $year]);

  return $stmt->fetchAll();
}

?>
