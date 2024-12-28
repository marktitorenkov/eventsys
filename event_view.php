<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

ensureLoggedIn();

$event_id = $_GET['id'];
?>

<?php
$page_title = "Create Event";
include 'templates/main_header.php'
?>

<section class="content">
  <h1>View Event</h1>
  <p>Fetch [id: <?php echo $event_id ?>] from DB</p>
</section>

<?php
include 'templates/main_footer.php'
?>