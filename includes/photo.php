<?php
/**
 * Photo helper utilities.
 *  - Save uploaded photo as {staffid}.jpg in Pictures/ (or images/engineers/{enggid}.jpg)
 *  - Find an existing photo URL by id
 *  - Render an avatar (image or initials fallback)
 */

function save_uploaded_photo(array $file, string $id, string $dir = 'Pictures'): ?string {
    if (empty($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) return null;
    if ($file['size'] > 5 * 1024 * 1024) return null;  // 5 MB limit

    $mime = function_exists('mime_content_type') ? mime_content_type($file['tmp_name']) : 'image/jpeg';
    $allowed = ['image/jpeg' => 'JPG', 'image/png' => 'JPG', 'image/jpg' => 'JPG', 'image/webp' => 'JPG'];
    if (!isset($allowed[$mime])) return null;

    if (!is_dir($dir)) @mkdir($dir, 0775, true);
    $id = preg_replace('/[^A-Za-z0-9_-]/', '', $id);
    $target = $dir . '/' . $id . '.JPG';

    // Try to read & re-encode as JPEG (compresses + normalises extension)
    if (function_exists('imagecreatefromstring')) {
        $data = @file_get_contents($file['tmp_name']);
        $img  = @imagecreatefromstring($data);
        if ($img) {
            // Resize to max 600x720 keeping aspect
            $w = imagesx($img); $h = imagesy($img);
            $maxW = 600; $maxH = 720;
            if ($w > $maxW || $h > $maxH) {
                $r = min($maxW/$w, $maxH/$h);
                $nw = (int)($w*$r); $nh = (int)($h*$r);
                $dst = imagecreatetruecolor($nw, $nh);
                imagecopyresampled($dst, $img, 0,0,0,0, $nw, $nh, $w, $h);
                imagejpeg($dst, $target, 85);
                imagedestroy($dst);
            } else {
                imagejpeg($img, $target, 88);
            }
            imagedestroy($img);
            return $target;
        }
    }

    // Fallback: copy as-is
    if (move_uploaded_file($file['tmp_name'], $target)) return $target;
    return null;
}

function user_photo(string $sid, string $dir = 'Pictures'): ?string {
    if ($sid === '') return null;
    $sid = preg_replace('/[^A-Za-z0-9_-]/', '', $sid);
    foreach (['JPG','jpg','jpeg','png'] as $ext) {
        $p = "$dir/$sid.$ext";
        if (is_file($p)) return $p;
    }
    return null;
}

function engineer_photo(string $eid): ?string {
    return user_photo($eid, 'images/engineers');
}

function render_avatar($sid, $name, $size = 34, $dir = 'Pictures'): string {
    $url = user_photo($sid, $dir);
    if ($url) {
        return '<img class="row-avatar" src="' . e($url) . '" alt="' . e($name) . '" style="width:'.$size.'px;height:'.$size.'px">';
    }
    return '<span class="row-avatar fallback" style="width:'.$size.'px;height:'.$size.'px">' . e(initials($name ?: 'U')) . '</span>';
}
