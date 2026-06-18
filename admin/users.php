<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$me = current_user();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $name     = trim((string) ($_POST['name'] ?? ''));
        $username = strtolower(trim((string) ($_POST['username'] ?? '')));
        $email    = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($username === '' || !preg_match('/^[a-z0-9._-]{3,60}$/', $username)) {
            $errors[] = 'Username must be 3–60 characters: lowercase letters, numbers, dot, dash or underscore.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address (or leave it blank).';
        }
        if (!$errors) {
            $exists = db()->prepare('SELECT 1 FROM users WHERE username = ? LIMIT 1');
            $exists->execute([$username]);
            if ($exists->fetch()) {
                $errors[] = 'That username is already taken.';
            } else {
                db()->prepare('INSERT INTO users (username, name, email, password_hash) VALUES (?, ?, ?, ?)')
                    ->execute([$username, $name ?: $username, $email, password_hash($password, PASSWORD_DEFAULT)]);
                flash('Admin “' . $username . '” created.');
                redirect('/admin/users.php');
            }
        }
    }

    if ($action === 'reset') {
        $uid = (int) ($_POST['id'] ?? 0);
        $new = (string) ($_POST['password'] ?? '');
        if (strlen($new) < 8) {
            $errors[] = 'New password must be at least 8 characters.';
        } elseif ($uid) {
            db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
                ->execute([password_hash($new, PASSWORD_DEFAULT), $uid]);
            flash('Password updated.');
            redirect('/admin/users.php');
        }
    }

    if ($action === 'delete') {
        $uid = (int) ($_POST['id'] ?? 0);
        $total = (int) db()->query('SELECT COUNT(*) FROM users')->fetchColumn();
        if ($uid === $me['id']) {
            $errors[] = 'You cannot delete the account you are currently signed in with.';
        } elseif ($total <= 1) {
            $errors[] = 'You cannot delete the only remaining admin.';
        } elseif ($uid) {
            db()->prepare('DELETE FROM users WHERE id = ?')->execute([$uid]);
            flash('Admin deleted.');
            redirect('/admin/users.php');
        }
    }
}

$users = db()->query('SELECT id, username, name, email, created_at FROM users ORDER BY created_at ASC')->fetchAll();

$admin_title = 'Admin Users';
$admin_active = 'users';
include __DIR__ . '/../includes/admin-header.php';
?>

<?php if ($errors): ?><div class="admin-flash error"><?= e(implode(' ', $errors)) ?></div><?php endif; ?>

<div class="panel">
  <h2>Add a new admin</h2>
  <p class="muted" style="margin-bottom:1.25rem">Anyone you add here can log in and manage the whole site, so only add people you trust.</p>
  <form method="post" action="/admin/users.php">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="create">
    <div class="field-row">
      <div class="field"><label for="name">Full name</label><input class="input" id="name" name="name" type="text" placeholder="Jane Doe"></div>
      <div class="field"><label for="username">Username</label><input class="input" id="username" name="username" type="text" required placeholder="jane" autocomplete="off"></div>
    </div>
    <div class="field-row">
      <div class="field"><label for="email">Email (optional)</label><input class="input" id="email" name="email" type="email" placeholder="jane@example.com"></div>
      <div class="field"><label for="password">Password</label><input class="input" id="password" name="password" type="password" required autocomplete="new-password" placeholder="At least 8 characters"></div>
    </div>
    <button class="btn btn-primary" type="submit">Create admin</button>
  </form>
</div>

<div class="panel">
  <div class="panel-head"><h2><?= count($users) ?> admin<?= count($users) === 1 ? '' : 's' ?></h2></div>
  <table class="table">
    <thead><tr><th>Name</th><th>Username</th><th>Email</th><th>Added</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($users as $u): ?>
      <tr>
        <td><?= e($u['name'] ?: '—') ?><?= $u['id'] === $me['id'] ? ' <span class="tag published">you</span>' : '' ?></td>
        <td class="muted"><?= e($u['username']) ?></td>
        <td class="muted"><?= e($u['email'] ?: '—') ?></td>
        <td class="muted"><?= e(fmt_date($u['created_at'])) ?></td>
        <td class="actions">
          <details>
            <summary class="btn btn-ghost btn-sm" style="list-style:none">Reset password</summary>
            <form method="post" action="/admin/users.php" style="margin-top:0.6rem;display:flex;gap:0.5rem;align-items:center">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="reset">
              <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
              <input class="input" name="password" type="password" placeholder="New password" required style="width:auto">
              <button class="btn btn-primary btn-sm" type="submit">Save</button>
            </form>
          </details>
          <?php if ($u['id'] !== $me['id']): ?>
          <form method="post" action="/admin/users.php" onsubmit="return confirm('Delete this admin permanently?');" style="display:inline">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
            <button class="btn btn-danger btn-sm" type="submit">Delete</button>
          </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
