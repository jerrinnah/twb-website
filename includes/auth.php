<?php
/**
 * Session + authentication helpers for the admin area.
 */
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

function start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || ($_SERVER['SERVER_PORT'] ?? '') == 443;
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_name('twb_admin');
    session_start();
}

function login_user(array $user): void
{
    start_session();
    session_regenerate_id(true);
    $_SESSION['uid']      = (int) $user['id'];
    $_SESSION['uname']    = $user['username'];
    $_SESSION['display']  = $user['name'] ?: $user['username'];
}

function logout_user(): void
{
    start_session();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

function is_logged_in(): bool
{
    start_session();
    return !empty($_SESSION['uid']);
}

function current_user(): ?array
{
    if (!is_logged_in()) {
        return null;
    }
    return [
        'id'       => (int) $_SESSION['uid'],
        'username' => $_SESSION['uname'] ?? '',
        'name'     => $_SESSION['display'] ?? '',
    ];
}

/** Guard: call at the top of every protected admin page. */
function require_login(): void
{
    if (!is_logged_in()) {
        redirect('/admin/login.php');
    }
}

/** Verify username + password against the DB. */
function attempt_login(string $username, string $password): bool
{
    $stmt = db()->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        login_user($user);
        return true;
    }
    return false;
}
