<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$id   = (int) ($_GET['id'] ?? 0);
$post = null;
if ($id) {
    $stmt = db()->prepare('SELECT * FROM posts WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $post = $stmt->fetch();
    if (!$post) { redirect('/admin/posts.php'); }
}

$cats   = get_categories();
$errors = [];

// Defaults / current values
$v = [
    'title'          => $post['title'] ?? '',
    'slug'           => $post['slug'] ?? '',
    'excerpt'        => $post['excerpt'] ?? '',
    'body'           => $post['body'] ?? '',
    'featured_image' => $post['featured_image'] ?? '',
    'category_id'    => $post['category_id'] ?? '',
    'status'         => $post['status'] ?? 'draft',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $errors[] = 'Your session expired. Please try again.';
    } else {
        foreach (['title','slug','excerpt','body','featured_image','category_id','status'] as $f) {
            $v[$f] = trim((string) ($_POST[$f] ?? ''));
        }
        if ($v['title'] === '')  { $errors[] = 'A title is required.'; }
        if (trim(strip_tags($v['body'])) === '') { $errors[] = 'The post body cannot be empty.'; }
        $v['status'] = $v['status'] === 'published' ? 'published' : 'draft';
        $catId = $v['category_id'] !== '' ? (int) $v['category_id'] : null;

        if (!$errors) {
            $slug = slugify($v['slug'] !== '' ? $v['slug'] : $v['title']);
            $slug = unique_post_slug($slug, $id ?: null);
            $excerpt = $v['excerpt'] !== '' ? mb_substr($v['excerpt'], 0, 500) : make_excerpt($v['body'], 200);

            if ($id) {
                $sql = 'UPDATE posts SET title=?, slug=?, excerpt=?, body=?, featured_image=?, category_id=?, status=?,
                        published_at = CASE WHEN ?="published" AND published_at IS NULL THEN NOW()
                                            WHEN ?="draft" THEN NULL ELSE published_at END
                        WHERE id=?';
                db()->prepare($sql)->execute([
                    $v['title'], $slug, $excerpt, $v['body'], $v['featured_image'], $catId, $v['status'],
                    $v['status'], $v['status'], $id,
                ]);
                flash('Post updated.');
            } else {
                $pub = $v['status'] === 'published' ? date('Y-m-d H:i:s') : null;
                $sql = 'INSERT INTO posts (title, slug, excerpt, body, featured_image, category_id, author_id, status, published_at)
                        VALUES (?,?,?,?,?,?,?,?,?)';
                db()->prepare($sql)->execute([
                    $v['title'], $slug, $excerpt, $v['body'], $v['featured_image'], $catId,
                    current_user()['id'], $v['status'], $pub,
                ]);
                $id = (int) db()->lastInsertId();
                flash('Post created.');
            }
            redirect('/admin/post-edit.php?id=' . $id);
        }
    }
}

$admin_title = $id ? 'Edit Post' : 'New Post';
$admin_active = 'posts';
include __DIR__ . '/../includes/admin-header.php';
?>

<?php if ($errors): ?><div class="admin-flash error"><?= e(implode(' ', $errors)) ?></div><?php endif; ?>

<form method="post" action="/admin/post-edit.php<?= $id ? '?id=' . $id : '' ?>" id="postForm">
  <?= csrf_field() ?>
  <div class="panel">
    <div class="field">
      <label for="title">Title</label>
      <input class="input" id="title" name="title" type="text" value="<?= e($v['title']) ?>" required>
    </div>

    <div class="field">
      <label for="body">Content</label>
      <input id="body" type="hidden" name="body" value="<?= e($v['body']) ?>">
      <trix-editor input="body"></trix-editor>
      <p class="hint">Use the toolbar for headings, bold, lists, quotes, and links. Drag an image in to upload it inline.</p>
    </div>
  </div>

  <div class="panel">
    <div class="field-row">
      <div class="field">
        <label for="status">Status</label>
        <select class="select" id="status" name="status">
          <option value="draft" <?= $v['status']==='draft'?'selected':'' ?>>Draft (hidden)</option>
          <option value="published" <?= $v['status']==='published'?'selected':'' ?>>Published (live)</option>
        </select>
      </div>
      <div class="field">
        <label for="category_id">Category</label>
        <select class="select" id="category_id" name="category_id">
          <option value="">— None —</option>
          <?php foreach ($cats as $c): ?>
            <option value="<?= (int)$c['id'] ?>" <?= (string)$v['category_id']===(string)$c['id']?'selected':'' ?>><?= e($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="field">
      <label for="slug">URL slug</label>
      <input class="input" id="slug" name="slug" type="text" value="<?= e($v['slug']) ?>" placeholder="auto-generated from title">
      <p class="hint">Leave blank to auto-generate. Becomes <code>/blog/your-slug</code>.</p>
    </div>

    <div class="field">
      <label for="excerpt">Excerpt</label>
      <textarea class="textarea" id="excerpt" name="excerpt" maxlength="500" placeholder="Short summary shown on the blog list (auto-generated if left blank)"><?= e($v['excerpt']) ?></textarea>
    </div>

    <div class="field">
      <label>Featured image</label>
      <input class="input" id="featured_image" name="featured_image" type="text" value="<?= e($v['featured_image']) ?>" placeholder="/uploads/your-image.jpg">
      <input type="file" id="featuredFile" accept="image/*" style="margin-top:0.6rem">
      <div class="image-preview" id="featuredPreview"><?php if ($v['featured_image']): ?><img src="<?= e($v['featured_image']) ?>" alt=""><?php endif; ?></div>
      <p class="hint">Upload a file or paste an image URL. Recommended ~1200×675px.</p>
    </div>
  </div>

  <div class="actions">
    <button class="btn btn-primary" type="submit">Save post</button>
    <a class="btn btn-ghost" href="/admin/posts.php">Cancel</a>
    <?php if ($id && $v['status']==='published'): ?><a class="btn btn-ghost" href="/blog/<?= e($v['slug']) ?>" target="_blank" rel="noopener">View live ↗</a><?php endif; ?>
  </div>
</form>

<link rel="stylesheet" href="https://unpkg.com/trix@2.1.1/dist/trix.css">
<script src="https://unpkg.com/trix@2.1.1/dist/trix.umd.min.js"></script>
<script>
  const CSRF = <?= json_encode(csrf_token()) ?>;

  // Inline image upload via Trix attachments
  addEventListener('trix-attachment-add', function (event) {
    const attachment = event.attachment;
    if (!attachment.file) return;
    uploadFile(attachment.file, function (url) {
      attachment.setAttributes({ url: url, href: url });
    }, function (pct) {
      attachment.setUploadProgress(pct);
    });
  });

  function uploadFile(file, onDone, onProgress) {
    const form = new FormData();
    form.append('file', file);
    form.append('csrf', CSRF);
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '/admin/upload.php', true);
    xhr.upload.onprogress = function (e) { if (e.lengthComputable && onProgress) onProgress((e.loaded / e.total) * 100); };
    xhr.onload = function () {
      try {
        const res = JSON.parse(xhr.responseText);
        if (res.url) onDone(res.url); else alert(res.error || 'Upload failed');
      } catch (e) { alert('Upload failed'); }
    };
    xhr.send(form);
  }

  // Featured image upload
  const fileInput = document.getElementById('featuredFile');
  if (fileInput) {
    fileInput.addEventListener('change', function () {
      if (!this.files[0]) return;
      uploadFile(this.files[0], function (url) {
        document.getElementById('featured_image').value = url;
        document.getElementById('featuredPreview').innerHTML = '<img src="' + url + '" alt="">';
      });
    });
  }
</script>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
