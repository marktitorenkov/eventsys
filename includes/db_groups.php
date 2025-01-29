<?php
require_once 'db.php';

// Queries

function getGroupById($group_id) {
  $pdo = getPDO();
  
  $stmt = $pdo->prepare('SELECT g.*, eg.event_id, eg.year
                        FROM `event_groups` g
                        JOIN `event_to_group` eg ON g.group_id = eg.group_id
                        WHERE g.group_id = ?');
  $stmt->execute([$group_id]);
  
  return $stmt->fetch();
}

function getMemberCount($group_id) {
  $pdo = getPDO();
  
  $stmt = $pdo->prepare('SELECT COUNT(1)
                        FROM `user_in_group` g
                        WHERE g.group_id = ?');
  $stmt->execute([$group_id]);
  
  return $stmt->fetch()['COUNT(1)'];
}

function getJoinedGroups($viewer, $user) {
  $pdo = getPDO();

  $stmt = $pdo->prepare('SELECT g.*,
                                e.name AS event_name
                        FROM `event_groups` g
                        JOIN `event_to_group` eg ON g.group_id = eg.group_id
                        JOIN `events` e ON eg.event_id = e.event_id
                        JOIN `user_in_group` ug ON g.group_id = ug.group_id AND ug.user_id = ? -- user
                        LEFT JOIN `users` u ON u.birthday_event = e.event_id AND u.id = ? -- viewer
                        LEFT JOIN `user_hidden_event` uhe ON e.event_id = uhe.event_id AND uhe.user_id = ? -- viewer
                        LEFT JOIN `user_hidden_group` uhg ON g.group_id = uhg.group_id AND uhg.user_id = ? -- viewer
                        WHERE u.id IS NULL AND uhe.user_id IS NULL AND uhg.user_id IS NULL');
  $stmt->execute([$user, $viewer, $viewer, $viewer]);

  return $stmt->fetchAll();
}

function getGroupsByEventIdYear($user_id, $event_id, $year) {
  $pdo = getPDO();
  
  // get groups for given Event and Year
  // exclude groups that are hidden for given User
  $stmt = $pdo->prepare('SELECT g.group_id,
                                g.group_name,
                                g.money_goal,
                                g.meeting_time,
                                g.meeting_place,
                                g.group_description,
                                g.group_pass,
                                g.creator_id,
                                u.id AS creator_name
                          FROM event_to_group AS eg
                          JOIN event_groups g ON eg.group_id = g.group_id
                          JOIN users u ON g.creator_id = u.id
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

function deleteGroup($group_id, $user_id, $pdo = null) {
  $pdo = $pdo ?? getPDO();

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

function leaveGroup($user_id, $group_id) {
  $exists = true;

  $pdo = getPDO();
  $pdo->beginTransaction();

  removeUserFromGroup($user_id, $group_id, $pdo);

  // get all users in group, excluding hidden
  $users_in_group = getUsersInGroup($group_id, false, null, null, $pdo);
  if (count($users_in_group) > 0) {
    // chose a random user for new admin
    $new_admin = $users_in_group[array_rand($users_in_group)]['user_id'];
    changeGroupOwner($group_id, $new_admin, $pdo);
  } else { // otherwise, delete group
    deleteGroup($group_id, $user_id, $pdo);
    $exists = false;
  }

  $pdo->commit();

  return $exists;
}

function changeGroupOwner($group_id, $new_admin, $pdo = null) {
  $pdo = $pdo ?? getPDO();
  $stmt = $pdo->prepare('UPDATE event_groups
                         SET creator_id = ?
                         WHERE group_id = ?');
  $stmt->execute([$new_admin, $group_id]);
}

function removeUserFromGroup($user_id, $group_id, $pdo = null) {
  $pdo = $pdo ?? getPDO();

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

function updateGroup($group_id, $creator_id, $group_name, $money_goal, $time, $place, $description, $group_pass, $is_private) {
  $pdo = getPDO();

  $meeting_time = date('G:i:s', strtotime($time));
  $meeting_place = empty($place) ? null : $place;
  $group_description = empty($description) ? null : $description;
  if (!$group_pass && $is_private) { // group is public and request to make private
    $group_pass = generateRandomString();
  } elseif ($group_pass && !$is_private) { // group is private and request to make public
    $group_pass = null;
  } // other 2 cases, we keep group_pass as is

  $stmt = $pdo->prepare('UPDATE event_groups
                        SET creator_id = ?, group_name = ?, money_goal = ?, meeting_time = ?, meeting_place = ?, group_description = ?, group_pass = ?
                        WHERE group_id = ?');
  $stmt->execute([$creator_id, $group_name, $money_goal, $meeting_time, $meeting_place, $group_description, $group_pass, $group_id]);
}

function _getUsersInGroupQuery(&$params, $group_id, $include_hidden) {
  // Inner Table: 'Users In Group' Union 'Users Hidden From Group'
  // Outer Table: adds username and email to result from Inner Table
  // user_status: 0 = group admin / 1 = user in group / 2 = group hidden from user
  $query = '
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

  return $query;
}

function getUsersInGroup($group_id, $include_hidden, $limit = null, $offset = null, $pdo = null) {
  $pdo = $pdo ?? getPDO();
  
  $params = [$group_id];
  $query = 'SELECT u.username, u.email, gr.user_id, gr.user_status'
            ._getUsersInGroupQuery($params, $group_id, $include_hidden);

  if (isset($limit) && isset($offset)) {
    $query .= '
    LIMIT ? OFFSET ?';
    $params[] = $limit;
    $params[] = $offset;
  }

  $stmt = $pdo->prepare($query);
  $stmt->execute($params);

  return $stmt->fetchAll();
}

function getUsersInGroupCount($group_id, $include_hidden) {
  $pdo = getPDO();

  $params = [$group_id];
  $query = 'SELECT COUNT(1)'
            ._getUsersInGroupQuery($params, $group_id, $include_hidden);

  $stmt = $pdo->prepare($query);
  $stmt->execute($params);

  return $stmt->fetch()['COUNT(1)'];
}

function _getUsersNotInGroupQuery() {
  return "
          FROM users u
          LEFT JOIN (SELECT *
                    FROM user_in_group
                    UNION
                    SELECT *
                    FROM user_hidden_group) as gr ON u.id = gr.user_id AND gr.group_id = ?
          LEFT JOIN user_hidden_event uhe ON uhe.user_id = u.id AND uhe.event_id = ?
          LEFT JOIN favorite_users fu ON fu.user_id = ? AND fu.favorite_user_id = u.id
          WHERE u.username LIKE CONCAT('%', ?, '%') AND gr.group_id IS NULL AND uhe.user_id IS NULL AND u.birthday_event != ?
          ";
}

function getUsersNotInGroup($event_id, $group_id, $viewer, $query, $limit, $offset) {
  $pdo = getPDO();

  // select users not in group and users group is not hidden from
  // exclude users that cannot see event, which group is attached to
  $stmt = $pdo->prepare("SELECT u.id,
                                u.username,
                                u.email,
                                fu.favorite_user_id IS NOT NULL as favorite"
                        ._getUsersNotInGroupQuery().
                        "ORDER BY fu.favorite_user_id DESC, username
                        LIMIT ? OFFSET ?");
  $stmt->execute([$group_id, $event_id, $viewer, $query, $event_id, $limit, $offset]);

  return $stmt->fetchAll();
}

function getUsersNotInGroupCount($event_id, $group_id, $viewer, $query) {
  $pdo = getPDO();

  $stmt = $pdo->prepare("SELECT COUNT(1) "._getUsersNotInGroupQuery());
  $stmt->execute([$group_id, $event_id, $viewer, $query, $event_id]);

  return $stmt->fetch()['COUNT(1)'];
}

?>