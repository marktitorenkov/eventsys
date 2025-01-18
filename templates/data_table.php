<?php
function data_table($get_records, $get_records_count, $pageSize, $columns) {
  $page = max($_GET['p'] ?? 1, 1);
  $records = $get_records($pageSize, ($page - 1) * $pageSize);
  $totalPages = ceil($get_records_count() / $pageSize);
  $baseUrl = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
  $render_default = function($row, $key) { echo $key ? $row[$key] : ""; };
  $additiona_class = basename($_SERVER['SCRIPT_NAME'], ".php");
?>
  <section class="data-table <?php echo $additiona_class ?>">

    <table>
      <thead>
        <tr>
        <?php foreach ($columns as $key => $column): ?>
          <th><?php echo $key ?></th>
        <?php endforeach ?>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($records as $record): ?>
        <tr>
        <?php foreach ($columns as $key => $column): ?>
            <td><?php (is_string($column) ? $render_default : $column)($record, $column) ?></td>
        <?php endforeach ?>
        </tr>
      <?php endforeach ?>
      </tbody>
    </table>

    <nav>
      <a <?php echo $page > 1 ? 'href="'.$baseUrl.'?p=1"' : '' ?>>&lt;&lt;</a>
      <a <?php echo $page > 1 ? 'href="'.$baseUrl.'?p='.($page - 1).'"' : '' ?>>&lt;</a>
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a <?php echo $i != $page ? 'href="'.$baseUrl.'?p='.$i.'"' : '' ?>><?php echo $i ?></a>
      <?php endfor ?>
      <a <?php echo $page < $totalPages ? 'href="'.$baseUrl.'?p='.($page + 1).'"' : '' ?>>&gt;</a>
      <a <?php echo $page < $totalPages ? 'href="'.$baseUrl.'?p='.($totalPages).'"' : '' ?>>&gt;&gt;</a>
    </nav>

  <section>
<?php } ?>
