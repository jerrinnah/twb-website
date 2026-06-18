<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id) {
        db()->prepare('DELETE FROM posts WHERE id = ?')->execute([$id]);
        flash('Post deleted.');
    }
}
redirect('/admin/posts.php');
