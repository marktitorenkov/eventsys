<?php
require_once 'includes/session.php';
require_once 'includes/gravatar.php';
require_once 'includes/db_users.php';

ensureLoggedIn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['favorite'])) {
    userFavorite($user['id'], $_POST['user_id'], $_POST['favorite']);
  }

  header('Location: '.$_SERVER['REQUEST_URI']);
  exit;
}

$query = $_GET['q'] ?? ""
?>

<?php
$page_title = "Users";
include 'templates/user_favorite.php';
include 'templates/main_header.php';
include 'templates/data_table.php'
?>

<section class="content">
  <header class="space-betwen">
    <h1>Users</h1>
    <form class="form-filter" method="GET">
      <input type="text" name="q" value="<?php echo $query ?>" placeholder="Filter..." autofocus>
      <button type="submit" class="btn alt">🔍</button>
    </form>
  </header>
  <?php
  data_table(function($limit, $offset) { global $user, $query; return getUsers($user['id'], $query, $limit, $offset); },
             function() { global $query; return getUsersCount($query); },
             10,
             [
              "Favorite" => function($row) {
                global $user;
                favorite_user_form($row['id'], $row['favorite'], $user['id'] !== $row['id']);
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
