<?php
/** All Calls / Tickets — Excel-style with photo, status workflow */
require_once 'includes/photo.php';

/* Determine if we're inside a status-locked wrapper page (Pending_Calls,
 * Attend_Calls, Solved_Calls, Closed_Calls). In that mode we hide the
 * inline status tabs and show only the search box, since the sidebar
 * already provides the segmentation. */
$lockedTab = $_GET['AdminTab']    ?? $_GET['EngineerTab'] ?? '';
$isLocked  = in_array($lockedTab, ['Pending_Calls','Attend_Calls','Solved_Calls','Closed_Calls'], true);

$status_filter = $_GET['status'] ?? 'open';

$counts = mysqli_fetch_assoc(mysqli_query($link, "SELECT
    COUNT(*) total,
    SUM(status='Pending') pending,
    SUM(status='Attend')  attend,
    SUM(status='Solved')  solved,
    SUM(status='Closed')  closed
    FROM complain_register"));

$where = '1=1';
if      ($status_filter === 'pending') $where = "status='Pending'";
elseif  ($status_filter === 'attend')  $where = "status='Attend'";
elseif  ($status_filter === 'solved')  $where = "status='Solved'";
elseif  ($status_filter === 'closed')  $where = "status='Closed'";
elseif  ($status_filter === 'open')    $where = "status IN ('Pending','Attend','Solved')";

$q = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['p'] ?? 1));
$per = 50; $off = ($page - 1) * $per;

