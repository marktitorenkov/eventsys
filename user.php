<?php
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/db_users.php';
require_once 'includes/db_events.php';
require_once 'includes/db_groups.php';

ensureLoggedIn();

$profile = getUserById($user['id'], $_GET['id']);
if (!$profile) {
  header('Location: users.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['favorite'])) {
    userFavorite($user['id'], $_POST['user_id'], $_POST['favorite']);
  }

  header('Location: '.$_SERVER['REQUEST_URI']);
  exit;
}

$events = getEventsByOwner($user['id'], $profile['id']);
$groups = getJoinedGroups($user['id'], $profile['id']);
?>

<?php
$page_title = "User Profile";
include 'templates/user_favorite.php';
include 'templates/main_header.php';
?>

<section class="content">
  <section class="profile-container">
    <section class="profile-info">
      <img class="profile-picture" alt="Profile Picture" src="<?php echo gravatarUrl($profile) ?>">
      <h1><?php echo $profile['username'] ?>'s profile</h1>
      <?php favorite_user_form($profile['id'], $profile['favorite'], $user['id'] !== $profile['id']) ?>
    </section>
    <p><strong>Birthday:</strong> <?php echo $profile['birthdate'] ?></p>
    <hr>

    <h2>Created Events</h2>
    <ul class="user-owned">
    <?php foreach ($events as $event): ?>
      <li>
        <a href="<?php echo "event_view.php?event_id=".$event['event_id']."&year=".date('Y') ?>">
          <?php echo htmlspecialchars($event['name']) ?> |
          <?php echo $event['date'] ?> |
          <?php echo $event['recurring'] ? "Recurring" : "Non Recurring" ?>
        </a>
      </li>
    <?php endforeach ?>
    </ul>

    <h2>Joined Groups</h2>
    <ul class="user-owned">
    <?php foreach ($groups as $group): ?>
      <li>
        <a href="<?php echo "group_view.php?group_id=".$group['group_id'] ?>">
          <?php echo $group['event_name'] ?> /
          <?php echo $group['group_name'] ?>
        </a>
      </li>
    <?php endforeach ?>
    </ul>

  </section>
</section>

<?php
include 'templates/main_footer.php'
?>
