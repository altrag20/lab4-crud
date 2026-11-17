<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';

/**
 * Конфігурація сутностей. Назви таблиць/стовпців беруться ТІЛЬКИ звідси (не від користувача),
 * а всі значення від користувача передаються лише через підготовлені запити.
 */
function entities(): array
{
    $status_seat   = ['Вільне', 'Заброньоване'];
    $status_ticket = ['Оплачено', 'Заброньовано'];

    return [
        'performances' => [
            'title' => 'Вистави', 'one' => 'виставу', 'table' => 'performances',
            'fields' => [
                'title'            => ['label' => 'Назва',      'type' => 'text', 'max' => 100, 'required' => true],
                'genre'            => ['label' => 'Жанр',       'type' => 'text', 'max' => 50],
                'performance_date' => ['label' => 'Дата',       'type' => 'date', 'required' => true],
                'duration'         => ['label' => 'Тривалість (хв)', 'type' => 'int', 'min' => 1, 'max' => 600, 'required' => true],
            ],
            'select' => 't.id, t.title, t.genre, t.performance_date, t.duration',
            'from'   => 'performances t',
            'cols'   => ['id' => 'ID', 'title' => 'Назва', 'genre' => 'Жанр', 'performance_date' => 'Дата', 'duration' => 'Хв'],
            'search' => ['t.title', 't.genre'],
            'order'  => 't.performance_date, t.id',
            'cascade' => [
                'DELETE FROM tickets WHERE performance_id = :id',
                'DELETE FROM roles WHERE performance_id = :id',
            ],
            'demo_rollback' => true,
        ],
        'actors' => [
            'title' => 'Актори', 'one' => 'актора', 'table' => 'actors',
            'fields' => [
                'full_name'  => ['label' => 'ПІБ',           'type' => 'text', 'max' => 150, 'required' => true],
                'birth_date' => ['label' => 'Дата народження', 'type' => 'date'],
                'experience' => ['label' => 'Досвід (років)', 'type' => 'int', 'min' => 0, 'max' => 80],
            ],
            'select' => 't.id, t.full_name, t.birth_date, t.experience',
            'from'   => 'actors t',
            'cols'   => ['id' => 'ID', 'full_name' => 'ПІБ', 'birth_date' => 'Дата народження', 'experience' => 'Досвід'],
            'search' => ['t.full_name'],
            'order'  => 't.id',
            'cascade' => ['DELETE FROM roles WHERE actor_id = :id'],
        ],
        'roles' => [
            'title' => 'Ролі', 'one' => 'роль', 'table' => 'roles',
            'fields' => [
                'role_name'      => ['label' => 'Назва ролі', 'type' => 'text', 'max' => 100, 'required' => true],
                'performance_id' => ['label' => 'Вистава', 'type' => 'fk', 'required' => true,
                                     'sql' => 'SELECT id, title AS name FROM performances ORDER BY title'],
                'actor_id'       => ['label' => 'Актор', 'type' => 'fk', 'required' => true,
                                     'sql' => 'SELECT id, full_name AS name FROM actors ORDER BY full_name'],
            ],
            'select' => 't.id, t.role_name, p.title AS performance_title, a.full_name AS actor_name',
            'from'   => 'roles t JOIN performances p ON p.id = t.performance_id JOIN actors a ON a.id = t.actor_id',
            'cols'   => ['id' => 'ID', 'role_name' => 'Роль', 'performance_title' => 'Вистава', 'actor_name' => 'Актор'],
            'search' => ['t.role_name', 'p.title', 'a.full_name'],
            'order'  => 't.id',
            'cascade' => [],
        ],
        'halls' => [
            'title' => 'Зали', 'one' => 'зал', 'table' => 'halls',
            'fields' => [
                'hall_name'   => ['label' => 'Назва залу', 'type' => 'text', 'max' => 100, 'required' => true],
                'seats_count' => ['label' => 'Кількість місць', 'type' => 'int', 'min' => 1, 'max' => 10000, 'required' => true],
            ],
            'select' => 't.id, t.hall_name, t.seats_count',
            'from'   => 'halls t',
            'cols'   => ['id' => 'ID', 'hall_name' => 'Назва', 'seats_count' => 'Місць'],
            'search' => ['t.hall_name'],
            'order'  => 't.id',
            'cascade' => [
                'DELETE FROM tickets WHERE seat_id IN (SELECT id FROM seats WHERE hall_id = :id)',
                'DELETE FROM seats WHERE hall_id = :id',
            ],
        ],
        'seats' => [
            'title' => 'Місця', 'one' => 'місце', 'table' => 'seats',
            'fields' => [
                'hall_id'     => ['label' => 'Зал', 'type' => 'fk', 'required' => true,
                                  'sql' => 'SELECT id, hall_name AS name FROM halls ORDER BY hall_name'],
                'row_number'  => ['label' => 'Ряд',   'type' => 'int', 'min' => 1, 'max' => 200, 'required' => true],
                'seat_number' => ['label' => 'Місце', 'type' => 'int', 'min' => 1, 'max' => 200, 'required' => true],
                'status'      => ['label' => 'Статус', 'type' => 'select', 'options' => $status_seat, 'required' => true],
            ],
            'select' => 't.id, h.hall_name, t."row_number" AS "row_number", t.seat_number, t.status',
            'from'   => 'seats t JOIN halls h ON h.id = t.hall_id',
            'cols'   => ['id' => 'ID', 'hall_name' => 'Зал', 'row_number' => 'Ряд', 'seat_number' => 'Місце', 'status' => 'Статус'],
            'search' => ['h.hall_name', 't.status'],
            'order'  => 'h.hall_name, t."row_number", t.seat_number',
            'cascade' => ['DELETE FROM tickets WHERE seat_id = :id'],
        ],
        'viewers' => [
            'title' => 'Глядачі', 'one' => 'глядача', 'table' => 'viewers',
            'fields' => [
                'full_name' => ['label' => 'ПІБ',     'type' => 'text', 'max' => 150, 'required' => true],
                'phone'     => ['label' => 'Телефон', 'type' => 'phone', 'max' => 30],
                'email'     => ['label' => 'Email',   'type' => 'email', 'max' => 100],
            ],
            'select' => 't.id, t.full_name, t.phone, t.email',
            'from'   => 'viewers t',
            'cols'   => ['id' => 'ID', 'full_name' => 'ПІБ', 'phone' => 'Телефон', 'email' => 'Email'],
            'search' => ['t.full_name', 't.email'],
            'order'  => 't.id',
            'cascade' => ['DELETE FROM tickets WHERE viewer_id = :id'],
        ],
        'tickets' => [
            'title' => 'Квитки', 'one' => 'квиток', 'table' => 'tickets',
            'fields' => [
                'price'          => ['label' => 'Ціна (грн)', 'type' => 'decimal', 'min' => 0, 'max' => 99999, 'required' => true],
                'status'         => ['label' => 'Статус', 'type' => 'select', 'options' => $status_ticket, 'required' => true],
                'performance_id' => ['label' => 'Вистава', 'type' => 'fk', 'required' => true,
                                     'sql' => 'SELECT id, title AS name FROM performances ORDER BY title'],
                'seat_id'        => ['label' => 'Місце', 'type' => 'fk', 'required' => true,
                                     'sql' => "SELECT s.id, h.hall_name || ', ряд ' || s.\"row_number\" || ', місце ' || s.seat_number AS name
                                               FROM seats s JOIN halls h ON h.id = s.hall_id ORDER BY h.hall_name, s.\"row_number\", s.seat_number"],
                'viewer_id'      => ['label' => 'Глядач', 'type' => 'fk', 'required' => true,
                                     'sql' => 'SELECT id, full_name AS name FROM viewers ORDER BY full_name'],
            ],
            'select' => 't.id, p.title AS performance_title, v.full_name AS viewer_name,
                        h.hall_name || \', ряд \' || s."row_number" || \', місце \' || s.seat_number AS seat_info,
                        t.price, t.status',
            'from'   => 'tickets t JOIN performances p ON p.id = t.performance_id
                         JOIN viewers v ON v.id = t.viewer_id
                         JOIN seats s ON s.id = t.seat_id JOIN halls h ON h.id = s.hall_id',
            'cols'   => ['id' => 'ID', 'performance_title' => 'Вистава', 'viewer_name' => 'Глядач', 'seat_info' => 'Місце', 'price' => 'Ціна', 'status' => 'Статус'],
            'search' => ['p.title', 'v.full_name', 't.status'],
            'order'  => 't.id',
            'cascade' => [],
        ],
    ];
}

