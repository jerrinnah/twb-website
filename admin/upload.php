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

$result = store_image_upload($_FILES['file'] ?? []);
if (isset($result['error'])) {
    http_response_code(400);
}
echo json_encode($result);
