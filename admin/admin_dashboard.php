<?php
/**
 * Admin Dashboard - Live stats overview
 */
require_once 'includes/photo.php';

// (Live Helpdesk Banner is intentionally NOT included here any more —
// admins open it as a separate resizable window via the sidebar
// "Open Live Board" launcher.)

$counts = [
    'tickets'   => (int) mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(*) FROM complain_register"))[0],
    'pending'   => (int) mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(*) FROM complain_register WHERE status='Pending'"))[0],
    'attend'    => (int) mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(*) FROM complain_register WHERE status='Attend'"))[0],
    'solved'    => (int) mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(*) FROM complain_register WHERE status='Solved'"))[0],
    'closed'    => (int) mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(*) FROM complain_register WHERE status='Closed'"))[0],
    'employees' => (int) mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(*) FROM emp_details"))[0],
    'engineers_active' => (int) mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(*) FROM s_engg_login WHERE status='0'"))[0],
    'engineers_total'  => (int) mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(*) FROM s_engg_login"))[0],
    'cartridge_pending'=> (int) mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(*) FROM request_master WHERE Status='Pending'"))[0],
    'hardware'  => (int) mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(*) FROM hardware_master"))[0],
];

$recent = mysqli_query($link, "SELECT t_no, Staff_no, user_name, dept, sec, problem_on, problem, status, r_DateTime
                               FROM complain_register WHERE status<>'Closed'
                               ORDER BY t_no DESC LIMIT 8");

$engineers = mysqli_query($link, "SELECT engg_name, enggid, support_field, presence, status FROM s_engg_login WHERE status='0' ORDER BY engg_name LIMIT 8");
?>

<div class="page-head">
    <div class="ic"><i class="fa-solid fa-gauge-high"></i></div>
    <div>
        <h2>Welcome back, <?= e($userName) ?></h2>
        <div class="sub">Real-time pulse of BEL Kotdwar IT Service Desk &middot; Jurisdiction: <b>Administrator</b></div>
    </div>
    <div class="actions">
        <a href="Admin_Home.php?AdminTab=CallGenerateByAdmin" class="btn btn-sm"><i class="fa-solid fa-plus"></i> New Ticket</a>
        <a href="Admin_Home.php?AdminTab=AddNewUser" class="btn btn-sm btn-secondary"><i class="fa-solid fa-user-plus"></i> Add User</a>
        <a href="Admin_Home.php?AdminTab=AddEngineer" class="btn btn-sm btn-secondary"><i class="fa-solid fa-user-gear"></i> Add Engineer</a>
    </div>
</div>

<div class="stat-grid">
    <div class="stat brand"><div class="ic"><i class="fa-solid fa-ticket"></i></div><div class="label">Total Tickets</div><div class="value"><?= number_format($counts['tickets']) ?></div><div class="delta">All-time records</div></div>
    <div class="stat warn"><div class="ic"><i class="fa-solid fa-hourglass-half"></i></div><div class="label">Unassigned</div><div class="value"><?= $counts['pending'] ?></div><div class="delta">Awaiting engineer</div></div>
    <div class="stat brand"><div class="ic"><i class="fa-solid fa-spinner"></i></div><div class="label">In Progress</div><div class="value"><?= $counts['attend'] ?></div><div class="delta">Being worked on</div></div>
    <div class="stat ok"><div class="ic"><i class="fa-solid fa-circle-check"></i></div><div class="label">Solved</div><div class="value"><?= $counts['solved'] ?></div><div class="delta">Awaiting close</div></div>
    <div class="stat brand"><div class="ic"><i class="fa-solid fa-users"></i></div><div class="label">Employees</div><div class="value"><?= number_format($counts['employees']) ?></div><div class="delta">Registered users</div></div>
    <div class="stat ok"><div class="ic"><i class="fa-solid fa-user-gear"></i></div><div class="label">Active Engineers</div><div class="value"><?= $counts['engineers_active'] ?> <small style="font-size:12px;color:#94a3b8">/ <?= $counts['engineers_total'] ?></small></div><div class="delta">Currently working</div></div>
    <div class="stat warn"><div class="ic"><i class="fa-solid fa-print"></i></div><div class="label">Cartridge Reqs.</div><div class="value"><?= $counts['cartridge_pending'] ?></div><div class="delta">Pending approval</div></div>
    <div class="stat danger"><div class="ic"><i class="fa-solid fa-server"></i></div><div class="label">Hardware Assets</div><div class="value"><?= number_format($counts['hardware']) ?></div><div class="delta">Tracked</div></div>
</div>

<div style="display:grid;grid-template-columns:1.5fr 1fr;gap:16px;align-items:start">
    <div class="card">
        <div class="card-title"><i class="fa-solid fa-clock-rotate-left"></i> Recent Open Tickets <span style="margin-left:auto"><a class="btn btn-sm btn-secondary" href="Admin_Home.php?AdminTab=All_Calls">View All</a></span></div>
        <div class="table-wrap" style="margin:0;border-radius:8px">
        <table>
            <thead><tr><th>Ticket</th><th style="width:40px"></th><th>User</th><th>Dept</th><th>Issue</th><th>Status</th></tr></thead>
            <tbody>
            <?php while ($r = mysqli_fetch_assoc($recent)):
                $s = strtolower($r['status']);
                $cls = $s === 'pending' ? 'pending' : ($s === 'attend' ? 'attend' : ($s === 'solved' ? 'solved' : 'closed'));
            ?>
                <tr>
                    <td><b><?= e($r['t_no']) ?></b></td>
                    <td><?= render_avatar($r['Staff_no'], $r['user_name'], 28) ?></td>
                    <td><?= e($r['user_name']) ?></td>
                    <td><?= e($r['dept']) ?></td>
                    <td style="max-width:260px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= e($r['problem_on'] . ' — ' . $r['problem']) ?></td>
                    <td><span class="badge <?= $cls ?>"><?= e($r['status']) ?></span></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>
    </div>

    <div class="card">
        <div class="card-title"><i class="fa-solid fa-user-check"></i> Active Engineers <span style="margin-left:auto"><a class="btn btn-sm btn-secondary" href="Admin_Home.php?AdminTab=EngineerList">All</a></span></div>
        <div style="display:flex;flex-direction:column;gap:8px">
        <?php while ($eng = mysqli_fetch_assoc($engineers)):
            $present = $eng['presence'] === 'P';
        ?>
            <div style="display:flex;align-items:center;gap:10px;padding:8px;border:1px solid var(--c-border);border-radius:8px;background:#fff">
                <?= render_avatar($eng['enggid'], $eng['engg_name'], 34, 'images/engineers') ?>
                <div style="flex:1;min-width:0">
                    <div style="font-weight:600;font-size:12.5px;color:#0a1f44"><?= e($eng['engg_name']) ?></div>
                    <div style="font-size:11px;color:var(--c-text-2)"><?= e($eng['support_field'] . ' · #' . $eng['enggid']) ?></div>
                </div>
                <span class="badge <?= $present ? 'active' : 'inactive' ?>"><?= $present ? 'Present' : 'Absent' ?></span>
            </div>
        <?php endwhile; ?>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-title"><i class="fa-solid fa-bolt"></i> Quick Actions</div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:8px">
        <a class="btn btn-secondary" href="Admin_Home.php?AdminTab=AddNewUser"><i class="fa-solid fa-user-plus"></i> Add New User</a>
        <a class="btn btn-secondary" href="Admin_Home.php?AdminTab=AddEngineer"><i class="fa-solid fa-user-gear"></i> Add Engineer</a>
        <a class="btn btn-secondary" href="Admin_Home.php?AdminTab=ManageUsers"><i class="fa-solid fa-users-gear"></i> Manage Users</a>
        <a class="btn btn-secondary" href="Admin_Home.php?AdminTab=BulkImport"><i class="fa-solid fa-file-import"></i> Bulk Import</a>
        <a class="btn btn-secondary" href="Admin_Home.php?AdminTab=Suggestions"><i class="fa-solid fa-lightbulb"></i> Suggestions</a>
        <a class="btn btn-secondary" href="Admin_Home.php?AdminTab=NewEntry"><i class="fa-solid fa-server"></i> Add Hardware</a>
        <a class="btn btn-secondary" href="Admin_Home.php?AdminTab=CallReport"><i class="fa-solid fa-chart-line"></i> Reports</a>
    </div>
</div>

<?php $hide_suggestion = true; include 'includes/info_widgets.php'; ?>
