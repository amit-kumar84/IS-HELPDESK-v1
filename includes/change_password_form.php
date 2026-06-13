<?php
/**
 * Shared "Change Password" view.
 * Expected inputs (set in the parent file):
 *   $pwd_table   : table name (admin_login | s_engg_login | emp_details)
 *   $pwd_idcol   : id column   (adminid | enggid | staffid)
 *   $pwd_passcol : password col (adminpass | enggpass | staffpass)
 *   $pwd_id      : current session id ($sid)
 *   $pwd_label   : nice label e.g. "Admin", "Engineer"
 */
$msg = ''; $msgType = 'danger';
if (isset($_POST['sub'])) {
    $op = $_POST['op'] ?? '';
    $np = $_POST['np'] ?? '';
    $cp = $_POST['cp'] ?? '';

    $stmt = mysqli_prepare($link, "SELECT $pwd_passcol AS pwd FROM $pwd_table WHERE $pwd_idcol = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 's', $pwd_id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$row) {
        $msg = 'Unable to verify current account.';
    } elseif (md5($op) !== $row['pwd'] && $op !== $row['pwd']) {
        $msg = 'Current password is not correct.';
    } elseif ($np !== $cp) {
        $msg = 'New password and confirmation do not match.';
    } elseif (strlen($np) < 8) {
        $msg = 'New password must be at least 8 characters.';
    } else {
        $hash = md5($np);
        $stmt = mysqli_prepare($link, "UPDATE $pwd_table SET $pwd_passcol = ? WHERE $pwd_idcol = ?");
        mysqli_stmt_bind_param($stmt, 'ss', $hash, $pwd_id);
        if (mysqli_stmt_execute($stmt)) {
            $msg = 'Password changed successfully.';
            $msgType = 'success';
        } else {
            $msg = 'Could not update password.';
        }
    }
}
?>

<div class="page-head">
    <div class="ic"><i class="fa-solid fa-key"></i></div>
    <div>
        <h2>Change Password</h2>
        <div class="sub">Update your <?= e($pwd_label) ?> account password. Choose a strong password &mdash; minimum 8 characters.</div>
    </div>
</div>

<?php if ($msg): ?>
    <div class="alert alert-<?= e($msgType) ?>" data-testid="pwd-message"><i class="fa-solid fa-circle-info"></i> <?= e($msg) ?></div>
<?php endif; ?>

<div class="card" style="max-width:560px">
    <form method="post" autocomplete="off" data-testid="change-password-form">
        <div style="display:flex;flex-direction:column;gap:14px">
            <div class="form-row"><label>Current Password</label>
                <input type="password" name="op" required placeholder="Enter current password" data-testid="input-old-pwd">
            </div>
            <div class="form-row"><label>New Password</label>
                <input type="password" name="np" required minlength="8" placeholder="At least 8 characters" data-testid="input-new-pwd">
            </div>
            <div class="form-row"><label>Confirm New Password</label>
                <input type="password" name="cp" required minlength="8" placeholder="Re-enter new password" data-testid="input-confirm-pwd">
            </div>
            <div class="flex-end">
                <button type="submit" name="sub" class="btn" data-testid="btn-change-pwd"><i class="fa-solid fa-floppy-disk"></i> Update Password</button>
            </div>
        </div>
    </form>
</div>
