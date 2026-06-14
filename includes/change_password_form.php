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

<div class="cp-center">
    <div class="cp-card">
        <form method="post" autocomplete="off" data-testid="change-password-form" id="cp-form">
            <div class="cp-header">
                <div class="cp-icon"><i class="fa-solid fa-key"></i></div>
                <h3>Secure your account</h3>
                <p class="cp-sub">Update your <?= e($pwd_label) ?> password. Use at least 8 characters.</p>
            </div>

            <div class="cp-body">
                <label class="cp-field">
                    <input type="password" name="op" id="cp-old" required placeholder=" " data-testid="input-old-pwd">
                    <button type="button" class="cp-toggle" onclick="togglePwd('cp-old')" title="Show password">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                    <span>Current Password</span>
                </label>

                <label class="cp-field">
                    <input type="password" name="np" id="cp-new" required minlength="8" placeholder=" " data-testid="input-new-pwd">
                    <button type="button" class="cp-toggle" onclick="togglePwd('cp-new')" title="Show password">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                    <span>New Password</span>
                    <small class="pwd-hint" id="pwd-hint">At least 8 characters</small>
                </label>

                <label class="cp-field">
                    <input type="password" name="cp" id="cp-confirm" required minlength="8" placeholder=" " data-testid="input-confirm-pwd">
                    <button type="button" class="cp-toggle" onclick="togglePwd('cp-confirm')" title="Show password">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                    <span>Confirm New Password</span>
                    <small class="pwd-match" id="pwd-match"></small>
                </label>
            </div>

            <div class="cp-actions">
                <button type="submit" name="sub" class="cp-btn" data-testid="btn-change-pwd">
                    <span class="btn-anim"></span>
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Update Password</span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Centering container */
.cp-center{display:flex;justify-content:center;padding:22px}
.cp-card{width:100%;max-width:600px;border-radius:16px;padding:24px;background:linear-gradient(135deg,#ffffffcc, #f8fafccc);box-shadow:0 10px 30px rgba(2,6,23,0.12);backdrop-filter:blur(6px);border:1px solid rgba(255,255,255,0.4);position:relative;overflow:hidden}

/* Animated background accent */
.cp-card::before{content:'';position:absolute;inset:-40%;background:radial-gradient(circle at 10% 20%, rgba(99,102,241,0.12), transparent 6%), radial-gradient(circle at 90% 80%, rgba(236,72,153,0.08), transparent 8%);transform:translateZ(0);animation:float 10s linear infinite;pointer-events:none}
@keyframes float{0%{transform:translateY(-6px)}50%{transform:translateY(6px)}100%{transform:translateY(-6px)}}

.cp-header{text-align:center;margin-bottom:12px;position:relative;z-index:2}
.cp-icon{width:64px;height:64px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#6366f1,#ec4899);color:#fff;font-size:22px;box-shadow:0 8px 24px rgba(99,102,241,0.18);margin-bottom:8px}
.cp-header h3{margin:0;font-size:18px;color:#0f172a}
.cp-sub{margin:6px 0 0;font-size:13px;color:#475569}

.cp-body{display:flex;flex-direction:column;gap:12px;margin-top:8px;position:relative;z-index:2}
.cp-field{position:relative;display:block}
.cp-field input{width:100%;padding:14px 14px 14px 14px;border-radius:10px;border:1px solid rgba(15,23,42,0.08);background:linear-gradient(180deg,#fff,#f8fafc);font-size:14px;outline:none;transition:box-shadow .18s,border-color .18s,transform .18s;padding-right:42px}
.cp-toggle{position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:0;cursor:pointer;color:#64748b;font-size:15px;transition:color .18s;z-index:3}
.cp-toggle:hover{color:#2563eb}
.cp-field span{position:absolute;left:14px;top:12px;font-size:12px;color:#64748b;transform-origin:left top;transition:transform .18s,top .18s,font-size .18s}
.cp-field input:focus{box-shadow:0 8px 30px rgba(37,99,235,0.08);border-color:rgba(37,99,235,0.35)}
.cp-field input:focus + span,.cp-field input:not(:placeholder-shown) + span{transform:translateY(-22px) scale(.92);top:6px;color:#2563eb}
.pwd-hint{display:block;margin-top:6px;color:#94a3b8;font-size:12px}
.pwd-match{display:block;margin-top:6px;font-size:12px}

.cp-actions{text-align:center;margin-top:14px}
.cp-btn{position:relative;display:inline-flex;align-items:center;gap:10px;padding:10px 16px;border-radius:12px;border:0;background:linear-gradient(90deg,#6366f1,#ec4899);color:#fff;font-weight:700;cursor:pointer;overflow:hidden;box-shadow:0 8px 18px rgba(99,102,241,0.2);transition:transform .12s}
.cp-btn:hover{transform:translateY(-3px)}
.cp-btn .btn-anim{position:absolute;left:-30%;top:0;width:60%;height:100%;background:linear-gradient(90deg,rgba(255,255,255,0.12),rgba(255,255,255,0.02));transform:skewX(-20deg);transition:left .6s}
.cp-btn:active .btn-anim{left:120%}

/* Success / error styles for message */
.alert{border-radius:10px;padding:10px 12px;margin:10px auto 0;max-width:520px}

@media (max-width:520px){.cp-card{padding:14px}}
</style>

<script>
function togglePwd(fieldId){
    var field = document.getElementById(fieldId);
    var toggle = event.target.closest('.cp-toggle');
    if (!field || !toggle) return;
    event.preventDefault();
    var icon = toggle.querySelector('i');
    if (field.type === 'password'){
        field.type = 'text';
        icon.className = 'fa-solid fa-eye-slash';
    } else {
        field.type = 'password';
        icon.className = 'fa-solid fa-eye';
    }
}

(function(){
    var newPwd = document.getElementById('cp-new');
    var confPwd = document.getElementById('cp-confirm');
    var hint = document.getElementById('pwd-hint');
    var match = document.getElementById('pwd-match');
    var form = document.getElementById('cp-form');

    function check(){
        var a = newPwd.value || '';
        hint.textContent = a.length < 8 ? 'Too short' : 'Looks good';
        hint.style.color = a.length < 8 ? '#ef4444' : '#10b981';
        if (confPwd.value.length > 0) {
            if (a === confPwd.value) {
                match.textContent = 'Passwords match'; match.style.color = '#10b981';
            } else {
                match.textContent = 'Passwords do not match'; match.style.color = '#ef4444';
            }
        } else { match.textContent = ''; }
    }
    newPwd && newPwd.addEventListener('input', check);
    confPwd && confPwd.addEventListener('input', check);

    // small submit animation: briefly disable button to show progress
    form && form.addEventListener('submit', function(e){
        var btn = form.querySelector('button[type=submit]');
        if (btn) { btn.disabled = true; btn.style.opacity = '0.9'; }
    });
})();
</script>
