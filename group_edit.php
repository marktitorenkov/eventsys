<?php
require_once 'includes/session.php';
require_once 'includes/db_groups.php';

ensureLoggedIn();

$event_id = $_GET['event_id'];
$group_id = $_GET['group_id'];
$query = '';
$group = getGroupById($group_id);

// check if User is a member of the group
if (!checkUserInGroup($user['id'], $event_id, $group_id)) {
  header('Location: group_view.php?event_id=' . $event_id . '&group_id=' . $group_id . '&year=' . $_GET['year']);
  exit;
}

// check if User is creator of group and has editing privileges
if ($user['id'] != $group['creator_id']) {
  header('Location: group_view.php?event_id=' . $event_id . '&group_id=' . $group_id . '&year=' . $_GET['year']);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['update-group'])) { // Update Group Information
    $group_name = $_POST['group-name'];
    $meeting_time = $_POST['meeting-time'];
    $meeting_place = $_POST['meeting-place'];
    $money_goal = $_POST['money-goal'];
    $group_description = $_POST['group-description'];
    $is_private = $_POST['is-private'];

    updateGroup(
      $group_id,
      $user['id'],
      $group_name,
      $money_goal,
      $meeting_time,
      $meeting_place,
      $group_description,
      $group['group_pass'],
      $is_private
    );

    header('Refresh:0');
    exit;
  }

  if (isset($_POST['delete-group'])) { // Delete Group
    deleteGroup($group_id, $user['id']);

    header('Location: event_view.php?event_id=' . $event_id . '&year=' . $_GET['year']);
    exit;
  }

  if (isset($_POST['transfer-group-ownership'])) { // Transfer Ownership of Group
    $new_owner = $_POST['user_id'];

    updateGroup(
      $group_id,
      $new_owner,
      $group['group_name'],
      $group['money_goal'],
      $group['meeting_time'],
      $group['meeting_place'],
      $group['group_description'],
      $group['group_pass']
    );

    header('Refresh:0');
    exit;
  }

  if (isset($_POST['hide-group-from-user'])) { // Hide Group From User
    $member_id = $_POST['user_id'];

    removeUserFromGroup($member_id, $group_id);
    hideGroupFromUser($member_id, $group_id);

    header('Refresh:0');
    exit;
  }

  if (isset($_POST['show-group-to-user'])) { // Show Group To User / Unhide
    $member_id = $_POST['user_id'];

    showGroupToUser($member_id, $group_id);
    addUserInGroup($member_id, $group_id);

    header('Refresh:0');
    exit;
  }

  if (isset($_POST['remove-user-from-group'])) { // Remove User from Group
    $member_id = $_POST['user_id'];

    removeUserFromGroup($member_id, $group_id);
    showGroupToUser($member_id, $group_id);

    header('Refresh:0');
    exit;
  }

  if (isset($_POST['add-member-to-group'])) { // Add User to Group
    $new_member_id = $_POST['user_id'];

    addUserInGroup($new_member_id, $group_id);

    header('Refresh:0');
    exit;
  }

  if (isset($_POST['query-user-filter'])) { // Get query for filtering users
    $query = $_POST['q'];
  }

  if (isset($_POST['favorite'])) { // Set User as Favorite
    userFavorite($user['id'], $_POST['user_id'], $_POST['favorite']);
  }
}
?>


<?php
$page_title = 'Group Settings';
$page_styles = ['styles/groups.css'];
$page_scripts = ['javascript/group_edit.js'];
include 'templates/main_header.php';
include 'templates/data_table.php';
?>