if ($q !== '') {
    $stmt = mysqli_prepare($link, "SELECT t_no, r_DateTime, dept, sec, user_name, Staff_no, phone_no, pc_no, printer, problem_type, problem, support_engg, solution, s_DateTime, status
        FROM complain_register WHERE $where
        AND (t_no LIKE ? OR user_name LIKE ? OR Staff_no LIKE ? OR pc_no LIKE ? OR problem LIKE ?)
        ORDER BY substring(t_no,1,6) DESC, substring(t_no,8,12) DESC
        LIMIT ? OFFSET ?");
    $like = "%$q%";
    mysqli_stmt_bind_param($stmt, 'sssssii', $like, $like, $like, $like, $like, $per, $off);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    $cstmt = mysqli_prepare($link, "SELECT COUNT(*) FROM complain_register WHERE $where
        AND (t_no LIKE ? OR user_name LIKE ? OR Staff_no LIKE ? OR pc_no LIKE ? OR problem LIKE ?)");
    mysqli_stmt_bind_param($cstmt, 'sssss', $like, $like, $like, $like, $like);
    mysqli_stmt_execute($cstmt);
    $totalFiltered = (int) mysqli_fetch_array(mysqli_stmt_get_result($cstmt))[0];
} else {
    $stmt = mysqli_prepare($link, "SELECT t_no, r_DateTime, dept, sec, user_name, Staff_no, phone_no, pc_no, printer, problem_type, problem, support_engg, solution, s_DateTime, status
        FROM complain_register WHERE $where
        ORDER BY substring(t_no,1,6) DESC, substring(t_no,8,12) DESC
        LIMIT ? OFFSET ?");
    mysqli_stmt_bind_param($stmt, 'ii', $per, $off);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    $totalFiltered = (int) mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(*) FROM complain_register WHERE $where"))[0];
}
$pages = max(1, (int) ceil($totalFiltered / $per));

$panel = $_GET['AdminTab'] ?? null;
$base = $panel ? 'Admin_Home.php?AdminTab=' . urlencode($panel) : 'Engineer_home.php?EngineerTab=' . urlencode($_GET['EngineerTab'] ?? 'All_Calls');

$canAct   = in_array(current_role(), ['ISKotAdmin','Admin','Engineer'], true);
$isAdmin  = in_array(current_role(), ['ISKotAdmin','Admin'], true);

/**
 * Render comma-separated engineer names as styled badge chips.
 */
function render_engg_badges(string $names): string {
    $parts = explode(',', $names);
    $out = '<div class="eng-badges">';
    foreach ($parts as $p) {
        $p = trim($p);
        if ($p === '') continue;
        $out .= '<span class="eng-badge">' . e($p) . '</span>';
    }
    return $out . '</div>';
}

// Pre-load engineer list once for the Assign-To dropdown (admin only)
$engineers_list = [];
if ($isAdmin) {
    $eq = mysqli_query($link, "SELECT engg_name, support_field FROM s_engg_login WHERE status=0 ORDER BY engg_name ASC");
    while ($eq && ($er = mysqli_fetch_assoc($eq))) $engineers_list[] = $er;
}
?>

<div class="page-head">
    <div class="ic"><i class="fa-solid fa-ticket"></i></div>
    <div>
        <h2><?php
            $heading_map = [
                'pending' => 'Unassigned Tickets',
                'attend'  => 'In Progress Tickets',
                'solved'  => 'Solved Tickets',
                'closed'  => 'Closed Tickets',
                'open'    => 'Open Tickets',
                'all'     => 'All Tickets',
            ];
            echo e($heading_map[$status_filter] ?? 'Tickets');
        ?></h2>
        <div class="sub">
            <?php if ($status_filter === 'pending'): ?>Tickets that no engineer has accepted yet — assign or attend them.
            <?php elseif ($status_filter === 'attend'): ?>Tickets currently being worked on (not solved yet).
            <?php elseif ($status_filter === 'solved'): ?>Tickets marked solved by engineer, awaiting closure.
            <?php elseif ($status_filter === 'closed'): ?>Archive of closed tickets.
            <?php else: ?>Browse, filter and take action on tickets.
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="card" style="padding:12px 16px">
    <div class="flex" style="gap:10px;flex-wrap:wrap;align-items:center">
        <?php if (!$isLocked): ?>
        <div class="flex" style="gap:5px;flex-wrap:wrap">
            <?php
            $tabs = ['open' => 'Open', 'pending' => 'Pending ('.$counts['pending'].')', 'attend' => 'In Progress ('.$counts['attend'].')', 'solved' => 'Solved ('.$counts['solved'].')', 'closed' => 'Closed', 'all' => 'All ('.$counts['total'].')'];
            foreach ($tabs as $k => $label):
                $active = $status_filter === $k ? '' : 'btn-secondary';
            ?>
                <a class="btn btn-sm <?= $active ?>" href="<?= $base ?>&status=<?= $k ?>"><?= e($label) ?></a>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div style="font-size:13px;color:#475569">
            <i class="fa-solid fa-filter" style="color:#1e3a8a"></i>
            Showing <b style="color:#0a1f44"><?= e($heading_map[$status_filter] ?? 'Tickets') ?></b>
            &middot; <span style="color:#64748b"><?= number_format($totalFiltered) ?> result<?= $totalFiltered === 1 ? '' : 's' ?></span>
        </div>
        <?php endif; ?>
        <?php if ($isAdmin): ?>
            <button type="button" class="btn btn-sm btn-primary" id="assignSelectedBtn" disabled style="margin-left:auto" title="Assign selected tickets to engineer"><i class="fa-solid fa-user-tie"></i> Assign Engineer</button>
        <?php endif; ?>
        <form method="get" class="flex" style="gap:6px;margin-left:auto">
            <?php foreach ($_GET as $k => $v): if (in_array($k, ['q','p'])) continue; ?>
                <input type="hidden" name="<?= e($k) ?>" value="<?= e($v) ?>">
            <?php endforeach; ?>
            <input type="search" name="q" value="<?= e($q) ?>" placeholder="Search ticket #, name, asset, problem…" style="width:300px" data-testid="tickets-search">
            <?php if ($q !== ''): ?>
                <a class="btn btn-sm btn-secondary" href="<?= $base ?>&status=<?= e($status_filter) ?>" title="Clear search"><i class="fa-solid fa-xmark"></i></a>
            <?php endif; ?>
            <button class="btn btn-sm" type="submit" data-testid="tickets-search-btn"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
        </form>
    </div>
</div>

<div class="table-wrap">
    <table>
        <thead><tr>
            <?php if ($isAdmin): ?><th style="width:40px;text-align:center"><input type="checkbox" id="selectAllTickets" title="Select all visible tickets"></th><?php endif; ?>
            <th>Ticket #</th><th style="width:46px">Photo</th><th>User</th><th>Dept (Sec)</th>
            <th>Phone</th><th>Asset</th><th>Printer</th><th>Category</th>
            <th>Problem</th><th>Engineer</th><th>Solution</th><th>Raised</th><th>Updated</th><th>Status</th>
            <?php if ($canAct): ?><th style="min-width:200px;text-align:center">Action</th><?php endif; ?>
        </tr></thead>
        <tbody>
        <?php while ($r = mysqli_fetch_assoc($rows)):
            $s = strtolower($r['status']);
            $cls = $s === 'pending' ? 'pending' : ($s === 'attend' ? 'attend' : ($s === 'solved' ? 'solved' : 'closed'));
        ?>
            <tr>
                <?php if ($isAdmin): ?>
                    <td style="text-align:center">
                        <input type="checkbox" class="ticket-checkbox" value="<?= e($r['t_no']) ?>" aria-label="Select ticket <?= e($r['t_no']) ?>" <?= in_array($r['status'], ['Solved','Closed'], true) ? 'disabled title="Cannot select solved or closed ticket"' : '' ?>>
                    </td>
                <?php endif; ?>
                <td><b style="color:#0a1f44"><?= e($r['t_no']) ?></b></td>
                <td><?= render_avatar($r['Staff_no'], $r['user_name'], 30) ?></td>
                <td><?= e($r['user_name']) ?><br><small style="color:#94a3b8;font-size:11px"><?= e($r['Staff_no']) ?></small></td>
                <td><?= e($r['dept'] . ' / ' . $r['sec']) ?></td>
                <td style="font-variant-numeric:tabular-nums;font-size:11.5px"><?= e($r['phone_no']) ?></td>
                <td><?= e($r['pc_no']) ?></td>
                <td><?= e($r['printer']) ?></td>
                <td><?= e($r['problem_type']) ?></td>
                <td style="max-width:340px;min-width:240px;font-size:12.5px;font-weight:500;color:#0a1f44;white-space:normal;word-break:break-word;line-height:1.4"><?= e($r['problem']) ?></td>
                <td><?= e($r['support_engg']) ? render_engg_badges($r['support_engg']) : '<span style="color:#94a3b8;font-size:12px">—</span>' ?></td>
                <td style="max-width:180px;font-size:12px"><?= e($r['solution']) ?></td>
                <td style="white-space:nowrap;font-variant-numeric:tabular-nums;font-size:11px"><?= e($r['r_DateTime']) ?></td>
                <td style="white-space:nowrap;font-variant-numeric:tabular-nums;font-size:11px"><?= e($r['s_DateTime']) ?></td>
                <td>
                    <span class="status-badge status-<?= $cls ?>" title="<?= e($r['status']) ?>">
                        <?php if ($cls === 'pending'): ?><i class="fa-solid fa-hourglass-half"></i><?php endif; ?>
                        <?php if ($cls === 'attend'): ?><i class="fa-solid fa-spinner"></i><?php endif; ?>
                        <?php if ($cls === 'solved'): ?><i class="fa-solid fa-check-circle"></i><?php endif; ?>
                        <?php if ($cls === 'closed'): ?><i class="fa-solid fa-lock"></i><?php endif; ?>
                        <?= e($r['status']) ?>
                    </span>
                </td>
                <?php if ($canAct): ?>
                <td style="min-width:200px;padding:8px !important">
                    <div style="display:flex;flex-direction:column;gap:6px">
                        <?php if ($r['status'] === 'Pending'): ?>
                            <form method="post" action="includes/ticket_action.php" style="width:100%">
                                <input type="hidden" name="t_no" value="<?= e($r['t_no']) ?>"><input type="hidden" name="action" value="attend">
                                <button class="btn btn-xs btn-warning" title="Mark In Progress" data-testid="act-attend-<?= e($r['t_no']) ?>" style="width:100%"><i class="fa-solid fa-play"></i> Attend</button>
                            </form>
                        <?php endif; ?>
                        <?php if ($r['status'] === 'Attend'): ?>
                            <button class="btn btn-xs btn-success" title="Mark Solved" onclick="solveTicket('<?= e($r['t_no']) ?>')" data-testid="act-solve-<?= e($r['t_no']) ?>" style="width:100%"><i class="fa-solid fa-check"></i> Solve</button>
                        <?php endif; ?>
                        <?php if ($r['status'] === 'Solved'): ?>
                            <form method="post" action="includes/ticket_action.php" style="width:100%" onsubmit="return confirm('Close ticket <?= e($r['t_no']) ?>?')">
                                <input type="hidden" name="t_no" value="<?= e($r['t_no']) ?>"><input type="hidden" name="action" value="close">
                                <button class="btn btn-xs btn-navy" title="Close ticket" data-testid="act-close-<?= e($r['t_no']) ?>" style="width:100%"><i class="fa-solid fa-lock"></i> Close</button>
                            </form>
                        <?php endif; ?>
                        <?php if ($r['status'] === 'Closed'): ?>
                            <form method="post" action="includes/ticket_action.php" style="width:100%" onsubmit="return confirm('Re-open <?= e($r['t_no']) ?>?')">
                                <input type="hidden" name="t_no" value="<?= e($r['t_no']) ?>"><input type="hidden" name="action" value="reopen">
                                <button class="btn btn-xs btn-secondary" title="Re-open" style="width:100%"><i class="fa-solid fa-rotate-left"></i> Re-open</button>
                            </form>
                        <?php endif; ?>

                        <?php if ($isAdmin): ?>
                            <!-- Admin-only: Assign/reassign engineer (pending tickets only) -->
                            <?php if ($r['status'] === 'Pending'): ?>
                                <button type="button" class="btn btn-xs btn-accent" title="Assign/reassign engineer"
                                    data-assign-tno="<?= e($r['t_no']) ?>"
                                    data-assign-engg="<?= e($r['support_engg']) ?>"
                                    data-assign-status="<?= e($r['status']) ?>"
                                    data-testid="act-admin-<?= e($r['t_no']) ?>"
                                    style="width:100%">
                                    <i class="fa-solid fa-user-tie"></i> Assign
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </td>
                <?php endif; ?>
            </tr>
        <?php endwhile; ?>
        <?php if (mysqli_num_rows($rows) === 0): ?>
            <tr><td colspan="<?= $canAct ? 16 : 15 ?>" style="text-align:center;padding:30px;color:var(--c-text-2)">No tickets match the filter.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
/* ==============================
   Engineer name badges
   ============================== */
.eng-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
}
.eng-badge {
    display: inline-block;
    padding: 3px 9px;
    border-radius: 6px;
    font-size: 11.5px;
    font-weight: 600;
    color: #0a1f44;
    background: #e0e7ff;
    border: 1px solid #c7d2fe;
    white-space: nowrap;
    line-height: 1.5;
}

/* ==============================
   Status badges
   ============================== */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    white-space: nowrap;
    box-shadow: 0 4px 12px -4px rgba(0,0,0,0.2);
    transition: all 0.2s ease;
}
.status-badge:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px -4px rgba(0,0,0,0.3);
}

