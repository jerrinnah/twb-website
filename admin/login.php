<?php
require_once __DIR__ . '/../includes/auth.php';

if (is_logged_in()) {
    redirect('/admin/index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'Your session expired. Please try again.';
    } else {
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        if ($username === '' || $password === '') {
            $error = 'Enter your username and password.';
        } elseif (attempt_login($username, $password)) {
            redirect('/admin/index.php');
        } else {
            usleep(400000); // small delay to slow brute force
            $error = 'Incorrect username or password.';
        }
    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Log in · TWB Admin</title>
<meta name="robots" content="noindex, nofollow">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/admin/admin.css">
</head>
<body class="admin">
<div class="login-wrap">
  <div class="login-card">
    <div class="login-logo"><span class="login-logo-mark">TWB</span> The Walking Billboard</div>
    <h1>Welcome back</h1>
    <p class="sub">Sign in to manage your site.</p>
    <?php if ($error): ?><div class="form-banner-admin admin-flash error"><?= e($error) ?></div><?php endif; ?>
    <form method="post" action="/admin/login.php">
      <?= csrf_field() ?>
      <div class="field">
        <label for="username">Username</label>
        <input class="input" id="username" name="username" type="text" autofocus autocomplete="username" required>
      </div>
      <div class="field">
        <label for="password">Password</label>
        <input class="input" id="password" name="password" type="password" autocomplete="current-password" required>
      </div>
      <button class="btn btn-primary" type="submit" style="width:100%;justify-content:center">Log in</button>
    </form>
  </div>
</div>
</body>
</html>
