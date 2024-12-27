<?php
require_once 'includes/db.php';
require_once 'includes/session.php';

ensureLoggedOut();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];

  if ($password != $confirm_password) {
    $error_msg =  'Passwords do not match!';
  }
  elseif (!registerUser($username, $password)) {
    $error_msg =  'Username is already taken!';
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

<h1>Register</h1>
<form method="POST">
  <input type="text" name="username" placeholder="Username" required>
  <input type="password" name="password" placeholder="Password" required>
  <input type="password" name="confirm_password" placeholder="Confirm Password" required>
  <button type="submit">Register</button>
  <?php include 'templates/form_error.php' ?>
</form>
<a href="login.php">Login</a>

<?php
include 'templates/main_footer.php'
?>
