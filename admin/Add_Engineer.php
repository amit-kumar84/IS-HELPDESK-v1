<?php
/** Add new engineer (admin) — with photo upload */
require_once 'includes/photo.php';

$err = '';
if (isset($_POST['sub'])) {
    $engg_name     = trim($_POST['engg_name'] ?? '');
    $enggid        = trim($_POST['enggid'] ?? '');
    $engg_staff_no = trim($_POST['engg_staff_no'] ?? '');
    $company_name  = trim($_POST['company_name'] ?? '');
    $support_field = trim($_POST['support_field'] ?? '');
    $joining_date  = trim($_POST['joining_date'] ?? '');
    $left_date     = trim($_POST['left_date'] ?? '9999-12-31');
    if ($support_field === 'Other') $support_field = trim($_POST['other_support_field'] ?? '');

    if ($engg_name === '' || $enggid === '' || $support_field === '' || $joining_date === '') {
        $err = 'Name, BEL ID, Support Field and Joining Date are required.';
    } else {
        $pass = md5('12345678');
        $status = '0'; $presence = 'P';
        $stmt = mysqli_prepare($link,
            "INSERT INTO `s_engg_login`
             (`enggid`,`engg_name`,`engg_staff_no`,`company`,`support_field`,`enggpass`,`joining_date`,`left_date`,`status`,`presence`)
             VALUES (?,?,?,?,?,?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt, 'ssssssssss',
            $enggid, $engg_name, $engg_staff_no, $company_name, $support_field, $pass, $joining_date, $left_date, $status, $presence);
        if (mysqli_stmt_execute($stmt)) {
            $photoMsg = '';
            if (!empty($_FILES['photo']['name'])) {
                $saved = save_uploaded_photo($_FILES['photo'], $enggid, 'images/engineers');
                $photoMsg = $saved ? ' Photo saved as ' . basename($saved) . '.' : ' Photo upload failed.';
            }
            flash_set('success', "Engineer '$engg_name' added. Default password: 12345678." . $photoMsg);
            header('Location: Admin_Home.php?AdminTab=AddEngineer');
            exit;
        }
        $err = 'Could not add engineer. Possible duplicate BEL ID or DB error.';
    }
}
?>

<div class="page-head">
    <div class="ic"><i class="fa-solid fa-user-gear"></i></div>
    <div>
        <h2>Add New Engineer</h2>
        <div class="sub">Onboard a support engineer. Default password: <code>12345678</code>. Upload a photo (saved as <code>images/engineers/{BEL#}.JPG</code>).</div>
    </div>
    <div class="actions"><a href="Admin_Home.php?AdminTab=EngineerList" class="btn btn-sm btn-secondary"><i class="fa-solid fa-list"></i> Engineer List</a></div>
</div>

<?php if ($err): ?>
    <div class="alert alert-danger" data-testid="add-eng-error"><i class="fa-solid fa-circle-exclamation"></i> <?= e($err) ?></div>
<?php endif; ?>

<div class="card">
    <form method="post" enctype="multipart/form-data" autocomplete="off" data-testid="add-engineer-form">

        <div class="photo-uploader" style="margin-bottom:14px">
            <div class="preview" id="engPhotoPreview"><i class="fa-solid fa-user"></i></div>
            <div style="flex:1">
                <label style="font-size:11px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.4px">Engineer Photo</label>
                <input type="file" name="photo" accept="image/jpeg,image/png" onchange="(function(i){var f=i.files&&i.files[0];if(!f)return;var r=new FileReader();r.onload=function(e){document.getElementById('engPhotoPreview').innerHTML='<img src=\''+e.target.result+'\' style=width:100%;height:100%;object-fit:cover;border-radius:6px>';};r.readAsDataURL(f);})(this)" data-testid="input-eng-photo">
                <div class="helper">JPG or PNG, max 5 MB. Saved as <b>images/engineers/{BEL#}.JPG</b>.</div>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-row"><label>Engineer Name <span class="req">*</span></label>
                <input type="text" name="engg_name" required placeholder="Full Name" data-testid="input-engg-name">
            </div>
            <div class="form-row"><label>BEL Staff No <span class="req">*</span></label>
                <input type="text" name="enggid" required placeholder="e.g. 620230" data-testid="input-engg-id">
            </div>
            <div class="form-row"><label>Company Staff No</label>
                <input type="text" name="engg_staff_no" placeholder="Internal staff #">
            </div>
            <div class="form-row"><label>Company Name</label>
                <input type="text" name="company_name" placeholder="e.g. ABC Services">
            </div>
            <div class="form-row"><label>Support Field <span class="req">*</span></label>
                <select name="support_field" onchange="document.getElementById('inputbox').style.display=(this.value==='Other'||this.value==='')?'block':'none'" required>
                    <option value="">Select Support Field</option>
                    <?php
                    $r = mysqli_query($link, "SELECT DISTINCT support_field FROM s_engg_login ORDER BY support_field");
                    while ($f = mysqli_fetch_array($r)) echo '<option>' . e($f['support_field']) . '</option>';
                    ?>
                    <option value="Other">Other&hellip;</option>
                </select>
                <input type="text" name="other_support_field" id="inputbox" placeholder="Enter Support Field" style="display:none;margin-top:6px">
            </div>
            <div class="form-row"><label>Joining Date <span class="req">*</span></label>
                <input type="date" name="joining_date" required>
            </div>
            <div class="form-row"><label>Left / End Date</label>
                <input type="date" name="left_date" value="9999-12-31">
            </div>
        </div>
        <div class="flex-end mt-3">
            <a href="Admin_Home.php?AdminTab=EngineerList" class="btn btn-secondary">Cancel</a>
            <button type="submit" name="sub" class="btn" data-testid="btn-add-engg-submit"><i class="fa-solid fa-floppy-disk"></i> Add Engineer</button>
        </div>
    </form>
</div>
