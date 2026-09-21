<?php
function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function slugify(string $text): string {
    $text = trim(strtolower($text));
    $text = preg_replace('/[^a-z0-9]+/i', '-', $text);
    return trim($text, '-') ?: 'item';
}

function redirect(string $url): never {
    header('Location: ' . $url);
    exit;
}

function is_post(): bool {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array {
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

function asset_path(string $path): string {
    if ($path === '') return '';
    if (preg_match('#^https?://#i', $path)) return $path;
    return BASE_URL . '/' . ltrim($path, '/');
}

function upload_image(string $field): ?string {
    if (empty($_FILES[$field]['name'])) return null;
    if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) return null;

    $allowed = ['jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','webp'=>'image/webp'];
    $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
    if (!isset($allowed[$ext])) return null;
    if ($_FILES[$field]['size'] > 5 * 1024 * 1024) return null;

    $name = bin2hex(random_bytes(10)) . '.' . $ext;
    $dir = __DIR__ . '/uploads/images';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    if (!move_uploaded_file($_FILES[$field]['tmp_name'], $dir . '/' . $name)) return null;
    return 'uploads/images/' . $name;
}

function upload_model(string $field): ?string {
    if (empty($_FILES[$field]['name'])) return null;
    if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) return null;

    $allowed = ['glb','gltf','fbx','obj'];
    $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true)) return null;
    if ($_FILES[$field]['size'] > 50 * 1024 * 1024) return null;

    $name = bin2hex(random_bytes(10)) . '.' . $ext;
    $dir = __DIR__ . '/uploads/models';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    if (!move_uploaded_file($_FILES[$field]['tmp_name'], $dir . '/' . $name)) return null;
    return 'uploads/models/' . $name;
}
