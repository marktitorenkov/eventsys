<?php

$config = [
  'db_host' => $_ENV["DB_HOST"] ?? 'localhost',
  'db_user' => $_ENV["DB_USER"] ?? 'root',
  'db_pass' => $_ENV["DB_PASS"] ?? '',
  'db_name' => 'eventsys',

  'polling_limit' => 0.8 * (function_exists('ini_get') ? (int)ini_get('max_execution_time') : 30),
  'polling_interval' => 1,
  'username_pattern' => '[a-zA-Z0-9]{3,50}',
  'group_pass_characters' => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
];

?>