function entity(string $e): array
{
    $all = entities();
    if (!isset($all[$e])) {
        http_response_code(404);
        exit('404');
    }
    return $all[$e];
}

function not_found(string $text = 'Запис не знайдено.'): never
{
    http_response_code(404);
    $pageTitle = '404';
    require __DIR__ . '/header.php';
    echo '<h1>404</h1><p>' . h($text) . '</p><p><a href="/index.php">На головну</a></p>';
    require __DIR__ . '/footer.php';
    exit;
}

/* ---------- Допоміжне для форм ---------- */

function fk_options(array $spec): array
{
    static $cache = [];
    return $cache[$spec['sql']] ??= db()->query($spec['sql'])->fetchAll(PDO::FETCH_KEY_PAIR);
}

function read_input(array $cfg): array
{
    $data = [];
    foreach ($cfg['fields'] as $f => $spec) {
        $v = $_POST[$f] ?? '';
        $v = is_string($v) ? trim($v) : '';
        if ($spec['type'] === 'decimal') {
            $v = str_replace(',', '.', $v);
        }
        $data[$f] = $v;
    }
    return $data;
}

function validate(array $cfg, array $d): array
{
    $err = [];
    foreach ($cfg['fields'] as $f => $s) {
        $v = $d[$f];
        $label = $s['label'];
        if ($v === '') {
            if (!empty($s['required'])) {
                $err[$f] = "Поле «{$label}» обов’язкове.";
            }
            continue;
        }
        switch ($s['type']) {
            case 'text':
            case 'phone':
            case 'email':
                if (mb_strlen($v) > $s['max']) {
                    $err[$f] = "Поле «{$label}»: не більше {$s['max']} символів.";
                } elseif ($s['type'] === 'email' && !filter_var($v, FILTER_VALIDATE_EMAIL)) {
                    $err[$f] = 'Невірний формат email.';
                } elseif ($s['type'] === 'phone' && !preg_match('/^\+?[0-9\s\-()]{7,20}$/', $v)) {
                    $err[$f] = 'Невірний формат телефону (напр. +380501234567).';
                }
                break;
            case 'int':
                $n = filter_var($v, FILTER_VALIDATE_INT, ['options' => ['min_range' => $s['min'], 'max_range' => $s['max']]]);
                if ($n === false) {
                    $err[$f] = "Поле «{$label}»: ціле число від {$s['min']} до {$s['max']}.";
                }
                break;
            case 'decimal':
                if (!preg_match('/^\d{1,6}(\.\d{1,2})?$/', $v) || (float)$v < $s['min'] || (float)$v > $s['max']) {
                    $err[$f] = "Поле «{$label}»: число від {$s['min']} до {$s['max']} (до 2 знаків після крапки).";
                }
                break;
            case 'date':
                $dt = DateTime::createFromFormat('Y-m-d', $v);
                if (!$dt || $dt->format('Y-m-d') !== $v) {
                    $err[$f] = "Поле «{$label}»: невірна дата.";
                }
                break;
            case 'select':
                if (!in_array($v, $s['options'], true)) {
                    $err[$f] = "Поле «{$label}»: оберіть значення зі списку.";
                }
                break;
            case 'fk':
                $id = filter_var($v, FILTER_VALIDATE_INT);
                if ($id === false || !array_key_exists($id, fk_options($s))) {
                    $err[$f] = "Поле «{$label}»: обраного запису не існує.";
                }
                break;
        }
    }
    return $err;
}

