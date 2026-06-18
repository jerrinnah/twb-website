<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

// Actions: mark read / delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    $mid = (int) ($_POST['id'] ?? 0);
    $act = $_POST['action'] ?? '';
    if ($mid && $act === 'delete') {
        db()->prepare('DELETE FROM messages WHERE id=?')->execute([$mid]);
        flash('Message deleted.');
        redirect('/admin/messages.php');
    } elseif ($mid && $act === 'unread') {
        db()->prepare('UPDATE messages SET is_read=0 WHERE id=?')->execute([$mid]);
        redirect('/admin/messages.php?id=' . $mid);
    }
}

$viewId = (int) ($_GET['id'] ?? 0);
$single = null;
if ($viewId) {
    $stmt = db()->prepare('SELECT * FROM messages WHERE id=? LIMIT 1');
    $stmt->execute([$viewId]);
    $single = $stmt->fetch();
    if ($single && !$single['is_read']) {
        db()->prepare('UPDATE messages SET is_read=1 WHERE id=?')->execute([$viewId]);
        $single['is_read'] = 1;
    }
}

$list = db()->query('SELECT * FROM messages ORDER BY created_at DESC')->fetchAll();

$admin_title = 'Messages';
$admin_active = 'messages';
include __DIR__ . '/../includes/admin-header.php';
?>

<?php if ($single): ?>
  <div class="panel">
    <div class="panel-head">
      <h2><?= e($single['name'] ?: 'Lead') ?> <span class="tag <?= $single['source']==='lead'?'draft':'published' ?>"><?= e($single['source']) ?></span></h2>
      <a href="/admin/messages.php" class="btn btn-ghost btn-sm">← All messages</a>
    </div>
    <table class="table" style="margin-bottom:1.5rem">
      <tbody>
        <tr><td class="muted" style="width:140px">Email</td><td><a href="mailto:<?= e($single['email']) ?>" style="color:var(--blue)"><?= e($single['email']) ?></a></td></tr>
        <?php if ($single['company']): ?><tr><td class="muted">Company</td><td><?= e($single['company']) ?></td></tr><?php endif; ?>
        <tr><td class="muted">Received</td><td><?= e(fmt_date($single['created_at'])) ?></td></tr>
      </tbody>
    </table>
    <div class="field">
      <label>Message</label>
      <div class="panel" style="background:#F7F9FC;white-space:pre-wrap;line-height:1.7;margin:0"><?= e($single['message']) ?></div>
    </div>
    <div class="actions">
      <a class="btn btn-primary" href="mailto:<?= e($single['email']) ?>?subject=Re:%20Your%20message%20to%20The%20Walking%20Billboard">Reply by email</a>
      <form method="post" style="display:inline"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$single['id'] ?>"><input type="hidden" name="action" value="unread"><button class="btn btn-ghost" type="submit">Mark unread</button></form>
      <form method="post" style="display:inline" onsubmit="return confirm('Delete this message?');"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$single['id'] ?>"><input type="hidden" name="action" value="delete"><button class="btn btn-danger" type="submit">Delete</button></form>
    </div>
  </div>
<?php endif; ?>

<div class="panel">
  <div class="panel-head"><h2><?= count($list) ?> message<?= count($list)===1?'':'s' ?></h2></div>
  <?php if (!$list): ?>
    <div class="empty"><p>No messages yet. Submissions from your contact form and homepage signups will appear here.</p></div>
  <?php else: ?>
    <table class="table">
      <thead><tr><th>From</th><th>Type</th><th>Preview</th><th>Received</th></tr></thead>
      <tbody>
      <?php foreach ($list as $m): ?>
        <tr style="<?= $m['is_read'] ? '' : 'font-weight:600' ?>">
          <td><a class="row-title" href="/admin/messages.php?id=<?= (int)$m['id'] ?>"><?= e($m['name'] ?: $m['email']) ?></a><?php if (!$m['is_read']): ?> <span class="tag unread">new</span><?php endif; ?></td>
          <td class="muted"><?= e(ucfirst($m['source'])) ?></td>
          <td class="muted"><?= e(mb_strimwidth($m['message'], 0, 60, '…')) ?></td>
          <td class="muted"><?= e(fmt_date($m['created_at'])) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
