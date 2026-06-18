<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$counts = ['published' => 0, 'draft' => 0, 'messages' => 0, 'unread' => 0];
try {
    $counts['published'] = (int) db()->query('SELECT COUNT(*) FROM posts WHERE status="published"')->fetchColumn();
    $counts['draft']     = (int) db()->query('SELECT COUNT(*) FROM posts WHERE status="draft"')->fetchColumn();
    $counts['messages']  = (int) db()->query('SELECT COUNT(*) FROM messages')->fetchColumn();
    $counts['unread']    = (int) db()->query('SELECT COUNT(*) FROM messages WHERE is_read=0')->fetchColumn();
    $recentPosts = db()->query('SELECT id, title, slug, status, created_at FROM posts ORDER BY created_at DESC LIMIT 5')->fetchAll();
    $recentMsgs  = db()->query('SELECT id, name, email, source, is_read, created_at FROM messages ORDER BY created_at DESC LIMIT 5')->fetchAll();
} catch (Throwable $e) {
    $recentPosts = $recentMsgs = [];
}

$admin_title = 'Dashboard';
$admin_active = 'dashboard';
include __DIR__ . '/../includes/admin-header.php';
?>

<div class="stat-row">
  <div class="stat-box"><div class="num"><?= $counts['published'] ?></div><div class="label">Published posts</div></div>
  <div class="stat-box"><div class="num"><?= $counts['draft'] ?></div><div class="label">Drafts</div></div>
  <div class="stat-box"><div class="num"><?= $counts['messages'] ?></div><div class="label">Total messages</div></div>
  <div class="stat-box"><div class="num"><?= $counts['unread'] ?></div><div class="label">Unread messages</div></div>
</div>

<div class="panel">
  <div class="panel-head">
    <h2>Recent posts</h2>
    <a href="/admin/post-edit.php" class="btn btn-primary btn-sm">+ New post</a>
  </div>
  <?php if (!$recentPosts): ?>
    <div class="empty"><p>No posts yet.</p><a href="/admin/post-edit.php" class="btn btn-primary">Write your first post</a></div>
  <?php else: ?>
    <table class="table">
      <thead><tr><th>Title</th><th>Status</th><th>Created</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($recentPosts as $p): ?>
        <tr>
          <td><a class="row-title" href="/admin/post-edit.php?id=<?= (int)$p['id'] ?>"><?= e($p['title']) ?></a></td>
          <td><span class="tag <?= e($p['status']) ?>"><?= e($p['status']) ?></span></td>
          <td class="muted"><?= e(fmt_date($p['created_at'])) ?></td>
          <td class="actions"><a class="btn btn-ghost btn-sm" href="/admin/post-edit.php?id=<?= (int)$p['id'] ?>">Edit</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<div class="panel">
  <div class="panel-head"><h2>Recent messages</h2><a href="/admin/messages.php" class="btn btn-ghost btn-sm">View all</a></div>
  <?php if (!$recentMsgs): ?>
    <div class="empty"><p>No messages yet.</p></div>
  <?php else: ?>
    <table class="table">
      <thead><tr><th>From</th><th>Type</th><th>Received</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($recentMsgs as $m): ?>
        <tr>
          <td><a class="row-title" href="/admin/messages.php?id=<?= (int)$m['id'] ?>"><?= e($m['name'] ?: $m['email']) ?></a><?php if (!$m['is_read']): ?> <span class="tag unread">new</span><?php endif; ?></td>
          <td class="muted"><?= e(ucfirst($m['source'])) ?></td>
          <td class="muted"><?= e(fmt_date($m['created_at'])) ?></td>
          <td class="actions"><a class="btn btn-ghost btn-sm" href="/admin/messages.php?id=<?= (int)$m['id'] ?>">Open</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