.status-pending {
    background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
    color: #78350f;
    border: 1px solid rgba(217, 119, 6, 0.3);
    animation: pulse-pending 2s infinite;
}
@keyframes pulse-pending {
    0%, 100% { box-shadow: 0 4px 12px -4px rgba(245, 158, 11, 0.4); }
    50% { box-shadow: 0 8px 20px -4px rgba(245, 158, 11, 0.6); }
}

.status-attend {
    background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
    color: #fff;
    border: 1px solid rgba(139, 92, 246, 0.3);
    animation: spin-attend 3s linear infinite;
}
@keyframes spin-attend {
    0% { filter: drop-shadow(0 0 0px rgba(139, 92, 246, 0.5)); }
    50% { filter: drop-shadow(0 0 8px rgba(139, 92, 246, 0.8)); }
    100% { filter: drop-shadow(0 0 0px rgba(139, 92, 246, 0.5)); }
}

.status-solved {
    background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
    color: #fff;
    border: 1px solid rgba(16, 185, 129, 0.3);
    animation: glow-solved 2s ease-in-out infinite;
}
@keyframes glow-solved {
    0%, 100% { box-shadow: 0 4px 12px -4px rgba(16, 185, 129, 0.4); }
    50% { box-shadow: 0 6px 16px -2px rgba(16, 185, 129, 0.6); }
}

