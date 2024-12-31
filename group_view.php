<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

ensureLoggedIn();

$event_id = $_GET['event_id'];
$event = getEventById($event_id);
$group = getGroupById($_GET['group_id']);

?>


<?php
$page_title = "View Group";
include 'templates/main_header.php'
?>

<!-- TODO: add group information -->
<section class="content">
    <a href="event_view.php?event_id=<?php echo $event_id ?>">Go back</a>
    <header>
        <h1><?php echo $event['name'] ?></h1>
        <h2><?php echo $group['group_name']; ?></h2>
    </header>
    
</section>


<?php
include 'templates/main_footer.php'
?>