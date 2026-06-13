<?php
/** Engineer dashboard widget */
require_once 'includes/photo.php';

// Floating Live Helpdesk Banner (top of dashboard)
include 'includes/floating_banner.php';

$counts = [
    'pending'   => (int) mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(*) FROM complain_register WHERE status='Pending'"))[0],
    'attend'    => (int) mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(*) FROM complain_register WHERE status='Attend'"))[0],
    'solved'    => (int) mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(*) FROM complain_register WHERE status='Solved'"))[0],
    'cart_pend' => (int) mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(*) FROM request_master WHERE Status='Pending'"))[0],
];

// My attended
$mineStmt = mysqli_prepare($link, "SELECT COUNT(*) FROM complain_register WHERE support_engg LIKE CONCAT('%', ?, '%')");
mysqli_stmt_bind_param($mineStmt, 's', $userName);
mysqli_stmt_execute($mineStmt);
$mineCount = (int) mysqli_fetch_array(mysqli_stmt_get_result($mineStmt))[0];

$recent = mysqli_query($link, "SELECT t_no, Staff_no, user_name, dept, sec, problem_on, problem, status, r_DateTime
                               FROM complain_register
                               WHERE status IN ('Pending','Attend')
                               ORDER BY t_no DESC LIMIT 8");
?>

<div class="page-head">
    <div class="ic"><i class="fa-solid fa-user-gear"></i></div>
    <div>
        <h2>Hello, <?= e($userName) ?></h2>
        <div class="sub">Here are the tickets that need your attention right now.</div>
    </div>
    <div class="actions">
        <a href="Engineer_home.php?EngineerTab=CallGenerate" class="btn btn-sm"><i class="fa-solid fa-plus"></i> New Call</a>
        <a href="Engineer_home.php?EngineerTab=Pending_Calls" class="btn btn-sm btn-secondary"><i class="fa-solid fa-list"></i> Open Queue</a>
    </div>
</div>

<div class="stat-grid">
    <div class="stat warn"><div class="ic"><i class="fa-solid fa-hourglass-half"></i></div><div class="label">Unassigned</div><div class="value"><?= $counts['pending'] ?></div><div class="delta">Awaiting pickup</div></div>
    <div class="stat brand"><div class="ic"><i class="fa-solid fa-spinner"></i></div><div class="label">In Progress</div><div class="value"><?= $counts['attend'] ?></div><div class="delta">Being worked on</div></div>
    <div class="stat ok"><div class="ic"><i class="fa-solid fa-circle-check"></i></div><div class="label">Solved</div><div class="value"><?= $counts['solved'] ?></div><div class="delta">Awaiting close</div></div>
    <div class="stat brand"><div class="ic"><i class="fa-solid fa-clipboard-user"></i></div><div class="label">Handled by Me</div><div class="value"><?= $mineCount ?></div><div class="delta">All-time</div></div>
</div>

<div class="card">
    <div class="card-title"><i class="fa-solid fa-clock-rotate-left"></i> Open Queue (Pending + In Progress)
        <span style="margin-left:auto"><a class="btn btn-sm btn-secondary" href="Engineer_home.php?EngineerTab=All_Calls">View All</a></span>
    </div>
    <div class="table-wrap" style="margin:0;border-radius:10px">
        <table>
            <thead><tr><th>Ticket</th><th style="width:40px"></th><th>User</th><th>Dept</th><th>Issue</th><th>Status</th><th>Raised</th><th style="text-align:right">Action</th></tr></thead>
            <tbody>
            <?php while ($r = mysqli_fetch_assoc($recent)):
                $s = strtolower($r['status']);
                $cls = $s === 'pending' ? 'pending' : 'attend';
                $raised = $r['r_DateTime'];
                $rt = is_numeric($raised) ? (int)$raised : strtotime($raised);
            ?>
                <tr>
                    <td><b><?= e($r['t_no']) ?></b></td>
                    <td><?= render_avatar($r['Staff_no'], $r['user_name'], 28) ?></td>
                    <td><?= e($r['user_name']) ?></td>
                    <td><?= e($r['dept']) ?></td>
                    <td style="max-width:260px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= e($r['problem_on'] . ' — ' . $r['problem']) ?></td>
                    <td><span class="badge <?= $cls ?>"><?= e($r['status']) ?></span></td>
                    <td style="white-space:nowrap;font-size:11px"><?= e($rt ? date('d M, H:i', $rt) : $raised) ?></td>
                    <td style="text-align:right;white-space:nowrap">
                        <?php if ($r['status'] === 'Pending'): ?>
                            <form method="post" action="includes/ticket_action.php" style="display:inline">
                                <input type="hidden" name="t_no" value="<?= e($r['t_no']) ?>"><input type="hidden" name="action" value="attend">
                                <button class="btn btn-xs btn-warning" data-testid="act-attend-<?= e($r['t_no']) ?>"><i class="fa-solid fa-play"></i> Attend</button>
                            </form>
                        <?php else: ?>
                            <a class="btn btn-xs btn-secondary" href="Engineer_home.php?EngineerTab=All_Calls&status=attend">View</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
