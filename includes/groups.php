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

function checkUserInGroup($user_id, $event_id, $group_id) {
  $pdo = getPDO();

  $stmt = $pdo->prepare('SELECT *
                        FROM user_in_group as ug
                            JOIN event_to_group as eg ON ug.group_id = eg.group_id
                        WHERE ug.user_id = ? AND eg.event_id = ? AND eg.group_id = ?');
  $stmt->execute([$user_id, $event_id, $group_id]);

  return !!$stmt->fetch();
}

function addUserToGroup($user_id, $group_id) {
  $pdo = getPDO();

  $stmt = $pdo->prepare('INSERT INTO user_in_group
                        VALUES (?, ?)');
  try {
    $stmt->execute([$user_id, $group_id]);
    return true;
  } catch(\PDOException $e) {
    if ($e->getCode() === '23000') {
      return true;
    }

    throw $e;
  }
}

function removeUserFromGroup($user_id, $group_id) {
  $pdo = getPDO();

  $stmt = $pdo->prepare('DELETE FROM user_in_group
                        WHERE user_id = ? AND group_id = ?');
  $stmt->execute([$user_id, $group_id]);
}


function updateGroup($group_id, $group_name, $money_goal, $time, $place, $description, $group_pass, $is_private) {
  $pdo = getPDO();

  $meeting_time = date('G:i:s', strtotime($time));
  $meeting_place = empty($place) ? null : $place;
  $group_description = empty($description) ? null : $description;
  if (!$group_pass && $is_private) { // group is public and request to make pilate
    $group_pass = generateRandomString();
  } elseif ($group_pass && !$is_private) { // group is private and request to make public
    $group_pass = null;
  } // other 2 cases, we keep group_pass as is


  $stmt = $pdo->prepare('UPDATE groups
                        SET group_name = ?, money_goal = ?, meeting_time = ?, meeting_place = ?, group_description = ?, group_pass = ?
                        WHERE group_id = ?');
  $stmt->execute([$group_name, $money_goal, $meeting_time, $meeting_place, $group_description, $group_pass, $group_id]);
}
?>