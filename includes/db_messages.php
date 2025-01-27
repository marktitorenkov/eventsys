<?php
require_once "db.php";

// Queries

function getMessages($viewer, $group_id, $time_after) {
  $pdo = getPDO();
  
  $stmt = $pdo->prepare('SELECT m.sender_id,
                                u.username AS sender_username,
                                m.sender_id = ? AS viewer_is_owner,
                                m.time,
                                m.content
                        FROM group_messages m
                        LEFT JOIN users u ON m.sender_id = u.id
                        WHERE m.group_id = ? AND m.time > ?
                        ORDER BY m.time ASC');

  $stmt->execute([$viewer, $group_id, $time_after]);

  return $stmt->fetchAll();
}

// Mutations

function storeMessage($viewer, $group_id, $content) {
  $content = trim($content);
  if (empty($content)) {
    return false;
  }

  $pdo = getPDO();

  $stmt = $pdo->prepare('INSERT INTO group_messages (sender_id, group_id, content)
                         VALUES (?, ?, ?)');

  $stmt->execute([$viewer, $group_id, $content]);

  return $stmt->rowCount() > 0;
}

?>
