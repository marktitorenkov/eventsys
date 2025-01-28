<?php
$table_number = 1;

function page_param($table_number) {
  return $table_number == 1 ? 'p' : 'p'.$table_number;
}

function page_url($page, $table) {
  global $table_number;

  $parsedUrl = parse_url($_SERVER['REQUEST_URI']);

  $queryParams = [];
  parse_str($parsedUrl['query'] ?? '', $queryParams);

  $queryParams[page_param($table_number)] = $page;

  return $parsedUrl['path'] . '?' . http_build_query($queryParams);
}

function data_table($get_records, $get_records_count, $pageSize, $columns, $columns_widths = null) {
  global $table_number;

  $count = $get_records_count();
  $totalPages = ceil($count / $pageSize);

  $page = min(max($_GET[page_param($table_number)] ?? 1, 1), $totalPages);

  $records = $get_records($pageSize, ($page - 1) * $pageSize); // (limit, offset * should_apply_offset)
  $baseUrl = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
  $render_default = function($row, $key) { echo $key ? $row[$key] : ""; };
  $additional_class = basename($_SERVER['SCRIPT_NAME'], ".php");
?>
  <section class="data-table <?php echo $additional_class ?>">
    <table>
      <thead>
        <tr>
        <?php $j = 0; foreach ($columns as $key => $column): ?>
          <th <?php if (isset($columns_widths)) echo 'style=width:' . $columns_widths[$j++] ?>><?php echo $key ?></th>
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
      <a <?php echo $page > 1 ? 'href="'.page_url(1, $table_number).'"' : '' ?>>&lt;&lt;</a>
      <a <?php echo $page > 1 ? 'href="'.page_url($page - 1, $table_number).'"' : '' ?>>&lt;</a>
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a <?php echo $i != $page ? 'href="'.page_url($i, $table_number).'"' : '' ?>><?php echo $i ?></a>
      <?php endfor ?>
      <a <?php echo $page < $totalPages ? 'href="'.page_url($page + 1, $table_number).'"' : '' ?>>&gt;</a>
      <a <?php echo $page < $totalPages ? 'href="'.page_url($totalPages, $table_number).'"' : '' ?>>&gt;&gt;</a>
    </nav>

  </section>
<?php $table_number++; } ?>
