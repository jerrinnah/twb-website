<?php
/**
 * Shared helpers: settings, escaping, slugs, CSRF, flash, queries.
 */
declare(strict_types=1);

require_once __DIR__ . '/db.php';

/* ── Friendly failure for uncaught errors (no info leak) ──── */
if (PHP_SAPI !== 'cli' && !defined('TWB_NO_HANDLER')) {
    set_exception_handler(function (Throwable $ex): void {
        http_response_code(500);
        $debug = defined('APP_DEBUG') && APP_DEBUG;
        if ($debug) {
            echo '<pre style="padding:2rem;font:14px monospace">' .
                 htmlspecialchars((string) $ex, ENT_QUOTES) . '</pre>';
        } else {
            echo '<!doctype html><meta charset="utf-8"><title>Temporarily unavailable</title>' .
                 '<div style="font-family:system-ui;max-width:480px;margin:18vh auto;text-align:center;color:#333">' .
                 '<h1 style="font-size:1.4rem">We\'ll be right back</h1>' .
                 '<p style="color:#777">The site is undergoing maintenance. Please try again in a moment.</p></div>';
        }
    });
}

/* ── Output escaping ─────────────────────────────────────── */
function e(?string $s): string
{
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

/* ── Settings / editable page content (key-value) ────────── */
function all_settings(): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    $cache = [];
    try {
        foreach (db()->query('SELECT setting_key, setting_value FROM settings') as $row) {
            $cache[$row['setting_key']] = $row['setting_value'];
        }
    } catch (Throwable $e) {
        // table may not exist yet (pre-install)
    }
    return $cache;
}

function setting(string $key, string $default = ''): string
{
    $all = all_settings();
    return array_key_exists($key, $all) && $all[$key] !== '' ? $all[$key] : $default;
}

function set_setting(string $key, string $value): void
{
    $stmt = db()->prepare(
        'INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
    );
    $stmt->execute([$key, $value]);
}

/* ── Slugs ───────────────────────────────────────────────── */
function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
    $text = trim($text, '-');
    return $text !== '' ? $text : 'post-' . substr(md5((string) mt_rand()), 0, 6);
}

function unique_post_slug(string $slug, ?int $ignoreId = null): string
{
    $base = $slug;
    $i = 1;
    while (true) {
        $sql = 'SELECT id FROM posts WHERE slug = ?' . ($ignoreId ? ' AND id <> ?' : '') . ' LIMIT 1';
        $stmt = db()->prepare($sql);
        $stmt->execute($ignoreId ? [$slug, $ignoreId] : [$slug]);
        if (!$stmt->fetch()) {
            return $slug;
        }
        $slug = $base . '-' . (++$i);
    }
}

/* ── Excerpts / dates ────────────────────────────────────── */
function make_excerpt(string $html, int $len = 160): string
{
    $text = trim(preg_replace('/\s+/', ' ', strip_tags($html)) ?? '');
    if (mb_strlen($text) <= $len) {
        return $text;
    }
    return rtrim(mb_substr($text, 0, $len)) . '…';
}

function fmt_date(?string $datetime): string
{
    if (!$datetime) {
        return '';
    }
    $ts = strtotime($datetime);
    return $ts ? date('M j, Y', $ts) : '';
}

/* ── Post queries ────────────────────────────────────────── */
function get_published_posts(int $limit = 9, int $offset = 0, ?string $categorySlug = null): array
{
    $where  = 'p.status = "published" AND (p.published_at IS NULL OR p.published_at <= NOW())';
    $params = [];
    if ($categorySlug) {
        $where   .= ' AND c.slug = ?';
        $params[] = $categorySlug;
    }
    $stmt = db()->prepare(
        'SELECT p.*, c.name AS category_name, c.slug AS category_slug
         FROM posts p LEFT JOIN categories c ON c.id = p.category_id
         WHERE ' . $where . '
         ORDER BY COALESCE(p.published_at, p.created_at) DESC
         LIMIT ? OFFSET ?'
    );
    $i = 1;
    foreach ($params as $p) { $stmt->bindValue($i++, $p); }
    $stmt->bindValue($i++, $limit, PDO::PARAM_INT);
    $stmt->bindValue($i++, $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function count_published_posts(?string $categorySlug = null): int
{
    try {
        $where  = 'p.status = "published" AND (p.published_at IS NULL OR p.published_at <= NOW())';
        $params = [];
        if ($categorySlug) {
            $where   .= ' AND c.slug = ?';
            $params[] = $categorySlug;
        }
        $stmt = db()->prepare(
            'SELECT COUNT(*) FROM posts p LEFT JOIN categories c ON c.id = p.category_id WHERE ' . $where
        );
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    } catch (Throwable $e) {
        return 0;
    }
}

function get_post_by_slug(string $slug): ?array
{
    $stmt = db()->prepare(
        'SELECT p.*, c.name AS category_name, c.slug AS category_slug
         FROM posts p LEFT JOIN categories c ON c.id = p.category_id
         WHERE p.slug = ? LIMIT 1'
    );
    $stmt->execute([$slug]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function get_categories(): array
{
    try {
        return db()->query('SELECT * FROM categories ORDER BY name')->fetchAll();
    } catch (Throwable $e) {
        return [];
    }
}

/* ── Image uploads ───────────────────────────────────────── */
/**
 * Validate + store an uploaded image into /uploads.
 * Returns ['url' => '/uploads/...'] on success or ['error' => '...'] on failure.
 */
function store_image_upload(array $file, int $maxBytes = 5242880): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return ['error' => 'No file received.'];
    }
    if (($file['size'] ?? 0) > $maxBytes) {
        return ['error' => 'Image too large (max ' . round($maxBytes / 1048576) . ' MB).'];
    }
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset($allowed[$mime])) {
        return ['error' => 'Only JPG, PNG, GIF, or WEBP images are allowed.'];
    }
    $dir = __DIR__ . '/../uploads';
    if (!is_dir($dir)) { @mkdir($dir, 0755, true); }
    $name = date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) {
        return ['error' => 'Could not save the file. Check that /uploads is writable.'];
    }
    @chmod($dir . '/' . $name, 0644);
    return ['url' => '/uploads/' . $name];
}

/* ── CSRF ────────────────────────────────────────────────── */
function csrf_token(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): bool
{
    $sent = $_POST['csrf'] ?? '';
    return is_string($sent) && hash_equals(csrf_token(), $sent);
}

/* ── Flash messages ──────────────────────────────────────── */
function flash(string $msg, string $type = 'success'): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $_SESSION['flash'][] = ['msg' => $msg, 'type' => $type];
}

function take_flash(): array
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $f;
}

/* ── Misc ────────────────────────────────────────────────── */
function redirect(string $to): never
{
    header('Location: ' . $to);
    exit;
}

function current_url_is(string $path): bool
{
    $cur = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
    return rtrim($cur, '/') === rtrim($path, '/');
}
