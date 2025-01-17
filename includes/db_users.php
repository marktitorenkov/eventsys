<?php

// Queries

function getUserById($user_id) {
  if ($user_id === null)
  return null;
  
  $pdo = getPDO();
  $stmt = $pdo->prepare('SELECT id, username, email, birthdate
                        FROM users
                        WHERE id = ?');
  $stmt->execute([$user_id]);
  
  $user = $stmt->fetch();
  
  return $user;
}

function getUsers($viewer, $query, $limit, $offset) {
  $pdo = getPDO();

  $stmt = $pdo->prepare("SELECT u.id,
                                u.username,
                                u.email,
                                u.birthdate,
                                fu.favorite_user_id IS NOT NULL as favorite
                        FROM users u
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
  $pdo = getPDO();
  $hashed_password = password_hash($password, PASSWORD_BCRYPT);
  
  $stmt = $pdo->prepare('INSERT INTO users (username, birthdate, email, password_hash)
                        VALUES (?, ?, ?, ?)');
  try {
    $stmt->execute([$username, $birthdate, $email, $hashed_password]);
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

function updateUser($id, $username, $email, $birthdate) {
  $pdo = getPDO();

  $stmt = $pdo->prepare('UPDATE users
                        SET username = ?, email = ?, birthdate = ?
                        WHERE id = ?');
  $stmt->execute([$username, $email, $birthdate, $id]);
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