<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

ensureLoggedIn();

$user = getUserById($_SESSION['user_id']);
$event_id = $_GET['event_id'];
$event = getEventById($event_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $group_name = $_POST['group-name'];
  $meeting_time = $_POST['meeting-time'];
  $meeting_place = $_POST['meeting-place'];
  $money_goal = $_POST['money-goal'];
  $group_description = $_POST['description'];
  $password = $_POST['password'];

  $error_messages = array();

  if (empty($group_name)) {
    $group_name = $user['username'].'s group';
  } elseif (strlen($group_name) > 50) {
    $error_messages[] = 'Group name is too long, should be less than 50 characters.';
  }

  if (!empty($meeting_place) && strlen($meeting_place) > 50) {
    $error_messages[] = 'Meeting place is too long, should be less than 50 characters.';
  }

  if (!empty($group_description) && strlen($group_description) > 250) {
    $error_messages[] = 'Description is too long, should be less than 250 characters.';
  }

  if (empty($error_messages)) {
    $result = createGroup(
      $_SESSION['user_id'],
      $group_name,
      $money_goal,
      $meeting_time,
      $meeting_place,
      $description,
      $password
    );

    if (!empty($result)) {
      $group_id = $result;

      attachGroupToEvent($group_id, $event_id);

      header('Location: group_view.php?event_id=' . $event_id . '&group_id=' . $group_id);
      exit;
    }
  }
}
?>


<?php
$page_title = "Create Group";
include 'templates/main_header.php'
?>


<!-- attaching to event will show up in next years -->
<section class="content">
  <a href="event_view.php?event_id=<?php echo $event_id ?>">Go back</a>
  <h1><?php echo $event['name']?> | <?php echo date('d F Y, l', $event['date']) ?></h1>
  <h2>Create Group</h2>
  <section class="content create-group">
    <form id="form-create-group" method="POST">
      <input type="text" name="group-name" placeholder="<?php echo $user['username']?>'s group">
      <input type="time" name="meeting-time" value="09:00:00">
      <input type="text" name="meeting-place" placeholder="Place">
      <input type="number" min="0" name="money-goal" placeholder="Money goal: 0">
      <textarea form="form-create-group" type="text" name="description" placeholder="Description"></textarea>
      <input type="password" name="password" placeholder="password: optional">
      <button type="submit">Create</button>
      <?php include 'templates/form_error.php' ?>
    </form>
  </section>
</section>


<?php
include 'templates/main_footer.php'
?>