<?php
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/db_users.php';

ensureLoggedIn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  updateUser($user['id'], $_POST['username'], $_POST['email'], $_POST['birthdate']);

  header('Location: '.$_SERVER['REQUEST_URI']);
  exit;
}
?>

<?php
$page_title = "Profile";
include 'templates/main_header.php';
?>

<section class="content">
  <section class="profile-container">
    <section class="profile-info">
      <img class="profile-picture" alt="Profile Picture" src="<?php echo gravatarUrl($user) ?>">
      <h1><?php echo $user['username'] ?>'s profile</h1>
    </section>
    <form method="POST">
      <h2>Edit information</h2>
      <label>Username <b class="red">*</b>
        <input type="text" name="username" placeholder="Username" pattern="<?php echo $config['username_pattern'] ?>" value="<?php echo $user['username'] ?>" required>
      </label>
      <label>Email
        <input type="email" name="email" placeholder="Email" value="<?php echo $user['email'] ?>">
      </label>
      <label>Birthday <b class="red">*</b>
        <input type="date" name="birthdate" placeholder="Birthday" value="<?php echo $user['birthdate'] ?>" required>
      </label>
      <button type="submit" class="btn">Edit</button>
      <a href="profile_password.php">Change password</a>
    </form>
  </section>
</section>

<?php
include 'templates/main_footer.php'
?>
