<?php
require_once 'includes/session.php';
require_once 'includes/db_events.php';
require_once 'includes/db_groups.php';
require_once 'includes/db_polls.php';

ensureLoggedIn();

$group_id = $_GET['group_id'];
$group = getGroupById($group_id);
if (!$group) {
  header("Location: ./");
  exit;
}

$user_id = $user['id'];
$year = $group['year'];
$event_id = $group['event_id'];
$event = getEventById($event_id);
$group_pass = $group['group_pass'];
$is_private = !!$group['group_pass'];

if (checkGroupHiddenFromUser($user_id, $event_id, $group_id)) {
  header("Location: event_view.php?event_id=$event_id&year=$year");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['form-join'])) { // join group
    if ($group_pass && $group_pass !== $_POST['group-pass']) { // private and password is incorrect
      $error_messages = ['Group pass is incorrect. Please try again.'];
    } else { // public or correct passsword
      addUserInGroup($user_id, $group_id);
      header('Location: '.$_SERVER['REQUEST_URI']);
      exit;
    }
  }

  if (isset($_POST['form-leave'])) { // leave group
    if (leaveGroup($user_id, $group_id)) { // group still exists
      header('Location: '.$_SERVER['REQUEST_URI']);
    } else { // group deleted
      header("Location: event_view.php?event_id=$event_id&year=$year");
    }
    exit;
  }
}

$in_group = checkUserInGroup($user_id, $event_id, $group_id);
$polls = getGroupPolls($group_id);
?>


<?php
$page_title = 'View Group';
$page_styles = ['styles/groups.css'];
$page_scripts = ["javascript/tabs.js", 'javascript/chat.js', 'javascript/polls.js', 'javascript/group_view.js'];
include 'templates/main_header.php'
?>

<section class="content">
  <header class="space-betwen">
    <a class="btn" href="event_view.php?event_id=<?php echo $event_id ?>&year=<?php echo $year ?>">&lt; Backt to Event</a>
    <h1><?php echo htmlspecialchars($event['name'].' / '.$group['group_name']) ?></h1>
    <?php if ($in_group): ?>
    <div>
      <form id="form-leave-group" method="POST">
        <button type="submit" name="form-leave" class="btn delete">Leave Group</button>
      </form>
      <?php if ($user_id === $group['creator_id']): ?>
        <a class="btn" href="group_edit.php?group_id=<?php echo $group_id ?>">Edit Group</a>
      <?php endif ?>
    </div>
    <?php endif ?>
  </header>
  <?php if ($in_group): // User in group => can show content ?>

    <ul>
      <li><b>💸 Money Goal:</b> <?php echo $group['money_goal'] != 0 ? $group['money_goal'] : 'Not set' ?></li>
      <li><b>📍 Location:</b> <?php echo htmlspecialchars($group['meeting_place'] ?? 'Not set') ?></li>
      <li><b>⏰ Time:</b> <?php echo htmlspecialchars($group['meeting_time'] ?? 'Not set') ?></li>
      <li>
        <?php echo ($is_private ? "🔒 Private" : "🌐 Public")." Group" ?> |
        <a href="group_members.php?group_id=<?php echo $group_id; ?>"><?php echo getMemberCount($group_id); ?> member(s)</a>
      </li>
    </ul>

    <p><?php echo htmlspecialchars($group['group_description'] ?? '') ?></p>

    <ul class="group-tabs">
      <li id="chat-toggle">Chat</li>
      <li id="polls-toggle">Polls</li>
    </ul>

    <section id="chat-panel" class="chat" style="display: none">
      <section id="chat-history" class="chat-history"></section>
      <form action="api/group_messages.php" id="chat-form" class="chat-form">
        <input type="hidden" name="group_id" value="<?php echo $group_id ?>">
        <textarea name="content" placeholder="Aa" autofocus></textarea>
        <button type="submit">➤</button>
      </form>
    </section>

    <section id="polls-panel" class="polls" style="display: none">
      <section class="polls-container">
        <?php include 'templates/polls_template.php' ?>
        <form method="POST" action="poll.php" class="poll-form create">
          <input type="hidden" name="group_id" value="<?php echo $group_id ?>">
          <input type="hidden" name="poll_create">
          <label>
            <div class="row">
              <input type="text" name="poll_title" placeholder="Poll Title" required>
            </div>
          </label>
          <div class="row">
            <button class="btn" type="submit">Create Poll</button>
          </div>
        </form>
      </section>
    </section>

    <?php else: // User NOT in group => JOIN before show content ?>
  <section class="content group-form">
    <form method="POST">
    <?php if ($group_pass): // Group is PRIVATE => need GROUP PASS ?>
      <h2>Group is private. You need its <span class="hover-pop-up" title="8 character long password. Ask group creator for it.">GROUP PASS</span> to join.</h2>
      <input id="input-group-pass" type="text" maxlength="8" name="group-pass" placeholder="Group pass" required>
    <?php else: ?>
      <h2>Join to see more!</h2>
    <?php endif ?>
      <button type="submit" name="form-join" class="btn create">Join Group</button>
      <?php include 'templates/form_error.php'?>
    </form>
  </section>
  <?php endif ?>
</section>


<?php
include 'templates/main_footer.php'
?>
