<?php
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/db_users.php';
require_once 'includes/db_events.php';

ensureLoggedIn();

if (!isset($_GET['id'])) {
  http_response_code(404);
  die;
}

$profile = getUserById($_GET['id']);
$events = getEventsByOwner($profile['id']);
?>

<?php
$page_title = "User Profile";
include 'templates/main_header.php';
?>

<section class="content">
  <section class="profile-container">
    <section class="profile-info">
      <img class="profile-picture" alt="Profile Picture" src="<?php echo gravatarUrl($profile) ?>">
      <h1><?php echo $profile['username'] ?>'s profile</h1>
    </section>
    <p><strong>Birthday:</strong> <?php echo $profile['birthdate'] ?></p>
    <hr>
    <h2><?php echo $profile['username'] ?>'s events</h2>
    <ul>
    <?php foreach ($events as $event): ?>
      <li>
        <a href="<?php echo "event_view.php?event_id=".$event['event_id']."&year=".date('Y') ?>">
          <?php echo $event['name'] ?> |
          <?php echo $event['date'] ?> |
          <?php echo $event['recurring'] ? "Recurring" : "Non Recurring" ?>
        </a>
      </li>
    <?php endforeach ?>
    </ul>
  </section>
</section>

<?php
include 'templates/main_footer.php'
?>
