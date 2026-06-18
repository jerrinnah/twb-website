<?php
/** Standard blog grid card. Expects $post in scope. */
$__img = $post['featured_image'] ?: '';
$__ex  = $post['excerpt'] !== '' ? $post['excerpt'] : make_excerpt($post['body']);
?>
<a class="blog-card fade-in" href="/blog/<?= e($post['slug']) ?>">
  <div class="blog-card-media"<?= $__img ? ' style="background-image:url(\'' . e($__img) . '\')"' : '' ?>>
    <?php if (!$__img): ?><span class="blog-card-mark">TWB</span><?php endif; ?>
  </div>
  <div class="blog-card-body">
    <?php if (!empty($post['category_name'])): ?><span class="blog-card-cat"><?= e($post['category_name']) ?></span><?php endif; ?>
    <h3 class="blog-card-title"><?= e($post['title']) ?></h3>
    <p class="blog-card-excerpt"><?= e($__ex) ?></p>
    <span class="blog-card-meta"><?= e(fmt_date($post['published_at'] ?: $post['created_at'])) ?> · Read more →</span>
  </div>
</a>