.status-closed {
    background: linear-gradient(135deg, #6b7280 0%, #9ca3af 100%);
    color: #fff;
    border: 1px solid rgba(107, 114, 128, 0.3);
}

/* ==============================
   Solve Modal
   ============================== */
#solveModal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15,23,42,.55);
    z-index: 60;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
#solveModal .smodal-inner {
    background: #fff;
    border-radius: 16px;
    width: 100%;
    max-width: 520px;
    padding: 24px 26px;
    box-shadow: 0 30px 70px -18px rgba(15,23,42,.5);
    position: relative;
    overflow: hidden;
}
#solveModal .smodal-inner .smodal-accent {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 6px;
    background: linear-gradient(180deg,#10b981,#34d399);
}
#solveModal .smodal-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 10px;
}
#solveModal .smodal-header .smodal-icon {
    width: 42px;
    height: 42px;
    border-radius: 11px;
    background: linear-gradient(135deg,#059669,#10b981);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}
#solveModal .smodal-header h3 {
    margin: 0;
    color: #0a1f44;
    font-size: 17px;
    font-weight: 800;
}
#solveModal .smodal-header h3 b {
    color: #059669;
}
#solveModal .smodal-header .smodal-close {
    margin-left: auto;
    background: #f1f5f9;
    border: 0;
    width: 34px;
    height: 34px;
    border-radius: 8px;
    color: #0a1f44;
    font-size: 14px;
    cursor: pointer;
}
#solveModal .smodal-body textarea {
    width: 100%;
    padding: 12px 14px;
    border: 1.5px solid #d1d5db;
    border-radius: 10px;
    background: #f9fafb;
    font-size: 13.5px;
    resize: vertical;
    min-height: 100px;
    font-family: inherit;
    line-height: 1.5;
    transition: border-color .15s;
    box-sizing: border-box;
}
#solveModal .smodal-body textarea:focus {
    outline: none;
    border-color: #10b981;
    background: #fff;
}
#solveModal .smodal-footer {
    display: flex;
    gap: 8px;
    justify-content: flex-end;
    margin-top: 14px;
}
</style>

