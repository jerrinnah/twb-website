<?php
/**
 * Authenticated image upload endpoint. Returns JSON { url } or { error }.
 */
require_once __DIR__ . '/../includes/auth.php';
require_login();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
    http_response_code(400);
    exit(json_encode(['error' => 'Bad request.']));
}

if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    exit(json_encode(['error' => 'No file received.']));
}

$file = $_FILES['file'];

// Size limit: 5 MB
if ($file['size'] > 5 * 1024 * 1024) {
    http_response_code(413);
    exit(json_encode(['error' => 'Image too large (max 5 MB).']));
}

// Validate by real MIME type, not extension
$allowed = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/gif'  => 'gif',
    'image/webp' => 'webp',
];
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime  = $finfo->file($file['tmp_name']);
if (!isset($allowed[$mime])) {
    http_response_code(415);
    exit(json_encode(['error' => 'Only JPG, PNG, GIF, or WEBP images are allowed.']));
}

$ext     = $allowed[$mime];
$dir     = __DIR__ . '/../uploads';
if (!is_dir($dir)) { @mkdir($dir, 0755, true); }

$name    = date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
$dest    = $dir . '/' . $name;

if (!move_uploaded_file($file['tmp_name'], $dest)) {
    http_response_code(500);
    exit(json_encode(['error' => 'Could not save the file.']));
}
@chmod($dest, 0644);

echo json_encode(['url' => '/uploads/' . $name]);
