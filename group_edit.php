<?php
require_once 'includes/session.php';
require_once 'includes/getters.php';
require_once 'includes/groups.php';

ensureLoggedIn();

$event_id = $_GET['event_id'];
$group_id = $_GET['group_id'];
$group = getGroupById($group_id);

if (!checkUserInGroup($_SESSION['user_id'], $event_id, $group_id)) {
  header('Location: group_view.php?event_id=' . $event_id . '&group_id=' . $group_id . '&year=' . $_GET['year']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['update-group-pass'])) {
    updateGroupPass($group_id, $_POST['is-private']);
  }
  elseif (isset($_POST['update-group-name'])) {
    updateGroupName($group_id, $_POST['new-group-name']);
  }
  elseif (isset($_POST['update-meeting-time'])) {
    updateGroupMeetingTime($group_id, $_POST['new-meeting-time']);
  }
  elseif (isset($_POST['update-meeting-place'])) {
    updateGroupMeetingPlace($group_id, $_POST['new-meeting-place']);
  }
  elseif (isset($_POST['clear-meeting-place'])) {
    updateGroupMeetingPlace($group_id, null);
  }
  elseif (isset($_POST['update-money-goal'])) {
    updateGroupMoneyGoal($group_id, $_POST['new-money-goal']);
  }
  elseif (isset($_POST['clear-money-goal'])) {
    updateGroupMoneyGoal($group_id, null);
  }
  elseif (isset($_POST['update-group-description'])) {
    updateGroupDescription($group_id, $_POST['new-group-description']);
  }
  elseif (isset($_POST['clear-group-description'])) {
    updateGroupDescription($group_id, null);
  }

  header("Refresh:0");
}
?>


<?php
$page_title = 'Edit Group';
$page_styles = ['styles/groups.css'];
include 'templates/main_header.php'
?>

<section class="content">
  <header>
    <a class="btn" href="group_view.php?event_id=<?php echo $event_id ?>&group_id=<?php echo $group_id ?>&year=<?php echo $_GET['year'] ?>">Go back</a>
    <h1><?php echo $group['group_name'] ?></h1>
  </header>
  <section class="content group-center">
    <form method="POST">
      <p>Current group pass: <?php echo $group['group_pass'] ?></p>
      <div class="form-checkbox-wrapper">
        <input type="checkbox" id="is-private" name="is-private" <?php if ($group['group_pass']) {echo 'checked';} ?>>
        <label for="is-private">make private</label>
      </div>
      <div><button type="submit" name="update-group-pass">Edit</button></div>
    </form>
    <form method="POST">
      <p>Current group name: <?php echo $group['group_name'] ?></p>
      <input type="text" name="new-group-name" maxlength="50" placeholder="New group name" required>
      <div><button type="submit" name="update-group-name">Edit</button></div>
    </form>
    <form method="POST">
      <p>Current meeting time: <?php echo $group['meeting_time'] ?></p>
      <input type="time" name="new-meeting-time" value="09:00:00" required>
      <div><button type="submit" name="update-meeting-time">Edit</button></div>
    </form>
    <form method="POST">
      <p>Current meeting place: <?php echo $group['meeting_place'] ?></p>
      <input type="text" name="new-meeting-place" maxlength="50" placeholder="New meeting place" required>
      <div class="two-items-apart">
        <button type="submit" name="update-meeting-place">Edit</button>
        <button class="btn group-clear" type="submit" name="clear-meeting-place" formnovalidate>Clear</button>
      </div>
    </form>
    <form method="POST">
      <p>Current money goal: <?php echo $group['money_goal'] ?></p>
      <input type="number" name="new-money-goal" min="0" placeholder="New money goal" required>
      <div class="two-items-apart">
        <button type="submit" name="update-money-goal">Edit</button>
        <button class="btn group-clear" type="submit" name="clear-money-goal" formnovalidate>Clear</button>
      </div>
    </form>
    <form method="POST">
      <p>Current description: <?php echo $group['group_description'] ?></p>
      <textarea type="text" name="new-group-description" maxlength="250" placeholder="New description" required></textarea>
      <div class="two-items-apart">
        <button type="submit" name="update-group-description">Edit</button>
        <button class="btn group-clear" type="submit" name="clear-group-description" formnovalidate>Clear</button>
      </div>
    </form>
  </section>
</section>


<?php
include 'templates/main_footer.php'
?>