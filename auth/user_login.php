<?php
error_reporting(0);
require_once 'includes/auth.php';
include_once 'connection.php';

$msg = '';
if (isset($_POST['sub'])) {
    $staffid = trim($_POST['staffid'] ?? '');
    $pwd     = $_POST['staffpass'] ?? '';

    $stmt = mysqli_prepare($link, "SELECT staffid, staffpass, username FROM emp_details WHERE staffid = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 's', $staffid);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);

    if ($row && (md5($pwd) === $row['staffpass'] || $pwd === $row['staffpass'])) {
        $_SESSION['sid']       = $row['staffid'];
        $_SESSION['login_as']  = 'user';
        $_SESSION['user_name'] = $row['username'];
        header('Location: home.php');
        exit;
    }
    $msg = 'Staff Number or password is incorrect.';
}
?>

<form method="post" autocomplete="off">
    <?php if ($msg): ?>
        <div class="login-msg" data-testid="login-error"><i class="fa-solid fa-circle-exclamation"></i> &nbsp;<?= e($msg) ?></div>
    <?php endif; ?>

    <div class="form-row">
        <label for="staffid">Staff Number</label>
        <input type="text" name="staffid" id="staffid" placeholder="e.g. 207512" required autofocus data-testid="user-id-input">
    </div>
    <div class="form-row">
        <label for="staffpass">Password</label>
        <input type="password" name="staffpass" id="staffpass" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" required data-testid="user-pass-input">
    </div>
    <input type="submit" name="sub" value="Sign In" data-testid="user-login-btn">
</form>
