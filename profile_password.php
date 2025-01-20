<?php
require_once 'includes/session.php';

ensureLoggedIn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $error_messages = [];
  if (!loginUser($user['username'], $_POST['current'])) {
    $error_messages[] = "Your current password is incorrect.";
  } else if ($_POST['new'] !== $_POST['new_repeat']) {
    $error_messages[] = "Passwords do not match.";
  } else {
    updatePassword($user['id'], $_POST['new']);
    header('Location: profile.php');
    exit;
  }
}
?>

<?php
$page_title = "Change Password";
include 'templates/main_header.php'
?>

<section class="content">
  <section class="profile-container">
    <h1>Change your password</h1>
    <form method="POST">
      <label>Current Password <b class="red">*</b>
        <input type="password" name="current" required>
      </label>
      <label>New Password <b class="red">*</b>
        <input type="password" name="new" required>
      </label>
      <label>Repeat New Password <b class="red">*</b>
        <input type="password" name="new_repeat" required>
      </label>
      <?php include 'templates/form_error.php' ?>
      <button type="submit"  class="btn">Change Password</button>
      <a href="profile.php">Back to profile</a>
    </form>
  </section>
</section>

<?php
include 'templates/main_footer.php'
?>
