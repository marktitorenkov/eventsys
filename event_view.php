<?php
require_once 'includes/session.php';
require_once 'includes/db_events.php';
require_once 'includes/db_groups.php';

ensureLoggedIn();

$event_id = $_GET['event_id'];
$year = $_GET['year'];
$event = getEventById($event_id);
$correct_date = strtotime(date('d M ', strtotime($event['date'])) . $year);
$groups = getGroupsByEventIdYear($event_id, $year);

// Check if the logged-in user is the admin of the event
$current_user_id = $_SESSION['user_id']; // Assuming the user ID is stored in session

// Handle Delete request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_event'])) {
    // Check if the current user is the admin of the event
    if ($event['admin'] != $current_user_id) {
        // If not the admin, show an error message
        $error_message = "You do not have permission to delete this event.";
    } else {
        // If the user is the admin, proceed with the deletion
        deleteEventById($event_id);
        header("Location: index.php"); // Redirect to events list after delete
        exit();
    }
}

// Handle Modify request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['modify_event'])) {
    // Check if the current user is the admin of the event
    if ($event['admin'] != $current_user_id) {
        // If not the admin, show an error message
        $error_message = "You do not have permission to modify this event.";
    } else {
        $modified_name = $_POST['event_name'];
        $modified_date = $_POST['event_date']; // Get the new date from the form
        $modified_description = $_POST['event_description'];
        
        modifyEvent($event_id, $modified_name, $modified_date, $modified_description);
        header("Location: event_view.php?event_id=$event_id&year=$year"); // Reload the page after modifying
        exit();
    }
}

?>

<?php
$page_title = "View Event";
$page_styles = ["styles/groups.css"];
include 'templates/main_header.php';
?>

<section class="content">
  <header>
    <div>
      <h1>View Event</h1>
    </div>
    <a class="btn" href="group_create.php?event_id=<?php echo $event_id?>&year=<?php echo $year ?>">Create Group</a>
  </header>

  <!-- Event Info Section -->
  <h2><?php echo $event['name']; ?> | <?php echo date('d F Y, l', $correct_date) ?>
  <br><?php echo $event['description'];?></h2>

  <!-- Display Error Message if Not Admin -->
  <?php if (isset($error_message)): ?>
    <div style="color: red; font-weight: bold;"><?php echo $error_message; ?></div>
  <?php endif; ?>

  <!-- Modify Event Form (Visible only when modifying and admin) -->
  <?php if ($event['admin'] == $current_user_id): ?>
    <form method="POST" id="modifyForm" style="display:none;">
      <label for="event_name">Event Name:</label>
      <input type="text" name="event_name" id="event_name" value="<?php echo htmlspecialchars($event['name']); ?>" required>
      
      <label for="event_date">Event Date:</label>
      <input type="date" name="event_date" id="event_date" value="<?php echo date('Y-m-d', strtotime($event['date'])); ?>" required>
      
      <label for="event_description">Event Description:</label>
      <textarea name="event_description" id="event_description" required><?php echo htmlspecialchars($event['description']); ?></textarea>
      
      <button type="submit" class="btn" name="modify_event">Save Changes</button>
      <button type="button" class="btn" onclick="toggleModifyForm()">Cancel</button>
    </form>

    <button class="btn" id="modifyButton" onclick="toggleModifyForm()">Modify Event</button>
  <?php else: ?>
    <button class="btn" id="modifyButton" disabled>Modify Event (You are not the admin)</button>
  <?php endif; ?>

  <!-- Delete Event Button -->
  <?php if ($event['admin'] == $current_user_id): ?>
    <button class="btn" id="deleteButton" onclick="confirmDelete()">Delete Event</button>
  <?php else: ?>
    <button class="btn" id="deleteButton" disabled>Delete Event (You are not the admin)</button>
  <?php endif; ?>

  <!-- Confirmation Modal for Deleting Event -->
  <div id="deleteModal" style="display:none;">
    <p>Are you sure you want to delete this event?</p>
    <form method="POST">
      <button type="submit" class="btn" name="delete_event">Yes, Delete</button>
      <button type="button" class="btn" onclick="toggleDeleteModal()">Cancel</button>
    </form>
  </div>

  <?php if (!empty($groups)): ?>
    <ul class="group-list">
    <?php
    foreach ($groups as $group):
    ?>
      <li>
        <div class="two-items-apart">
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
        <div class="two-items-apart">
          <p>Money goal: <?php echo $group['money_goal'] ?></p>
        </div>
        <div class="two-items-apart">
          <p>Meeting time: <?php echo date('h:i:sa', strtotime($group['meeting_time'])) ?></p>
          <?php if ($group['meeting_place']): ?>
            <p>Meeting place: <?php echo $group['meeting_place'] ?></p>
          <?php endif ?>
        </div>
        <div class="two-items-apart">
        <?php if ($group['group_description']): ?>
          <p>Description: <?php echo $group['group_description'] ?></p>
        <?php endif ?>
        </div>
      </li>
    <?php endforeach ?>
    </ul>
  <?php else: ?>
    <p>No groups created. Be the first!</p>
  <?php endif; ?>
</section>

<script>
// Toggle the Modify Event form visibility
function toggleModifyForm() {
    const form = document.getElementById('modifyForm');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

// Show/Hide Delete Confirmation Modal
function toggleDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.style.display = modal.style.display === 'none' ? 'block' : 'none';
}

// Confirm Delete Event
function confirmDelete() {
    toggleDeleteModal(); // Show confirmation dialog
}
</script>

<?php
include 'templates/main_footer.php'
?>
