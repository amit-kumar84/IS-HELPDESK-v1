<?php
error_reporting(0);
require_once 'includes/auth.php';
include_once 'connection.php';

$msg = '';
if (isset($_POST['sub'])) {
    $enggid = trim($_POST['enggid'] ?? '');
    $pwd    = $_POST['enggpass'] ?? '';

    $stmt = mysqli_prepare($link, "SELECT enggid, enggpass, engg_name FROM s_engg_login WHERE enggid = ? AND status='0' AND presence='P' LIMIT 1");
    mysqli_stmt_bind_param($stmt, 's', $enggid);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);

    if ($row && (md5($pwd) === $row['enggpass'] || $pwd === $row['enggpass'])) {
        $_SESSION['sid']       = $row['enggid'];
        $_SESSION['login_as']  = 'Engineer';
        $_SESSION['user_name'] = $row['engg_name'];
        header('Location: Engineer_home.php');
        exit;
    }
    $msg = 'Login ID or password is incorrect (or you are marked Absent).';
}
?>

<form method="post" autocomplete="off">
    <?php if ($msg): ?>
        <div class="login-msg" data-testid="login-error"><i class="fa-solid fa-circle-exclamation"></i> &nbsp;<?= e($msg) ?></div>
    <?php endif; ?>

    <div class="form-row">
        <label for="enggid">Engineer BEL ID</label>
        <input type="text" name="enggid" id="enggid" placeholder="e.g. 620230" required autofocus data-testid="engineer-id-input">
    </div>
    <div class="form-row">
        <label for="enggpass">Password</label>
        <input type="password" name="enggpass" id="enggpass" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" required data-testid="engineer-pass-input">
    </div>
    <input type="submit" name="sub" value="Sign In as Engineer" data-testid="engineer-login-btn">
</form>
