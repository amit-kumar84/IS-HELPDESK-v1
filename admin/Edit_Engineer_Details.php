<?php
/** Edit Engineer Details — search then edit, prepared statements */
require_once 'includes/photo.php';
$searchResult = null;
$selected = trim($_POST['valueToSearch'] ?? ($_GET['eid'] ?? ''));

if (isset($_POST['sub'])) {
    $enggid        = trim($_POST['enggid'] ?? '');
    $engg_staff_no = trim($_POST['engg_staff_no'] ?? '');
    $support_field = trim($_POST['support_field'] ?? '');
    $joining_date  = trim($_POST['joining_date'] ?? '');
    $left_date     = trim($_POST['left_date'] ?? '');

    if ($enggid !== '') {
        $stmt = mysqli_prepare($link,
            "UPDATE s_engg_login SET engg_staff_no=?, support_field=?, joining_date=?, left_date=? WHERE enggid=?");
        mysqli_stmt_bind_param($stmt, 'sssss', $engg_staff_no, $support_field, $joining_date, $left_date, $enggid);
        if (mysqli_stmt_execute($stmt)) {
            $extra = '';
            if (!empty($_FILES['photo']['name'])) {
                $saved = save_uploaded_photo($_FILES['photo'], $enggid, 'images/engineers');
                $extra = $saved ? ' Photo updated.' : ' (Photo upload failed.)';
            }
            flash_set('success', "Engineer $enggid updated successfully." . $extra);
        } else {
            flash_set('danger', 'Could not update engineer details.');
        }
        header('Location: Admin_Home.php?AdminTab=EditEngineer&eid=' . urlencode($enggid));
        exit;
    }
}

if (isset($_POST['search']) || $selected !== '') {
    if (ctype_digit($selected)) {
        $stmt = mysqli_prepare($link, "SELECT * FROM s_engg_login WHERE enggid = ? LIMIT 1");
    } else {
        $stmt = mysqli_prepare($link, "SELECT * FROM s_engg_login WHERE engg_name = ? LIMIT 1");
    }
    mysqli_stmt_bind_param($stmt, 's', $selected);
    mysqli_stmt_execute($stmt);
    $searchResult = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}
?>

<div class="page-head">
    <div class="ic"><i class="fa-solid fa-user-pen"></i></div>
    <div>
        <h2>Edit Engineer Details</h2>
        <div class="sub">Search an active engineer by name, then update their details.</div>
    </div>
</div>

<div class="card">
    <form method="post" class="flex" style="gap:10px">
        <select name="valueToSearch" required style="flex:1;min-width:280px" data-testid="edit-engg-select">
            <option value="">-- Choose Engineer --</option>
            <?php
            $r = mysqli_query($link, "SELECT enggid, engg_name FROM s_engg_login WHERE status='0' ORDER BY engg_name ASC");
            while ($e = mysqli_fetch_assoc($r)) {
                $sel = ($selected === $e['engg_name'] || $selected === $e['enggid']) ? 'selected' : '';
                echo '<option value="' . e($e['engg_name']) . '" ' . $sel . '>' . e($e['engg_name']) . ' (#' . e($e['enggid']) . ')</option>';
            }
            ?>
        </select>
        <button type="submit" name="search" class="btn" data-testid="btn-edit-engg-search"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
    </form>
</div>

<?php if ($searchResult): ?>
<div class="card">
    <div class="card-title"><i class="fa-solid fa-pen-to-square"></i> Update <?= e($searchResult['engg_name']) ?></div>
    <form method="post" enctype="multipart/form-data" data-testid="edit-engg-form">
        <div class="photo-uploader" style="margin-bottom:14px">
            <?php $cur = engineer_photo($searchResult['enggid']); ?>
            <div class="preview" id="editEnggPreview">
                <?php if ($cur): ?>
                    <img src="<?= e($cur) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:6px">
                <?php else: ?>
                    <i class="fa-solid fa-user"></i>
                <?php endif; ?>
            </div>
            <div style="flex:1">
                <label style="font-size:11px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.4px">Replace Photo (optional)</label>
                <input type="file" name="photo" accept="image/jpeg,image/png" onchange="(function(i){var f=i.files&&i.files[0];if(!f)return;var r=new FileReader();r.onload=function(e){document.getElementById('editEnggPreview').innerHTML='<img src=\''+e.target.result+'\' style=width:100%;height:100%;object-fit:cover;border-radius:6px>';};r.readAsDataURL(f);})(this)">
                <div class="helper">Will be saved as <b>images/engineers/<?= e($searchResult['enggid']) ?>.JPG</b>.</div>
            </div>
        </div>
        <div class="form-grid">
            <div class="form-row"><label>Engineer Name</label>
                <input type="text" value="<?= e($searchResult['engg_name']) ?>" readonly>
            </div>
            <div class="form-row"><label>BEL Staff No</label>
                <input type="text" name="enggid" value="<?= e($searchResult['enggid']) ?>" readonly>
            </div>
            <div class="form-row"><label>Company Staff No</label>
                <input type="text" name="engg_staff_no" value="<?= e($searchResult['engg_staff_no']) ?>">
            </div>
            <div class="form-row"><label>Support Field <span class="req">*</span></label>
                <input type="text" name="support_field" value="<?= e($searchResult['support_field']) ?>" required>
            </div>
            <div class="form-row"><label>Joining Date <span class="req">*</span></label>
                <input type="date" name="joining_date" value="<?= e($searchResult['joining_date']) ?>" required>
            </div>
            <div class="form-row"><label>Left / End Date <span class="req">*</span></label>
                <input type="date" name="left_date" value="<?= e($searchResult['left_date']) ?>" required>
            </div>
        </div>
        <div class="flex-end mt-3">
            <button type="submit" name="sub" class="btn" data-testid="btn-edit-engg-save"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
        </div>
    </form>
</div>
<?php elseif (isset($_POST['search'])): ?>
    <div class="alert alert-warning"><i class="fa-solid fa-circle-info"></i> No matching engineer found.</div>
<?php endif; ?>
