<?php
/** Manage Users — list + photo + Excel-style row + XLSX export */
require_once 'includes/photo.php';

function xlsx_xml_escape(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
}

function xlsx_column_letter(int $index): string {
    $letter = '';
    while ($index > 0) {
        $index--;
        $letter = chr(65 + ($index % 26)) . $letter;
        $index = intdiv($index, 26);
    }
    return $letter;
}

function xlsx_cell(int $row, int $column, string $value): string {
    $ref = xlsx_column_letter($column) . $row;
    return '<c r="' . $ref . '" t="inlineStr"><is><t>' . xlsx_xml_escape($value) . '</t></is></c>';
}

function xlsx_zip_build(array $entries): string {
    $localData = '';
    $centralData = '';
    $offset = 0;

    foreach ($entries as $entry) {
        $name = str_replace('\\', '/', $entry['name']);
        $data = $entry['data'];
        $crc = crc32($data);
        if ($crc < 0) {
            $crc = $crc + 4294967296;
        }
        $compressedSize = strlen($data);
        $uncompressedSize = $compressedSize;
        $nameLength = strlen($name);

        $localHeader = pack(
            'VvvvvvVVVvv',
            0x04034b50,
            20,
            0,
            0,
            0,
            0,
            $crc,
            $compressedSize,
            $uncompressedSize,
            $nameLength,
            0
        ) . $name . $data;

        $localData .= $localHeader;

        $centralHeader = pack(
            'VvvvvvvVVVvvvvvVV',
            0x02014b50,
            20,
            20,
            0,
            0,
            0,
            0,
            $crc,
            $compressedSize,
            $uncompressedSize,
            $nameLength,
            0,
            0,
            0,
            0,
            0,
            $offset
        ) . $name;

        $centralData .= $centralHeader;
        $offset += strlen($localHeader);
    }

    $entryCount = count($entries);
    $centralSize = strlen($centralData);
    $centralOffset = strlen($localData);

    $endRecord = pack(
        'VvvvvVVv',
        0x06054b50,
        0,
        0,
        $entryCount,
        $entryCount,
        $centralSize,
        $centralOffset,
        0
    );

    return $localData . $centralData . $endRecord;
}

// XLSX export
if (isset($_GET['export']) && $_GET['export'] === 'xlsx') {
    while (ob_get_level()) ob_end_clean();
    $q = trim($_GET['q'] ?? '');

    $headers = ['Staff #', 'Photo File', 'Name', 'Department', 'Section', 'Designation', 'Grade', 'Gender', 'IP Phone', 'Phone', 'D.O.B.', 'Cost Center'];
    $rowsXml = [];
    $rowIndex = 1;
    $headerCells = [];
    foreach ($headers as $columnIndex => $header) {
        $headerCells[] = xlsx_cell($rowIndex, $columnIndex + 1, $header);
    }
    $rowsXml[] = '<row r="1">' . implode('', $headerCells) . '</row>';

    if ($q !== '') {
        $like = "%$q%";
        $stmt = mysqli_prepare($link, "SELECT staffid,username,deptt,sec,desg,grade,gender,ip_phone,phone_no,d_o_b,cost_center FROM emp_details WHERE staffid LIKE ? OR username LIKE ? OR deptt LIKE ? OR sec LIKE ? OR desg LIKE ? ORDER BY staffid");
        mysqli_stmt_bind_param($stmt, 'sssss', $like,$like,$like,$like,$like);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        $result = mysqli_query($link, "SELECT staffid,username,deptt,sec,desg,grade,gender,ip_phone,phone_no,d_o_b,cost_center FROM emp_details ORDER BY staffid");
    }

    $rowIndex = 2;
    while ($u = mysqli_fetch_assoc($result)) {
        $photo = user_photo($u['staffid']);
        $photoFile = $photo ? basename($photo) : '';
        $values = [
            $u['staffid'],
            $photoFile,
            $u['username'],
            $u['deptt'],
            $u['sec'],
            $u['desg'],
            $u['grade'],
            $u['gender'],
            $u['ip_phone'],
            $u['phone_no'],
            $u['d_o_b'],
            $u['cost_center'],
        ];
        $cells = [];
        foreach ($values as $columnIndex => $value) {
            $cells[] = xlsx_cell($rowIndex, $columnIndex + 1, (string) $value);
        }
        $rowsXml[] = '<row r="' . $rowIndex . '">' . implode('', $cells) . '</row>';
        $rowIndex++;
    }

    $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
        . '<sheetData>' . implode('', $rowsXml) . '</sheetData>'
        . '</worksheet>';

    $workbookXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
        . '<sheets><sheet name="Manage Users" sheetId="1" r:id="rId1"/></sheets>'
        . '</workbook>';

    $stylesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
        . '<fonts count="1"><font><sz val="11"/><color theme="1"/><name val="Calibri"/><family val="2"/></font></fonts>'
        . '<fills count="1"><fill><patternFill patternType="none"/></fill></fills>'
        . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
        . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
        . '<cellXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/></cellXfs>'
        . '</styleSheet>';

    $contentTypesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
        . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
        . '<Default Extension="xml" ContentType="application/xml"/>'
        . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
        . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
        . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
        . '</Types>';

    $rootRelsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
        . '</Relationships>';

    $workbookRelsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
        . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
        . '</Relationships>';

    $xlsxData = xlsx_zip_build([
        ['name' => '[Content_Types].xml', 'data' => $contentTypesXml],
        ['name' => '_rels/.rels', 'data' => $rootRelsXml],
        ['name' => 'xl/workbook.xml', 'data' => $workbookXml],
        ['name' => 'xl/_rels/workbook.xml.rels', 'data' => $workbookRelsXml],
        ['name' => 'xl/worksheets/sheet1.xml', 'data' => $sheetXml],
        ['name' => 'xl/styles.xml', 'data' => $stylesXml],
    ]);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename=BEL_Employees_' . date('Ymd_His') . '.xlsx');
    header('Content-Length: ' . strlen($xlsxData));
    echo $xlsxData;
    exit;
}

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
        <div class="sub">Browse, search, reset password, remove employees. Export data as CSV.</div>
    </div>
    <div class="actions">
        <a href="Admin_Home.php?AdminTab=AddNewUser" class="btn btn-sm" data-testid="btn-goto-add-user"><i class="fa-solid fa-user-plus"></i> Add New User</a>
        <a href="Admin_Home.php?AdminTab=ManageUsers&export=xlsx<?= $q!=='' ? '&q='.urlencode($q) : '' ?>" class="btn btn-sm btn-success"><i class="fa-solid fa-file-excel"></i> Export XLSX</a>
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

<div class="table-wrap manage-users-table-wrap">
    <table data-testid="users-table">
        <thead>
            <tr>
                <th class="photo-col" style="width:84px">Photo</th><th>Staff #</th><th>Name</th><th>Department</th>
                <th>Section</th><th>Designation</th><th>Grade</th><th>Gender</th>
                <th>Phone</th><th>D.O.B.</th><th style="text-align:right;width:160px">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($u = mysqli_fetch_assoc($rows)): ?>
                <tr>
                    <td class="photo-col"><?= render_avatar($u['staffid'], $u['username'], 44) ?></td>
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
