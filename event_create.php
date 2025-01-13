<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

ensureLoggedIn();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $date=$_POST['date'];
  $description = $_POST['description'];
  $recurring = isset($_POST['recurring']) ? true : false;

  $error_messages = array();

  if (empty($name)) {
    $name = $user['username'].'s event';
  } elseif (strlen($name) > 50) {
    $error_messages[] = 'Event name is too long, should be less than 50 characters.';
  }

  if (!empty($group_description) && strlen($group_description) > 250) {
    $error_messages[] = 'Description is too long, should be less than 250 characters.';
  }


  if (empty($error_messages)) {
    $result = createEvent(
      $_SESSION['user_id'],
      true,
      $name,
      $date,
      $description,
      $recurring
    );

    if (!empty($result)) {
      $event_id= $result;

      header('Location: event_view.php?event_id=' . $event_id . '&year=' . $year);
      exit;
    }
  }
}
?>

<?php
$page_title = "Create Event";
include 'templates/main_header.php'
?>


<!-- attaching to event will show up in next years -->
<section class="content">
  <h1>Create Event</h1>
  <section class="content create-event">
    <form id="form-create-event" method="POST">
      <input type="text" name="name" placeholder="<?php echo $user['username']?>'s event" required>
      
      <!-- Change input type to date -->
      <input type="date" name="date" value="" required>
      
      <textarea form="form-create-event" type="text" name="description" placeholder="Description"></textarea>
      
      <div class="form-checkbox-wrapper">
        <input type="checkbox" id="recurring" name="recurring">
        <label for="recurring">Is Repeated</label>
      </div>
      
      <button type="submit">Create</button>
      
      <!-- Include form error template if exists -->
      <?php include 'templates/form_error.php'; ?>
    </form>
  </section>
</section>

<?php
include 'templates/main_footer.php'
?>