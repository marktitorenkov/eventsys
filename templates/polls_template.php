<?php foreach ($polls as $poll): ?>
<form method="POST" action="poll.php" class="poll-form">
  <input type="hidden" name="poll_id" value="<?php echo $poll['poll_id'] ?>">
  <h3><?php echo htmlspecialchars($poll['poll_title']) ?></h3>
  <?php foreach ($poll['options'] as $opt): ?>
  <label>
    <div class="row">
      <span>
        <?php $checked = in_array($user['id'], $opt['votes']) ? "checked" : "" ?>
        <input type="checkbox" name="options[]" value="<?php echo $opt['option_id'] ?>" <?php echo $checked ?>>
        <?php echo htmlspecialchars($opt['option_title']) ?>
      </span>
      <span><?php echo $opt['vote_count'].'/'.$poll['vote_count'] ?></span>
    </div>
    <div class="row">
      <meter max="<?php echo $poll['vote_count'] ?>" value="<?php echo $opt['vote_count'] ?>"></meter>
    </div>
  </label>
  <?php endforeach ?>
  <div class="row">
    <button type="submit" name="option_add" class="add-option">+</button>
    <input type="text" name="option_title" placeholder="Add Option" required>
  </div>
</form>
<?php endforeach ?>
