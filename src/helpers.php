<?php
// Обгортка для екранування виводу (захист від XSS)
function h($v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// CSRF
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}
function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . h(csrf_token()) . '">';
}
function csrf_check(): void
{
    $token   = $_POST['csrf'] ?? '';
    $session = $_SESSION['csrf'] ?? '';
    if (!is_string($token) || $token === '' || $session === '' || !hash_equals($session, $token)) {
        http_response_code(419);
        exit('419: недійсний або відсутній CSRF-токен. Поверніться назад і оновіть сторінку.');
    }
}

// Flash-повідомлення (Post/Redirect/Get)
function flash(string $type, string $msg): void
{
    $_SESSION['flash'][] = [$type, $msg];
}
function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}
