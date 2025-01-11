<?php
require_once "db.php";

function generateRandomString($length = 8) {
  global $config;
  
  $allowed_characters = $config['group_pass_characters'];
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
  $group_pass = $is_private ? generateRandomString() : null;
  
  // insert new group into table 'groups'
  $stmt = $pdo->prepare('INSERT INTO groups (creator_id, group_name, money_goal, meeting_time, meeting_place, group_description, group_pass)
                        VALUES (?, ?, ?, ?, ?, ?, ?)');
  $stmt->execute([$creator_id, $group_name, $money_goal, $meeting_time, $meeting_place, $group_description, $group_pass]);
  
  return $pdo->lastInsertId();
}

function attachGroupToEvent($group_id, $event_id, $year) {
  $pdo = getPDO();
  
  $stmt = $pdo->prepare('INSERT INTO event_to_group
                        VALUES (?, ?, ?)');
  $stmt->execute([$event_id, $group_id, $year]);
  
  return $stmt->fetchAll();
}

function checkUserInGroup($user_id, $group_id) {
  $pdo = getPDO();
  
  $stmt = $pdo->prepare('SELECT *
                        FROM user_in_group
                        WHERE user_id = ? AND group_id = ?');
  $stmt->execute([$user_id, $group_id]);
  
  return !!$stmt->fetch();
}

function addUserToGroup($user_id, $group_id) {
  $pdo = getPDO();

  $stmt = $pdo->prepare('INSERT INTO user_in_group
                  VALUES (?, ?)');
  $stmt->execute([$user_id, $group_id]);
}

function removeUserFromGroup($user_id, $group_id) {
  $pdo = getPDO();

  $stmt = $pdo->prepare('DELETE FROM user_in_group
                  WHERE user_id = ? AND group_id = ?');
  $stmt->execute([$user_id, $group_id]);
}

?>