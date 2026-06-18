<?php
require_once __DIR__ . '/includes/functions.php';

$perPage = 9;
$page    = max(1, (int) ($_GET['p'] ?? 1));
$offset  = ($page - 1) * $perPage;
$total   = count_published_posts();
$pages   = max(1, (int) ceil($total / $perPage));
$posts   = $total ? get_published_posts($perPage, $offset) : [];

$page_title = setting('blog.meta_title', 'Blog | The Walking Billboard — PR & Brand Insights');
$page_desc  = setting('blog.meta_desc', 'Brand strategy, PR tips, and stories from The Walking Billboard in Port Harcourt.');
$active = 'blog';
include __DIR__ . '/includes/header.php';
?>

<header class="page-header">
  <div class="page-header-tag fade-in"><span class="dot"></span> <?= e(setting('blog.eyebrow', 'The Journal')) ?></div>
  <h1 class="fade-in"><?= setting('blog.header_title', 'Brand insights worth <em>reading.</em>') ?></h1>
  <p class="fade-in"><?= e(setting('blog.header_text', 'Notes on PR, brand storytelling, and turning visibility into revenue — straight from the TWB team.')) ?></p>
</header>

<section class="blog-section">
  <?php if (!$posts): ?>
    <div class="blog-empty fade-in">
      <p>No posts published yet. Check back soon.</p>
    </div>
  <?php else: ?>
    <div class="blog-grid">
      <?php foreach ($posts as $post):
        $img = $post['featured_image'] ?: '';
        $excerpt = $post['excerpt'] !== '' ? $post['excerpt'] : make_excerpt($post['body']);
      ?>
      <a class="blog-card fade-in" href="/blog/<?= e($post['slug']) ?>">
        <div class="blog-card-media"<?= $img ? ' style="background-image:url(\'' . e($img) . '\')"' : '' ?>>
          <?php if (!$img): ?><span class="blog-card-mark">TWB</span><?php endif; ?>
        </div>
        <div class="blog-card-body">
          <?php if (!empty($post['category_name'])): ?>
            <span class="blog-card-cat"><?= e($post['category_name']) ?></span>
          <?php endif; ?>
          <h3 class="blog-card-title"><?= e($post['title']) ?></h3>
          <p class="blog-card-excerpt"><?= e($excerpt) ?></p>
          <span class="blog-card-meta"><?= e(fmt_date($post['published_at'] ?: $post['created_at'])) ?> · Read more →</span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>

    <?php if ($pages > 1): ?>
    <div class="blog-pagination">
      <?php if ($page > 1): ?><a href="/blog?p=<?= $page - 1 ?>" class="page-btn">← Prev</a><?php endif; ?>
      <span class="page-info">Page <?= $page ?> of <?= $pages ?></span>
      <?php if ($page < $pages): ?><a href="/blog?p=<?= $page + 1 ?>" class="page-btn">Next →</a><?php endif; ?>
    </div>
    <?php endif; ?>
  <?php endif; ?>
</section>

<div class="cta-banner">
  <div class="cta-banner-text">Want this kind of visibility for your brand?</div>
  <div class="cta-banner-right">
    <a href="/contact" class="btn-pill-white">Book a Free Call <span>↗</span></a>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
