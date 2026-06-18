<?php
/**
 * Admin chrome (sidebar + top bar). Guards login.
 * Set $admin_title and $admin_active before including.
 */
require_once __DIR__ . '/auth.php';
require_login();

$admin_title  = $admin_title ?? 'Dashboard';
$admin_active = $admin_active ?? '';
$me = current_user();

$menu = [
    ['dashboard', '/admin/index.php',    'Dashboard'],
    ['posts',     '/admin/posts.php',    'Blog Posts'],
    ['pages',     '/admin/pages.php',    'Page Content'],
    ['messages',  '/admin/messages.php', 'Messages'],
    ['settings',  '/admin/settings.php', 'Settings'],
];

// Unread message count for the badge
try {
    $unread = (int) db()->query('SELECT COUNT(*) FROM messages WHERE is_read = 0')->fetchColumn();
} catch (Throwable $e) {
    $unread = 0;
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($admin_title) ?> · TWB Admin</title>
<meta name="robots" content="noindex, nofollow">
<link rel="icon" href="/assets/logo.png" type="image/png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/admin/admin.css">
</head>
<body class="admin">
<div class="admin-shell">
  <aside class="admin-sidebar">
    <a href="/admin/index.php" class="admin-logo"><img class="brand-logo invert" src="/assets/logo.png" alt="The Walking Billboard"> <span>Admin</span></a>
    <nav class="admin-nav">
      <?php foreach ($menu as [$key, $href, $label]): ?>
        <a href="<?= e($href) ?>" class="<?= $admin_active === $key ? 'active' : '' ?>">
          <?= e($label) ?>
          <?php if ($key === 'messages' && $unread): ?><span class="admin-badge"><?= $unread ?></span><?php endif; ?>
        </a>
      <?php endforeach; ?>
    </nav>
    <div class="admin-sidebar-foot">
      <a href="/" target="_blank" rel="noopener" class="admin-view-site">View site ↗</a>
      <a href="/admin/logout.php" class="admin-logout">Log out</a>
    </div>
  </aside>

  <main class="admin-main">
    <header class="admin-topbar">
      <h1><?= e($admin_title) ?></h1>
      <div class="admin-user">Signed in as <strong><?= e($me['name']) ?></strong></div>
    </header>
    <div class="admin-content">
      <?php foreach (take_flash() as $f): ?>
        <div class="admin-flash <?= e($f['type']) ?>"><?= e($f['msg']) ?></div>
      <?php endforeach; ?>