<!-- Solve Modal -->
<div id="solveModal">
    <div class="smodal-inner">
        <div class="smodal-accent"></div>
        <form method="post" action="includes/ticket_action.php">
            <input type="hidden" name="action" value="solve">
            <input type="hidden" name="t_no" id="solveTnoInput" value="">
            <div class="smodal-header">
                <div class="smodal-icon"><i class="fa-solid fa-check"></i></div>
                <div>
                    <h3>Mark Solved — Ticket <b id="solveTnoLabel"></b></h3>
                    <p style="margin:3px 0 0;color:#475569;font-size:12px">Describe the solution applied to resolve this ticket.</p>
                </div>
                <button type="button" class="smodal-close" onclick="closeSolveModal()"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="smodal-body">
                <textarea name="solution" placeholder="Describe what was done to resolve the issue…" required></textarea>
            </div>
            <div class="smodal-footer">
                <button type="button" class="btn btn-sm btn-secondary" onclick="closeSolveModal()">Cancel</button>
                <button type="submit" class="btn btn-sm btn-success"><i class="fa-solid fa-check-circle"></i> Mark Solved</button>
            </div>
        </form>
    </div>
</div>

<script>
function solveTicket(t) {
    document.getElementById('solveTnoLabel').textContent = t;
    document.getElementById('solveTnoInput').value = t;
    document.getElementById('solveModal').style.display = 'flex';
}
function closeSolveModal() {
    document.getElementById('solveModal').style.display = 'none';
}

<?php if ($isAdmin): ?>
function openAssign(ticketIds, currentEnggStr){
    var ids = Array.isArray(ticketIds) ? ticketIds : [ticketIds];
    var selectedText = ids.length === 1 ? ids[0] : ids.length + ' tickets';
    document.getElementById('admModalTno').textContent = selectedText;
    document.getElementById('admSelectedTickets').textContent = 'Selected ticket(s): ' + ids.join(', ');
    var ticketInputs = document.getElementById('admTicketInputs');
    ticketInputs.innerHTML = '';
    ids.forEach(function(id){
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 't_no[]';
        input.value = id;
        ticketInputs.appendChild(input);
    });
    // Pre-check engineers that are currently assigned (comma-separated)
    var currentEnggs = (currentEnggStr || '').split(',').map(function(e){ return e.trim(); }).filter(function(e){ return e !== ''; });
    var chk = document.querySelectorAll('.adm-engg-checkbox');
    chk.forEach(function(c){
        c.checked = currentEnggs.indexOf(c.value) !== -1;
    });
    document.getElementById('admEnggMsg').textContent = '';
    document.getElementById('adminModal').style.display = 'flex';
}
function closeAdminModal(){ document.getElementById('adminModal').style.display = 'none'; }

