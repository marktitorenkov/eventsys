<?php
require_once 'includes/session.php';
require_once 'includes/db_events.php';

ensureLoggedIn();

function anyFavorite($events) {
  foreach ($events as $e) {
    if ($e['favorite']) return true;
  }
  return false;
}

function generateSchedule($events, $startDate, $endDate) {
  $schedule = [];
  foreach ($events as $event) {
    $eventDate = new DateTime($event['date']);

    if ($eventDate >= $startDate && $eventDate < $endDate) {
      $schedule[$eventDate->getTimestamp()][] = $event;
    }

    if ($event['recurring']) {
      while ($eventDate < $endDate) {
        $eventDate->modify("+1 year");
        if ($eventDate >= $startDate && $eventDate < $endDate) {
          $schedule[$eventDate->getTimestamp()][] = $event;
        }
      }
    }
  }

  ksort($schedule);
  return $schedule;
}

$events = getEvents($user['id']);
if ($events) {
  $start = new DateTimeImmutable(date('Y-1-1'));
  $end = $start->modify("+3 years");
  $schedule = generateSchedule($events, $start, $end);
}
?>

<?php
$page_title = "Events";
$page_scripts = ['javascript/event_list.js'];
include 'templates/main_header.php'
?>

<section class="content">
  <header class="space-betwen">
    <h1>Events</h1>
    <a class="btn" href="event_create.php">Create Event</a>
  </header>
  <?php if (anyFavorite($events)): ?>
    <header class="space-betwen">
      <button type="button" class="btn" id="toggle_expand">&nbsp;</button>
    </header>
  <?php endif ?>
  <?php
  if (isset($schedule)):
  ?>
    <ul class="date-list">
    <?php
    $prevYear = 0;
    foreach ($schedule as $date => $date_events):
      $year = (int)date('Y', $date);
      $dateFavorite = anyFavorite($date_events);
    ?>
      <?php
      if ($year > $prevYear):
        $prevYear = $year;
      ?>
        <li class="year-item">
          <h2><?php echo $year ?></h2>
        </li>
      <?php endif ?>
      <li class="date-item <?php echo $dateFavorite ? "" : "hidden" ?>">
        <h4>
          <?php echo date('d F Y, l', $date) ?>
        </h4>
        <ul class="event-list">
        <?php foreach ($date_events as $event): ?>
          <li class="event-item <?php echo $event['favorite'] ? "" : "hidden" ?>">
            <span>
              <?php echo htmlspecialchars($event['name']) ?>
              <?php if ($event['creator_id']): ?>
                <span> • <a href="user.php?id=<?php echo $event['creator_id'] ?>"><?php echo $event['creator_username'] ?></a></span>
              <?php endif ?>
            </span>
            <a href="event_view.php?event_id=<?php echo $event['event_id'] ?>&year=<?php echo $year ?>">View</a>
          </li>
        <?php endforeach ?>
        </ul>
      </li>
    <?php endforeach ?>
    </ul>
  <?php else: ?>
    <p>No events available.</p>
  <?php endif ?>
</section>

<?php
include 'templates/main_footer.php'
?>
