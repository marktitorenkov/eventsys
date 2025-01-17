<?php
function gravatarUrl($arg) {
  $email = is_string($arg) ? $arg : ($arg['email'] ?: $arg['username']);
  return "https://www.gravatar.com/avatar/".hash("sha256", strtolower(trim($email)))."?d=identicon";
}
?>
