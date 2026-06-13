<?php
/** Add New User / Employee (admin) — with photo upload */
require_once 'includes/photo.php';

$err = ''; $ok = '';
if (isset($_POST['sub'])) {
    $staff_id    = trim($_POST['staff_id'] ?? '');
    $emp_name    = trim($_POST['emp_name'] ?? '');
    $cost_center = trim($_POST['cost_center'] ?? '');
    $dept        = trim($_POST['dept'] ?? '');
    $sec         = trim($_POST['sec'] ?? '');
    $ip_no       = trim($_POST['ip_no'] ?? '');
    $base_no     = trim($_POST['base_no'] ?? '');
    $dob         = trim($_POST['dob'] ?? '');
    $desg        = trim($_POST['desg'] ?? '');
    $grade       = trim($_POST['grade'] ?? '');
    $gender      = trim($_POST['gender'] ?? '');
    $emp_subgrp  = trim($_POST['emp_subgrp'] ?? '');
    if ($dept === 'Other') $dept = trim($_POST['other_dept'] ?? '');
    if ($sec  === 'Other') $sec  = trim($_POST['other_sec']  ?? '');

    if ($staff_id === '' || $emp_name === '') {
        $err = 'Staff number and Employee name are required.';
    } else {
        $pass = md5('Init@123');
        $division = $dept;
        $stmt = mysqli_prepare($link,
            "INSERT INTO `emp_details`
             (`staffid`,`username`,`cost_center`,`division`,`deptt`,`sec`,`ip_phone`,`phone_no`,
              `d_o_b`,`desg`,`grade`,`gender`,`Employee_Subgroup`,`staffpass`)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt, 'ssssssssssssss',
            $staff_id, $emp_name, $cost_center, $division, $dept, $sec, $ip_no, $base_no,
            $dob, $desg, $grade, $gender, $emp_subgrp, $pass);

        if (mysqli_stmt_execute($stmt)) {
            // Save photo as Pictures/{staff_id}.JPG
            $photoMsg = '';
            if (!empty($_FILES['photo']['name'])) {
                $saved = save_uploaded_photo($_FILES['photo'], $staff_id, 'Pictures');
                $photoMsg = $saved ? ' Photo saved as ' . basename($saved) . '.' : ' Photo upload failed (only JPG/PNG, max 5 MB).';
            }
            flash_set('success', 'Employee "' . $emp_name . '" added. Default password: Init@123.' . $photoMsg);
            header('Location: Admin_Home.php?AdminTab=AddNewUser');
            exit;
        } else {
            $err = 'Could not add user. Possible duplicate staff number or DB error.';
        }
    }
}
?>

