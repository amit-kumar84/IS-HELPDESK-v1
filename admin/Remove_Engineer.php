<?php
/** Remove Engineer — admin only, modern UI, prepared statements */
if (isset($_POST['delete_engg'])) {
    $eid = trim($_POST['delete_engg']);
    $stmt = mysqli_prepare($link, "DELETE FROM s_engg_login WHERE enggid = ?");
    mysqli_stmt_bind_param($stmt, 's', $eid);
    if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
        flash_set('success', "Engineer $eid removed permanently.");
    } else {
        flash_set('danger', "Could not remove engineer $eid.");
    }
    header('Location: Admin_Home.php?AdminTab=RemoveEngineer');
    exit;
}

$rows = mysqli_query($link, "SELECT enggid, engg_name, support_field, company, joining_date, left_date, status, presence
                              FROM s_engg_login ORDER BY status ASC, engg_name ASC");
?>

<div class="page-head">
    <div class="ic" style="background:#fee2e2;color:#b91c1c"><i class="fa-solid fa-user-minus"></i></div>
    <div>
        <h2>Remove Engineer</h2>
        <div class="sub">Permanently delete an engineer record. Use <b>Deactivate</b> instead if you only want to mark them as left.</div>
    </div>
    <div class="actions">
        <a href="Admin_Home.php?AdminTab=ActDeactEngg" class="btn btn-sm btn-secondary"><i class="fa-solid fa-toggle-off"></i> Deactivate Instead</a>
    </div>
</div>

<div class="table-wrap">
    <table data-testid="remove-engg-table">
        <thead>
            <tr><th>#</th><th>Engineer</th><th>BEL ID</th><th>Support Field</th><th>Company</th><th>Joining</th><th>Status</th><th style="text-align:right">Action</th></tr>
        </thead>
        <tbody>
            <?php $i = 1; while ($r = mysqli_fetch_assoc($rows)): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= e($r['engg_name']) ?></td>
                    <td><?= e($r['enggid']) ?></td>
                    <td><?= e($r['support_field']) ?></td>
                    <td><?= e($r['company']) ?></td>
                    <td style="white-space:nowrap"><?= e($r['joining_date']) ?></td>
                    <td>
                        <?php if ($r['status'] === '1' || $r['status'] === 1): ?>
                            <span class="badge inactive">Inactive</span>
                        <?php elseif ($r['presence'] === 'A'): ?>
                            <span class="badge pending">Absent</span>
                        <?php else: ?>
                            <span class="badge active">Active</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:right">
                        <form method="post" style="display:inline" onsubmit="return confirm('Permanently delete engineer <?= e($r['engg_name']) ?> (<?= e($r['enggid']) ?>)?')">
                            <button type="submit" name="delete_engg" value="<?= e($r['enggid']) ?>" class="btn btn-sm btn-danger" data-testid="btn-remove-engg-<?= e($r['enggid']) ?>"><i class="fa-solid fa-trash"></i> Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
