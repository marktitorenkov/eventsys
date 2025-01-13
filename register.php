<?php
require_once 'includes/session.php';
require_once 'includes/user_auth.php';

ensureLoggedOut();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];
  $birthdate = $_POST['birthdate'];

  $error_messages = array();

  if ($password !== $confirm_password) {
    $error_messages[] = 'Passwords do not match!';
  }
  elseif (!registerUser($username, $password)) {
    $error_messages[] = 'Username is already taken!';
  }
  else {
    header('Location: login.php');
    exit;
  }
}
?>

<?php
$page_title = "Register";
include 'templates/main_header.php'
?>

<section class="box-container">
  <section class="content login">
    <h1>Register</h1>
    <form method="POST">
      <input type="text" name="username" placeholder="Username" required>
      <input type="date" name="birthdate" value="" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="password" name="confirm_password" placeholder="Confirm Password" required>
      <button type="submit">Register</button>
      <?php include 'templates/form_error.php' ?>
    </form>
    <a href="login.php">Login</a>
  </section>
</section>

<?php
include 'templates/main_footer.php'
?>
