<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

ensureLoggedIn();

$event_id = $_GET['event_id'];
$year = $_GET['year'];
$event = getEventById($event_id);
$correct_date = strtotime(date('d M ', $event['date']) . $year);
$groups = getGroupsByEventIdYear($event_id, $year);
?>


<link rel="stylesheet" href="styles/groups.css">
<?php
$page_title = "View Event";
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
        <div>
          <h3><?php echo $group['group_name'] ?></h3>
          <p><a href="group_view.php?event_id=<?php echo $event_id ?>&group_id=<?php echo $group['group_id'] ?>&year=<?php echo $year ?>">View</a></p>
        </div>
        <div>
          <p>Money goal: <?php echo $group['money_goal'] ?></p>
        </div>
        <div>
          <p>Meeting place: <?php echo $group['meeting_place'] ?></p>
          <p>Meeting time: <?php echo date('h:i:sa', strtotime($group['meeting_time'])) ?></p>
        </div>
        <div>
          <p>Description: <?php echo $group['group_description'] ?></p>
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