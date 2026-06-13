<?php
/** Manage Users — list + photo + Excel-style row + CSV export */
require_once 'includes/photo.php';

// CSV export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    while (ob_get_level()) ob_end_clean();
    $q = trim($_GET['q'] ?? '');
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=BEL_Employees_' . date('Ymd_His') . '.csv');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Staff #', 'Photo File', 'Name', 'Department', 'Section', 'Designation', 'Grade', 'Gender', 'IP Phone', 'Phone', 'D.O.B.', 'Cost Center']);
    if ($q !== '') {
        $like = "%$q%";
        $stmt = mysqli_prepare($link, "SELECT staffid,username,deptt,sec,desg,grade,gender,ip_phone,phone_no,d_o_b,cost_center FROM emp_details WHERE staffid LIKE ? OR username LIKE ? OR deptt LIKE ? OR sec LIKE ? OR desg LIKE ? ORDER BY staffid");
        mysqli_stmt_bind_param($stmt, 'sssss', $like,$like,$like,$like,$like);
        mysqli_stmt_execute($stmt);
        $r = mysqli_stmt_get_result($stmt);
    } else {
        $r = mysqli_query($link, "SELECT staffid,username,deptt,sec,desg,grade,gender,ip_phone,phone_no,d_o_b,cost_center FROM emp_details ORDER BY staffid");
    }
    while ($u = mysqli_fetch_assoc($r)) {
        $photo = user_photo($u['staffid']);
        fputcsv($out, [
            $u['staffid'], $photo ? basename($photo) : '',
            $u['username'], $u['deptt'], $u['sec'], $u['desg'], $u['grade'], $u['gender'],
            $u['ip_phone'], $u['phone_no'], $u['d_o_b'], $u['cost_center'],
        ]);
    }
    fclose($out);
    exit;
}

// ZIP export (data + photos)
if (isset($_GET['export']) && $_GET['export'] === 'zip' && class_exists('ZipArchive')) {
    while (ob_get_level()) ob_end_clean();
    $q = trim($_GET['q'] ?? '');
    $tmp = tempnam(sys_get_temp_dir(), 'bel');
    $zip = new ZipArchive();
    $zip->open($tmp, ZipArchive::OVERWRITE);

    $csv = "Staff #,Name,Department,Section,Designation,Grade,Gender,IP Phone,Phone,DOB,Cost Center,Photo File\n";
    $where = '1=1';
    if ($q !== '') {
        $like = '%' . $q . '%';
        $stmt = mysqli_prepare($link, "SELECT * FROM emp_details WHERE staffid LIKE ? OR username LIKE ? OR deptt LIKE ? OR sec LIKE ? OR desg LIKE ? ORDER BY staffid");
        mysqli_stmt_bind_param($stmt, 'sssss', $like,$like,$like,$like,$like);
        mysqli_stmt_execute($stmt);
        $rows = mysqli_stmt_get_result($stmt);
    } else {
        $rows = mysqli_query($link, "SELECT * FROM emp_details ORDER BY staffid");
    }
    while ($u = mysqli_fetch_assoc($rows)) {
        $photo = user_photo($u['staffid']);
        $photoFile = $photo ? $u['staffid'] . '.JPG' : '';
        $csv .= '"'.str_replace('"','""',$u['staffid']).'","'.str_replace('"','""',$u['username']).'","'.str_replace('"','""',$u['deptt']).'","'.str_replace('"','""',$u['sec']).'","'.str_replace('"','""',$u['desg']).'","'.str_replace('"','""',$u['grade']).'","'.str_replace('"','""',$u['gender']).'","'.str_replace('"','""',$u['ip_phone']).'","'.str_replace('"','""',$u['phone_no']).'","'.str_replace('"','""',$u['d_o_b']).'","'.str_replace('"','""',$u['cost_center']).'","'.$photoFile."\"\n";
        if ($photo && is_file($photo)) $zip->addFile($photo, 'photos/' . $u['staffid'] . '.JPG');
    }
    $zip->addFromString('employees.csv', $csv);
    $zip->addFromString('README.txt', "BEL Kotdwar IT Helpdesk — Employee Export\nGenerated " . date('c') . "\n\n• employees.csv  — tabular data\n• photos/        — JPGs named by Staff Number\n");
    $zip->close();

    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename=BEL_Employees_' . date('Ymd_His') . '.zip');
    header('Content-Length: ' . filesize($tmp));
    readfile($tmp); @unlink($tmp); exit;
}