function validateAssignSelection(){
    var checked = document.querySelectorAll('.adm-engg-checkbox:checked');
    var count = checked.length;
    var msgEl = document.getElementById('admEnggMsg');
    if (count < 1) {
        msgEl.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i> Select at least 1 engineer.';
        return false;
    }
    if (count > 5) {
        msgEl.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i> Maximum 5 engineers allowed.';
        return false;
    }
    msgEl.textContent = '';
    return true;
}

(function(){
    var checkboxes = Array.from(document.querySelectorAll('.ticket-checkbox'));
    var selectAll = document.getElementById('selectAllTickets');
    var assignBtn = document.getElementById('assignSelectedBtn');

    function updateAssignButton(){
        var selected = checkboxes.filter(function(ch){ return ch.checked; }).map(function(ch){ return ch.value; });
        assignBtn.disabled = selected.length === 0;
        assignBtn.dataset.tickets = selected.join(',');
    }

    if (selectAll){
        selectAll.addEventListener('change', function(){
            checkboxes.forEach(function(ch){ ch.checked = selectAll.checked; });
            updateAssignButton();
        });
    }
    checkboxes.forEach(function(ch){
        ch.addEventListener('change', function(){
            if (!this.checked && selectAll) selectAll.checked = false;
            updateAssignButton();
        });
    });

    assignBtn && assignBtn.addEventListener('click', function(){
        var selected = checkboxes.filter(function(ch){ return ch.checked && !ch.disabled; }).map(function(ch){ return ch.value; });
        if (selected.length === 0) return;
        openAssign(selected, '');
    });

    document.querySelectorAll('[data-assign-tno]').forEach(function(btn){
        if (btn.disabled) return;
        btn.addEventListener('click', function(){
            openAssign(btn.dataset.assignTno, btn.dataset.assignEngg);
        });
    });

    // Limit engineer checkboxes to max 5
    document.querySelector('.adm-engg-list') && document.querySelector('.adm-engg-list').addEventListener('change', function(e){
        if (e.target.classList.contains('adm-engg-checkbox')) {
            var checked = document.querySelectorAll('.adm-engg-checkbox:checked');
            var msgEl = document.getElementById('admEnggMsg');
            if (checked.length > 5) {
                e.target.checked = false;
                msgEl.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i> Maximum 5 engineers allowed.';
            } else {
                msgEl.textContent = '';
            }
        }
    });
})();
<?php endif; ?>
</script>

