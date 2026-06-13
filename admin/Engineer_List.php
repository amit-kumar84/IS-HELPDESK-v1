<?php
/** Engineer Directory — with photos + CSV/ZIP export */
require_once 'includes/photo.php';

if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    while (ob_get_level()) ob_end_clean();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=BEL_Engineers_' . date('Ymd_His') . '.csv');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Sr.', 'BEL ID', 'Photo File', 'Name', 'Staff No', 'Support Field', 'Company', 'Joining Date', 'Left Date', 'Status', 'Presence']);
    $r = mysqli_query($link, "SELECT * FROM s_engg_login ORDER BY status ASC, engg_name ASC");
    $i = 1;
    while ($row = mysqli_fetch_assoc($r)) {
        $p = engineer_photo($row['enggid']);
        fputcsv($out, [$i++, $row['enggid'], $p ? basename($p) : '',
            $row['engg_name'], $row['engg_staff_no'], $row['support_field'], $row['company'],
            $row['joining_date'], $row['left_date'],
            $row['status'] === '0' ? 'Active' : 'Inactive',
            $row['presence'] === 'P' ? 'Present' : 'Absent']);
    }
    fclose($out); exit;
}

if (isset($_GET['export']) && $_GET['export'] === 'zip' && class_exists('ZipArchive')) {
    while (ob_get_level()) ob_end_clean();
    $tmp = tempnam(sys_get_temp_dir(), 'belg');
    $zip = new ZipArchive(); $zip->open($tmp, ZipArchive::OVERWRITE);
    $csv = "BEL ID,Name,Staff No,Support Field,Company,Joining Date,Left Date,Status,Presence,Photo File\n";
    $r = mysqli_query($link, "SELECT * FROM s_engg_login ORDER BY engg_name ASC");
    while ($row = mysqli_fetch_assoc($r)) {
        $p = engineer_photo($row['enggid']);
        $pf = $p ? $row['enggid'] . '.JPG' : '';
        $csv .= '"'.$row['enggid'].'","'.str_replace('"','""',$row['engg_name']).'","'.$row['engg_staff_no'].'","'.str_replace('"','""',$row['support_field']).'","'.str_replace('"','""',$row['company']).'","'.$row['joining_date'].'","'.$row['left_date'].'","'.($row['status']==='0'?'Active':'Inactive').'","'.($row['presence']==='P'?'Present':'Absent').'","'.$pf.'"'."\n";
        if ($p && is_file($p)) $zip->addFile($p, 'photos/'.$row['enggid'].'.JPG');
    }
    $zip->addFromString('engineers.csv', $csv);
    $zip->close();
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename=BEL_Engineers_'.date('Ymd_His').'.zip');
    header('Content-Length: ' . filesize($tmp));
    readfile($tmp); @unlink($tmp); exit;
}

$active = mysqli_query($link, "SELECT * FROM s_engg_login WHERE status='0' AND CURDATE() BETWEEN joining_date AND left_date ORDER BY engg_name ASC");
$all    = mysqli_query($link, "SELECT * FROM s_engg_login ORDER BY status ASC, joining_date DESC");
?>

<div class="page-head">
    <div class="ic"><i class="fa-solid fa-list"></i></div>
    <div>
        <h2>Engineer Directory</h2>
        <div class="sub">Active engineers currently servicing and the full historical roster.</div>
    </div>
    <div class="actions">
        <a href="Admin_Home.php?AdminTab=AddEngineer" class="btn btn-sm"><i class="fa-solid fa-user-plus"></i> Add Engineer</a>
        <a href="Admin_Home.php?AdminTab=EngineerList&export=csv" class="btn btn-sm btn-success"><i class="fa-solid fa-file-csv"></i> Export CSV</a>
        <a href="Admin_Home.php?AdminTab=EngineerList&export=zip" class="btn btn-sm btn-accent"><i class="fa-solid fa-file-zipper"></i> Export ZIP (with photos)</a>
    </div>
</div>