// Actions
if (isset($_POST['delete_user'])) {
    $staffid = trim($_POST['delete_user']);
    $stmt = mysqli_prepare($link, "DELETE FROM emp_details WHERE staffid = ?");
    mysqli_stmt_bind_param($stmt, 's', $staffid);
    if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
        flash_set('success', "User $staffid removed.");
        $photo = user_photo($staffid);
        if ($photo) @unlink($photo);
    } else flash_set('danger', "Could not remove user $staffid.");
    header('Location: Admin_Home.php?AdminTab=ManageUsers' . (!empty($_POST['q']) ? '&q=' . urlencode($_POST['q']) : ''));
    exit;
}
if (isset($_POST['reset_user'])) {
    $staffid = trim($_POST['reset_user']);
    $pwd = md5('Init@123');
    $stmt = mysqli_prepare($link, "UPDATE emp_details SET staffpass = ? WHERE staffid = ?");
    mysqli_stmt_bind_param($stmt, 'ss', $pwd, $staffid);
    if (mysqli_stmt_execute($stmt)) flash_set('success', "Password reset for $staffid → 'Init@123'.");
    else flash_set('danger', "Could not reset password for $staffid.");
    header('Location: Admin_Home.php?AdminTab=ManageUsers' . (!empty($_POST['q']) ? '&q=' . urlencode($_POST['q']) : ''));
    exit;
}

// Query
$q = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['p'] ?? 1));
$per = 25; $off = ($page - 1) * $per;

if ($q !== '') {
    $like = "%$q%";
    $stmt = mysqli_prepare($link, "SELECT staffid,username,deptt,sec,desg,grade,gender,ip_phone,phone_no,d_o_b FROM emp_details WHERE staffid LIKE ? OR username LIKE ? OR deptt LIKE ? OR sec LIKE ? OR desg LIKE ? ORDER BY staffid LIMIT ? OFFSET ?");
    mysqli_stmt_bind_param($stmt, 'sssssii', $like,$like,$like,$like,$like, $per, $off);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    $cstmt = mysqli_prepare($link, "SELECT COUNT(*) FROM emp_details WHERE staffid LIKE ? OR username LIKE ? OR deptt LIKE ? OR sec LIKE ? OR desg LIKE ?");
    mysqli_stmt_bind_param($cstmt, 'sssss', $like,$like,$like,$like,$like);
    mysqli_stmt_execute($cstmt);
    $total = (int) mysqli_fetch_array(mysqli_stmt_get_result($cstmt))[0];
} else {
    $stmt = mysqli_prepare($link, "SELECT staffid,username,deptt,sec,desg,grade,gender,ip_phone,phone_no,d_o_b FROM emp_details ORDER BY staffid LIMIT ? OFFSET ?");
    mysqli_stmt_bind_param($stmt, 'ii', $per, $off);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    $total = (int) mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(*) FROM emp_details"))[0];
}
$pages = max(1, (int) ceil($total / $per));
?>

<div class="page-head">
    <div class="ic"><i class="fa-solid fa-users-gear"></i></div>
    <div>
        <h2>Manage Employees</h2>
        <div class="sub">Browse, search, reset password, remove employees. Export data + photos as Excel or ZIP.</div>
    </div>
    <div class="actions">
        <a href="Admin_Home.php?AdminTab=AddNewUser" class="btn btn-sm" data-testid="btn-goto-add-user"><i class="fa-solid fa-user-plus"></i> Add New User</a>
        <a href="Admin_Home.php?AdminTab=ManageUsers&export=csv<?= $q!=='' ? '&q='.urlencode($q) : '' ?>" class="btn btn-sm btn-success"><i class="fa-solid fa-file-csv"></i> Export CSV</a>
        <a href="Admin_Home.php?AdminTab=ManageUsers&export=zip<?= $q!=='' ? '&q='.urlencode($q) : '' ?>" class="btn btn-sm btn-accent"><i class="fa-solid fa-file-zipper"></i> Export ZIP (with photos)</a>
    </div>
</div>

