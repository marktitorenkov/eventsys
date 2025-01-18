<?php
require_once '../includes/session.php';
require_once '../includes/db_users.php';

ensureLoggedIn();

$q = $_GET["q"] ?? '';
$limit = min($_GET["limit"] ?? 0, 20);

$users = getUserNames($q, $limit);
$response = [];
foreach ($users as $u) {
  $response[] = ['text' => $u['username'], 'value' => $u['id']];
}

header("Content-Type: application/json");
echo json_encode($response);
?>
