<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

ensureLoggedIn();
?>

<?php
$page_title = "Create Event";
include 'templates/main_header.php'
?>

<section class="content">
  <h1>Create Event</h1>
</section>

<?php
include 'templates/main_footer.php'
?>