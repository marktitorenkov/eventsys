<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

ensureLoggedIn();

$event_id = $_GET['event_id'];
$year = $_GET['year'];
$event = getEventById($event_id);
$group = getGroupById($_GET['group_id']);

?>


<?php
$page_title = "View Group";
$page_styles = ["styles/groups.css"];
include 'templates/main_header.php'
?>

<!-- TODO: add group information -->
<section class="content">
    <a href="event_view.php?event_id=<?php echo $event_id ?>&year=<?php echo $year?>">Go back</a>
    <header>
        <h1><?php echo $event['name'] ?></h1>
        <h2><?php echo $group['group_name']; ?></h2>
    </header>
    
</section>


<?php
include 'templates/main_footer.php'
?>