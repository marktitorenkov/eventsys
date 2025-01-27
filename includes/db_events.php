<?php
require_once "db.php";

// Queries

function getEvents($viewer) {
  $pdo = getPDO();
  
  $stmt = $pdo->prepare('SELECT e.event_id,
                                e.creator_id,
                                u.username AS creator_username,
                                e.name,
                                e.date,
                                e.recurring,
                                fu.user_id IS NOT NULL OR e.creator_id = ? AS favorite
                        FROM events e
                        LEFT JOIN users u ON e.creator_id = u.id
                        LEFT JOIN user_hidden_event eh ON e.event_id = eh.event_id AND eh.user_id = ?
                        LEFT JOIN favorite_users fu ON e.creator_id = fu.favorite_user_id AND fu.user_id = ?
                        WHERE eh.user_id IS NULL');

  $stmt->execute([$viewer, $viewer, $viewer]);

  return $stmt->fetchAll();
}

function getEventById($event_id) {
  if ($event_id === null)
  return null;
  
  $pdo = getPDO();
  $stmt = $pdo->prepare('SELECT e.*,
                                u.username AS creator_username
                        FROM events e
                        LEFT JOIN users u ON e.creator_id = u.id
                        WHERE e.event_id = ?');
  $stmt->execute([$event_id]);
  
  return $stmt->fetch();
}

function getEventsByOwner($viewer, $creator) {
  $pdo = getPDO();
  
  $stmt = $pdo->prepare('SELECT e.*
                         FROM events e
                         LEFT JOIN user_hidden_event eh ON e.event_id = eh.event_id AND eh.user_id = ?
                         WHERE e.creator_id = ? AND eh.user_id IS NULL');
  $stmt->execute([$viewer, $creator]);

  return $stmt->fetchAll();
}

function getHiddenSelect($event_id){
  $pdo=getPDO();
  $stmt = $pdo->prepare('SELECT user_id as value,
                                username as text
                         FROM user_hidden_event
                         JOIN users u ON u.id = user_id
                         WHERE event_id = ?');
  $stmt->execute([$event_id]);

  return $stmt->fetchAll();
}

// Mutations

function _updateHiddenUsers($pdo, $event_id, $users_to_hide) {
  $stmt = $pdo->prepare('DELETE FROM user_hidden_event WHERE event_id = ?');
  $stmt->execute([$event_id]);
  foreach ($users_to_hide as $user_hide ){
    $stmt = $pdo->prepare('INSERT INTO user_hidden_event (event_id, user_id) VALUES (?,?)');
    $stmt->execute([$event_id, $user_hide]);
  }
}

function createEvent($creator, $name, $date, $description, $recurring, $users_to_hide) {
  $pdo = getPDO();
  $pdo->beginTransaction();

  $stmt = $pdo->prepare('INSERT INTO events (creator_id, name, date, description, recurring)
                         VALUES (?,?,?,?,?)');
  $stmt->execute([$creator, $name, $date, $description, intval($recurring)]);
  $event_id = $pdo->lastInsertId();

  _updateHiddenUsers($pdo, $event_id, $users_to_hide);

  $pdo->commit();
  return $event_id;
}

function deleteEventById($event_id) {
  $pdo = getPDO(); 

  $stmt = $pdo->prepare('DELETE FROM events WHERE event_id = ?');
  
  $stmt->execute([$event_id]);
  
  return $stmt->rowCount() > 0;
}

function modifyEvent($event_id, $modified_name, $modified_date, $modified_description, $users_to_hide) {
  $pdo = getPDO();
  $pdo->beginTransaction();

  $stmt = $pdo->prepare('UPDATE events SET name = ?, date = ?, description = ? WHERE event_id = ?');
  $stmt->execute([$modified_name, $modified_date, $modified_description, $event_id]);

  _updateHiddenUsers($pdo, $event_id, $users_to_hide);

  $pdo->commit();
  return $stmt->rowCount() > 0;
}

?>
