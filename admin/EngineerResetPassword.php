<?php
/**
 * Engineer Reset Password — ISKot Admin only.
 *
 * Accepts POST: enggid, new_password
 * Updates s_engg_login.enggpass = MD5(new_password) for the matching row.
 *
 *  This is the central, role-gated handler used by:
 *   - admin/Engineer_List.php  (inline modal per engineer row)
 *   - admin/Edit_Engineer_Details.php
 *
 * Only the ISKot Admin role may invoke this endpoint.
 */
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/connection.php';
require_role('ISKotAdmin');

$return = $_SERVER['HTTP_REFERER'] ?? '../Admin_Home.php?AdminTab=EngineerList';

$enggid = trim($_POST['enggid'] ?? '');
$pwd    = $_POST['new_password'] ?? '';

if ($enggid === '' || $pwd === '') {
    flash_set('warning', 'Engineer ID and new password are both required.');
    header('Location: ' . $return); exit;
}
if (strlen($pwd) < 4) {
    flash_set('warning', 'New password must be at least 4 characters.');
    header('Location: ' . $return); exit;
}

$hash = md5($pwd);
$stmt = mysqli_prepare($link, "UPDATE s_engg_login SET enggpass = ? WHERE enggid = ?");
mysqli_stmt_bind_param($stmt, 'ss', $hash, $enggid);
$stmt->execute();
$ok = mysqli_stmt_affected_rows($stmt) >= 0;

// Look up engineer name for the message
$nm = '';
$nq = mysqli_prepare($link, "SELECT engg_name FROM s_engg_login WHERE enggid = ? LIMIT 1");
mysqli_stmt_bind_param($nq, 's', $enggid);
mysqli_stmt_execute($nq);
$nr = mysqli_fetch_assoc(mysqli_stmt_get_result($nq));
if ($nr) $nm = $nr['engg_name'];

flash_set($ok ? 'success' : 'danger',
    $ok ? "Password reset for engineer " . ($nm ?: $enggid) . "."
        : "Could not reset password for $enggid.");
header('Location: ' . $return);
exit;
