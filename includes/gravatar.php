<?php
function gravatarUrl($email) {
  echo "https://www.gravatar.com/avatar/".hash("sha256", strtolower(trim($email)))."?d=identicon";
}
?>