<section class="content">
  <header>
    <a class="btn" href="group_view.php?event_id=<?php echo $event_id ?>&group_id=<?php echo $group_id ?>&year=<?php echo $_GET['year'] ?>"
    >&lt Go back</a>
    <h1><?php echo $group['group_name'] ?></h1>
  </header>
  
  <!-- Buttons that switch between Panels -->
  <div class="two-items">
    <button id="btn-toggle-edit" class="btn" onclick="changeToEditGroupPanel()">Edit Group</button>
    <button id="btn-toggle-members" class="btn inactive" onclick="changeToGroupMembersPanel()">Group Members</button>
    <button id="btn-toggle-add" class="btn inactive" onclick="changeToAddMembersPanel()">Add Members</button>
  </div>

  <!-- Panel for Editing Group Information / Delete Group -->
  <section id="group-edit-panel" class="content group-form">
    <form method="POST">
      <label for="group-name">Group name:</label>
      <input type="text" id="group-name" name="group-name" maxlength="50" value="<?php echo $group['group_name'] ?>">
      <label for="meeting-time">Meeting time:</label>
      <input type="time" id="meeting-time" name="meeting-time" value="<?php echo $group['meeting_time'] ?>">
      <label for="meeting-place">Meeting place:</label>
      <input type="text" id="meeting-place" name="meeting-place" maxlength="50" value="<?php echo $group['meeting_place'] ?>">
      <label for="money-goal">Money goal:</label>
      <input type="number" id="money-goal" name="money-goal" min="0" value="<?php echo $group['money_goal'] ?>">
      <label for="group-description">Group description:</label>
      <textarea type="text" id="group-description" name="group-description" maxlength="250"><?php echo $group['group_description'] ?></textarea>
      <label>Group pass: <?php if ($group['group_pass']) echo $group['group_pass']; else echo 'None. Group is public.'; ?></label>
      <div class="two-items">
        <input type="checkbox" id="is-private" name="is-private" <?php if ($group['group_pass']) {echo 'checked';} ?>>
        <label for="is-private">make private</label>
      </div>
      <button type="submit" class="btn" name="update-group">Edit</button>
      <button type="button" id="btn-delete-group" class="btn delete" onclick="confirmGroupDelete()">DELETE GROUP</button>
      <div id="delete-modal" style="display:none;">
        <p>Are you sure you want to delete this group?</p>
        <button type="submit" class="btn delete" name="delete-group">Yes, I'm sure.</button>
        <button type="button" class="btn" onclick="confirmGroupDelete()">Cancel</button>
      </div>
    </form>
  </section>


  <!-- Panel for managing Group Members -->
  <section id="group-members-panel" class="content" style="display:none;">
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
      'Actions' => function ($row) { ?>
    <?php if ($row['user_status'] != 0): ?> <!-- show buttons if user is not admin -->
      <form method="POST">
        <input type="hidden" name="user_id" value="<?php echo $row['user_id'] ?>">
        <?php if ($row['user_status'] == 2): ?> <!-- show buttons if group is hidden from user -->
          <button type="submit" name="show-group-to-user" class="btn alt">Show Group To</button>
        <?php else: ?> <!-- show buttons if group is NOT hidden from user -->
          <button type="submit" name="transfer-group-ownership" class="btn alt">Transfer Ownership</button>
          <button type="submit" name="hide-group-from-user" class="btn alt">Hide From</button>
        <?php endif ?>
        <button type="submit" name="remove-user-from-group" class="btn delete">Remove</button>
      </form>
    <?php endif ?>
    <?php
      },
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
    ],
    ['30%', '60%', '10%']
  ) ?>
</section>

<!-- Panel for Adding new Group Members / Hiding Group from Users -->
<section id="group-add-panel" class="content" style="display:none;">
  <header>
    <h2>Add Users To Group</h2>
    <form class="form-filter" method="POST" >
      <input type="text" name="q" value="<?php echo $query ?>" placeholder="Search user...">
      <button type="submit" name="query-user-filter" class="btn alt">🔍</button>
    </form>
  </header>
  <?php
  data_table(
    function ($limit, $offset) {
      global $user, $query, $group_id;
      return getUsersNotInGroup($group_id, $user['id'], $query, $limit, $offset);
    },
    function () {
      global $user, $group_id, $query;
      return getUsersNotInGroupCount($group_id, $user['id'], $query);
    },
    10,
    [
      'Actions' => function ($row) { ?>
    <form method="POST">
      <input type="hidden" name="user_id" value="<?php echo $row['id'] ?>">
      <button type="submit" name="add-member-to-group" class="btn alt">Add Member</button>
      <button type="submit" name="hide-group-from-user" class="btn alt">Hide From</button>
    </form>
    <?php
      },
      'User' => function ($row) { ?>
    <div class="profile-info">
      <img class="profile-picture" alt="Profile Picture" src="<?php echo gravatarUrl($row) ?>"></img>
      <a href="<?php echo 'user.php?id=' . $row['id'] ?>"><?php echo $row['username'] ?></a>
    </div>
    <?php
      },
      'Favorite' => function ($row) {
        global $user; ?>
    <form method="POST">
      <input type="hidden" name="user_id" value="<?php echo $row['id'] ?>">
      <input type="hidden" name="favorite" value="<?php echo !($row['favorite']) ?>">
      <?php if ($user['id'] !== $row['id']): ?>
        <button type="submit" class="btn-favorite"><?php echo $row['favorite'] ? '★' : '☆' ?></button>
      <?php endif ?>
    </form><?php
      },
    ],
    ['30%', '60%', '10%']
  )
    ?>
  </section>
</section>


<?php
include 'templates/main_footer.php'
?>