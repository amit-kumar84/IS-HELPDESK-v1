<?php
/** Admin: Bulk import users from CSV / Excel-CSV */
require_once 'includes/photo.php';

// Template download
if (isset($_GET['template'])) {
    while (ob_get_level()) ob_end_clean();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=BEL_Employee_Import_Template.csv');
    echo "staffid,username,deptt,sec,desg,grade,gender,ip_phone,phone_no,d_o_b,cost_center,Employee_Subgroup\n";
    echo "207512,JOHN DOE,PROD,SMD,JR SECTION OFFICER,WG-X,Male,4500,9876543210,15.08.1990,15102022,EMPLOYEE\n";
    echo "207513,JANE SMITH,MM,STORE,SR ASSISTANT,WG-IX,Female,4501,9876543211,20.03.1985,15103014,EMPLOYEE\n";
    exit;
}

$result = null;
if (isset($_POST['import']) && !empty($_FILES['csv']['tmp_name'])) {
    $f = $_FILES['csv'];
    if ($f['error'] !== UPLOAD_ERR_OK) {
        flash_set('danger', 'Upload failed.');
    } elseif ($f['size'] > 5 * 1024 * 1024) {
        flash_set('danger', 'File too large (max 5 MB).');
    } else {
        $fh = fopen($f['tmp_name'], 'r');
        $hdr = fgetcsv($fh);
        $expected = ['staffid','username','deptt','sec','desg','grade','gender','ip_phone','phone_no','d_o_b','cost_center','Employee_Subgroup'];
        if (!$hdr || array_map('strtolower', array_map('trim', $hdr)) !== array_map('strtolower', $expected)) {
            flash_set('danger', 'CSV header mismatch. Use the provided template — columns must match exactly: ' . implode(', ', $expected));
        } else {
            $defaultPass = md5('Init@123');
            $stmt = mysqli_prepare($link,
                "INSERT INTO emp_details (staffid,username,deptt,division,sec,desg,grade,gender,ip_phone,phone_no,d_o_b,cost_center,Employee_Subgroup,staffpass)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)
                 ON DUPLICATE KEY UPDATE username=VALUES(username),deptt=VALUES(deptt),sec=VALUES(sec),desg=VALUES(desg),grade=VALUES(grade),gender=VALUES(gender),ip_phone=VALUES(ip_phone),phone_no=VALUES(phone_no),d_o_b=VALUES(d_o_b),cost_center=VALUES(cost_center),Employee_Subgroup=VALUES(Employee_Subgroup)");

            $ok = 0; $fail = 0; $errors = []; $row = 1;
            while (($r = fgetcsv($fh)) !== false) {
                $row++;
                if (count($r) < 12) { $errors[] = "Row $row: not enough columns"; $fail++; continue; }
                [$sid,$nm,$d,$s,$ds,$gr,$gd,$ip,$ph,$dob,$cc,$sg] = array_map('trim', array_slice($r, 0, 12));
                if ($sid === '' || $nm === '') { $errors[] = "Row $row: blank staffid or name"; $fail++; continue; }
                $division = $d;
                mysqli_stmt_bind_param($stmt, 'ssssssssssssss', $sid,$nm,$d,$division,$s,$ds,$gr,$gd,$ip,$ph,$dob,$cc,$sg,$defaultPass);
                if (mysqli_stmt_execute($stmt)) $ok++;
                else { $errors[] = "Row $row ($sid): " . mysqli_stmt_error($stmt); $fail++; }
            }
            fclose($fh);
            $result = ['ok'=>$ok,'fail'=>$fail,'errors'=>$errors];
            if ($ok > 0) flash_set('success', "$ok rows imported / updated successfully. " . ($fail ? "$fail failed (see below)." : ''));
            else flash_set('danger', 'No rows could be imported — check errors below.');
        }
    }
}
?>

<div class="page-head">
    <div class="ic"><i class="fa-solid fa-file-import"></i></div>
    <div>
        <h2>Bulk Import Employees</h2>
        <div class="sub">Upload an Excel-style CSV to add or update multiple employees at once.</div>
    </div>
    <div class="actions">
        <a href="Admin_Home.php?AdminTab=BulkImport&template=1" class="btn btn-sm btn-success"><i class="fa-solid fa-file-csv"></i> Download Template</a>
        <a href="Admin_Home.php?AdminTab=ManageUsers" class="btn btn-sm btn-secondary"><i class="fa-solid fa-users"></i> Manage Users</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1.1fr 1fr;gap:14px">
    <div class="card">
        <div class="card-title"><i class="fa-solid fa-upload"></i> Upload CSV File</div>
        <form method="post" enctype="multipart/form-data" data-testid="bulk-import-form">
            <div class="photo-uploader" style="padding:16px">
                <div class="preview" style="width:60px;height:60px;font-size:24px;background:#dbeafe;color:#1e40af"><i class="fa-solid fa-file-csv"></i></div>
                <div style="flex:1">
                    <label style="font-size:11px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.4px">CSV File <span class="req">*</span></label>
                    <input type="file" name="csv" accept=".csv,text/csv" required data-testid="bulk-import-file">
                    <div class="helper">Max 5 MB. Must match the template format exactly. Existing staffids will be UPDATED, new ones INSERTED.</div>
                </div>
            </div>
            <div class="flex-end mt-3">
                <button type="submit" name="import" class="btn" data-testid="btn-bulk-import"><i class="fa-solid fa-cloud-arrow-up"></i> Import</button>
            </div>
        </form>

        <?php if ($result): ?>
            <div style="margin-top:14px;background:<?= $result['ok']>0?'#dcfce7':'#fee2e2' ?>;border:1px solid <?= $result['ok']>0?'#bbf7d0':'#fecaca' ?>;border-radius:8px;padding:12px;font-size:12.5px">
                <b><?= $result['ok'] ?> succeeded</b> &middot; <b><?= $result['fail'] ?> failed</b>
                <?php if (!empty($result['errors'])): ?>
                    <ul style="margin:8px 0 0 16px;font-size:11.5px;color:#991b1b">
                    <?php foreach (array_slice($result['errors'], 0, 30) as $err): ?>
                        <li><?= e($err) ?></li>
                    <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-title"><i class="fa-solid fa-circle-info"></i> Format Guide</div>
        <p style="font-size:12.5px;color:#475569;margin:0 0 10px">The CSV file must have exactly <b>12 columns</b> in the order shown below. The first row must be the header. Save your Excel file as <b>CSV (Comma Separated)</b> before uploading.</p>
        <div class="table-wrap" style="margin:0;border-radius:6px;font-size:11.5px">
            <table>
                <thead><tr><th style="padding:6px 8px">Column</th><th style="padding:6px 8px">Required</th><th style="padding:6px 8px">Example / Notes</th></tr></thead>
                <tbody>
                    <tr><td><b>staffid</b></td><td>Yes</td><td>207512 &middot; Unique. Existing IDs are updated.</td></tr>
                    <tr><td><b>username</b></td><td>Yes</td><td>JOHN DOE</td></tr>
                    <tr><td>deptt</td><td>-</td><td>PROD / MM / FIN / R&amp;D &hellip;</td></tr>
                    <tr><td>sec</td><td>-</td><td>SMD / STORE / OFFICE &hellip;</td></tr>
                    <tr><td>desg</td><td>-</td><td>JR SECTION OFFICER</td></tr>
                    <tr><td>grade</td><td>-</td><td>WG-X / E-II &hellip;</td></tr>
                    <tr><td>gender</td><td>-</td><td>Male / Female</td></tr>
                    <tr><td>ip_phone</td><td>-</td><td>4500</td></tr>
                    <tr><td>phone_no</td><td>-</td><td>9876543210</td></tr>
                    <tr><td>d_o_b</td><td>-</td><td>15.08.1990 (dd.mm.yyyy)</td></tr>
                    <tr><td>cost_center</td><td>-</td><td>15102022</td></tr>
                    <tr><td>Employee_Subgroup</td><td>-</td><td>EMPLOYEE / APPRENTICE &hellip;</td></tr>
                </tbody>
            </table>
        </div>
        <div style="background:#fef3c7;border:1px solid #fde68a;border-radius:6px;padding:10px;font-size:11.5px;color:#92400e;margin-top:12px">
            <i class="fa-solid fa-triangle-exclamation"></i> <b>Default password</b> for all newly imported employees will be <code>Init@123</code>. They should change it on first login.
        </div>
    </div>
</div>
