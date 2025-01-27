<?php function favorite_user_form($user_id, $value, $enabled = true) { ?>
  <?php if ($enabled): ?>
  <form method="POST">
    <input type="hidden" name="user_id" value="<?php echo $user_id ?>">
    <input type="hidden" name="favorite" value="<?php echo !($value) ?>">
    <button type="submit" class="btn-favorite"><?php echo $value ? '★' : '☆' ?></button>
  </form>
  <?php endif ?>
<?php } ?>
