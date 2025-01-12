<?php
  if (isset($error_messages)):
    foreach ($error_messages as $error_msg):
?>
      <p class="error"><?php echo $error_msg ?></p>
    <?php endforeach?>
<?php endif ?>