function q(string $col): string
{
    return '"' . $col . '"';
}

/* ---------- READ: список + пошук + пагінація ---------- */

function crud_index(string $e): void
{
    $cfg = entity($e);
    $pdo = db();
    $per = 5;
    $search = trim((string)($_GET['q'] ?? ''));
    $page = max(1, (int)($_GET['page'] ?? 1));

    $where = '';
    $params = [];
    if ($search !== '') {
        $parts = [];
        foreach ($cfg['search'] as $i => $col) {
            $parts[] = "$col ILIKE :q$i";
            $params[":q$i"] = '%' . addcslashes($search, '%_\\') . '%';
        }
        $where = ' WHERE (' . implode(' OR ', $parts) . ')';
    }

    $st = $pdo->prepare("SELECT COUNT(*) FROM {$cfg['from']}{$where}");
    $st->execute($params);
    $total = (int)$st->fetchColumn();
    $pages = max(1, (int)ceil($total / $per));
    $page = min($page, $pages);

    $st = $pdo->prepare("SELECT {$cfg['select']} FROM {$cfg['from']}{$where} ORDER BY {$cfg['order']} LIMIT :lim OFFSET :off");
    foreach ($params as $k => $v) {
        $st->bindValue($k, $v, PDO::PARAM_STR);
    }
    $st->bindValue(':lim', $per, PDO::PARAM_INT);
    $st->bindValue(':off', ($page - 1) * $per, PDO::PARAM_INT);
    $st->execute();
    $rows = $st->fetchAll();

    $pageTitle = $cfg['title'];
    require __DIR__ . '/header.php';
    ?>
    <h1><?= h($cfg['title']) ?></h1>
    <div class="bar">
      <form method="get" class="bar" style="margin:0">
        <input type="search" name="q" value="<?= h($search) ?>" placeholder="Пошук…" aria-label="Пошук">
        <button type="submit">Знайти</button>
        <?php if ($search !== ''): ?><a class="btn alt" href="/<?= h($e) ?>/index.php">Скинути</a><?php endif; ?>
      </form>
      <a class="btn" href="/<?= h($e) ?>/create.php">Додати <?= h($cfg['one']) ?></a>
    </div>

    <?php if (!$rows): ?>
      <p class="muted">Записів не знайдено.</p>
    <?php else: ?>
    <div class="wrap"><table>
      <thead><tr>
        <?php foreach ($cfg['cols'] as $label): ?><th><?= h($label) ?></th><?php endforeach; ?>
        <th>Дії</th>
      </tr></thead>
      <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <?php foreach ($cfg['cols'] as $key => $_): ?>
            <td><?= $r[$key] === null ? '<span class="muted">—</span>' : h($r[$key]) ?></td>
          <?php endforeach; ?>
          <td><div class="acts">
            <a class="btn alt" href="/<?= h($e) ?>/edit.php?id=<?= (int)$r['id'] ?>">Редагувати</a>
            <form method="post" action="/<?= h($e) ?>/delete.php" onsubmit="return confirm('Видалити запис?')">
              <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <button class="danger" name="mode" value="plain">Видалити</button>
            </form>
            <?php if ($cfg['cascade']): ?>
            <form method="post" action="/<?= h($e) ?>/delete.php" onsubmit="return confirm('Видалити запис разом із залежними?')">
              <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <button class="danger alt" name="mode" value="cascade">Видалити з залежними</button>
              <?php if (!empty($cfg['demo_rollback'])): ?>
              <button class="alt" name="mode" value="cascade_fail" title="Демонстрація відкату транзакції">Тест відкату</button>
              <?php endif; ?>
            </form>
            <?php endif; ?>
          </div></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
    <p class="muted">Знайдено: <?= $total ?>. Сторінка <?= $page ?> з <?= $pages ?>.</p>
    <?php if ($pages > 1): ?>
    <div class="pager">
      <?php for ($p = 1; $p <= $pages; $p++): ?>
        <?php if ($p === $page): ?><span><?= $p ?></span>
        <?php else: ?><a href="?q=<?= urlencode($search) ?>&page=<?= $p ?>"><?= $p ?></a><?php endif; ?>
      <?php endfor; ?>
    </div>
    <?php endif; endif;
    require __DIR__ . '/footer.php';
}

