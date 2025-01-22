<?php 
$page_scripts[] = "javascript/select_dynamic.js";
$page_styles[] = "styles/select_dynamic.css"; 

function select_dynamic($name, $url, $initial) {
?>
<select name="<?php echo $name ?>[]" multiple data-select-dynamic data-url="<?php echo $url ?>">
<?php foreach ($initial as $o): ?>
  <option value="<?php echo $o['value'] ?>" selected><?php echo $o['text'] ?></option>
<?php endforeach ?>
</select>
<?php } ?>
