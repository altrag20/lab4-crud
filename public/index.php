<?php
require __DIR__ . '/../src/crud.php';
$pageTitle = 'Головна';
$counts = [];
foreach (entities() as $key => $cfg) {
    $counts[$key] = (int)db()->query("SELECT COUNT(*) FROM {$cfg['table']}")->fetchColumn();
}
require __DIR__ . '/../src/header.php';
?>
<h1>Театр: керування даними</h1>
<div class="wrap"><table>
  <thead><tr><th>Розділ</th><th>Записів</th></tr></thead>
  <tbody>
  <?php foreach (entities() as $key => $cfg): ?>
    <tr><td><a href="/<?= h($key) ?>/index.php"><?= h($cfg['title']) ?></a></td><td><?= $counts[$key] ?></td></tr>
  <?php endforeach; ?>
  </tbody>
</table></div>
<?php require __DIR__ . '/../src/footer.php';
