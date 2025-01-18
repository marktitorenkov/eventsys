<?php
require_once 'includes/session.php';
require_once 'includes/db_events.php';
require_once 'includes/db_groups.php';

ensureLoggedIn();

$year = $_GET['year'];
$user_id = $user['id'];
$group_id = $_GET['group_id'];
$event_id = $_GET['event_id'];
$event = getEventById($event_id);
$group = getGroupById($group_id);
$group_pass = $group['group_pass'];
$in_group = checkUserInGroup($user_id, $event_id, $group_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['form-join'])) { // join group

    if ($group_pass) { // if group is private
      if ($group_pass === $_POST['group-pass']) { // user provided password is correct
        addUserToGroup($user_id, $group_id);
        $in_group = true;
      } else { // user provided password is NOT correct
        $error_messages = ['Group pass is incorrect. Please try again.'];
        $in_group = false;
      }
    } else { // if group is public
      addUserToGroup($user_id, $group_id);
      $in_group = true;
    }
  }

  if (isset($_POST['form-leave'])) { // leave group
    removeUserFromGroup($user_id, $group_id);
    $in_group = false;
  }
}
?>


<?php
$page_title = 'View Group';
$page_styles = ['styles/groups.css'];
include 'templates/main_header.php'
?>

<section class="content">
  <div class="two-items between">
    <a class="btn" href="event_view.php?event_id=<?php echo $event_id ?>&year=<?php echo $year ?>">Go back</a>
    <?php if ($in_group): ?>
    <div>
      <form id="form-leave-group" method="POST">
        <button type="submit" name="form-leave" class="btn delete">Leave Group</button>
      </form>
      <?php if ($user_id === $group['creator_id']): ?>
        <a class="btn" href="group_edit.php?event_id=<?php echo $event_id ?>&group_id=<?php echo $group_id ?>&year=<?php echo $year ?>">Edit Group</a>
      <?php endif ?>
    </div>
    <?php endif ?>
  </div>
  <header>
    <h1><?php echo $event['name'] ?></h1>
    <h2><?php echo $group['group_name']; ?></h2>
  </header>
  <?php if ($in_group): ?>  <!-- User in group => can show content -->
    <header>
      <h2>...Chat...</h2>
    </header>
  <?php else: ?>  <!-- User NOT in group => JOIN before show content-->
  <section class="content group-center">
    <form method="POST">
    <?php if ($group_pass): ?> <!-- Group is PRIVATE => need GROUP PASS -->
      <h2>Group is private. You need its <span class="hover-pop-up" title="8 character long password. Ask group creator for it.">GROUP PASS</span> to join.</h2>
      <input id="input-group-pass" type="text" maxlength="8" name="group-pass" placeholder="Group pass" required>
    <?php else: ?>
      <h2>Join to see more!</h2>
    <?php endif ?>
      <button type="submit" name="form-join" class="btn create">Join Group</button>
      <?php include 'templates/form_error.php'?>
    </form>
  </section>
  <?php endif ?>
</section>


<?php
include 'templates/main_footer.php'
?>