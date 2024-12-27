<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

ensureLoggedIn();
$user = getUserById(getUserId());
$username = $user['username'];
?>

<?php
$page_title = "Home";
include 'templates/main_header.php'
?>

<main>
  <h1>Welcome, <?php echo $username; ?>!</h1>
  <p><a href="logout.php">Logout</a></p>
</main>

<?php
include 'templates/main_footer.php'
?>
