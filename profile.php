<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

ensureLoggedIn();

$user = getUserById(getUserId())
?>

<?php
$page_title = "Profile";
include 'templates/main_header.php'
?>

<section class="content">
  <h1>Your Profile</h1>
  <label>Username
    <input value="<?php echo $user['username'] ?>" disabled>
  </label>
</section>

<?php
include 'templates/main_footer.php'
?>
