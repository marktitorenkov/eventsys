<?php
require_once "db.php";

// Queries

function getEvents() {
  $pdo = getPDO();
  
  $stmt = $pdo->prepare('SELECT *
                        FROM events');
  $stmt->execute();
  
  return $stmt->fetchAll();
}

function getEventById($event_id) {
  if ($event_id === null)
  return null;
  
  $pdo = getPDO();
  $stmt = $pdo->prepare('SELECT *
                        FROM events
                        WHERE event_id = ?');
  $stmt->execute([$event_id]);
  
  return $stmt->fetch();
}

function getEventsByOwner($admin_id) {
  $pdo = getPDO();
  
  $stmt = $pdo->prepare('SELECT *
                         FROM events e
                         WHERE e.admin = ?');
  $stmt->execute([$admin_id]);

  return $stmt->fetchAll();
}

// Mutations

function createEvent($admin,$canChange,$name,$date,$description,$recurring){
  $pdo = getPDO();

  $stmt = $pdo->prepare('INSERT INTO events (admin,canChange,name,date,description,recurring)
                          VALUES(?,?,?,?,?,?)');
  $stmt->execute([$admin, $canChange, $name,$date,$description,$recurring]);
  
  

  $stmt2 = $pdo->prepare('SELECT MAX(event_id)
                          FROM events
                          WHERE admin = ?');
  $stmt2->execute([$admin]);

  return $stmt2->fetch()['MAX(event_id)'];
}

function deleteEventById($event_id) {
  $pdo = getPDO();  // Get PDO connection

  // Prepare the DELETE query to remove the event by ID
  $stmt = $pdo->prepare('DELETE FROM events WHERE event_id = ?');
  
  // Execute the statement with the event ID
  $stmt->execute([$event_id]);

  // Check if the event was deleted
  if ($stmt->rowCount() > 0) {
      return true;  // Event deleted successfully
  } else {
      return false; // Event deletion failed (likely no matching event_id)
  }
}

function modifyEvent($event_id, $modified_name, $modified_date, $modified_description) {
  $pdo = getPDO();  // Get PDO connection

  // Prepare the update query to modify the event details
  $stmt = $pdo->prepare('UPDATE events SET name = ?, date = ?, description = ? WHERE event_id = ?');
  
  // Execute the statement with the new values for the event
  $stmt->execute([$modified_name, $modified_date, $modified_description, $event_id]);

  // Check if the update was successful
  if ($stmt->rowCount() > 0) {
      return true;  // Event updated successfully
  } else {
      return false; // Event update failed (likely no changes or invalid event_id)
  }
}

?>
