<?php
require_once 'includes/session.php';
require_once 'includes/db_events.php';

ensureLoggedIn();

function generateSchedule($events, $startDate, $endDate) {
  $schedule = [];
  foreach ($events as $event) {
    $eventDate = new DateTime($event['date']);

    if ($eventDate >= $startDate && $eventDate <= $endDate) {
      $schedule[$eventDate->getTimestamp()][] = $event;
    }

    if ($event['recurring']) {
      while ($eventDate <= $endDate) {
        $eventDate->modify("+1 year");
        if ($eventDate >= $startDate && $eventDate <= $endDate) {
          $schedule[$eventDate->getTimestamp()][] = $event;
        }
      }
    }
  }

  ksort($schedule);
  return $schedule;
}

$events = getEvents();
if ($events) {
  $start = new DateTimeImmutable();
  $end = $start->modify("+3 years");
  $schedule = generateSchedule($events, $start, $end);
}
?>

<?php
$page_title = "Events";
include 'templates/main_header.php'
?>

<section class="content">
  <header>
    <h1>Events</h1>
    <a class="btn" href="event_create.php">Create Event</a>
  </header>
  <?php
  if (isset($schedule)):
  ?>
    <ul class="date-list">
    <?php
    $prevYear = 0;
    foreach ($schedule as $date => $events):
    ?>
      <li>
        <?php
        $year = (int)date('Y', $date);
        if ($year > $prevYear):
          $prevYear = $year;
        ?>
          <h2><?php echo $year ?></h2>
        <?php endif ?>
        <h4><?php echo date('d F Y, l', $date) ?></h4>
        <ul class="event-list">
        <?php foreach ($events as $event): ?>
          <li>
            <span><?php echo $event['name'] ?></span>
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
