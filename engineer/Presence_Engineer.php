<?php
/** Engineer presence — mark Present/Absent */
if (isset($_POST['toggle_presence'])) {
    $eid = trim($_POST['toggle_presence']);
    $new = $_POST['new_presence'] === 'A' ? 'A' : 'P';
    $stmt = mysqli_prepare($link, "UPDATE s_engg_login SET presence = ? WHERE enggid = ?");
    mysqli_stmt_bind_param($stmt, 'ss', $new, $eid);
    if (mysqli_stmt_execute($stmt)) {
        flash_set('success', "Engineer $eid marked " . ($new === 'P' ? 'Present' : 'Absent') . '.');
    } else {
        flash_set('danger', 'Could not update presence.');
    }
    header('Location: Admin_Home.php?AdminTab=P_Engineer');
    exit;
}

$rows = mysqli_query($link, "SELECT * FROM s_engg_login WHERE status='0' ORDER BY engg_name ASC");
?>

<div class="page-head">
    <div class="ic"><i class="fa-solid fa-user-check"></i></div>
    <div>
        <h2>Engineer Presence</h2>
        <div class="sub">Mark each engineer as Present or Absent for today.</div>
    </div>
</div>

<div class="table-wrap">
    <table>
        <thead><tr><th>#</th><th>Engineer</th><th>BEL ID</th><th>Support Field</th><th>Presence</th><th style="text-align:right">Toggle</th></tr></thead>
        <tbody>
        <?php $i = 1; while ($r = mysqli_fetch_assoc($rows)): $present = $r['presence'] === 'P'; ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= e($r['engg_name']) ?></td>
                <td><?= e($r['enggid']) ?></td>
                <td><?= e($r['support_field']) ?></td>
                <td><span class="badge <?= $present ? 'active' : 'inactive' ?>"><?= $present ? 'Present' : 'Absent' ?></span></td>
                <td style="text-align:right">
                    <form method="post" style="display:inline">
                        <input type="hidden" name="new_presence" value="<?= $present ? 'A' : 'P' ?>">
                        <button type="submit" name="toggle_presence" value="<?= e($r['enggid']) ?>" class="btn btn-sm <?= $present ? 'btn-warning' : 'btn-success' ?>" data-testid="btn-presence-<?= e($r['enggid']) ?>">
                            <i class="fa-solid <?= $present ? 'fa-user-slash' : 'fa-user-check' ?>"></i> Mark <?= $present ? 'Absent' : 'Present' ?>
                        </button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
