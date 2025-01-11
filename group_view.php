<?php
require_once 'includes/session.php';
require_once 'includes/getters.php';
require_once 'includes/groups.php';

ensureLoggedIn();

$year = $_GET['year'];
$user_id = $_SESSION['user_id'];
$group_id = $_GET['group_id'];
$event_id = $_GET['event_id'];
$event = getEventById($event_id);
$group = getGroupById($group_id);
$group_pass = $group['group_pass'];
$in_group = checkUserInGroup($user_id, $group_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if ($in_group) { // leave group
    removeUserFromGroup($user_id, $group_id);
    $in_group = false;
  } else { // join group
    if ($group_pass === $_POST['group-pass']) {
      addUserToGroup($user_id, $group_id);
      $in_group = true;
    } else {
      $error_messages = ['Group pass is incorrect. Please try again.'];
      $in_group = false;
    }
  }
}
?>


<?php
$page_title = 'View Group';
$page_styles = ['styles/groups.css'];
include 'templates/main_header.php'
?>

<section class="content">
  <div class="two-items-apart">
    <a class="btn" href="event_view.php?event_id=<?php echo $event_id ?>&year=<?php echo $year ?>">Go back</a>
    <form id="form-leave-group" method="POST">
    <?php if ($in_group): ?>
      <button type="submit" id="btn-leave-group">Leave Group</button>
    <?php endif ?>
    </form>
  </div>
  <header>
    <h1><?php echo $event['name'] ?></h1>
    <h2><?php echo $group['group_name']; ?></h2>
  </header>
  <?php if ($in_group): ?>  <!-- User in group => can show content -->
    <header>
      <h2>Content...</h2>
    </header>
  <?php else: ?>  <!-- User NOT in group => JOIN before show content-->
    <?php if ($group_pass): ?> <!-- Group is PRIVATE => need GROUP PASS -->
      <section class="content group-center">
        <form method="POST">
          <h2>Group is private. You need its <span class="hover-pop-up" title="8 character long password. Ask group creator for it.">GROUP PASS</span> to join.</h2>
          <input id="input-group-pass" type="text" maxlength="8" name="group-pass" placeholder="Group pass" required>
          <button type="submit" id="btn-join-group">Join Group</button>
          <?php include 'templates/form_error.php' ?>
        </form>
      </section>
    <?php else: ?> <!-- Group is PUBLIC => can join freely -->
      <section class="content group-center">
        <form method="POST">
          <h2>Join to see more!</h2>
          <button type="submit" id="btn-join-group">Join Group</button>
        </form>
      </section>
    <?php endif?>
  <?php endif ?>
</section>


<?php
include 'templates/main_footer.php'
?>