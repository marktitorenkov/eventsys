<?php

// Queries

function getUserById($user_id) {
  if ($user_id === null)
  return null;
  
  $pdo = getPDO();
  $stmt = $pdo->prepare('SELECT u.id,
                                u.username,
                                u.email,
                                e.date AS birthdate
                        FROM users u
                        JOIN events e ON u.birthday_event = e.event_id
                        WHERE u.id = ?');
  $stmt->execute([$user_id]);
  
  $user = $stmt->fetch();
  
  return $user;
}

function getUsers($viewer, $query, $limit, $offset) {
  $pdo = getPDO();

  $stmt = $pdo->prepare("SELECT u.id,
                                u.username,
                                u.email,
                                e.date AS birthdate,
                                fu.favorite_user_id IS NOT NULL as favorite
                        FROM users u
                        JOIN events e ON u.birthday_event = e.event_id
                        LEFT JOIN favorite_users fu ON fu.user_id = ? AND fu.favorite_user_id = u.id
                        WHERE u.username LIKE CONCAT('%', ?, '%')
                        LIMIT ? OFFSET ?");
  $stmt->execute([$viewer, $query, $limit, $offset]);

  return $stmt->fetchAll();
}

function getUsersCount($query) {
  $pdo = getPDO();

  $stmt = $pdo->prepare("SELECT COUNT(1)
                        FROM users u
                        WHERE u.username LIKE CONCAT('%', ?, '%')");
  $stmt->execute([$query]);

  return $stmt->fetch()['COUNT(1)'];
}

function getUserNames($query, $limit) {
  $pdo = getPDO();
  
  $stmt = $pdo->prepare("SELECT id, username
                        FROM users
                        WHERE username LIKE CONCAT('%', ?, '%')
                        LIMIT ?");
  $stmt->execute([$query, $limit]);
  
  return $stmt->fetchAll();
}

// Mutations

function registerUser($username, $password, $birthdate, $email) {
  $hashed_password = password_hash($password, PASSWORD_BCRYPT);

  $pdo = getPDO();
  $pdo->beginTransaction();

  try {

    $stmt = $pdo->prepare('INSERT INTO events (`name`, `date`, `recurring`)
                          VALUES (?, ?, ?)');
    $stmt->execute(['Birthday: '.$username, $birthdate, true]);
    $event_id = $pdo->lastInsertId();

    $stmt = $pdo->prepare('INSERT INTO users (username, birthday_event, email, password_hash)
                          VALUES (?, ?, ?, ?)');
    $stmt->execute([$username, $event_id, $email, $hashed_password]);
    $user_id = $pdo->lastInsertId();

    $stmt = $pdo->prepare('UPDATE events SET creator_id = ? WHERE event_id = ?');
    $stmt->execute([$user_id, $event_id]);

  } catch (\PDOException $e) {
    $pdo->rollback();
    // Duplicate entry (username already exists)
    if ($e->getCode() === '23000') {
      return false;
    }
    throw $e;
  }

  $pdo->commit();
  return true;
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

function updateUser($id, $username, $email, $birthdate) {
  $pdo = getPDO();
  $pdo->beginTransaction();

  $stmt = $pdo->prepare('UPDATE users
                        SET username = ?, email = ?
                        WHERE id = ?');
  $stmt->execute([$username, $email, $id]);

  $stmt = $pdo->prepare('UPDATE events e
                        JOIN users u ON e.event_id = u.birthday_event
                        SET e.date = ?
                        WHERE u.id = ?');
  $stmt->execute([$birthdate, $id]);

  $pdo->commit();
}

function updatePassword($id, $password) {
  $pdo = getPDO();
  $hashed_password = password_hash($password, PASSWORD_BCRYPT);
  
  $stmt = $pdo->prepare('UPDATE users
                        SET password_hash = ?
                        WHERE id = ?');

  $stmt->execute([$hashed_password, $id]);
}

function userFavorite($user_viewer, $user_favorite, $favorite) {
  $pdo = getPDO();

  if (boolval($favorite)) {
    $stmt = $pdo->prepare('INSERT IGNORE INTO favorite_users (user_id, favorite_user_id) VALUES (?, ?);');
  } else {
    $stmt = $pdo->prepare('DELETE FROM favorite_users WHERE user_id = ? AND favorite_user_id = ?');
  }

  $stmt->execute([$user_viewer, $user_favorite]);
}

?>