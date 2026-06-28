<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$siteFields = [
    ['site.name', 'Business name', 'The Walking Billboard'],
    ['site.phone', 'Phone (dial link, no spaces)', '+2348174623187'],
    ['site.phone_display', 'Phone (shown)', '+234 817 462 3187'],
    ['site.email', 'Email (shown on site)', 'enquiry@thewbillboard.com'],
    ['site.enquiry_email', 'Enquiry inbox (form notifications)', 'enquiry@thewbillboard.com'],
    ['site.address', 'Address', 'Port Harcourt, Rivers State · Nigeria'],
    ['site.instagram', 'Instagram URL', '#'],
    ['site.twitter', 'X / Twitter URL', '#'],
    ['site.facebook', 'Facebook URL', '#'],
    ['site.linkedin', 'LinkedIn URL', '#'],
    ['site.whatsapp', 'WhatsApp URL', 'https://wa.me/2348174623187'],
];

$errors = [];
$me = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    $form = $_POST['form'] ?? '';

    if ($form === 'branding') {
        if (isset($_POST['reset'])) {
            set_setting('site.logo', '/assets/logo.png');
            flash('Logo reset to the default.');
            redirect('/admin/settings.php');
        }
        $result = store_image_upload($_FILES['logo'] ?? []);
        if (isset($result['error'])) {
            $errors[] = $result['error'];
        } else {
            set_setting('site.logo', $result['url']);
            flash('Logo updated.');
            redirect('/admin/settings.php');
        }
    }

    if ($form === 'site') {
        // Field names use array notation (settings[site.phone]) because PHP
        // rewrites dots to underscores in plain POST keys.
        $posted = is_array($_POST['settings'] ?? null) ? $_POST['settings'] : [];
        foreach ($siteFields as [$key, , ]) {
            if (array_key_exists($key, $posted)) {
                set_setting($key, trim((string) $posted[$key]));
            }
        }
        flash('Site settings saved.');
        redirect('/admin/settings.php');
    }

    if ($form === 'password') {
        $cur = (string) ($_POST['current'] ?? '');
        $new = (string) ($_POST['new'] ?? '');
        $cf  = (string) ($_POST['confirm'] ?? '');
        $stmt = db()->prepare('SELECT password_hash FROM users WHERE id=?');
        $stmt->execute([$me['id']]);
        $hash = $stmt->fetchColumn();

        if (!$hash || !password_verify($cur, $hash)) {
            $errors[] = 'Your current password is incorrect.';
        } elseif (strlen($new) < 8) {
            $errors[] = 'New password must be at least 8 characters.';
        } elseif ($new !== $cf) {
            $errors[] = 'New password and confirmation do not match.';
        } else {
            db()->prepare('UPDATE users SET password_hash=? WHERE id=?')
                ->execute([password_hash($new, PASSWORD_DEFAULT), $me['id']]);
            flash('Password updated.');
            redirect('/admin/settings.php');
        }
    }
}

$admin_title = 'Settings';
$admin_active = 'settings';
include __DIR__ . '/../includes/admin-header.php';
?>

<?php if ($errors): ?><div class="admin-flash error"><?= e(implode(' ', $errors)) ?></div><?php endif; ?>

<?php $logo = setting('site.logo', '/assets/logo.png'); ?>
<form method="post" action="/admin/settings.php" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <input type="hidden" name="form" value="branding">
  <div class="panel">
    <h2>Logo</h2>
    <p class="muted" style="margin-bottom:1.25rem">Shown in the navigation, footer, admin, and as the browser icon. A transparent PNG works best — it appears in white automatically on dark sections.</p>
    <div class="logo-preview-row">
      <div class="logo-swatch light"><img src="<?= e($logo) ?>" alt="Logo on light background"></div>
      <div class="logo-swatch dark"><img class="invert" src="<?= e($logo) ?>" alt="Logo on dark background"></div>
    </div>
    <div class="field" style="margin-top:1.25rem">
      <label for="logo">Upload a new logo</label>
      <input class="input" id="logo" name="logo" type="file" accept="image/png,image/jpeg,image/webp,image/gif">
      <p class="hint">PNG, JPG, WEBP or GIF · max 5 MB · recommended ~400–600px wide.</p>
    </div>
    <div class="actions">
      <button class="btn btn-primary" type="submit">Save logo</button>
      <button class="btn btn-ghost" type="submit" name="reset" value="1" onclick="return confirm('Reset to the default logo?')">Reset to default</button>
    </div>
  </div>
</form>

<form method="post" action="/admin/settings.php">
  <?= csrf_field() ?>
  <input type="hidden" name="form" value="site">
  <div class="panel">
    <h2>Contact details &amp; social links</h2>
    <p class="muted" style="margin-bottom:1.25rem">These appear in the navigation, footer, and contact sections across every page.</p>
    <?php foreach ($siteFields as [$key, $label, $default]): ?>
      <div class="field">
        <label for="<?= e($key) ?>"><?= e($label) ?></label>
        <input class="input" id="<?= e($key) ?>" name="settings[<?= e($key) ?>]" type="text" value="<?= e(setting($key, $default)) ?>">
      </div>
    <?php endforeach; ?>
    <button class="btn btn-primary" type="submit">Save settings</button>
  </div>
</form>

<form method="post" action="/admin/settings.php">
  <?= csrf_field() ?>
  <input type="hidden" name="form" value="password">
  <div class="panel">
    <h2>Change your password</h2>
    <div class="field"><label for="current">Current password</label><input class="input" id="current" name="current" type="password" autocomplete="current-password" required></div>
    <div class="field-row">
      <div class="field"><label for="new">New password</label><input class="input" id="new" name="new" type="password" autocomplete="new-password" required></div>
      <div class="field"><label for="confirm">Confirm new password</label><input class="input" id="confirm" name="confirm" type="password" autocomplete="new-password" required></div>
    </div>
    <button class="btn btn-primary" type="submit">Update password</button>
  </div>
</form>

<div class="panel">
  <h2>Account</h2>
  <p class="muted">Signed in as <strong><?= e($me['name']) ?></strong> (<?= e($me['username']) ?>).</p>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
