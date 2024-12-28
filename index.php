<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

ensureLoggedIn();
?>

<?php
$page_title = "Home";
include 'templates/main_header.php'
?>

<section class="content">
  <h1>Welcome, <?php echo $user['username'] ?>!</h1>
</section>

<?php
include 'templates/main_footer.php'
?>