<div class="card">
    <div class="card-title"><i class="fa-solid fa-circle-check" style="color:#16a34a"></i> Currently Active (<?= mysqli_num_rows($active) ?>)</div>
    <div class="table-wrap" style="margin:0;border-radius:8px">
        <table>
            <thead><tr><th style="width:42px">#</th><th style="width:50px">Photo</th><th>Engineer</th><th>BEL ID</th><th>Staff No</th><th>Support Field</th><th>Company</th><th>Joining</th><th>Left</th><th>Presence</th><th style="text-align:right;min-width:170px">Action</th></tr></thead>
            <tbody>
            <?php $i=1; while ($r = mysqli_fetch_assoc($active)): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= render_avatar($r['enggid'], $r['engg_name'], 36, 'images/engineers') ?></td>
                    <td><b><?= e($r['engg_name']) ?></b></td>
                    <td><?= e($r['enggid']) ?></td>
                    <td><?= e($r['engg_staff_no']) ?></td>
                    <td><?= e($r['support_field']) ?></td>
                    <td><?= e($r['company']) ?></td>
                    <td><?= e($r['joining_date']) ?></td>
                    <td><?= e($r['left_date']) ?></td>
                    <td><span class="badge <?= $r['presence']==='P' ? 'active' : 'pending' ?>"><?= $r['presence']==='P' ? 'Present' : 'Absent' ?></span></td>
                    <td style="text-align:right;white-space:nowrap">
                        <button class="btn btn-xs btn-warning" onclick="resetEnggPwd('<?= e($r['enggid']) ?>', '<?= e(addslashes($r['engg_name'])) ?>')" data-testid="reset-pwd-<?= e($r['enggid']) ?>"><i class="fa-solid fa-key"></i> Reset Pwd</button>
                        <a class="btn btn-xs btn-primary" href="Admin_Home.php?AdminTab=EditEngineer&id=<?= e($r['enggid']) ?>"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-title"><i class="fa-solid fa-clock-rotate-left"></i> All Engineers (<?= mysqli_num_rows($all) ?>)</div>
    <div class="table-wrap" style="margin:0;border-radius:8px">
        <table>
            <thead><tr><th style="width:42px">#</th><th style="width:50px">Photo</th><th>Engineer</th><th>BEL ID</th><th>Support Field</th><th>Company</th><th>Joining</th><th>Left</th><th>Status</th></tr></thead>
            <tbody>
            <?php $i=1; while ($r = mysqli_fetch_assoc($all)): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= render_avatar($r['enggid'], $r['engg_name'], 36, 'images/engineers') ?></td>
                    <td><b><?= e($r['engg_name']) ?></b></td>
                    <td><?= e($r['enggid']) ?></td>
                    <td><?= e($r['support_field']) ?></td>
                    <td><?= e($r['company']) ?></td>
                    <td><?= e($r['joining_date']) ?></td>
                    <td><?= e($r['left_date']) ?></td>
                    <td><?php if ($r['status']==='1'): ?><span class="badge inactive">Inactive</span><?php else: ?><span class="badge active">Active</span><?php endif; ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Reset Engineer Password Modal -->
<div id="resetPwdModal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.55);z-index:60;align-items:center;justify-content:center;padding:20px">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:480px;padding:24px 26px;box-shadow:0 30px 70px -18px rgba(15,23,42,.5);position:relative;overflow:hidden">
        <div style="position:absolute;left:0;top:0;bottom:0;width:6px;background:linear-gradient(180deg,#FF9933 0% 33%, #fff 33% 66%, #138808 66% 100%)"></div>
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px">
            <div style="width:44px;height:44px;border-radius:11px;background:linear-gradient(135deg,#f59e0b,#facc15);color:#1c1917;display:flex;align-items:center;justify-content:center;font-size:18px"><i class="fa-solid fa-key"></i></div>
            <div>
                <h3 style="margin:0;color:#0a1f44;font-size:17px;font-weight:800">Reset Engineer Password</h3>
                <p style="margin:3px 0 0;color:#475569;font-size:12.5px"><span id="rpModalName" style="color:#1d4ed8;font-weight:700"></span> &middot; <span id="rpModalId" style="font-family:monospace;color:#64748b"></span></p>
            </div>
            <button type="button" onclick="document.getElementById('resetPwdModal').style.display='none'" style="margin-left:auto;background:#f1f5f9;border:0;width:34px;height:34px;border-radius:8px;color:#0a1f44;font-size:14px;cursor:pointer"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="post" action="admin/EngineerResetPassword.php" autocomplete="off">
            <input type="hidden" name="enggid" id="rpEnggId">
            <label style="display:block;font-size:11px;color:#1e3a8a;font-weight:800;text-transform:uppercase;letter-spacing:.6px;margin-bottom:6px">
                <i class="fa-solid fa-lock"></i> New Password
            </label>
            <input type="text" name="new_password" required minlength="4" placeholder="Enter the new password" data-testid="rp-new-pwd" style="width:100%;padding:11px 13px;border:1.5px solid #d6e0ed;border-radius:10px;font-size:14px;font-weight:600;color:#0a1f44;background:#fafbff">
            <p style="margin:8px 0 14px;color:#64748b;font-size:11.5px"><i class="fa-solid fa-circle-info"></i> Engineer will need to use this password to log in next time.</p>
            <div style="display:flex;justify-content:flex-end;gap:8px">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('resetPwdModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-warning" data-testid="rp-submit"><i class="fa-solid fa-floppy-disk"></i> Save Password</button>
            </div>
        </form>
    </div>
</div>

<script>
function resetEnggPwd(id, name){
    document.getElementById('rpEnggId').value      = id;
    document.getElementById('rpModalId').textContent   = id;
    document.getElementById('rpModalName').textContent = name;
    document.getElementById('resetPwdModal').style.display = 'flex';
}
</script>
