<?php
require_once 'includes/session.php';
require_once 'includes/db_polls.php';

ensureLoggedIn();

$poll_id = $_GET['poll_id'] ?? $_POST['poll_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['poll_create'])) {
    $poll_id = createPoll($user['id'], $_POST['group_id'], $_POST['poll_title']);
  } else {
    if (!$poll_id) exit;

    if (isset($_POST['option_add'])) {
      createOption($poll_id, $_POST['option_title'] ?? '');
    } else {
      updateVotes($user['id'], $poll_id, $_POST['options'] ?? []);
    }
  }
}

if (!$poll_id) exit;
$polls = getPollsById($poll_id);
?>

<?php include 'templates/polls_template.php' ?>
