<?php
/** Engineer Directory — with XLSX export */
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

if (isset($_GET['export']) && ($_GET['export'] === 'xlsx' || $_GET['export'] === 'csv')) {
    while (ob_get_level()) ob_end_clean();
    $headers = ['Sr.', 'BEL ID', 'Photo File', 'Name', 'Staff No', 'Support Field', 'Company', 'Joining Date', 'Left Date', 'Status', 'Presence'];
    $rowIndex = 1;
    $headerCells = [];
    foreach ($headers as $columnIndex => $header) {
        $headerCells[] = xlsx_cell($rowIndex, $columnIndex + 1, $header);
    }
    $rowsXml = ['<row r="1">' . implode('', $headerCells) . '</row>'];

    $result = mysqli_query($link, "SELECT * FROM s_engg_login ORDER BY status ASC, engg_name ASC");
    $rowIndex = 2;
    while ($row = mysqli_fetch_assoc($result)) {
        $photo = engineer_photo($row['enggid']);
        $photoFile = $photo ? basename($photo) : '';
        $values = [
            $rowIndex - 1,
            $row['enggid'],
            $photoFile,
            $row['engg_name'],
            $row['engg_staff_no'],
            $row['support_field'],
            $row['company'],
            $row['joining_date'],
            $row['left_date'],
            $row['status'] === '0' ? 'Active' : 'Inactive',
            $row['presence'] === 'P' ? 'Present' : 'Absent',
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
        . '<sheets><sheet name="Engineer Directory" sheetId="1" r:id="rId1"/></sheets>'
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
    header('Content-Disposition: attachment; filename=BEL_Engineers_' . date('Ymd_His') . '.xlsx');
    header('Content-Length: ' . strlen($xlsxData));
    echo $xlsxData;
    exit;
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
        <a href="Admin_Home.php?AdminTab=EngineerList&export=xlsx" class="btn btn-sm btn-success"><i class="fa-solid fa-file-excel"></i> Export XLSX</a>
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
