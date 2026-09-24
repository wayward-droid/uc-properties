<?php
function save_upload(string $field, string $type): ?string
{
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $file = $_FILES[$field];
    if (is_array($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException(
            'The upload failed. Check the file size and PHP upload settings.',
        );
    }
    if (!is_uploaded_file($file['tmp_name'])) {
        throw new RuntimeException('Invalid upload.');
    }
    $max = $type === 'image' ? 5 * 1024 * 1024 : 10 * 1024 * 1024;
    if ($file['size'] < 1 || $file['size'] > $max) {
        throw new RuntimeException('Use an image up to 5 MB, or a document up to 10 MB.');
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $allowed =
        $type === 'pdf'
            ? ['application/pdf' => 'pdf']
            : ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if ($type === 'document') {
        $allowed['application/pdf'] = 'pdf';
    }
    $originalExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $validExtensions = [
        'image/jpeg' => ['jpg', 'jpeg'],
        'image/png' => ['png'],
        'image/webp' => ['webp'],
        'application/pdf' => ['pdf'],
    ];
    if (!isset($allowed[$mime]) || !in_array($originalExt, $validExtensions[$mime], true)) {
        throw new RuntimeException(
            'The file extension and contents must match an allowed JPG, PNG, WebP, or PDF file.',
        );
    }
    if (str_starts_with($mime, 'image/')) {
        if ($file['size'] > 5 * 1024 * 1024) {
            throw new RuntimeException('Images must be 5 MB or smaller.');
        }
        $size = @getimagesize($file['tmp_name']);
        if (!$size || $size[0] * $size[1] > 60000000) {
            throw new RuntimeException('Use a valid image smaller than 60 megapixels.');
        }
    } elseif (file_get_contents($file['tmp_name'], false, null, 0, 5) !== '%PDF-') {
        throw new RuntimeException('Use a valid PDF document.');
    }
    $path = 'uploads/' . bin2hex(random_bytes(20)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($file['tmp_name'], ROOT . '/' . $path)) {
        throw new RuntimeException('The uploads folder is not writable.');
    }
    @chmod(ROOT . '/' . $path, 0644);
    return $path;
}
