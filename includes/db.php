<?php
require_once "config.php";

function getPDO() {
  global $config;
  
  $dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4";
  $options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ];
  
  static $pdo = null;
  if ($pdo === null) {
    try {
      $pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], $options);
    } catch (\PDOException $e) {
      die("Database connection failed: " . $e->getMessage());
    }
  }
  return $pdo;
}

?>