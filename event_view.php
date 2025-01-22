<?php
require_once 'includes/session.php';
require_once 'includes/db_events.php';
require_once 'includes/db_groups.php';

ensureLoggedIn();

$event_id = $_GET['event_id'];
$year = $_GET['year'];
$event = getEventById($event_id);
$correct_date = strtotime(date('d M ', strtotime($event['date'])) . $year);
$groups = getGroupsByEventIdYear($user['id'], $event_id, $year);

$viewer_admin = $event['creator_id'] == $user['id'];

$hidden_select = getHiddenSelect($event_id);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_event'])) {
  if (!$viewer_admin) {
    $error_message = "You do not have permission to delete this event.";
  } else {
    deleteEventById($event_id);
    header("Location: index.php");
    exit();
  }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['modify_event'])) {
  if (!$viewer_admin) {
    $error_message = "You do not have permission to modify this event.";
  } else {
    $modified_name = $_POST['event_name'];
    $modified_date = $_POST['event_date'];
    $modified_description = $_POST['event_description'];
    
    $users_to_hide = $_POST['users_to_hide'] ?? [];
    modifyEvent($event_id, $modified_name, $modified_date, $modified_description, $users_to_hide);
    header("Location: event_view.php?event_id=$event_id&year=$year");
    exit();
  }
}
?>

<?php
$page_title = "View Event";
$page_styles = ["styles/groups.css"];
include 'templates/select_dynamic.php';
include 'templates/main_header.php';
?>

<section class="content">
  <header>
    <h1>View Event</h1>
  </header>

  <h3><?php echo $event['name']; ?> | <?php echo date('d F Y, l', $correct_date) ?></h3>
  <?php if ($event['creator_id']): ?>
    Created by <a href="user.php?id=<?php echo $event['creator_id'] ?>"><?php echo $event['creator_username'] ?></a>
  <?php endif ?>

  <p><?php echo $event['description'];?></p>

  <?php if (isset($error_message)): ?>
    <div style="color: red; font-weight: bold;"><?php echo $error_message; ?></div>
  <?php endif; ?>

  <?php if ($viewer_admin): ?>
    <button type="button" class="btn" id="modifyButton" onclick="toggleModifyForm()">Modify Event</button>

    <form method="POST" id="modifyForm" style="display:none;">
      <label for="event_name">Event Name:</label>
      <input type="text" name="event_name" id="event_name" value="<?php echo htmlspecialchars($event['name']); ?>" required>
      
      <label for="event_date">Event Date:</label>
      <input type="date" name="event_date" id="event_date" value="<?php echo date('Y-m-d', strtotime($event['date'])); ?>" required>
      
      <label for="event_description">Event Description:</label>
      <textarea name="event_description" id="event_description"><?php echo htmlspecialchars($event['description']); ?></textarea>

      <label >Hide from:
        <?php select_dynamic('users_to_hide', 'api/users_select.php', $hidden_select) ?>
      </label>

      <button type="submit" class="btn" name="modify_event">Save Changes</button>
      <button type="button" class="btn" onclick="toggleModifyForm()">Cancel</button>
    </form>
  <?php endif; ?>

  <?php if ($viewer_admin): ?>
    <button type="button" class="btn delete" id="deleteButton" onclick="confirmDelete()">Delete Event</button>

    <div id="deleteModal" style="display:none;">
      <p>Are you sure you want to delete this event?</p>
      <form method="POST">
        <button type="submit" class="btn delete" name="delete_event">Yes, Delete</button>
        <button type="button" class="btn" onclick="toggleDeleteModal()">Cancel</button>
      </form>
    </div>
  <?php endif; ?>

  <header>
    <h2>Groups</h2>
    <a class="btn" href="group_create.php?event_id=<?php echo $event_id?>&year=<?php echo $year ?>">Create Group</a>
  </header>

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
            echo '&#x1F512 ';
          }
          echo $group['group_name']
          ?>
          </h3>
          <p><a href="group_view.php?event_id=<?php echo $event_id ?>&group_id=<?php echo $group['group_id'] ?>&year=<?php echo $year ?>">View</a></p>
        </div>
        <div class="two-items between">
          <p>Money goal: <?php echo $group['money_goal'] ?></p>
        </div>
        <div class="two-items between">
          <p>Meeting time: <?php echo date('h:i:sa', strtotime($group['meeting_time'])) ?></p>
          <?php if ($group['meeting_place']): ?>
            <p>Meeting place: <?php echo $group['meeting_place'] ?></p>
          <?php endif ?>
        </div>
        <?php if($group['group_description']): ?>
          <div class="two-items between">
          <p>Description: <?php echo $group['group_description'] ?></p>
        </div>
        <?php endif ?>
      </li>
    <?php endforeach ?>
    </ul>
  <?php else: ?>
    <p>No groups created. Be the first!</p>
  <?php endif; ?>
</section>

<script>
function toggleModifyForm() {
  const form = document.getElementById('modifyForm');
  form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

function toggleDeleteModal() {
  const modal = document.getElementById('deleteModal');
  modal.style.display = modal.style.display === 'none' ? 'block' : 'none';
}

function confirmDelete() {
  toggleDeleteModal();
}
</script>

<?php
include 'templates/main_footer.php'
?>
