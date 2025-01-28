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

$correct_date = strtotime(date('d M ', strtotime($event['date'])) . $year);
$groups = getGroupsByEventIdYear($user['id'], $event_id, $year);

$viewer_admin = $event['creator_id'] == $user['id'];

$hidden_select = getHiddenSelect($event_id);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['delete_event'])) {
    if (!$viewer_admin) {
      $error_message = "You do not have permission to delete this event.";
    } else {
      deleteEventById($event_id);
      header("Location: ./");
      exit;
    }
  }

  if (isset($_POST['modify_event'])) {
    if (!$viewer_admin) {
      $error_message = "You do not have permission to modify this event.";
    } else {
      $modified_name = $_POST['event_name'];
      $modified_date = $_POST['event_date'];
      $modified_description = $_POST['event_description'];
      
      $users_to_hide = $_POST['users_to_hide'] ?? [];
      modifyEvent($event_id, $modified_name, $modified_date, $modified_description, $users_to_hide);
      header("Location: ".$_SERVER['REQUEST_URI']);
      exit;
    }
  }
}
?>

<?php
$page_title = "View Event";
$page_styles = ["styles/groups.css"];
$page_scripts = ["javascript/event_view.js"];
include 'templates/select_dynamic.php';
include 'templates/main_header.php';
?>

<section class="content">
  <header class="space-betwen">
    <div>
      <h2><?php echo htmlspecialchars($event['name']) ?> | <?php echo date('d F Y, l', $correct_date) ?></h2>
      <?php if ($event['creator_id']): ?>
        Created by <a href="user.php?id=<?php echo $event['creator_id'] ?>"><?php echo $event['creator_username'] ?></a>
      <?php endif ?>
    </div>
    <?php if ($viewer_admin): ?>
    <div>
      <button type="button" class="btn" id="modifyButton" onclick="toggleModifyForm()">Modify Event</button>
      <button type="button" class="btn delete" id="deleteButton" onclick="toggleDeleteModal()">Delete Event</button>
    </div>
    <?php endif; ?>
  </header>

  <p><?php echo htmlspecialchars($event['description'] ?? '');?></p>

  <?php if (isset($error_message)): ?>
    <div style="color: red; font-weight: bold;"><?php echo $error_message; ?></div>
  <?php endif; ?>

  <?php if ($viewer_admin): ?>
    <form method="POST" id="modifyForm" style="display:none;">
      <label for="event_name">Event Name:</label>
      <input type="text" name="event_name" id="event_name" value="<?php echo htmlspecialchars($event['name']); ?>" required>
      
      <label for="event_date">Event Date:</label>
      <input type="date" name="event_date" id="event_date" value="<?php echo date('Y-m-d', strtotime($event['date'])); ?>" required>
      
      <label for="event_description">Event Description:</label>
      <textarea name="event_description" id="event_description"><?php echo htmlspecialchars($event['description'] ?? ''); ?></textarea>

      <label >Hide from:
        <?php select_dynamic('users_to_hide', 'api/users_select.php', $hidden_select) ?>
      </label>

      <button type="submit" class="btn" name="modify_event">Save Changes</button>
      <button type="button" class="btn" onclick="toggleModifyForm()">Cancel</button>
    </form>

    <div id="deleteModal" style="display:none;">
      <p>Are you sure you want to delete this event?</p>
      <form method="POST">
        <button type="submit" class="btn delete" name="delete_event">Yes, Delete</button>
        <button type="button" class="btn" onclick="toggleDeleteModal()">Cancel</button>
      </form>
    </div>
  <?php endif; ?>

  <header class="space-betwen">
    <h2>Groups</h2>
    <a class="btn" href="group_create.php?event_id=<?php echo $event_id?>&year=<?php echo $year ?>">Create Group</a>
  </header>

  <!-- Show groups to user, if event isn't users birthday -->
  <?php if ($user['birthday_event'] != $event_id): ?>
    <?php if (!empty($groups)): ?>
    <ul class="group-list">
    <?php
    foreach ($groups as $group):
    ?>
      <li>
        <div class="two-items between">
          <h3>
          <?php
          if ($group['group_pass']) {
            echo '🔒 ';
          }
          echo htmlspecialchars($group['group_name'])
          ?>
          </h3>
          <p><a href="group_view.php?group_id=<?php echo $group['group_id'] ?>">View</a></p>
        </div>
        <div class="two-items between">
          <p>Money goal: <?php echo $group['money_goal'] ?></p>
        </div>
        <div class="two-items between">
          <p>Meeting time: <?php echo date('h:i:sa', strtotime($group['meeting_time'])) ?></p>
          <?php if ($group['meeting_place']): ?>
            <p>Meeting place: <?php echo htmlspecialchars($group['meeting_place'] ?? '') ?></p>
          <?php endif ?>
        </div>
        <?php if($group['group_description']): ?>
          <div class="two-items between">
          <p>Description: <?php echo htmlspecialchars($group['group_description'] ?? '') ?></p>
        </div>
        <?php endif ?>
      </li>
    <?php endforeach ?>
    </ul>
    <?php else: ?>
    <p>No groups created. Be the first!</p>
    <?php endif; ?>
  <?php else: ?>
    <p>Your birthday is a surprise! <span style="font-weight: bold;"><?php echo count($groups) ?></span> group/s are hidden from you.</p>
  <?php endif; ?>
</section>

<?php
include 'templates/main_footer.php'
?>
