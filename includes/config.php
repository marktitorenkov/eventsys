<?php

$config = [
  'db_host' => $_ENV["DB_HOST"] ?? 'localhost',
  'db_user' => $_ENV["DB_USER"] ?? 'root',
  'db_pass' => $_ENV["DB_PASS"] ?? '',
  'db_name' => 'eventsys',

  'username_pattern' => '[a-zA-Z0-9]{3,50}',
  'group_pass_characters' => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
];

?>
