<?php
require_once 'includes/session.php';
require_once 'includes/gravatar.php';
require_once 'includes/db_users.php';

ensureLoggedIn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['favorite'])) {
    userFavorite($user['id'], $_POST['user_id'], $_POST['favorite']);
  }

  header('Refresh: 0');
  exit;
}

$query = $_GET['q'] ?? ""
?>

<?php
$page_title = "Users";
include 'templates/main_header.php';
include 'templates/data_table.php'
?>

<section class="content">
  <header>
    <h1>Users</h1>
    <form class="form-filter" method="GET">
      <input type="text" name="q" value="<?php echo $query ?>" placeholder="Filter..." autofocus>
      <button type="submit" class="btn alt">🔍</button>
    </form>
  </header>
  <?php
  data_table(function($limit, $offset) { global $user, $query; return getUsers($user['id'], $query, $limit, $offset); },
             function() { return getUsersCount(); },
             10,
             [
              "Favorite" => function($row) {
                global $user;
                ?><form method="POST">
                  <input type="hidden" name="user_id" value="<?php echo $row['id'] ?>">
                  <input type="hidden" name="favorite" value="<?php echo !($row['favorite']) ?>">
                  <?php if ($user['id'] !== $row['id']): ?>
                  <button type="submit" class="btn-favorite"><?php echo $row['favorite'] ? '★' : '☆' ?></button>
                  <?php endif ?>
                </form><?php
              },
              "User" => function($row) {
                ?><a href="<?php echo "user.php?id=".$row['id'] ?>">
                  <div class="profile-info">
                    <img class="profile-picture" alt="Profile Picture" src="<?php echo gravatarUrl($row) ?>"></img>
                    <span><?php echo $row['username'] ?></span>
                  </div>
                </a><?php
              },
              "Birthdate" => "birthdate",
            ])
  ?>
</section>

<?php
include 'templates/main_footer.php';
?>