/* ---------- CREATE / UPDATE ---------- */

function crud_form(string $e, bool $edit = false): void
{
    $cfg = entity($e);
    $pdo = db();
    $id = null;
    $errors = [];
    $data = array_fill_keys(array_keys($cfg['fields']), '');

    if ($edit) {
        $id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT);
        $cols = implode(', ', array_map('q', array_keys($cfg['fields'])));
        $row = false;
        if ($id !== false) {
            $st = $pdo->prepare("SELECT $cols FROM {$cfg['table']} WHERE id = :id");
            $st->execute([':id' => $id]);
            $row = $st->fetch();
        }
        if (!$row) {
            not_found();
        }
        foreach ($row as $k => $v) {
            $data[$k] = $v === null ? '' : (string)$v;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_check();                              // 1) CSRF
        $data = read_input($cfg);                  // 2) прочитати й очистити
        $errors = validate($cfg, $data);           // 3) серверна валідація
        if (!$errors) {
            $params = [];
            foreach ($data as $f => $v) {
                $params[":$f"] = $v === '' ? null : $v;
            }
            try {                                  // 4) запис
                if ($edit) {
                    $set = implode(', ', array_map(fn($f) => q($f) . " = :$f", array_keys($cfg['fields'])));
                    $params[':id'] = $id;
                    $st = $pdo->prepare("UPDATE {$cfg['table']} SET $set WHERE id = :id");
                } else {
                    $cols = implode(', ', array_map('q', array_keys($cfg['fields'])));
                    $ph = implode(', ', array_map(fn($f) => ":$f", array_keys($cfg['fields'])));
                    $st = $pdo->prepare("INSERT INTO {$cfg['table']} ($cols) VALUES ($ph)");
                }
                $st->execute($params);
                flash('ok', $edit ? 'Зміни збережено.' : 'Запис створено.');
                redirect("/$e/index.php");         // 5) PRG
            } catch (PDOException $ex) {
                $code = (string)$ex->getCode();
                if ($code === '23505') {
                    $errors['_'] = 'Такий запис уже існує (порушено унікальність).';
                } elseif ($code === '23503') {
                    $errors['_'] = 'Вказано неіснуючий пов’язаний запис.';
                } else {
                    error_log($ex->getMessage());
                    $errors['_'] = 'Помилка бази даних. Спробуйте ще раз.';
                }
            }
        }
    }

    $pageTitle = ($edit ? 'Редагування' : 'Додавання') . ': ' . $cfg['title'];
    $action = $edit ? "/$e/edit.php?id=" . (int)$id : "/$e/create.php";
    require __DIR__ . '/header.php';
    ?>
    <h1><?= $edit ? 'Редагувати' : 'Додати' ?> <?= h($cfg['one']) ?></h1>
    <?php if (isset($errors['_'])): ?><div class="msg err"><?= h($errors['_']) ?></div><?php endif; ?>
    <form class="f" method="post" action="<?= h($action) ?>" novalidate>
      <?= csrf_field() ?>
      <?php foreach ($cfg['fields'] as $f => $s): $val = $data[$f]; ?>
        <div class="row">
          <label for="<?= h($f) ?>"><?= h($s['label']) ?><?= !empty($s['required']) ? ' *' : '' ?></label>
          <?php if ($s['type'] === 'fk' || $s['type'] === 'select'): ?>
            <select id="<?= h($f) ?>" name="<?= h($f) ?>">
              <option value="">— оберіть —</option>
              <?php $opts = $s['type'] === 'fk' ? fk_options($s) : array_combine($s['options'], $s['options']);
                    foreach ($opts as $k => $label): ?>
                <option value="<?= h($k) ?>" <?= (string)$k === (string)$val ? 'selected' : '' ?>><?= h($label) ?></option>
              <?php endforeach; ?>
            </select>
          <?php else:
              $type = ['int' => 'number', 'decimal' => 'text', 'date' => 'date', 'email' => 'email', 'phone' => 'tel'][$s['type']] ?? 'text'; ?>
            <input id="<?= h($f) ?>" name="<?= h($f) ?>" type="<?= $type ?>" value="<?= h($val) ?>"
                   <?= $s['type'] === 'decimal' ? 'inputmode="decimal"' : '' ?>>
          <?php endif; ?>
          <?php if (isset($errors[$f])): ?><div class="fe"><?= h($errors[$f]) ?></div><?php endif; ?>
        </div>
      <?php endforeach; ?>
      <button type="submit">Зберегти</button>
      <a class="btn alt" href="/<?= h($e) ?>/index.php">Скасувати</a>
    </form>
    <?php
    require __DIR__ . '/footer.php';
}

