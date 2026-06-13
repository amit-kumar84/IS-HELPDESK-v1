<?php
/** Admin: Edit existing employee details, photo and password */
require_once 'includes/photo.php';

$staffid = trim($_GET['sid'] ?? $_POST['staffid'] ?? '');
$emp = null;
if ($staffid !== '') {
    $stmt = mysqli_prepare($link, "SELECT * FROM emp_details WHERE staffid=? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 's', $staffid);
    mysqli_stmt_execute($stmt);
    $emp = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

if (isset($_POST['save']) && $emp) {
    $fields = [
        'username'  => trim($_POST['username']  ?? ''),
        'deptt'     => trim($_POST['deptt']     ?? ''),
        'sec'       => trim($_POST['sec']       ?? ''),
        'desg'      => trim($_POST['desg']      ?? ''),
        'grade'     => trim($_POST['grade']     ?? ''),
        'gender'    => trim($_POST['gender']    ?? ''),
        'ip_phone'  => trim($_POST['ip_phone']  ?? ''),
        'phone_no'  => trim($_POST['phone_no']  ?? ''),
        'd_o_b'     => trim($_POST['d_o_b']     ?? ''),
        'cost_center' => trim($_POST['cost_center'] ?? ''),
        'Employee_Subgroup' => trim($_POST['Employee_Subgroup'] ?? ''),
    ];
    $set = []; $vals = []; $types = '';
    foreach ($fields as $k => $v) { $set[] = "`$k`=?"; $vals[] = $v; $types .= 's'; }
    $vals[] = $staffid; $types .= 's';
    $stmt = mysqli_prepare($link, "UPDATE emp_details SET " . implode(',', $set) . " WHERE staffid=?");
    mysqli_stmt_bind_param($stmt, $types, ...$vals);
    $extra = '';
    if (mysqli_stmt_execute($stmt)) {
        // Photo upload (optional)
        if (!empty($_FILES['photo']['name'])) {
            $saved = save_uploaded_photo($_FILES['photo'], $staffid, 'Pictures');
            $extra = $saved ? ' Photo updated.' : ' (Photo upload failed.)';
        }
        // Password reset (optional)
        if (!empty($_POST['new_password'])) {
            $np = trim($_POST['new_password']);
            if (strlen($np) < 4) {
                $extra .= ' Password too short — not changed.';
            } else {
                $hash = md5($np);
                $ps = mysqli_prepare($link, "UPDATE emp_details SET staffpass=? WHERE staffid=?");
                mysqli_stmt_bind_param($ps, 'ss', $hash, $staffid);
                mysqli_stmt_execute($ps);
                $extra .= ' Password updated.';
            }
        }
        flash_set('success', "Employee $staffid updated successfully." . $extra);
        header('Location: Admin_Home.php?AdminTab=EditUser&sid=' . urlencode($staffid));
        exit;
    }
    flash_set('danger', 'Could not save changes.');
}
?>

<div class="page-head">
    <div class="ic"><i class="fa-solid fa-user-pen"></i></div>
    <div>
        <h2>Edit Employee Details</h2>
        <div class="sub">Update any employee's profile, photo or password.</div>
    </div>
    <div class="actions">
        <a href="Admin_Home.php?AdminTab=ManageUsers" class="btn btn-sm btn-secondary"><i class="fa-solid fa-users"></i> Manage Users</a>
    </div>
</div>

<div class="card">
    <form method="get" class="flex" style="gap:10px">
        <input type="hidden" name="AdminTab" value="EditUser">
        <input type="text" name="sid" value="<?= e($staffid) ?>" placeholder="Enter Staff Number to load…" required data-testid="edit-user-search">
        <button class="btn" data-testid="btn-load-user"><i class="fa-solid fa-magnifying-glass"></i> Load</button>
    </form>
</div>

<?php if ($staffid !== '' && !$emp): ?>
    <div class="alert alert-warning"><i class="fa-solid fa-triangle-exclamation"></i> No employee found with Staff # <b><?= e($staffid) ?></b>.</div>
<?php elseif ($emp): ?>
<div class="card">
    <form method="post" enctype="multipart/form-data" autocomplete="off" data-testid="edit-user-form">
        <input type="hidden" name="staffid" value="<?= e($emp['staffid']) ?>">

        <div class="photo-uploader" style="margin-bottom:14px">
            <?php $cur = user_photo($emp['staffid']); ?>
            <div class="preview" id="editPreview">
                <?php if ($cur): ?>
                    <img src="<?= e($cur) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:6px">
                <?php else: ?>
                    <i class="fa-solid fa-user"></i>
                <?php endif; ?>
            </div>
            <div style="flex:1">
                <label style="font-size:11px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.4px">Replace Photo (optional)</label>
                <input type="file" name="photo" accept="image/jpeg,image/png" onchange="(function(i){var f=i.files&&i.files[0];if(!f)return;var r=new FileReader();r.onload=function(e){document.getElementById('editPreview').innerHTML='<img src=\''+e.target.result+'\' style=width:100%;height:100%;object-fit:cover;border-radius:6px>';};r.readAsDataURL(f);})(this)">
                <div class="helper">Will be saved as <b>Pictures/<?= e($emp['staffid']) ?>.JPG</b>.</div>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-row"><label>Staff Number</label><input type="text" value="<?= e($emp['staffid']) ?>" readonly></div>
            <div class="form-row"><label>Employee Name</label><input type="text" name="username" required value="<?= e($emp['username']) ?>"></div>
            <div class="form-row"><label>Department</label><input type="text" name="deptt" value="<?= e($emp['deptt']) ?>"></div>
            <div class="form-row"><label>Section</label><input type="text" name="sec" value="<?= e($emp['sec']) ?>"></div>
            <div class="form-row"><label>Designation</label><input type="text" name="desg" value="<?= e($emp['desg']) ?>"></div>
            <div class="form-row"><label>Grade</label><input type="text" name="grade" value="<?= e($emp['grade']) ?>"></div>
            <div class="form-row"><label>Gender</label>
                <select name="gender">
                    <option <?= $emp['gender']==='Male'?'selected':'' ?>>Male</option>
                    <option <?= $emp['gender']==='Female'?'selected':'' ?>>Female</option>
                </select>
            </div>
            <div class="form-row"><label>IP Phone</label><input type="text" name="ip_phone" value="<?= e($emp['ip_phone']) ?>"></div>
            <div class="form-row"><label>Phone</label><input type="text" name="phone_no" value="<?= e($emp['phone_no']) ?>"></div>
            <div class="form-row"><label>Date of Birth</label><input type="text" name="d_o_b" value="<?= e($emp['d_o_b']) ?>" placeholder="dd.mm.yyyy"></div>
            <div class="form-row"><label>Cost Center</label><input type="text" name="cost_center" value="<?= e($emp['cost_center']) ?>"></div>
            <div class="form-row"><label>Employee Subgroup</label><input type="text" name="Employee_Subgroup" value="<?= e($emp['Employee_Subgroup']) ?>"></div>
        </div>

        <div style="margin-top:18px;background:#fef9c3;border:1px dashed #facc15;border-radius:8px;padding:14px">
            <div style="font-weight:700;color:#92400e;font-size:12.5px;margin-bottom:6px"><i class="fa-solid fa-key"></i> Change Password (optional)</div>
            <input type="text" name="new_password" placeholder="Leave blank to keep current password" autocomplete="new-password" data-testid="edit-user-newpwd">
        </div>

        <div class="flex-end mt-3">
            <a href="Admin_Home.php?AdminTab=PrintEmployee&sid=<?= urlencode($emp['staffid']) ?>" target="_blank" class="btn btn-secondary"><i class="fa-solid fa-print"></i> Print</a>
            <a href="Admin_Home.php?AdminTab=ManageUsers" class="btn btn-secondary">Cancel</a>
            <button type="submit" name="save" class="btn" data-testid="btn-save-user"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
        </div>
    </form>
</div>
<?php endif; ?>