<div class="card">
    <form method="get" class="flex" style="gap:10px;flex-wrap:wrap">
        <input type="hidden" name="AdminTab" value="ManageUsers">
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Search by Staff #, Name, Dept, Section, Designation…" style="flex:1;min-width:260px" data-testid="users-search-input">
        <button type="submit" class="btn" data-testid="users-search-btn"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
        <?php if ($q !== ''): ?>
            <a class="btn btn-secondary" href="Admin_Home.php?AdminTab=ManageUsers">Clear</a>
        <?php endif; ?>
        <span class="text-muted" style="margin-left:auto;font-size:12.5px"><b><?= number_format($total) ?></b> employees</span>
    </form>
</div>

<div class="table-wrap">
    <table data-testid="users-table">
        <thead>
            <tr>
                <th style="width:50px">Photo</th><th>Staff #</th><th>Name</th><th>Department</th>
                <th>Section</th><th>Designation</th><th>Grade</th><th>Gender</th>
                <th>Phone</th><th>D.O.B.</th><th style="text-align:right;width:160px">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($u = mysqli_fetch_assoc($rows)): ?>
                <tr>
                    <td><?= render_avatar($u['staffid'], $u['username'], 36) ?></td>
                    <td><b style="color:#0a1f44"><?= e($u['staffid']) ?></b></td>
                    <td><?= e($u['username']) ?></td>
                    <td><?= e($u['deptt']) ?></td>
                    <td><?= e($u['sec']) ?></td>
                    <td><?= e($u['desg']) ?></td>
                    <td><?= e($u['grade']) ?></td>
                    <td><?= e($u['gender']) ?></td>
                    <td style="white-space:nowrap;font-variant-numeric:tabular-nums;font-size:11.5px"><?= e(trim(($u['ip_phone'] ?? '') . ' / ' . ($u['phone_no'] ?? ''),' /')) ?></td>
                    <td style="white-space:nowrap;font-size:11.5px"><?= e($u['d_o_b']) ?></td>
                    <td style="text-align:right;white-space:nowrap">
                        <a href="Admin_Home.php?AdminTab=EditUser&sid=<?= urlencode($u['staffid']) ?>" class="btn btn-xs btn-secondary" title="Edit details / password"><i class="fa-solid fa-pen-to-square"></i></a>
                        <a href="Admin_Home.php?AdminTab=PrintEmployee&sid=<?= urlencode($u['staffid']) ?>" target="_blank" class="btn btn-xs btn-secondary" title="Print record"><i class="fa-solid fa-print"></i></a>
                        <form method="post" style="display:inline" onsubmit="return confirm('Reset password to Init@123 for <?= e($u['staffid']) ?>?')">
                            <input type="hidden" name="q" value="<?= e($q) ?>">
                            <button type="submit" name="reset_user" value="<?= e($u['staffid']) ?>" class="btn btn-xs btn-secondary" title="Reset password"><i class="fa-solid fa-key"></i></button>
                        </form>
                        <form method="post" style="display:inline" onsubmit="return confirm('Remove user <?= e($u['staffid']) ?> permanently? This cannot be undone.')">
                            <input type="hidden" name="q" value="<?= e($q) ?>">
                            <button type="submit" name="delete_user" value="<?= e($u['staffid']) ?>" class="btn btn-xs btn-danger" title="Remove user" data-testid="btn-delete-<?= e($u['staffid']) ?>"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            <?php if (mysqli_num_rows($rows) === 0): ?>
                <tr><td colspan="11" style="text-align:center;padding:30px;color:var(--c-text-2)">No employees found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($pages > 1): ?>
<div class="flex" style="justify-content:center;gap:6px;margin-top:8px;flex-wrap:wrap">
    <?php
    $base = 'Admin_Home.php?AdminTab=ManageUsers' . ($q !== '' ? '&q=' . urlencode($q) : '');
    for ($i = max(1, $page - 3); $i <= min($pages, $page + 3); $i++):
    ?>
        <a class="btn btn-sm <?= $i === $page ? '' : 'btn-secondary' ?>" href="<?= $base ?>&p=<?= $i ?>"><?= $i ?></a>
    <?php endfor; ?>
    <span class="text-muted" style="font-size:11.5px;margin-left:8px">Page <?= $page ?> / <?= $pages ?></span>
</div>
<?php endif; ?>