/* ---------- DELETE (тільки POST) + транзакція ---------- */

function crud_delete(string $e): void
{
    $cfg = entity($e);
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('405: видалення можливе лише методом POST.');
    }
    csrf_check();
    $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
    $mode = $_POST['mode'] ?? 'plain';
    if ($id === false || !in_array($mode, ['plain', 'cascade', 'cascade_fail'], true)) {
        flash('err', 'Некоректний запит.');
        redirect("/$e/index.php");
    }
    if ($mode !== 'plain' && !$cfg['cascade']) {
        $mode = 'plain';
    }

    $pdo = db();
    try {
        if ($mode === 'plain') {
            $st = $pdo->prepare("DELETE FROM {$cfg['table']} WHERE id = :id");
            $st->execute([':id' => $id]);
            $st->rowCount() ? flash('ok', 'Запис видалено.') : flash('err', 'Запис не знайдено.');
        } else {
            $pdo->beginTransaction();
            foreach ($cfg['cascade'] as $sql) {
                $pdo->prepare($sql)->execute([':id' => $id]);
            }
            if ($mode === 'cascade_fail') {
                throw new RuntimeException('Імітація помилки під час видалення.');
            }
            $st = $pdo->prepare("DELETE FROM {$cfg['table']} WHERE id = :id");
            $st->execute([':id' => $id]);
            $pdo->commit();
            flash('ok', 'Запис і всі залежні записи видалено.');
        }
    } catch (Throwable $ex) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        if ($ex instanceof PDOException && (string)$ex->getCode() === '23503') {
            flash('err', 'Неможливо видалити: на цей запис посилаються інші таблиці. Спочатку видаліть залежні записи або скористайтеся «Видалити з залежними».');
        } elseif ($ex instanceof RuntimeException) {
            flash('err', $ex->getMessage() . ' Транзакцію відкочено — дані не змінено.');
        } else {
            error_log($ex->getMessage());
            flash('err', 'Помилка бази даних.');
        }
    }
    redirect("/$e/index.php");
}
