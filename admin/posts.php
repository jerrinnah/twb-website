<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$posts = db()->query(
    'SELECT p.id, p.title, p.slug, p.status, p.created_at, p.published_at, c.name AS category_name
     FROM posts p LEFT JOIN categories c ON c.id = p.category_id
     ORDER BY p.created_at DESC'
)->fetchAll();

$admin_title = 'Blog Posts';
$admin_active = 'posts';
include __DIR__ . '/../includes/admin-header.php';
?>

<div class="panel">
  <div class="panel-head">
    <h2><?= count($posts) ?> post<?= count($posts) === 1 ? '' : 's' ?></h2>
    <a href="/admin/post-edit.php" class="btn btn-primary btn-sm">+ New post</a>
  </div>

  <?php if (!$posts): ?>
    <div class="empty"><p>No posts yet. Time to write something.</p><a href="/admin/post-edit.php" class="btn btn-primary">Write your first post</a></div>
  <?php else: ?>
    <table class="table">
      <thead><tr><th>Title</th><th>Category</th><th>Status</th><th>Date</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($posts as $p): ?>
        <tr>
          <td><a class="row-title" href="/admin/post-edit.php?id=<?= (int)$p['id'] ?>"><?= e($p['title']) ?></a></td>
          <td class="muted"><?= e($p['category_name'] ?? '—') ?></td>
          <td><span class="tag <?= e($p['status']) ?>"><?= e($p['status']) ?></span></td>
          <td class="muted"><?= e(fmt_date($p['published_at'] ?: $p['created_at'])) ?></td>
          <td class="actions">
            <?php if ($p['status'] === 'published'): ?><a class="btn btn-ghost btn-sm" href="/blog/<?= e($p['slug']) ?>" target="_blank" rel="noopener">View</a><?php endif; ?>
            <a class="btn btn-ghost btn-sm" href="/admin/post-edit.php?id=<?= (int)$p['id'] ?>">Edit</a>
            <form method="post" action="/admin/post-delete.php" onsubmit="return confirm('Delete this post permanently?');" style="display:inline">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
              <button class="btn btn-danger btn-sm" type="submit">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
