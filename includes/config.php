<?php

$config = [
  'db_host' => $_ENV["DB_HOST"] ?? 'localhost',
  'db_user' => $_ENV["DB_USER"] ?? 'root',
  'db_pass' => $_ENV["DB_PASS"] ?? '',
  'db_name' => 'eventsys',

  'group_pass_characters' => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
];

?>
