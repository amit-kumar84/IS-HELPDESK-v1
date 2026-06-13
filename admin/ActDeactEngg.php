<?php
/** Activate / Deactivate engineer */
if (isset($_POST['toggle'])) {
    $eid = trim($_POST['toggle']);
    $newStatus = $_POST['new'] === '1' ? '1' : '0';

    if ($newStatus === '1') {
        // deactivate
        $stmt = mysqli_prepare($link, "UPDATE s_engg_login SET status='1', presence='A', left_date=CURDATE() WHERE enggid=?");
    } else {
        // re-activate
        $stmt = mysqli_prepare($link, "UPDATE s_engg_login SET status='0', presence='P', joining_date=CURDATE(), left_date='9999-12-31' WHERE enggid=?");
    }
    mysqli_stmt_bind_param($stmt, 's', $eid);
    if (mysqli_stmt_execute($stmt)) {
        flash_set('success', "Engineer $eid " . ($newStatus === '1' ? 'deactivated' : 'reactivated') . '.');
    } else {
        flash_set('danger', 'Could not update engineer status.');
    }
    header('Location: Admin_Home.php?AdminTab=ActDeactEngg');
    exit;
}

$active   = mysqli_query($link, "SELECT * FROM s_engg_login WHERE status='0' ORDER BY joining_date ASC");
$inactive = mysqli_query($link, "SELECT * FROM s_engg_login WHERE status='1' ORDER BY left_date DESC");
?>

<div class="page-head">
    <div class="ic"><i class="fa-solid fa-toggle-on"></i></div>
    <div>
        <h2>Activate / Deactivate Engineer</h2>
        <div class="sub">Mark engineers as left or bring them back. Their login is enabled only when active.</div>
    </div>
</div>

<div class="card">
    <div class="card-title"><i class="fa-solid fa-circle-check" style="color:#16a34a"></i> Active Engineers (<?= mysqli_num_rows($active) ?>)</div>
    <div class="table-wrap" style="margin:0;border-radius:10px">
        <table>
            <thead><tr><th>#</th><th>Engineer</th><th>BEL ID</th><th>Staff No</th><th>Support Field</th><th>Joining</th><th>Presence</th><th style="text-align:right">Action</th></tr></thead>
            <tbody>
                <?php $i=1; while ($r = mysqli_fetch_assoc($active)): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= e($r['engg_name']) ?></td>
                        <td><?= e($r['enggid']) ?></td>
                        <td><?= e($r['engg_staff_no']) ?></td>
                        <td><?= e($r['support_field']) ?></td>
                        <td style="white-space:nowrap"><?= e($r['joining_date']) ?></td>
                        <td><span class="badge <?= $r['presence']==='P' ? 'active' : 'pending' ?>"><?= $r['presence']==='P' ? 'Present' : 'Absent' ?></span></td>
                        <td style="text-align:right">
                            <form method="post" style="display:inline" onsubmit="return confirm('Deactivate engineer <?= e($r['engg_name']) ?>?')">
                                <input type="hidden" name="new" value="1">
                                <button type="submit" name="toggle" value="<?= e($r['enggid']) ?>" class="btn btn-sm btn-danger" data-testid="btn-deact-<?= e($r['enggid']) ?>"><i class="fa-solid fa-toggle-off"></i> Deactivate</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-title"><i class="fa-solid fa-circle-xmark" style="color:#dc2626"></i> Inactive Engineers (<?= mysqli_num_rows($inactive) ?>)</div>
    <div class="table-wrap" style="margin:0;border-radius:10px">
        <table>
            <thead><tr><th>#</th><th>Engineer</th><th>BEL ID</th><th>Staff No</th><th>Support Field</th><th>Joining</th><th>Left</th><th style="text-align:right">Action</th></tr></thead>
            <tbody>
                <?php $i=1; while ($r = mysqli_fetch_assoc($inactive)): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= e($r['engg_name']) ?></td>
                        <td><?= e($r['enggid']) ?></td>
                        <td><?= e($r['engg_staff_no']) ?></td>
                        <td><?= e($r['support_field']) ?></td>
                        <td style="white-space:nowrap"><?= e($r['joining_date']) ?></td>
                        <td style="white-space:nowrap"><?= e($r['left_date']) ?></td>
                        <td style="text-align:right">
                            <form method="post" style="display:inline" onsubmit="return confirm('Re-activate <?= e($r['engg_name']) ?>?')">
                                <input type="hidden" name="new" value="0">
                                <button type="submit" name="toggle" value="<?= e($r['enggid']) ?>" class="btn btn-sm btn-success" data-testid="btn-act-<?= e($r['enggid']) ?>"><i class="fa-solid fa-toggle-on"></i> Activate</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
