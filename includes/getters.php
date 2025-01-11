<?php
require_once "db.php";

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