<?php if ($isAdmin): ?>
<!-- Admin Tools modal — Assign Engineer checkboxes (1-5) + Update Status -->
<div id="adminModal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.55);z-index:60;align-items:center;justify-content:center;padding:20px">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:760px;padding:24px 26px;box-shadow:0 30px 70px -18px rgba(15,23,42,.5);position:relative;overflow:hidden">
        <div style="position:absolute;left:0;top:0;bottom:0;width:6px;background:linear-gradient(180deg,#FF9933 0% 33%, #fff 33% 66%, #138808 66% 100%)"></div>

        <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px">
            <div style="width:42px;height:42px;border-radius:11px;background:linear-gradient(135deg,#1d4ed8,#0a1f44);color:#fff;display:flex;align-items:center;justify-content:center"><i class="fa-solid fa-user-gear"></i></div>
            <div>
                <h3 style="margin:0;color:#0a1f44;font-size:17px;font-weight:800">Admin Tools — Ticket <b id="admModalTno" style="color:#1d4ed8"></b></h3>
                <p style="margin:3px 0 0;color:#475569;font-size:12px">Assign 1–5 engineers to the selected ticket(s).</p>
            </div>
            <button type="button" onclick="closeAdminModal()" style="margin-left:auto;background:#f1f5f9;border:0;width:34px;height:34px;border-radius:8px;color:#0a1f44;font-size:14px;cursor:pointer"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <!-- Assign engineer form — checkboxes -->
        <form method="post" action="includes/ticket_action.php" onsubmit="return validateAssignSelection()" style="margin:14px 0 18px;padding:14px 16px;background:linear-gradient(135deg,#eff6ff,#f0fdfa);border:1px solid #bfdbfe;border-radius:11px">
            <input type="hidden" name="action" value="assign">
            <div id="admSelectedTickets" style="margin-bottom:10px;font-size:13px;color:#0f172a"></div>
            <div id="admTicketInputs"></div>
            <label style="display:block;font-size:11px;color:#1e3a8a;font-weight:800;text-transform:uppercase;letter-spacing:.6px;margin-bottom:6px">
                <i class="fa-solid fa-user-tie"></i> Assign To Engineers (select 1–5)
            </label>
            <div id="admEnggMsg" style="font-size:12px;color:#dc2626;margin-bottom:6px"></div>
            <div class="adm-engg-list" style="display:grid;grid-template-columns:1fr 1fr;gap:6px;max-height:240px;overflow-y:auto;padding:4px 0">
                <?php foreach ($engineers_list as $eng): ?>
                <label style="display:flex;align-items:center;gap:8px;padding:6px 10px;background:#fff;border:1.5px solid #e2e8f0;border-radius:8px;cursor:pointer;transition:all .15s;font-size:13px;font-weight:500;color:#0a1f44"
                       onmouseover="this.style.borderColor='#93c5fd'" onmouseout="this.style.borderColor='#e2e8f0'">
                    <input type="checkbox" name="assignee[]" value="<?= e($eng['engg_name']) ?>" class="adm-engg-checkbox" style="width:16px;height:16px;accent-color:#1d4ed8;flex-shrink:0">
                    <span style="flex:1"><?= e($eng['engg_name'])?></span>
                    <span style="font-size:10.5px;color:#64748b;background:#f1f5f9;padding:1px 7px;border-radius:4px;white-space:nowrap"><?= e($eng['support_field'])?></span>
                </label>
                <?php endforeach; ?>
            </div>
            <div style="margin-top:10px;display:flex;gap:8px;justify-content:flex-end">
                <button type="button" class="btn btn-sm btn-secondary" onclick="closeAdminModal()">Cancel</button>
                <button type="submit" class="btn btn-sm btn-primary" data-testid="adm-assign-submit"><i class="fa-solid fa-paper-plane"></i> Assign</button>
            </div>
            <small style="color:#64748b;font-size:11px;display:block;margin-top:6px">Assigning a Pending ticket also moves it to <b>In-Progress</b>. Multiple engineers can be assigned to the same ticket.</small>
        </form>

        <!-- Update Status form (unchanged) -->
        <form method="post" action="includes/ticket_action.php" style="padding:14px 16px;background:linear-gradient(135deg,#fef3c7,#fee2e2);border:1px solid #fcd34d;border-radius:11px">
            <div id="admStatusTicketInputs"></div>
            <input type="hidden" name="action" value="status_update">
            <label style="display:block;font-size:11px;color:#92400e;font-weight:800;text-transform:uppercase;letter-spacing:.6px;margin-bottom:6px">
                <i class="fa-solid fa-arrows-spin"></i> Override Status (admin)
            </label>
            <div style="display:flex;gap:8px;margin-bottom:10px">
                <select name="new_status" id="admNewStatus" required style="flex:1;padding:10px 12px;border:1.5px solid #fbbf24;border-radius:9px;background:#fff;font-size:13.5px;font-weight:600;color:#92400e">
                    <option value="Pending">Pending (re-open)</option>
                    <option value="Attend">In-Progress (Attend)</option>
                    <option value="Solved">Solved</option>
                    <option value="Closed">Closed</option>
                </select>
                <button type="submit" class="btn btn-sm btn-warning" data-testid="adm-status-submit"><i class="fa-solid fa-rotate"></i> Update</button>
            </div>
            <textarea name="solution" placeholder="Optional: solution / remark to attach with this status change…" style="width:100%;padding:9px 12px;border:1.5px solid #fcd34d;border-radius:9px;background:#fffbeb;font-size:13px;resize:vertical;min-height:60px"></textarea>
        </form>

    </div>
</div>
<?php endif; ?>
