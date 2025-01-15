<?php $page_scripts[] = "javascript/select_dynamic.js" ?>
<select name="user_ids[]" data-select-dynamic data-url="api/users_select.php">
<?php foreach ($selected_users as $u): ?>
  <option value="<?php echo $u['id'] ?>" selected><?php echo $u['username'] ?></option>
<?php endforeach ?>
</select>
