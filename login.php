<?php
require_once 'includes/db.php';
require_once 'includes/session.php';

ensureLoggedOut();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  $userId = loginUser($username, $password);
  if (!$userId) {
    $error_msg = 'Invalid username or password!';
  } else {
    $_SESSION['user_id'] = $userId;
    header('Location: login.php');
    exit;
  }
}
?>

<?php
$page_title = "Login";
include 'templates/main_header.php'
?>

<h1>Login</h1>
<form method="POST">
  <input type="text" name="username" placeholder="Username" required>
  <input type="password" name="password" placeholder="Password" required>
  <button type="submit">Login</button>
  <?php include 'templates/form_error.php'; ?>
</form>
<a href="register.php">Register</a>

<?php
include 'templates/main_footer.php'
?>
