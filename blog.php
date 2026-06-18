<?php
require_once __DIR__ . '/includes/functions.php';

$cat     = preg_match('/^[a-z0-9-]+$/', (string) ($_GET['cat'] ?? '')) ? $_GET['cat'] : '';
$perPage = 12;
$page    = max(1, (int) ($_GET['p'] ?? 1));
$offset  = ($page - 1) * $perPage;
$total   = count_published_posts($cat ?: null);
$pages   = max(1, (int) ceil($total / $perPage));
$posts   = $total ? get_published_posts($perPage, $offset, $cat ?: null) : [];
$cats    = get_categories();

$catName = '';
if ($cat) {
    foreach ($cats as $c) { if ($c['slug'] === $cat) { $catName = $c['name']; } }
}

$page_title = ($catName ? $catName . ' — ' : '') . setting('blog.meta_title', 'Blog | The Walking Billboard — PR & Brand Insights');
$page_desc  = setting('blog.meta_desc', 'Brand strategy, PR tips, and stories from The Walking Billboard in Port Harcourt.');
$active = 'blog';

// Show the magazine bento only on page 1 of the unfiltered feed with enough posts
$showBento = ($page === 1 && $cat === '' && count($posts) >= 4);

/** Render an image-overlay bento tile for a post. */
function bento_image_card(array $post, string $area): void
{
    $img  = $post['featured_image'] ?? '';
    $catn = $post['category_name'] ?? '';
    $date = fmt_date($post['published_at'] ?: $post['created_at']);
    echo '<a class="bento-card bento-img ' . $area . '" href="/blog/' . e($post['slug']) . '"'
        . ($img ? ' style="background-image:url(\'' . e($img) . '\')"' : '') . '>';
    echo '<span class="bento-arrow">↗</span>';
    if (!$img) { echo '<span class="bento-mark">TWB</span>'; }
    echo '<span class="bento-overlay"></span>';
    echo '<span class="bento-content">';
    echo '<span class="bento-meta">' . ($catn ? '<span class="bento-cat-tag">' . e($catn) . '</span> · ' : '') . e($date) . '</span>';
    echo '<span class="bento-title">' . e($post['title']) . '</span>';
    echo '</span></a>';
}

include __DIR__ . '/includes/header.php';
?>

<header class="page-header">
  <div class="page-header-tag fade-in"><span class="dot"></span> <?= e(setting('blog.eyebrow', 'The Journal')) ?></div>
  <h1 class="fade-in"><?= $catName ? 'Category · ' . e($catName) : setting('blog.header_title', 'Brand insights worth <em>reading.</em>') ?></h1>
  <p class="fade-in"><?= e(setting('blog.header_text', 'Notes on PR, brand storytelling, and turning visibility into revenue — straight from the TWB team.')) ?></p>
  <?php if ($cat): ?><p class="fade-in" style="margin-top:0.75rem"><a href="/blog" style="color:var(--blue);font-weight:600;font-size:0.85rem">← All posts</a></p><?php endif; ?>
</header>

<section class="blog-section">
  <?php if (!$posts): ?>
    <div class="blog-empty fade-in"><p>No posts published yet. Check back soon.</p></div>
  <?php else: ?>

    <?php if ($showBento):
      $feature   = $posts[0];
      $highlight = $posts[1];
      $tall      = $posts[2];
      $middle    = $posts[3];
      $sub       = array_slice($posts, 4, 2);
      $rest      = array_slice($posts, 6);
      $hlExcerpt = $highlight['excerpt'] !== '' ? $highlight['excerpt'] : make_excerpt($highlight['body'], 150);
    ?>
    <div class="blog-bento fade-in">
      <?php bento_image_card($feature, 'bento-feature'); ?>

      <div class="bento-card bento-highlight">
        <a class="bento-highlight-main" href="/blog/<?= e($highlight['slug']) ?>">
          <span class="bento-cat-line"><?= $highlight['category_name'] ? 'Category · ' . e($highlight['category_name']) : 'Latest' ?></span>
          <span class="bento-arrow light">↗</span>
          <h3 class="bento-highlight-title"><?= e($highlight['title']) ?></h3>
          <p class="bento-highlight-excerpt"><?= e($hlExcerpt) ?> <span class="more">More</span></p>
        </a>
        <?php if ($sub): ?>
        <div class="bento-sublinks">
          <?php foreach ($sub as $s): ?>
            <a class="bento-sublink" href="/blog/<?= e($s['slug']) ?>"><?= e($s['title']) ?> <span>→</span></a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

      <?php bento_image_card($tall, 'bento-tall'); ?>
      <?php bento_image_card($middle, 'bento-middle'); ?>

      <div class="bento-card bento-cats">
        <div class="bento-cat-head">Browse topics</div>
        <div class="bento-cat-pills">
          <?php foreach ($cats as $c): ?>
            <a class="cat-pill" href="/blog?cat=<?= e($c['slug']) ?>"><?= e($c['name']) ?></a>
          <?php endforeach; ?>
        </div>
        <a class="bento-cats-foot" href="/blog">View all posts <span class="circle">→</span></a>
      </div>
    </div>

      <?php if ($rest): ?>
        <div class="blog-grid" style="margin-top:1.25rem">
          <?php foreach ($rest as $post): include __DIR__ . '/includes/blog-card.php'; endforeach; ?>
        </div>
      <?php endif; ?>

    <?php else: ?>
      <div class="blog-grid">
        <?php foreach ($posts as $post): include __DIR__ . '/includes/blog-card.php'; endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if ($pages > 1): ?>
    <div class="blog-pagination">
      <?php $q = $cat ? '&cat=' . e($cat) : ''; ?>
      <?php if ($page > 1): ?><a href="/blog?p=<?= $page - 1 ?><?= $q ?>" class="page-btn">← Prev</a><?php endif; ?>
      <span class="page-info">Page <?= $page ?> of <?= $pages ?></span>
      <?php if ($page < $pages): ?><a href="/blog?p=<?= $page + 1 ?><?= $q ?>" class="page-btn">Next →</a><?php endif; ?>
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
