<?php
require_once 'includes/session.php';
require_once 'includes/db_groups.php';

ensureLoggedIn();

$event_id = $_GET['event_id'];
$group_id = $_GET['group_id'];
$group = getGroupById($group_id);

if (!checkUserInGroup($user['id'], $event_id, $group_id)) {
  header('Location: group_view.php?event_id=' . $event_id . '&group_id=' . $group_id . '&year=' . $_GET['year']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['update-group'])) {
    $group_name = $_POST['group-name'];
    $meeting_time = $_POST['meeting-time'];
    $meeting_place = $_POST['meeting-place'];
    $money_goal = $_POST['money-goal'];
    $group_description = $_POST['group-description'];
    $is_private = $_POST['is-private'];

    updateGroup(
      $group_id,
      $group_name,
      $money_goal,
      $meeting_time,
      $meeting_place,
      $group_description,
      $group['group_pass'],
      $is_private
    );

    header('Refresh:0');
    exit;
  }

  if (isset($_POST['delete-group'])) {
    deleteGroup($group_id, $user['id']);

    header('Location: event_view.php?event_id=' . $event_id . '&year=' . $_GET['year']);
    exit;
  }
}
?>


<?php
$page_title = 'Edit Group';
$page_styles = ['styles/groups.css'];
$page_scripts = ['javascript/group_edit.js'];
include 'templates/main_header.php'
?>

<section class="content">
  <header>
    <a class="btn" href="group_view.php?event_id=<?php echo $event_id ?>&group_id=<?php echo $group_id ?>&year=<?php echo $_GET['year'] ?>">Go back</a>
    <h1><?php echo $group['group_name'] ?></h1>
  </header>
  <section class="content group-center">
    <form method="POST">
      <label for="group-name">Group name:</label>
      <input type="text" id="group-name" name="group-name" maxlength="50" value="<?php echo $group['group_name'] ?>">
      <label for="meeting-time">Meeting time:</label>
      <input type="time" id="meeting-time" name="meeting-time" value="<?php echo $group['meeting_time'] ?>">
      <label for="meeting-place">Meeting place:</label>
      <input type="text" id="meeting-place" name="meeting-place" maxlength="50" value="<?php echo $group['meeting_place'] ?>">
      <label for="money-goal">Money goal:</label>
      <input type="number" id="money-goal" name="money-goal" min="0" value="<?php echo $group['money_goal'] ?>">
      <label for="group-description">Group description:</label>
      <textarea type="text" id="group-description" name="group-description" maxlength="250"><?php echo $group['group_description'] ?></textarea>
      <label>Group pass: <?php if ($group['group_pass']) echo $group['group_pass']; else echo 'Group is public.'; ?></label>
      <div class="form-checkbox-wrapper">
        <input type="checkbox" id="is-private" name="is-private" <?php if ($group['group_pass']) {echo 'checked';} ?>>
        <label for="is-private">make private</label>
      </div>
      <button type="submit" class="btn" name="update-group">Edit</button>
      <button type="submit" class="btn delete" id="btn-delete-group" name="delete-group">DELETE GROUP</button>
    </form>
  </section>
</section>


<?php
include 'templates/main_footer.php'
?>