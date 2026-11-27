<?php
function save_public_upload(array $file, string $directory, bool $allowVideo = false, string $existing = '')
{
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return $existing;
    }

    if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
        return false;
    }

    if ($allowVideo) {
        $allowed = [
            'mp4' => ['video/mp4', 'application/mp4'],
            'webm' => ['video/webm'],
        ];
    } else {
        $allowed = [
            'jpg' => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png' => ['image/png'],
            'webp' => ['image/webp'],
        ];
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!isset($allowed[$extension])) {
        return false;
    }

    $maximumSize = in_array($extension, ['mp4', 'webm'], true)
        ? 100 * 1024 * 1024
        : 10 * 1024 * 1024;
    if ($file['size'] > $maximumSize) {
        return false;
    }

    $fileInfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $fileInfo->file($file['tmp_name']);
    if (!in_array($mimeType, $allowed[$extension], true)) {
        return false;
    }

    $root = dirname(__DIR__);
    $folder = $root . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $directory;
    if (!is_dir($folder) && !mkdir($folder, 0775, true) && !is_dir($folder)) {
        throw new RuntimeException('Unable to create the upload directory.');
    }

    $filename = bin2hex(random_bytes(12)) . '.' . $extension;
    if (!move_uploaded_file($file['tmp_name'], $folder . DIRECTORY_SEPARATOR . $filename)) {
        return false;
    }

    return 'uploads/' . $directory . '/' . $filename;
}

function remove_public_upload(string $path, string $directory): void
{
    $prefix = 'uploads/' . $directory . '/';
    if (strpos($path, $prefix) !== 0) {
        return;
    }

    $root = realpath(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $directory);
    $file = realpath(dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path));
    if ($root !== false && $file !== false && strpos($file, $root . DIRECTORY_SEPARATOR) === 0 && is_file($file)) {
        unlink($file);
    }
}
