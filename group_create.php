<?php
require_once 'includes/session.php';
require_once 'includes/db_events.php';
require_once 'includes/db_groups.php';

ensureLoggedIn();

$event_id = $_GET['event_id'];
$year = $_GET['year'];
$event = getEventById($event_id);
if (!$event || !$year) {
  header("Location: ./");
  exit;
}
$correct_date = strtotime(date('d M ', strtotime($event['date'])).$year);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $group_name = $_POST['group-name'];
  $meeting_time = $_POST['meeting-time'];
  $meeting_place = $_POST['meeting-place'];
  $money_goal = $_POST['money-goal'];
  $group_description = $_POST['group-description'] ?? null;
  $is_private = $_POST['is-private'] ?? false;

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
    $user_id = $user['id'];

    $result = createGroup(
      $user_id,
      $group_name,
      $money_goal,
      $meeting_time,
      $meeting_place,
      $group_description,
      $is_private,
    );

    if (!empty($result)) {
      $group_id = $result;

      attachGroupToEvent($group_id, $event_id, $year);
      addUserInGroup($user_id, $group_id);

      header("Location: group_view.php?group_id=$group_id");
      exit;
    }
  }
}
?>


<?php
$page_title = "Create Group";
$page_styles = ["styles/groups.css"];
include 'templates/main_header.php'
?>


<!-- attaching to event will show up in next years -->
<section class="content">
  <a class="btn" href="event_view.php?event_id=<?php echo $event_id ?>&year=<?php echo $year ?>"
  >&lt Go back</a>
  <h1><?php echo htmlspecialchars($event['name']) ?> | <?php echo date('d F Y, l', $correct_date) ?></h1>
  <h2>Create Group</h2>
  <section class="content group-form">
    <form class="form-group" method="POST">
      <input type="text" name="group-name" placeholder="<?php echo $user['username']?>'s group">
      <input type="time" name="meeting-time" value="09:00:00">
      <input type="text" name="meeting-place" placeholder="Place">
      <input type="number" min="0" name="money-goal" placeholder="Money goal: 0">
      <textarea type="text" name="group-description" placeholder="Description"></textarea>
      <div class="two-items">
        <input type="checkbox" id="is-private" name="is-private">
        <label for="is-private">make private</label>
      </div>
      <button type="submit" class="btn">Create</button>
      <?php include 'templates/form_error.php' ?>
    </form>
  </section>
</section>


<?php
include 'templates/main_footer.php'
?>