<?php
/**
 * Manage ISKot Admins  —  visible / accessible ONLY to the Super Admin
 * (`iskot`). Lets the Super Admin add new admins, change another admin's
 * password / display name, or remove an admin (except the Super Admin
 * itself).
 *
 * Storage: `iskotadmin_login (adminid, adminName, adminpass)`
 * Passwords are stored as MD5 (same convention used everywhere else).
 */
require_once __DIR__ . '/../includes/auth.php';
require_super_admin();
require_once __DIR__ . '/../connection.php';

$flash = ['ok'=>'', 'err'=>''];

// ---- POST handlers -------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $id   = trim($_POST['adminid'] ?? '');
        $nm   = trim($_POST['adminName'] ?? '');
        $pwd  = $_POST['adminpass'] ?? '';

        if ($id === '' || $nm === '' || $pwd === '') {
            $flash['err'] = 'All fields are required.';
        } else {
            // Uniqueness check
            $chk = mysqli_prepare($link, "SELECT 1 FROM iskotadmin_login WHERE adminid = ? LIMIT 1");
            mysqli_stmt_bind_param($chk, 's', $id);
            mysqli_stmt_execute($chk);
            $taken = (bool) mysqli_fetch_row(mysqli_stmt_get_result($chk));
            if ($taken) {
                $flash['err'] = "An admin with ID '$id' already exists.";
            } else {
                $hash = md5($pwd);
                $ins  = mysqli_prepare($link, "INSERT INTO iskotadmin_login (adminName, adminid, adminpass) VALUES (?,?,?)");
                mysqli_stmt_bind_param($ins, 'sss', $nm, $id, $hash);
                if (mysqli_stmt_execute($ins)) $flash['ok']  = "Admin '$id' created.";
                else                            $flash['err'] = mysqli_error($link);
            }
        }
    }
    elseif ($action === 'reset_password') {
        $id  = trim($_POST['adminid'] ?? '');
        $pwd = $_POST['adminpass'] ?? '';
        if ($id === '' || $pwd === '') {
            $flash['err'] = 'Admin and new password are both required.';
        } else {
            $hash = md5($pwd);
            $upd  = mysqli_prepare($link, "UPDATE iskotadmin_login SET adminpass = ? WHERE adminid = ?");
            mysqli_stmt_bind_param($upd, 'ss', $hash, $id);
            if (mysqli_stmt_execute($upd) && mysqli_stmt_affected_rows($upd) >= 0) {
                $flash['ok'] = "Password reset for '$id'.";
            } else {
                $flash['err'] = mysqli_error($link);
            }
        }
    }
    elseif ($action === 'rename') {
        $id  = trim($_POST['adminid'] ?? '');
        $nm  = trim($_POST['adminName'] ?? '');
        if ($id === '' || $nm === '') {
            $flash['err'] = 'Admin and new name are both required.';
        } else {
            $upd = mysqli_prepare($link, "UPDATE iskotadmin_login SET adminName = ? WHERE adminid = ?");
            mysqli_stmt_bind_param($upd, 'ss', $nm, $id);
            if (mysqli_stmt_execute($upd)) $flash['ok']  = "Updated display name for '$id'.";
            else                           $flash['err'] = mysqli_error($link);
        }
    }
    elseif ($action === 'remove') {
        $id = trim($_POST['adminid'] ?? '');
        if (strtolower($id) === SUPER_ADMIN_ID()) {
            $flash['err'] = 'The Super Admin account cannot be removed.';
        } elseif ($id === '') {
            $flash['err'] = 'Admin ID is required.';
        } else {
            $del = mysqli_prepare($link, "DELETE FROM iskotadmin_login WHERE adminid = ?");
            mysqli_stmt_bind_param($del, 's', $id);
            if (mysqli_stmt_execute($del) && mysqli_stmt_affected_rows($del) > 0) {
                $flash['ok'] = "Admin '$id' removed.";
            } else {
                $flash['err'] = "No admin '$id' found.";
            }
        }
    }
}

