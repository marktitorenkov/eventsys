<?php
require_once 'includes/session.php';
require_once 'includes/db_events.php';

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

  $users_to_hide = $_POST['users_to_hide'] ?? [];
  if (empty($error_messages)) {
    $result = createEvent(
      $user['id'],
      $name,
      $date,
      $description,
      $recurring,
      $users_to_hide
    );

    if (!empty($result)) {
      $event_id= $result;

      header('Location: event_view.php?event_id=' . $event_id . '&year=' . date('Y', strtotime(($date))));
      exit;
    }
  }
}
?>

<?php
$page_title = "Create Event";
include 'templates/select_dynamic.php';
include 'templates/main_header.php'
?>


<section class="content">
  <h1>Create Event</h1>
  <section class="content create-event">
    <form method="POST">
      <label>
        Name:
        <input type="text" name="name" placeholder="<?php echo $user['username']?>'s event" required>
      </label>

      <label>
        Date:
        <input type="date" name="date" value="" required>
      </label>

      <label>
        Description:
        <textarea type="text" name="description" placeholder="Description"></textarea>
      </label>

      <label>
        <input type="checkbox" name="recurring">
        Is Repeated
      </label>

      <label >Hide from:
        <?php select_dynamic('users_to_hide','api/users_select.php', []) ?>
      </label>
      <button class="btn" type="submit">Create</button>

      <?php include 'templates/form_error.php'; ?>
    </form>
  </section>
</section>

<?php
include 'templates/main_footer.php'
?>