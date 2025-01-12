<?php
require_once 'includes/session.php';
require_once 'includes/getters.php';

ensureLoggedIn();

$event_id = $_GET['event_id'];
$year = $_GET['year'];
$event = getEventById($event_id);
$correct_date = strtotime(date('d M ', strtotime($event['date'])) . $year);
$groups = getGroupsByEventIdYear($event_id, $year);
?>


<?php
$page_title = "View Event";
$page_styles = ["styles/groups.css"];
include 'templates/main_header.php'
?>


<section class="content">
  <header>
    <div>
      <h1>View Event</h1>
    </div>
    <a class="btn" href="group_create.php?event_id=<?php echo $event_id?>&year=<?php echo $year ?>">Create Group</a>
  </header>
    <h2><?php echo $event['name']; ?> | <?php echo date('d F Y, l', $correct_date) ?></h2>
    <?php
    if (!empty($groups)):
    ?>
    <ul class="group-list">
    <?php
    foreach ($groups as $group):
    ?>
      <li>
        <div class="two-items-apart">
          <h3>
          <?php
          if ($group['group_pass']) {
            echo '&#x1F512 ';
          }
          echo $group['group_name']
          ?>
          </h3>
          <p><a href="group_view.php?event_id=<?php echo $event_id ?>&group_id=<?php echo $group['group_id'] ?>&year=<?php echo $year ?>">View</a></p>
        </div>
        <div class="two-items-apart">
          <p>Money goal: <?php echo $group['money_goal'] ?></p>
        </div>
        <div class="two-items-apart">
          <p>Meeting time: <?php echo date('h:i:sa', strtotime($group['meeting_time'])) ?></p>
          <?php if ($group['meeting_place']): ?>
            <p>Meeting place: <?php echo $group['meeting_place'] ?></p>
          <?php endif ?>
        </div>
        <div class="two-items-apart">
        <?php if ($group['group_description']): ?>
          <p>Description: <?php echo $group['group_description'] ?></p>
        <?php endif ?>
        </div>
      </li>
    <?php endforeach ?>
    </ul>
    <?php else: ?>
    <p>No groups created. Be the first!</p>
    <?php endif ?>
</section>


<?php
include 'templates/main_footer.php'
?>