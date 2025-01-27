<?php
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/db_messages.php';

ensureLoggedIn();

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $res = storeMessage($user['id'], $_POST['group_id'], $_POST['content']);

  echo json_encode($res);
  exit;
}

// Release the session lock in this connection before blocking,
// so that other requests can be processed at the same time.
session_write_close();

function no_messages($messages) {
  global $config;
  if (empty($messages)) {
    usleep($config['polling_interval'] * 1_000_000);
    return true;
  }
  return false;
}

function has_time($elapsed) {
  global $config;
  return $elapsed < $config['polling_limit'];
}

$start = time();
$messages = [];
do {
  $messages = getMessages($user['id'], $_GET['group_id'], $_GET['time_after'] ?? '1-1-1');
} while (no_messages($messages) && has_time(time() - $start));

echo json_encode($messages);
?>