// ---- Fetch list ----------------------------------------------------
$admins = [];
$q = @mysqli_query($link, "SELECT adminid, adminName, adminpass FROM iskotadmin_login ORDER BY (adminid='".SUPER_ADMIN_ID()."') DESC, adminName ASC");
if ($q) while ($r = mysqli_fetch_assoc($q)) $admins[] = $r;
$total = count($admins);
?>

<div class="page-block" data-testid="manage-admins-page">

    <div class="ma-header">
        <div>
            <h2><i class="fa-solid fa-user-shield"></i> Manage ISKot Admins</h2>
            <p class="ma-sub">
                Super-Admin control panel &middot;
                <span class="pill super"><i class="fa-solid fa-crown"></i> Super Admin: <?= e(SUPER_ADMIN_ID()) ?></span>
                <span class="pill"><i class="fa-solid fa-users"></i> <?= $total ?> admin<?= $total!==1?'s':'' ?></span>
            </p>
        </div>
        <button class="btn btn-primary" id="ma-show-add" data-testid="ma-show-add-btn">
            <i class="fa-solid fa-user-plus"></i> Add New Admin
        </button>
    </div>

    <?php if ($flash['ok']): ?>
        <div class="ma-flash ok" data-testid="ma-flash-ok"><i class="fa-solid fa-circle-check"></i> <?= e($flash['ok']) ?></div>
    <?php endif; ?>
    <?php if ($flash['err']): ?>
        <div class="ma-flash err" data-testid="ma-flash-err"><i class="fa-solid fa-circle-exclamation"></i> <?= e($flash['err']) ?></div>
    <?php endif; ?>

    <!-- ===== ADD FORM (hidden by default) ===== -->
    <form method="post" class="ma-card ma-add-form" id="ma-add-form" style="display:none" data-testid="ma-add-form" autocomplete="off">
        <input type="hidden" name="action" value="add">
        <h3><i class="fa-solid fa-user-plus"></i> Create New ISKot Admin</h3>
        <div class="ma-grid">
            <div><label>Admin ID</label><input type="text" name="adminid" required placeholder="e.g. 654321 or amit" data-testid="ma-new-id"></div>
            <div><label>Display Name</label><input type="text" name="adminName" required placeholder="e.g. AMIT KUMAR" data-testid="ma-new-name"></div>
            <div><label>Password</label><input type="text" name="adminpass" required placeholder="Initial password" data-testid="ma-new-pass"></div>
        </div>
        <div class="ma-actions">
            <button type="button" class="btn btn-ghost" id="ma-cancel-add">Cancel</button>
            <button type="submit" class="btn btn-primary" data-testid="ma-submit-add"><i class="fa-solid fa-floppy-disk"></i> Save Admin</button>
        </div>
    </form>

    <!-- ===== LIST ===== -->
    <div class="ma-list" data-testid="ma-list">
        <?php foreach ($admins as $a):
            $isSuper = (strtolower($a['adminid']) === SUPER_ADMIN_ID());
        ?>
            <div class="ma-row <?= $isSuper ? 'super' : '' ?>" data-testid="ma-row-<?= e($a['adminid']) ?>">
                <div class="ma-avatar">
                    <?= e(strtoupper(mb_substr($a['adminName'], 0, 2))) ?>
                    <?php if ($isSuper): ?><span class="crown" title="Super Admin"><i class="fa-solid fa-crown"></i></span><?php endif; ?>
                </div>
                <div class="ma-meta">
                    <div class="ma-name"><?= e($a['adminName']) ?>
                        <?php if ($isSuper): ?>
                            <span class="pill super tiny"><i class="fa-solid fa-crown"></i> Super Admin</span>
                        <?php endif; ?>
                    </div>
                    <div class="ma-id"><i class="fa-solid fa-id-badge"></i> <?= e($a['adminid']) ?></div>
                </div>
                <details class="ma-tool">
                    <summary><i class="fa-solid fa-key"></i> Reset Password</summary>
                    <form method="post" autocomplete="off">
                        <input type="hidden" name="action" value="reset_password">
                        <input type="hidden" name="adminid" value="<?= e($a['adminid']) ?>">
                        <input type="text" name="adminpass" placeholder="New password" required data-testid="ma-pwd-<?= e($a['adminid']) ?>">
                        <button class="btn btn-warn" type="submit" data-testid="ma-pwd-submit-<?= e($a['adminid']) ?>"><i class="fa-solid fa-check"></i> Save</button>
                    </form>
                </details>
                <details class="ma-tool">
                    <summary><i class="fa-solid fa-pen"></i> Rename</summary>
                    <form method="post" autocomplete="off">
                        <input type="hidden" name="action" value="rename">
                        <input type="hidden" name="adminid" value="<?= e($a['adminid']) ?>">
                        <input type="text" name="adminName" value="<?= e($a['adminName']) ?>" required>
                        <button class="btn btn-primary" type="submit"><i class="fa-solid fa-check"></i> Save</button>
                    </form>
                </details>
                <?php if (!$isSuper): ?>
                    <form method="post" class="ma-del-form" onsubmit="return confirm('Remove admin &quot;<?= e($a['adminid']) ?>&quot;? This cannot be undone.')">
                        <input type="hidden" name="action" value="remove">
                        <input type="hidden" name="adminid" value="<?= e($a['adminid']) ?>">
                        <button class="btn btn-danger" type="submit" data-testid="ma-remove-<?= e($a['adminid']) ?>"><i class="fa-solid fa-trash"></i> Remove</button>
                    </form>
                <?php else: ?>
                    <span class="ma-locked" title="The Super Admin account is permanent."><i class="fa-solid fa-lock"></i> Protected</span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.ma-header{display:flex;justify-content:space-between;align-items:center;gap:14px;margin:0 0 16px;flex-wrap:wrap}
