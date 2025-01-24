<?php
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/db_users.php';

ensureLoggedOut();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];

  $error_messages = array();

  if ($password !== $confirm_password) {
    $error_messages[] = 'Passwords do not match!';
  }
  elseif (!registerUser($username, $password, $_POST['birthdate'], $_POST['email'])) {
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
      <label>Username <b class="red">*</b>
        <input type="text" name="username" placeholder="Username" pattern="<?php echo $config['username_pattern'] ?>" required>
      </label>
      <label>Birthdate <b class="red">*</b>
      <input type="date" name="birthdate" value="" required>
      </label>
      <label>Email
        <input type="email" name="email" placeholder="Email">
      </label>
      <label>Password <b class="red">*</b>
        <input type="password" name="password" placeholder="Password" required>
      </label>
      <label>Confirm Password <b class="red">*</b>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
      </label>
      <button type="submit" class="btn">Register</button>
      <?php include 'templates/form_error.php' ?>
    </form>
    <a href="login.php">Login</a>
  </section>
</section>

<?php
include 'templates/main_footer.php'
?>
