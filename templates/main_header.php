<?php
require_once 'includes/gravatar.php';
$user = getUserById(getUserId());

function active($param) {
  $isActive = basename($_SERVER['SCRIPT_NAME'], ".php") === $param;
  echo $isActive ? 'class="active"' : '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/x-icon" href="<?php echo gravatarUrl('Events System') ?>">
  <link rel="stylesheet" href="styles/main.css">
  <title><?php echo $page_title." | Events System" ?></title>
</head>
<body>
  <nav>
    <div class="wrapper">
      <ul>
        <li>
          <span><h1>Events System</h1></span>
        </li>
      </ul>
      <?php if ($user): ?>
      <ul class="middle">
        <li>
          <a href="./" <?php active("index") ?>>Events</a>
        </li>
      </ul>
      <ul>
        <li>
          <a href="logout.php">Logout</a>
        </li>
        <li>
          <a href="profile.php" <?php active("profile") ?>>
            <div class="profile">
              <strong><?php echo $user['username'] ?></strong>
              <img alt="Your Profile" src="<?php gravatarUrl($user['username']) ?>">
            </div>
          </a>
        </li>
      </ul>
      <?php endif ?>
    </div>
  </nav>
  <main class="wrapper">
