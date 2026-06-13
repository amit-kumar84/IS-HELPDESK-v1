<?php
/**
 * Shared HTML <head> + gov-header + sidebar opener for authenticated pages.
 *
 * Required variables before include:
 *   $pageTitle, $pageHeading, $crumbs, $userName, $userRoleLbl
 */
$avatarPath = '';
$sidAvatar  = current_user_id();
if ($sidAvatar !== '') {
    foreach (['Pictures/'.$sidAvatar.'.JPG','Pictures/'.$sidAvatar.'.jpg','Pictures/'.$sidAvatar.'.png','Pictures/'.$sidAvatar.'.jpeg'] as $cand) {
        if (file_exists($cand)) { $avatarPath = $cand; break; }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="author" content="Amit Kumar">
<meta name="copyright" content="© 2026 Amit Kumar. All Rights Reserved.">
<meta name="description" content="BEL Kotdwar IT Helpdesk Portal — developed by Amit Kumar">
<title><?= e($pageTitle ?? 'BEL Kotdwar · IT Helpdesk') ?></title>
<link rel="shortcut icon" href="images/bel.ico" type="image/x-icon">
<link rel="stylesheet" href="assets/css/app.css">
<link rel="stylesheet" href="assets/fa/all.min.css">
<link rel="stylesheet" href="assets/fa/fonts.css">
</head>
<body>

<?php
$context_left  = $contextLeft  ?? 'IT HELPDESK PORTAL · BEL KOTDWAR';
$context_right = $contextRight ?? 'Ministry of Defence · Public Sector Undertaking';
$show_gov_header = $showGovHeader ?? true;
if ($show_gov_header) {
    include 'includes/gov_header.php';
}
?>

<div class="app-shell">

<aside class="app-sidebar" id="appSidebar">
    <div class="user-card">
        <div class="avatar">
            <?php if ($avatarPath): ?>
                <img src="<?= e($avatarPath) ?>" alt="<?= e($userName ?? 'User') ?>">
            <?php else: ?>
                <?= e(initials($userName ?? 'User')) ?>
            <?php endif; ?>
        </div>
        <div class="meta">
            <div class="name"><?= e($userName ?? 'Welcome') ?></div>
            <div class="role"><?= e($userRoleLbl ?? '') ?></div>
        </div>
        <a class="logout-btn" href="logout.php" title="Logout"><i class="fa-solid fa-right-from-bracket"></i></a>
    </div>