<div class="page-head">
    <div class="ic"><i class="fa-solid fa-user-plus"></i></div>
    <div>
        <h2>Add New Employee</h2>
        <div class="sub">Create a new user account. Default password: <code>Init@123</code>. Upload a passport-style photo (saved as <code>{Staff#}.JPG</code>).</div>
    </div>
    <div class="actions">
        <a href="Admin_Home.php?AdminTab=ManageUsers" class="btn btn-sm btn-secondary"><i class="fa-solid fa-users"></i> Manage Users</a>
    </div>
</div>

<?php if ($err): ?>
    <div class="alert alert-danger" data-testid="add-user-error"><i class="fa-solid fa-circle-exclamation"></i> <?= e($err) ?></div>
<?php endif; ?>

<div class="card">
    <form method="post" enctype="multipart/form-data" autocomplete="off" data-testid="add-user-form">

        <div class="photo-uploader" style="margin-bottom:14px">
            <div class="preview" id="photoPreview"><i class="fa-solid fa-user"></i></div>
            <div style="flex:1">
                <label style="font-size:11px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.4px">Employee Photo</label>
                <input type="file" name="photo" accept="image/jpeg,image/png" onchange="(function(i){var f=i.files&&i.files[0];if(!f)return;var r=new FileReader();r.onload=function(e){document.getElementById('photoPreview').innerHTML='<img src=\''+e.target.result+'\' style=width:100%;height:100%;object-fit:cover;border-radius:6px>';};r.readAsDataURL(f);})(this)" data-testid="input-photo">
                <div class="helper">Passport-style photo, JPG or PNG, max 5 MB. Will be saved as <b>Pictures/{Staff#}.JPG</b>.</div>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-row"><label>Staff Number <span class="req">*</span></label>
                <input type="text" name="staff_id" required maxlength="20" placeholder="e.g. 207512" data-testid="input-staff-id">
            </div>
            <div class="form-row"><label>Employee Name <span class="req">*</span></label>
                <input type="text" name="emp_name" required placeholder="Full name" data-testid="input-emp-name">
            </div>
            <div class="form-row"><label>Department</label>
                <select name="dept" onchange="document.getElementById('inputbox_dept').style.display=(this.value==='Other'||this.value==='')?'block':'none'">
                    <option value="">Select Department</option>
                    <?php
                    $r = mysqli_query($link, "SELECT DISTINCT deptt FROM emp_details ORDER BY deptt ASC");
                    while ($d = mysqli_fetch_array($r)) echo '<option>' . e($d['deptt']) . '</option>';
                    ?>
                    <option value="Other">Other&hellip;</option>
                </select>
                <input type="text" name="other_dept" id="inputbox_dept" placeholder="Enter Department" style="display:none;margin-top:6px">
            </div>
            <div class="form-row"><label>Section</label>
                <select name="sec" onchange="document.getElementById('inputbox_sec').style.display=(this.value==='Other'||this.value==='')?'block':'none'">
                    <option value="">Select Section</option>
                    <?php
                    $r = mysqli_query($link, "SELECT DISTINCT sec FROM emp_details ORDER BY sec ASC");
                    while ($s = mysqli_fetch_array($r)) echo '<option>' . e($s['sec']) . '</option>';
                    ?>
                    <option value="Other">Other&hellip;</option>
                </select>
                <input type="text" name="other_sec" id="inputbox_sec" placeholder="Enter Section" style="display:none;margin-top:6px">
            </div>
            <div class="form-row"><label>IP Phone Number</label>
                <input type="text" name="ip_no" placeholder="IP phone">
            </div>
            <div class="form-row"><label>Base Phone Number</label>
                <input type="text" name="base_no" placeholder="Base phone">
            </div>
            <div class="form-row"><label>Date of Birth <span class="req">*</span></label>
                <input type="date" name="dob" required max="<?= date('Y-m-d') ?>">
            </div>
            <div class="form-row"><label>Cost Center <span class="req">*</span></label>
                <input type="text" name="cost_center" required placeholder="e.g. 991474">
            </div>
            <div class="form-row"><label>Designation <span class="req">*</span></label>
                <input type="text" name="desg" required>
            </div>
            <div class="form-row"><label>Grade <span class="req">*</span></label>
                <input type="text" name="grade" required>
            </div>
            <div class="form-row"><label>Gender <span class="req">*</span></label>
                <div style="display:flex;gap:14px;padding:6px 0">
                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-weight:500;text-transform:none;letter-spacing:normal"><input type="radio" name="gender" value="Male" required style="width:auto"> Male</label>
                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-weight:500;text-transform:none;letter-spacing:normal"><input type="radio" name="gender" value="Female" style="width:auto"> Female</label>
                </div>
            </div>
            <div class="form-row"><label>Employee Subgroup <span class="req">*</span></label>
                <select name="emp_subgrp" required>
                    <option value="">Select Subgroup</option>
                    <?php
                    $r = mysqli_query($link, "SELECT DISTINCT Employee_Subgroup FROM emp_details WHERE Employee_Subgroup<>'' ORDER BY Employee_Subgroup ASC");
                    while ($s = mysqli_fetch_array($r)) echo '<option>' . e($s['Employee_Subgroup']) . '</option>';
                    ?>
                </select>
            </div>
        </div>
        <div class="flex-end mt-3">
            <a href="Admin_Home.php?AdminTab=ManageUsers" class="btn btn-secondary">Cancel</a>
            <button type="submit" name="sub" class="btn" data-testid="btn-add-user-submit"><i class="fa-solid fa-floppy-disk"></i> Add Employee</button>
        </div>
    </form>
</div>
