<?php
/**
 * ISKot Admin login (replaces the legacy "Administrator" login).
 *
 * Validates against the `iskotadmin_login` table — same table used by the
 * old dashboard repo. Passwords are stored as MD5; we still accept plain
 * for completeness if an admin row was inserted manually.
 *
 * On success: $_SESSION['login_as'] = 'ISKotAdmin' and we redirect to
 * Admin_Home.php (the modern dashboard, role-gated to ISKotAdmin).
 *
 * The single account `iskot` (default password set by Super Admin) is
 * the "Super Admin" — only it can manage other admin accounts. See
 * `is_super_admin()` in includes/auth.php.
 */

error_reporting(0);
require_once 'includes/auth.php';
include_once 'connection.php';

$msg = '';
if (isset($_POST['sub'])) {
    $adminid = trim($_POST['iskotadminid'] ?? $_POST['adminid'] ?? '');
    $pwd     = $_POST['adminpass'] ?? '';

    $stmt = mysqli_prepare($link, "SELECT adminid, adminpass, adminName FROM iskotadmin_login WHERE adminid = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 's', $adminid);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);

    if ($row && ($pwd === $row['adminpass'] || md5($pwd) === $row['adminpass'])) {
        $_SESSION['sid']       = $row['adminid'];
        $_SESSION['login_as']  = 'ISKotAdmin';
        $_SESSION['user_name'] = $row['adminName'];
        header('Location: Admin_Home.php');
        exit;
    }
    $msg = 'ISKot Admin ID or password is incorrect.';
}
?>

<form method="post" autocomplete="off" data-testid="iskot-admin-login-form">
    <?php if ($msg): ?>
        <div class="login-msg" data-testid="login-error"><i class="fa-solid fa-circle-exclamation"></i> &nbsp;<?= e($msg) ?></div>
    <?php endif; ?>

    <div class="form-row">
        <label for="iskotadminid">ISKot Admin ID</label>
        <input type="text" name="iskotadminid" id="iskotadminid" placeholder="e.g. iskot" required autofocus data-testid="iskot-admin-id-input">
    </div>
    <div class="form-row">
        <label for="adminpass">Password</label>
        <input type="password" name="adminpass" id="adminpass" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" required data-testid="iskot-admin-pass-input">
    </div>
    <input type="submit" name="sub" value="Sign In as ISKot Admin" data-testid="iskot-admin-login-btn">
</form>
