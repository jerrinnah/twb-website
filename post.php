<?php
require_once __DIR__ . '/includes/functions.php';

$slug = (string) ($_GET['slug'] ?? '');
$post = $slug !== '' ? get_post_by_slug($slug) : null;

// 404 for missing or unpublished posts
if (!$post || ($post['status'] !== 'published') ||
    ($post['published_at'] && strtotime($post['published_at']) > time())) {
    http_response_code(404);
    $page_title = 'Post not found | The Walking Billboard';
    $active = 'blog';
    include __DIR__ . '/includes/header.php';
    echo '<header class="page-header"><h1>Post not found</h1><p>That article doesn\'t exist or has moved. <a href="/blog" style="color:var(--blue)">Back to the blog →</a></p></header>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$excerpt = $post['excerpt'] !== '' ? $post['excerpt'] : make_excerpt($post['body']);
$page_title = $post['title'] . ' | The Walking Billboard';
$page_desc  = $excerpt;
$active = 'blog';
$date = fmt_date($post['published_at'] ?: $post['created_at']);

// Up to 3 more recent posts (excluding this one)
$more = array_values(array_filter(get_published_posts(4), fn($p) => $p['id'] != $post['id']));
$more = array_slice($more, 0, 3);

include __DIR__ . '/includes/header.php';
?>

<article class="post">
  <header class="post-header">
    <a href="/blog" class="post-back">← Blog</a>
    <?php if (!empty($post['category_name'])): ?>
      <span class="post-cat"><?= e($post['category_name']) ?></span>
    <?php endif; ?>
    <h1 class="post-title"><?= e($post['title']) ?></h1>
    <p class="post-meta"><?= e($date) ?></p>
  </header>

  <?php if ($post['featured_image']): ?>
    <div class="post-hero" style="background-image:url('<?= e($post['featured_image']) ?>')"></div>
  <?php endif; ?>

  <div class="post-body">
    <?= $post['body'] /* trusted admin-authored HTML */ ?>
  </div>

  <div class="post-cta">
    <a href="/contact" class="btn-pill-blue">Work with TWB <span>→</span></a>
  </div>
</article>

<?php if ($more): ?>
<section class="blog-section">
  <h2 class="related-heading">More from the journal</h2>
  <div class="blog-grid">
    <?php foreach ($more as $m):
      $img = $m['featured_image'] ?: '';
      $ex  = $m['excerpt'] !== '' ? $m['excerpt'] : make_excerpt($m['body']);
    ?>
    <a class="blog-card" href="/blog/<?= e($m['slug']) ?>">
      <div class="blog-card-media"<?= $img ? ' style="background-image:url(\'' . e($img) . '\')"' : '' ?>>
        <?php if (!$img): ?><span class="blog-card-mark">TWB</span><?php endif; ?>
      </div>
      <div class="blog-card-body">
        <?php if (!empty($m['category_name'])): ?><span class="blog-card-cat"><?= e($m['category_name']) ?></span><?php endif; ?>
        <h3 class="blog-card-title"><?= e($m['title']) ?></h3>
        <p class="blog-card-excerpt"><?= e($ex) ?></p>
        <span class="blog-card-meta"><?= e(fmt_date($m['published_at'] ?: $m['created_at'])) ?> · Read more →</span>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
