<?php
/**
 * One-time setup wizard.
 *
 * 1. Create includes/config.php (copy of config.sample.php) with your DB details.
 * 2. Visit /install.php in your browser.
 * 3. It creates the database tables and your admin account.
 * 4. DELETE this file afterwards (it refuses to run once an admin exists).
 */
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

$step  = 'form';
$error = '';
$pdo   = null;

try {
    $pdo = db();
} catch (Throwable $e) {
    $error = 'Could not connect to the database. Check includes/config.php. (' . $e->getMessage() . ')';
}

// Block re-running once an admin exists
$adminExists = false;
if ($pdo) {
    try {
        $adminExists = (bool) $pdo->query('SELECT 1 FROM users LIMIT 1')->fetchColumn();
    } catch (Throwable $e) {
        $adminExists = false; // tables not created yet
    }
}

if ($adminExists) {
    $step = 'done';
    $error = 'Setup already completed. For security, delete install.php from your server now.';
}

if ($step === 'form' && $pdo && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $name     = trim((string) ($_POST['name'] ?? ''));
    $email    = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Username and password are required.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } else {
        try {
            // 1) Create schema
            $sql = file_get_contents(__DIR__ . '/sql/schema.sql');
            if ($sql === false) { throw new RuntimeException('Could not read sql/schema.sql'); }
            $pdo->exec($sql);

            // 2) Seed core site settings (only if absent)
            $seed = [
                'site.name' => 'The Walking Billboard',
                'site.phone' => '+2348174623187',
                'site.phone_display' => '+234 817 462 3187',
                'site.email' => $email !== '' ? $email : 'hello@thewalkingbillboard.com',
                'site.address' => 'Port Harcourt, Rivers State · Nigeria',
                'site.whatsapp' => 'https://wa.me/2348174623187',
                'site.instagram' => '#', 'site.twitter' => '#', 'site.facebook' => '#', 'site.linkedin' => '#',
                'site.description' => 'PR and brand strategy studio in Port Harcourt, Nigeria.',
                'site.url' => defined('SITE_URL') ? SITE_URL : 'https://thewbillboard.com',
            ];
            $ins = $pdo->prepare('INSERT IGNORE INTO settings (setting_key, setting_value) VALUES (?, ?)');
            foreach ($seed as $k => $v) { $ins->execute([$k, $v]); }

            // 3) Create admin
            $stmt = $pdo->prepare('INSERT INTO users (username, name, email, password_hash) VALUES (?, ?, ?, ?)');
            $stmt->execute([$username, $name ?: $username, $email, password_hash($password, PASSWORD_DEFAULT)]);

            $step = 'success';
        } catch (Throwable $e) {
            $error = 'Setup failed: ' . $e->getMessage();
        }
    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Install · The Walking Billboard CMS</title>
<meta name="robots" content="noindex, nofollow">
<link rel="icon" href="/assets/logo.png" type="image/png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/admin/admin.css">
</head>
<body class="admin">
<div class="login-wrap">
  <div class="login-card" style="max-width:440px">
    <div class="login-logo"><img class="brand-logo" src="/assets/logo.png" alt="The Walking Billboard"> <span>CMS Setup</span></div>

    <?php if ($error): ?><div class="admin-flash error"><?= e($error) ?></div><?php endif; ?>

    <?php if ($step === 'success'): ?>
      <h1>✓ All set!</h1>
      <p class="sub">Your database is ready and your admin account is created.</p>
      <div class="admin-flash error" style="margin:1rem 0">⚠ Important: delete <code>install.php</code> from your server now.</div>
      <a class="btn btn-primary" href="/admin/login.php" style="width:100%;justify-content:center">Go to admin login</a>

    <?php elseif ($step === 'done'): ?>
      <h1>Already installed</h1>
      <p class="sub">Delete install.php, then use the admin login.</p>
      <a class="btn btn-primary" href="/admin/login.php" style="width:100%;justify-content:center">Admin login</a>

    <?php elseif (!$pdo): ?>
      <h1>Database not connected</h1>
      <p class="sub">Copy <code>includes/config.sample.php</code> to <code>includes/config.php</code> and fill in your cPanel MySQL details, then reload this page.</p>

    <?php else: ?>
      <h1>Create your admin account</h1>
      <p class="sub">This sets up the database and your login.</p>
      <form method="post" action="/install.php">
        <div class="field"><label for="name">Your name</label><input class="input" id="name" name="name" type="text" placeholder="Jerry Nnah"></div>
        <div class="field"><label for="username">Username</label><input class="input" id="username" name="username" type="text" required placeholder="admin"></div>
        <div class="field"><label for="email">Email</label><input class="input" id="email" name="email" type="email" placeholder="hello@thewalkingbillboard.com"></div>
        <div class="field"><label for="password">Password (min 8 chars)</label><input class="input" id="password" name="password" type="password" required></div>
        <button class="btn btn-primary" type="submit" style="width:100%;justify-content:center">Install</button>
      </form>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
