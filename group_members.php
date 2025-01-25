<?php
require_once 'includes/session.php';
require_once 'includes/db_events.php';
require_once 'includes/db_groups.php';

ensureLoggedIn();

$year = $_GET['year'];
$user_id = $user['id'];
$group_id = $_GET['group_id'];
$event_id = $_GET['event_id'];

if (checkGroupHiddenFromUser($user_id, $event_id, $group_id)) {
  header('Location: event_view.php?event_id=' . $event_id . '&year=' . $_GET['year']);
  exit;
}

$in_group = checkUserInGroup($user_id, $event_id, $group_id);

?>


<?php
$page_title = 'View Group';
$page_styles = ['styles/groups.css'];
include 'templates/main_header.php';
include 'templates/data_table.php';
?>

<section class="content">
  <a class="btn" href="group_view.php?event_id=<?php echo $event_id; ?>&group_id=<?php echo $group_id; ?>&year=<?php echo $year; ?>">&lt Go back</a>
  <?php
  data_table(
    function ($limit, $offset) {
      global $group_id;
      return getUsersInGroup($group_id, $limit, $offset, true);
    },
    function () {
      global $group_id;
      return getUsersInGroupCount($group_id);
    },
    10,
    [
      'User' => function ($row) { ?>
    <div class="profile-info">
      <img class="profile-picture" alt="Profile Picture" src="<?php echo gravatarUrl($row) ?>"></img>
      <a href="<?php echo 'user.php?id=' . $row['user_id'] ?>"><?php echo $row['username'] ?></a>
    </div>
    <?php
      },
      'Status' => function ($row) {
        switch ($row['user_status']) {
          case 0:
            echo '<span class="green">Admin</span>';
            break;
          case 1:
            echo 'Member';
            break;
          case 2:
            echo '<span class="red">Hidden</span>';
            break;
        }
      }
    ]
  ) ?>
</section>


<?php
include 'templates/main_footer.php'
?>