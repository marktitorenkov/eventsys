<?php
require_once "db.php";

// Queries

function getGroupPolls($group_id) {
  return _getPollsFull('WHERE p.group_id = ?', [$group_id]);
}

function getPollsById($poll_id) {
  return _getPollsFull('WHERE p.poll_id = ?', [$poll_id]);
}

function _getPollsFull($where = '', $params = []) {
  $pdo = getPDO();

  $stmt = $pdo->prepare('SELECT p.poll_id,
                                p.group_id,
                                p.creator_id,
                                p.poll_title,
                                o.option_id,
                                o.option_title,
                                v.user_id
                        FROM polls p
                        LEFT JOIN poll_options o ON p.poll_id = o.poll_id
                        LEFT JOIN poll_votes v ON o.option_id = v.option_id
                        '.$where.'
                        ORDER BY p.poll_id, o.option_id');

  $stmt->execute($params);
  $rows = $stmt->fetchAll();

  $polls = [];
  foreach ($rows as $row) {
    $poll_id = $row['poll_id'];
    if (!isset($polls[$poll_id])) {
      $polls[$poll_id] = array_merge($row, [
        'vote_count' => 0,
        'options' => [],
      ]);
    }

    $option_id = $row['option_id'] ?? null;
    if (!isset($polls[$poll_id]['options'][$option_id]) && $option_id !== null) {
      $polls[$poll_id]['options'][$option_id] = array_merge($row, [
        'vote_count' => 0,
        'votes' => [],
      ]);
    }

    $user_id = $row['user_id'] ?? null;
    if ($user_id !== null) {
      $polls[$poll_id]['vote_count']++;
      $polls[$poll_id]['options'][$option_id]['vote_count']++;
      $polls[$poll_id]['options'][$option_id]['votes'][] = $user_id;
    }
  }

  return $polls;
}


// Mutations

function createPoll($viewer, $group_id, $title) {
  $pdo = getPDO();

  $stmt = $pdo->prepare('INSERT IGNORE INTO polls (group_id, creator_id, poll_title) VALUES (?, ?, ?)');
  $stmt->execute([$group_id, $viewer, $title]);
  
  return $pdo->lastInsertId();
}

function createOption($poll_id, $title) {
  $pdo = getPDO();

  $stmt = $pdo->prepare('INSERT IGNORE INTO poll_options (poll_id, option_title) VALUES (?, ?)');
  $stmt->execute([$poll_id, $title]);

  return $pdo->lastInsertId();
}

function updateVotes($viewer, $poll_id, $options) {
  $pdo = getPDO();
  $pdo->beginTransaction();

  $stmt = $pdo->prepare('DELETE v
                         FROM poll_votes v
                         JOIN poll_options o ON v.option_id = o.option_id
                         WHERE o.poll_id = ? AND v.user_id = ?');
  $stmt->execute([$poll_id, $viewer]);

  foreach ($options as $opt) {
    $stmt = $pdo->prepare('INSERT INTO poll_votes (option_id, user_id) VALUES (?, ?)');
    $stmt->execute([$opt, $viewer]);
  }

  $pdo->commit();
}

?>
