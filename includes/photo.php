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
        return '<img class="row-avatar" src="' . e($url) . '" alt="' . e($name) . '" style="width:'.$size.'px;height:'.$size.'px;border-radius:50%;object-fit:cover;border:2px solid #e5edf6;">';
    }
    
    // Default avatar SVG with teal background and user icon
    $defaultAvatar = 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22%3E%3Cdefs%3E%3ClinearGradient id=%22bg%22 x1=%220%25%22 y1=%220%25%22 x2=%22100%25%22 y2=%22100%25%22%3E%3Cstop offset=%220%25%22 style=%22stop-color:%230891b2;stop-opacity:1%22 /%3E%3Cstop offset=%22100%25%22 style=%22stop-color:%2306b6d4;stop-opacity:1%22 /%3E%3C/linearGradient%3E%3C/defs%3E%3Ccircle cx=%2250%22 cy=%2250%22 r=%2250%22 fill=%22url(%23bg)%22 /%3E%3Ccircle cx=%2250%22 cy=%2240%22 r=%2218%22 fill=%22%23fff%22 /%3E%3Cpath d=%22M 25 65 Q 25 55 50 55 Q 75 55 75 65 L 75 85 Q 75 90 70 90 L 30 90 Q 25 90 25 85 Z%22 fill=%22%23fff%22 /%3E%3C/svg%3E';
    
    return '<img class="row-avatar default-avatar" src="' . $defaultAvatar . '" alt="' . e($name) . '" style="width:'.$size.'px;height:'.$size.'px;border-radius:50%;object-fit:cover;border:2px solid #e5edf6;">';
}