.ma-header h2{margin:0;font-size:22px;color:#0a1f44;letter-spacing:.3px}
.ma-header h2 i{color:#1d4ed8;margin-right:6px}
.ma-sub{margin:6px 0 0;color:#475569;font-size:13px;display:flex;gap:8px;align-items:center;flex-wrap:wrap}
.pill{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;letter-spacing:.4px;padding:3px 9px;border-radius:99px;background:#e0e7ff;color:#1e3a8a}
.pill.super{background:linear-gradient(135deg,#f59e0b,#facc15);color:#1c1917;box-shadow:0 3px 10px -2px rgba(250,204,21,.55)}
.pill.tiny{font-size:9.5px;padding:2px 7px;margin-left:6px}

.btn{border:0;border-radius:9px;padding:9px 16px;font-weight:700;font-size:13px;cursor:pointer;display:inline-flex;align-items:center;gap:7px;transition:.18s}
.btn-primary{background:linear-gradient(135deg,#1d4ed8,#2563eb);color:#fff;box-shadow:0 6px 14px -4px rgba(29,78,216,.55)}
.btn-primary:hover{transform:translateY(-1px);filter:brightness(1.05)}
.btn-warn{background:linear-gradient(135deg,#d97706,#f59e0b);color:#fff}
.btn-danger{background:linear-gradient(135deg,#dc2626,#ef4444);color:#fff}
.btn-ghost{background:#e5e7eb;color:#1e293b}

.ma-flash{padding:10px 14px;border-radius:10px;font-size:13.5px;font-weight:600;margin:0 0 14px;display:flex;align-items:center;gap:8px}
.ma-flash.ok{background:#dcfce7;color:#166534;border:1px solid #86efac}
.ma-flash.err{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5}

.ma-card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:16px 18px;margin:0 0 18px;box-shadow:0 6px 20px -10px rgba(15,23,42,.15)}
.ma-card h3{margin:0 0 12px;font-size:15px;color:#0a1f44;display:flex;align-items:center;gap:8px}
.ma-card h3 i{color:#1d4ed8}
.ma-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px}
.ma-grid label{font-size:11px;letter-spacing:.5px;text-transform:uppercase;color:#64748b;font-weight:700;display:block;margin:0 0 4px}
.ma-grid input{width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:9px 12px;font-size:13.5px;background:#f8fafc}
.ma-grid input:focus{outline:none;border-color:#2563eb;background:#fff;box-shadow:0 0 0 3px rgba(37,99,235,.18)}
.ma-actions{display:flex;justify-content:flex-end;gap:8px;margin-top:14px}

.ma-list{display:flex;flex-direction:column;gap:10px}
.ma-row{display:flex;align-items:center;gap:14px;background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:12px 14px;box-shadow:0 4px 14px -8px rgba(15,23,42,.10);transition:.18s}
.ma-row:hover{transform:translateY(-1px);box-shadow:0 8px 22px -10px rgba(15,23,42,.18)}
.ma-row.super{border:2px solid #facc15;background:linear-gradient(180deg,#fffbeb,#fff)}
.ma-avatar{position:relative;width:46px;height:46px;border-radius:50%;background:linear-gradient(135deg,#0a1f44,#1e3a8a);color:#fff;font-weight:800;display:flex;align-items:center;justify-content:center;font-size:15px;letter-spacing:.5px;flex-shrink:0}
.ma-avatar .crown{position:absolute;top:-9px;right:-9px;width:24px;height:24px;border-radius:50%;background:linear-gradient(135deg,#f59e0b,#facc15);color:#1c1917;display:flex;align-items:center;justify-content:center;font-size:11px;box-shadow:0 3px 8px -2px rgba(250,204,21,.6)}
.ma-meta{flex:1;min-width:0}
.ma-name{font-weight:700;color:#0a1f44;font-size:14.5px;display:flex;align-items:center;flex-wrap:wrap}
.ma-id{font-size:12px;color:#64748b;font-family:'JetBrains Mono',ui-monospace,monospace;margin-top:2px}

.ma-tool{flex-shrink:0}
.ma-tool summary{list-style:none;cursor:pointer;font-size:12px;font-weight:700;color:#1d4ed8;padding:6px 12px;border-radius:8px;background:#eff6ff;border:1px solid #bfdbfe;display:inline-flex;align-items:center;gap:5px}
.ma-tool summary::-webkit-details-marker{display:none}
.ma-tool summary:hover{background:#dbeafe}
.ma-tool[open] summary{background:#1d4ed8;color:#fff;border-color:#1d4ed8}
.ma-tool form{position:absolute;background:#fff;border:1px solid #cbd5e1;border-radius:10px;padding:10px;box-shadow:0 12px 28px -8px rgba(15,23,42,.25);margin-top:6px;z-index:10;display:flex;gap:6px;align-items:center}
.ma-tool input{border:1px solid #cbd5e1;border-radius:7px;padding:6px 10px;font-size:13px;min-width:160px}

.ma-del-form{flex-shrink:0;margin:0}
.ma-locked{font-size:11.5px;color:#92400e;background:#fef3c7;border:1px solid #fde68a;padding:5px 10px;border-radius:8px;font-weight:600;display:inline-flex;align-items:center;gap:5px}

@media (max-width: 720px){
    .ma-row{flex-direction:column;align-items:stretch}
    .ma-row .ma-meta{order:1}
    .ma-tool form{position:relative;margin-top:8px}
}
</style>

<script>
(function(){
    var addBtn = document.getElementById('ma-show-add');
    var form   = document.getElementById('ma-add-form');
    var cancel = document.getElementById('ma-cancel-add');
    if (addBtn && form) {
        addBtn.addEventListener('click', function(){ form.style.display = 'block'; form.scrollIntoView({behavior:'smooth'}); });
        cancel && cancel.addEventListener('click', function(){ form.style.display = 'none'; });
    }
})();
</script>
