<?php
// Очікує змінну $pageTitle. Меню будується з конфігурації сутностей.
$menu = entities();
?><!doctype html>
<html lang="uk">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= h($pageTitle ?? 'Театр') ?> — Театр</title>
<style>
  :root{--bg:#f6f5f2;--ink:#1d1d1f;--mute:#6b6b70;--line:#dcdad4;--brand:#7a1f2b;--ok:#1f6b3a;--err:#a31621}
  *{box-sizing:border-box}
  body{margin:0;font:16px/1.5 Georgia,'Times New Roman',serif;background:var(--bg);color:var(--ink)}
  nav{background:var(--brand);padding:.6rem 1rem;display:flex;flex-wrap:wrap;gap:.25rem 1.2rem}
  nav a{color:#fff;text-decoration:none;padding:.2rem 0;border-bottom:2px solid transparent}
  nav a:hover,nav a:focus-visible{border-bottom-color:#fff}
  main{max-width:1000px;margin:1.5rem auto;padding:0 1rem}
  h1{font-size:1.7rem;margin:.2rem 0 1rem}
  table{width:100%;border-collapse:collapse;background:#fff;border:1px solid var(--line)}
  th,td{padding:.5rem .7rem;text-align:left;border-bottom:1px solid var(--line);vertical-align:top}
  th{background:#efede8;font-weight:600}
  .wrap{overflow-x:auto}
  a{color:var(--brand)}
  .btn,button{font:inherit;padding:.35rem .8rem;border:1px solid var(--brand);background:var(--brand);color:#fff;border-radius:3px;cursor:pointer;text-decoration:none;display:inline-block}
  .btn.alt,button.alt{background:#fff;color:var(--brand)}
  button.danger{background:var(--err);border-color:var(--err)}
  button.danger.alt{background:#fff;color:var(--err);border-color:var(--err)}
  .msg{padding:.6rem .9rem;margin:0 0 1rem;border-left:4px solid}
  .msg.ok{background:#e6f2ea;border-color:var(--ok)} .msg.err{background:#f9e5e6;border-color:var(--err)}
  form.f{background:#fff;border:1px solid var(--line);padding:1rem;max-width:560px}
  .row{margin-bottom:.9rem} label{display:block;font-weight:600;margin-bottom:.2rem}
  input,select{font:inherit;width:100%;padding:.4rem .5rem;border:1px solid #bdbab2;border-radius:3px;background:#fff}
  .fe{color:var(--err);font-size:.9rem;margin-top:.15rem}
  .bar{display:flex;gap:.6rem;align-items:center;margin-bottom:1rem;flex-wrap:wrap}
  .bar input{max-width:280px}
  .pager{margin-top:1rem;display:flex;gap:.4rem;flex-wrap:wrap}
  .pager a,.pager span{padding:.2rem .6rem;border:1px solid var(--line);background:#fff;text-decoration:none}
  .pager span{background:var(--brand);color:#fff;border-color:var(--brand)}
  td form{display:inline;margin:0} td .acts{display:flex;gap:.3rem;flex-wrap:wrap}
  .muted{color:var(--mute)}
</style>
</head>
<body>
<nav>
  <a href="/index.php">Головна</a>
<?php foreach ($menu as $menuKey => $menuCfg): ?>
    <a href="/<?= h($menuKey) ?>/index.php"><?= h($menuCfg['title']) ?></a>
<?php endforeach; ?>
</nav>
<main>
<?php foreach ($_SESSION['flash'] ?? [] as [$flashType, $flashText]): ?>
  <div class="msg <?= $flashType === 'ok' ? 'ok' : 'err' ?>" role="status"><?= h($flashText) ?></div>
<?php endforeach; unset($_SESSION['flash']); ?>
