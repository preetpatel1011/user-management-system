<?php
$file = isset($_GET['file']) ? $_GET['file'] : null;

if (!$file) {
    http_response_code(400);
    exit('Invalid request');
}

$file = basename($file);

$avatarPath = __DIR__ . '/../storage/avatars/' . $file;

$extension = strtolower(pathinfo($avatarPath, PATHINFO_EXTENSION));

$mimeTypes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif'];

$contentType = $mimeTypes[$extension] ?? 'image/jpeg';

readfile($avatarPath);
?>