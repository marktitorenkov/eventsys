<?php
require_once 'db.php';

// Queries

function getGroupById($group_id) {
  $pdo = getPDO();
  
  $stmt = $pdo->prepare('SELECT *
                        FROM event_groups
                        WHERE group_id = ?');
  $stmt->execute([$group_id]);
  
  return $stmt->fetch();
}

function getGroupsByEventIdYear($user_id, $event_id, $year) {
  $pdo = getPDO();
  
  // get groups for given Event and Year
  // exclude groups that are hidden for given User
  $stmt = $pdo->prepare('SELECT g.group_id,
                                      group_name,
                                      money_goal,
                                      meeting_time,
                                      meeting_place,
                                      group_description,
                                      group_pass
                                FROM event_to_group AS eg
                                JOIN event_groups g ON eg.group_id = g.group_id
                                    LEFT JOIN user_hidden_group uhg ON eg.group_id = uhg.group_id AND uhg.user_id = ?
                                WHERE event_id = ? AND year = ? AND uhg.user_id IS NULL');
  $stmt->execute([$user_id, $event_id, $year]);
  
  return $stmt->fetchAll();
}

// Helper Functions

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

// Mutations

function createGroup($creator_id, $group_name, $money_goal, $time, $place, $description, $is_private) {
  $pdo = getPDO();
  
  $meeting_time = date('G:i:s', strtotime($time));
  $meeting_place = empty($place) ? null : $place;
  $group_description = empty($description) ? null : $description;
  $group_pass = $is_private ? generateRandomString() : null;
  
  $stmt = $pdo->prepare('INSERT INTO event_groups (creator_id, group_name, money_goal, meeting_time, meeting_place, group_description, group_pass)
                        VALUES (?, ?, ?, ?, ?, ?, ?)');
  $stmt->execute([$creator_id, $group_name, $money_goal, $meeting_time, $meeting_place, $group_description, $group_pass]);
  
  return $pdo->lastInsertId();
}

function deleteGroup($group_id, $user_id) {
  $pdo = getPDO();

  $stmt = $pdo->prepare('DELETE FROM event_groups
                        WHERE group_id = ? AND creator_id = ?');
  $stmt->execute([$group_id, $user_id]);
}

function attachGroupToEvent($group_id, $event_id, $year) {
  $pdo = getPDO();
  
  $stmt = $pdo->prepare('INSERT INTO event_to_group
                        VALUES (?, ?, ?)');
  $stmt->execute([$event_id, $group_id, $year]);
}

function checkUserInGroup($user_id, $event_id, $group_id) {
  $pdo = getPDO();

  // check if User is in Group
  $stmt = $pdo->prepare('SELECT *
                        FROM user_in_group as ug
                            JOIN event_to_group as eg ON ug.group_id = eg.group_id
                        WHERE ug.user_id = ? AND eg.event_id = ? AND eg.group_id = ?');
  $stmt->execute([$user_id, $event_id, $group_id]);

  return !!$stmt->fetch();
}

function checkGroupHiddenFromUser($user_id, $event_id, $group_id) {
  $pdo = getPDO();

  // check if Group is hidden from User
  $stmt = $pdo->prepare('SELECT *
                        FROM user_hidden_group as ug
                            JOIN event_to_group as eg ON ug.group_id = eg.group_id
                        WHERE ug.user_id = ? AND eg.event_id = ? AND eg.group_id = ?');
  $stmt->execute([$user_id, $event_id, $group_id]);
  
  return !!$stmt->fetch();
}

function addUserInGroup($user_id, $group_id) {
  $pdo = getPDO();

  $stmt = $pdo->prepare('INSERT INTO user_in_group
                        VALUES (?, ?)');
  try {
    $stmt->execute([$user_id, $group_id]);
    return true;
  } catch(\PDOException $e) {
    if ($e->getCode() == 23000) {
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

function hideGroupFromUser($user_id, $group_id) {
  $pdo = getPDO();

  $stmt = $pdo->prepare('INSERT INTO user_hidden_group
                        VALUES (?, ?)');
  try {
    $stmt->execute([$user_id, $group_id]);
    return true;
  } catch(\PDOException $e) {
    if ($e->getCode() == 23000) {
      return true;
    }

    throw $e;
  }
}

function showGroupToUser($user_id, $group_id) {
  $pdo = getPDO();

  $stmt = $pdo->prepare('DELETE FROM user_hidden_group
                        WHERE user_id = ? AND group_id = ?');
  $stmt->execute([$user_id, $group_id]);
}

function updateGroup($group_id, $creator_id, $group_name, $money_goal, $time, $place, $description, $group_pass, $is_private = null) {
  $pdo = getPDO();

  $meeting_time = date('G:i:s', strtotime($time));
  $meeting_place = empty($place) ? null : $place;
  $group_description = empty($description) ? null : $description;
  if (!$is_private) {
    if (!$group_pass && $is_private) { // group is public and request to make pilate
      $group_pass = generateRandomString();
    } elseif ($group_pass && !$is_private) { // group is private and request to make public
      $group_pass = null;
    } // other 2 cases, we keep group_pass as is
  }

  $stmt = $pdo->prepare('UPDATE event_groups
                        SET creator_id = ?, group_name = ?, money_goal = ?, meeting_time = ?, meeting_place = ?, group_description = ?, group_pass = ?
                        WHERE group_id = ?');
  $stmt->execute([$creator_id, $group_name, $money_goal, $meeting_time, $meeting_place, $group_description, $group_pass, $group_id]);
}

function getUsersInGroup($group_id, $limit = null, $offset = null, $include_hidden = false) {
  $pdo = getPDO();
  
  // Inner Table: 'Users In Group' Union 'Users Hidden From Group'
  // Outer Table: adds username and email to result from Inner Table
  // user_status: 0 = group admin / 1 = user in group / 2 = group hidden from user
  $params = [$group_id];

  $query = 'SELECT u.username, u.email, gr.user_id, gr.user_status
            FROM users u
            JOIN (SELECT g.group_id, ug.user_id, IF(g.creator_id = ug.user_id, 0, 1) AS user_status
                FROM event_groups g
                    JOIN user_in_group ug ON ug.group_id = g.group_id
                WHERE g.group_id = ?';
  
  if ($include_hidden) {
    $query .= '
                UNION
                SELECT g.group_id, uhg.user_id, IF(g.creator_id = uhg.user_id, 0, 2) AS user_status
                FROM event_groups g
                    JOIN user_hidden_group uhg ON uhg.group_id = g.group_id
                WHERE g.group_id = ?';
    $params[] = $group_id;
  }

  $query .= ') AS gr ON gr.user_id = u.id
            ORDER BY gr.user_status';

  if ($limit && $offset) {
    $query .= 'LIMIT ? OFFSET ?';
    $params[] = $limit;
    $params[] = $offset;
  }

  $stmt = $pdo->prepare($query);
  $stmt->execute($params);

  return $stmt->fetchAll();
}

function getUsersInGroupCount($group_id) {
  $pdo = getPDO();

  // trimmed down version of getUsersInGroup() query
  $stmt = $pdo->prepare('SELECT COUNT(1)
                        FROM users u
                        JOIN (SELECT ug.user_id
                            FROM event_groups g
                                JOIN user_in_group ug ON ug.group_id = g.group_id
                            WHERE g.group_id = ?
                            UNION
                            SELECT uhg.user_id
                            FROM event_groups g
                                JOIN user_hidden_group uhg ON uhg.group_id = g.group_id
                            WHERE g.group_id = ?) AS gr ON gr.user_id = u.id');
  $stmt->execute([$group_id, $group_id]);

  return $stmt->fetch()['COUNT(1)'];
}

function getUsersNotInGroup($group_id, $viewer, $query, $limit, $offset) {
  // TODO: exclude users who cannot see Event, so that group admin can't add them to group
  $pdo = getPDO();

  // select users not in group and users group is not hidden from
  $stmt = $pdo->prepare("SELECT u.id,
                                       u.username,
                                       u.email,
                                       fu.favorite_user_id IS NOT NULL as favorite
                        FROM users u
                            LEFT JOIN (SELECT *
                                      FROM user_in_group
                                      UNION
                                      SELECT *
                                      FROM user_hidden_group) as gr ON u.id = gr.user_id AND gr.group_id = ?
                            LEFT JOIN favorite_users fu ON fu.user_id = ? AND fu.favorite_user_id = u.id
                        WHERE u.username LIKE CONCAT('%', ?, '%') AND gr.group_id IS NULL
                        ORDER BY fu.favorite_user_id DESC, username
                        LIMIT ? OFFSET ?");
  $stmt->execute([$group_id, $viewer, $query, $limit, $offset]);

  return $stmt->fetchAll();
}

function getUsersNotInGroupCount($group_id, $viewer, $query) {
  $pdo = getPDO();

  $stmt = $pdo->prepare("SELECT COUNT(1)
                        FROM users u
                            LEFT JOIN (SELECT *
                                      FROM user_in_group
                                      UNION
                                      SELECT *
                                      FROM user_hidden_group) as gr ON u.id = gr.user_id AND gr.group_id = ?
                            LEFT JOIN favorite_users fu ON fu.user_id = ? AND fu.favorite_user_id = u.id
                        WHERE u.username LIKE CONCAT('%', ?, '%') AND gr.group_id IS NULL");
  $stmt->execute([$group_id, $viewer, $query]);

  return $stmt->fetch()['COUNT(1)'];
}

?>