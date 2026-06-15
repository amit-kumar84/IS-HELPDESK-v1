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

$isEngineer = current_role() === 'Engineer';
$engineerName = $isEngineer ? ($_SESSION['user_name'] ?? '') : '';

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

// Engineers can only see their own attended and solved tickets
if ($isEngineer && in_array($status_filter, ['attend', 'solved'], true) && $engineerName !== '') {
    $where .= " AND FIND_IN_SET(?, support_engg)";
}

$q = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['p'] ?? 1));
$allowedPer = [20, 50, 100];
$per = (int)($_GET['per'] ?? 100);
if (!in_array($per, $allowedPer, true)) $per = 100;
$off = ($page - 1) * $per;

if ($q !== '') {
    $stmt = mysqli_prepare($link, "SELECT t_no, r_DateTime, dept, sec, user_name, Staff_no, phone_no, pc_no, printer, problem_type, problem, support_engg, solution, s_DateTime, status
        FROM complain_register WHERE $where
        AND (t_no LIKE ? OR user_name LIKE ? OR Staff_no LIKE ? OR pc_no LIKE ? OR problem LIKE ?)
        ORDER BY substring(t_no,1,6) DESC, substring(t_no,8,12) DESC
        LIMIT ? OFFSET ?");
    $like = "%$q%";
    if ($isEngineer && in_array($status_filter, ['attend', 'solved'], true) && $engineerName !== '') {
        mysqli_stmt_bind_param($stmt, 'sssssii', $engineerName, $like, $like, $like, $like, $like, $per, $off);
    } else {
        mysqli_stmt_bind_param($stmt, 'sssssii', $like, $like, $like, $like, $like, $per, $off);
    }
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    
    $cstmt = mysqli_prepare($link, "SELECT COUNT(*) FROM complain_register WHERE $where
        AND (t_no LIKE ? OR user_name LIKE ? OR Staff_no LIKE ? OR pc_no LIKE ? OR problem LIKE ?)");
    $like = "%$q%";
    if ($isEngineer && in_array($status_filter, ['attend', 'solved'], true) && $engineerName !== '') {
        mysqli_stmt_bind_param($cstmt, 'ssssss', $engineerName, $like, $like, $like, $like, $like);
    } else {
        mysqli_stmt_bind_param($cstmt, 'sssss', $like, $like, $like, $like, $like);
    }
    mysqli_stmt_execute($cstmt);
    $totalFiltered = (int) mysqli_fetch_array(mysqli_stmt_get_result($cstmt))[0];
} else {
    $stmt = mysqli_prepare($link, "SELECT t_no, r_DateTime, dept, sec, user_name, Staff_no, phone_no, pc_no, printer, problem_type, problem, support_engg, solution, s_DateTime, status
        FROM complain_register WHERE $where
        ORDER BY substring(t_no,1,6) DESC, substring(t_no,8,12) DESC
        LIMIT ? OFFSET ?");
    if ($isEngineer && in_array($status_filter, ['attend', 'solved'], true) && $engineerName !== '') {
        mysqli_stmt_bind_param($stmt, 'sii', $engineerName, $per, $off);
    } else {
        mysqli_stmt_bind_param($stmt, 'ii', $per, $off);
    }
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    
    $countQuery = "SELECT COUNT(*) FROM complain_register WHERE $where";
    if ($isEngineer && in_array($status_filter, ['attend', 'solved'], true) && $engineerName !== '') {
        $countStmt = mysqli_prepare($link, $countQuery);
        mysqli_stmt_bind_param($countStmt, 's', $engineerName);
        mysqli_stmt_execute($countStmt);
        $totalFiltered = (int) mysqli_fetch_array(mysqli_stmt_get_result($countStmt))[0];
    } else {
        $totalFiltered = (int) mysqli_fetch_array(mysqli_query($link, $countQuery))[0];
    }
}
$pages = max(1, (int) ceil($totalFiltered / $per));
$page = min($page, $pages);
$off = ($page - 1) * $per;

$pageSizeOptions = [20, 50, 100];
$rangeStart = $totalFiltered > 0 ? (($page - 1) * $per) + 1 : 0;
$rangeEnd = $totalFiltered > 0 ? min($page * $per, $totalFiltered) : 0;

$panel = $_GET['AdminTab'] ?? null;
$base = $panel ? 'Admin_Home.php?AdminTab=' . urlencode($panel) : 'Engineer_home.php?EngineerTab=' . urlencode($_GET['EngineerTab'] ?? 'All_Calls');

$openCount = (int)($counts['pending'] ?? 0) + (int)($counts['attend'] ?? 0) + (int)($counts['solved'] ?? 0);
$statusTabs = [
    'open' => [
        'label' => 'Open',
        'icon' => 'fa-layer-group',
        'count' => $openCount,
        'hint' => 'Pending + active + solved',
        'tone' => 'open',
    ],
    'pending' => [
        'label' => 'Pending',
        'icon' => 'fa-hourglass-half',
        'count' => (int)($counts['pending'] ?? 0),
        'hint' => 'Waiting for acceptance',
        'tone' => 'pending',
    ],
    'attend' => [
        'label' => 'In Progress',
        'icon' => 'fa-spinner',
        'count' => (int)($counts['attend'] ?? 0),
        'hint' => 'Being worked on now',
        'tone' => 'attend',
    ],
    'solved' => [
        'label' => 'Solved',
        'icon' => 'fa-check-circle',
        'count' => (int)($counts['solved'] ?? 0),
        'hint' => 'Ready for closure',
        'tone' => 'solved',
    ],
    'closed' => [
        'label' => 'Closed',
        'icon' => 'fa-lock',
        'count' => (int)($counts['closed'] ?? 0),
        'hint' => 'Finished archive',
        'tone' => 'closed',
    ],
    'all' => [
        'label' => 'All',
        'icon' => 'fa-ticket',
        'count' => (int)($counts['total'] ?? 0),
        'hint' => 'Everything in one view',
        'tone' => 'all',
    ],
];

$statusHero = [
    'open' => [
        'kicker' => 'Open tickets',
        'title' => 'Open Tickets',
        'text' => 'Browse the live queue of pending, active, and solved tickets in one polished view.',
    ],
    'pending' => [
        'kicker' => 'Pending queue',
        'title' => 'Pending Tickets',
        'text' => 'Tickets waiting for assignment or first response are shown here.',
    ],
    'attend' => [
        'kicker' => 'Work in progress',
        'title' => 'In Progress Tickets',
        'text' => 'See the tickets currently being attended and actively worked on.',
    ],
    'solved' => [
        'kicker' => 'Ready to close',
        'title' => 'Solved Tickets',
        'text' => 'Review solved tickets that are waiting for closure or final confirmation.',
    ],
    'closed' => [
        'kicker' => 'Archived items',
        'title' => 'Closed Tickets',
        'text' => 'Browse the finished ticket archive for completed support history.',
    ],
    'all' => [
        'kicker' => 'All tickets',
        'title' => 'All Tickets',
        'text' => 'Browse, filter, and take action on the complete ticket list.',
    ],
];

$heroMeta = $statusHero[$status_filter] ?? $statusHero['open'];

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

<div class="ticket-status-shell card">
    <div class="ticket-status-hero">
        <div>
            <div class="ticket-status-kicker"><i class="fa-solid fa-sparkles"></i> <?= e($heroMeta['kicker']) ?></div>
            <h3><?= e($heroMeta['title']) ?></h3>
            <p><?= e($heroMeta['text']) ?></p>
        </div>
        <div class="ticket-status-metric">
            <span>Visible results</span>
            <strong><?= number_format($totalFiltered) ?></strong>
            <small><?= e($heroMeta['title']) ?></small>
        </div>
    </div>

    <?php if (!$isLocked): ?>
    <div class="ticket-status-grid">
        <?php foreach ($statusTabs as $k => $meta): ?>
            <a class="status-pill <?= $status_filter === $k ? 'active' : '' ?> tone-<?= e($meta['tone']) ?>" href="<?= $base ?>&status=<?= e($k) ?>">
                <span class="pill-icon"><i class="fa-solid <?= e($meta['icon']) ?>"></i></span>
                <span class="pill-copy">
                    <span class="pill-label"><?= e($meta['label']) ?></span>
                    <span class="pill-hint"><?= e($meta['hint']) ?></span>
                </span>
                <span class="pill-count"><?= number_format($meta['count']) ?></span>
            </a>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="ticket-status-locked">
        <div>
            <i class="fa-solid fa-filter"></i>
            Showing <b><?= e($heroMeta['title']) ?></b>
        </div>
        <span><?= number_format($totalFiltered) ?> result<?= $totalFiltered === 1 ? '' : 's' ?></span>
    </div>
    <?php endif; ?>

    <div class="ticket-status-actions">
        <div class="ticket-actions-row">
            <form method="get" class="ticket-page-size-form">
                <?php foreach ($_GET as $k => $v): if (in_array($k, ['p','per'])) continue; ?>
                    <input type="hidden" name="<?= e($k) ?>" value="<?= e($v) ?>">
                <?php endforeach; ?>
                <label for="ticketPageSize">Calls per page</label>
                <select id="ticketPageSize" name="per" onchange="this.form.submit()">
                    <?php foreach ($pageSizeOptions as $opt): ?>
                        <option value="<?= $opt ?>" <?= $per === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                    <?php endforeach; ?>
                </select>
            </form>

            <div class="ticket-pagination-center">
                <div class="ticket-pagination-summary">
                    Showing <b><?= $rangeStart ?></b>-<b><?= $rangeEnd ?></b> of <b><?= number_format($totalFiltered) ?></b>
                </div>

                <?php if ($pages > 1): ?>
                <div class="ticket-page-links">
                    <a class="page-chip <?= $page <= 1 ? 'disabled' : '' ?>" href="<?= $page <= 1 ? '#' : $base . '&status=' . e($status_filter) . '&q=' . urlencode($q) . '&per=' . $per . '&p=' . max(1, $page - 1) ?>">Prev</a>
                    <a class="page-chip <?= $page >= $pages ? 'disabled' : '' ?>" href="<?= $page >= $pages ? '#' : $base . '&status=' . e($status_filter) . '&q=' . urlencode($q) . '&per=' . $per . '&p=' . min($pages, $page + 1) ?>">Next</a>
                </div>
                <?php endif; ?>
            </div>

            <form method="get" class="ticket-search-form">
                <?php foreach ($_GET as $k => $v): if (in_array($k, ['q','p'])) continue; ?>
                    <input type="hidden" name="<?= e($k) ?>" value="<?= e($v) ?>">
                <?php endforeach; ?>
                <input type="search" name="q" value="<?= e($q) ?>" placeholder="Search ticket #, name, asset, problem…" data-testid="tickets-search">
                <?php if ($q !== ''): ?>
                    <a class="btn btn-sm btn-secondary" href="<?= $base ?>&status=<?= e($status_filter) ?>" title="Clear search"><i class="fa-solid fa-xmark"></i></a>
                <?php endif; ?>
                <button class="btn btn-sm" type="submit" data-testid="tickets-search-btn"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
            </form>
        </div>
    </div>
</div>

<div class="table-wrap tickets-table-wrap">
    <table class="tickets-table">
        <thead><tr>
            <th>Ticket #</th><th style="width:72px;min-width:72px;max-width:72px">Photo</th><th>User</th><th>Dept (Sec)</th>
            <th>Phone</th><th>Asset</th><th>Printer</th><th>Category</th>
            <th>Problem</th><th>Engineer</th><th>Solution</th><th>Raised</th><th>Updated</th><th>Status</th>
            <?php if ($canAct): ?><th style="min-width:140px;text-align:center">Action</th><?php endif; ?>
        </tr></thead>
        <tbody>
        <?php while ($r = mysqli_fetch_assoc($rows)):
            $s = strtolower($r['status']);
            $cls = $s === 'pending' ? 'pending' : ($s === 'attend' ? 'attend' : ($s === 'solved' ? 'solved' : 'closed'));
        ?>
            <tr>
                <td><b style="color:#000000"><?= e($r['t_no']) ?></b></td>
                <td><?= render_avatar($r['Staff_no'], $r['user_name'], 38) ?></td>
                <td><?= e($r['user_name']) ?><br><small style="color:#000000;font-size:11px"><?= e($r['Staff_no']) ?></small></td>
                <td><?= e($r['dept'] . ' / ' . $r['sec']) ?></td>
                <td style="font-variant-numeric:tabular-nums;font-size:11.5px"><?= e($r['phone_no']) ?></td>
                <td><?= e($r['pc_no']) ?></td>
                <td><?= e($r['printer']) ?></td>
                <td><?= e($r['problem_type']) ?></td>
                <td><?= e($r['problem']) ?></td>
                <td><?= e($r['support_engg']) ? render_engg_badges($r['support_engg']) : '<span style="color:#000000;font-size:12px">—</span>' ?></td>
                <td class="solution-cell"><?= e($r['solution']) ?></td>
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
                <td style="min-width:140px;padding:8px !important">
                    <div style="display:flex;flex-direction:column;gap:6px">
                        <?php if ($r['status'] === 'Pending' && !$isAdmin): ?>
                            <form method="post" action="includes/ticket_action.php" style="width:100%">
                                <input type="hidden" name="t_no" value="<?= e($r['t_no']) ?>"><input type="hidden" name="action" value="attend">
                                <button class="btn btn-xs btn-warning" title="Mark In Progress" data-testid="act-attend-<?= e($r['t_no']) ?>" style="width:100%"><i class="fa-solid fa-play"></i> Attend</button>
                            </form>
                        <?php endif; ?>
                        <?php if ($r['status'] === 'Attend'): ?>
                            <button class="btn btn-xs btn-success" title="Mark Solved" onclick="solveTicket('<?= e($r['t_no']) ?>')" data-testid="act-solve-<?= e($r['t_no']) ?>" style="width:100%"><i class="fa-solid fa-check"></i> Solve</button>
                        <?php endif; ?>
                        <?php if ($r['status'] === 'Solved'): ?>
                            <form method="post" action="includes/ticket_action.php" style="width:100%" onsubmit="return confirmTicketAction(event, this, 'Close ticket <?= e($r['t_no']) ?>?')">
                                <input type="hidden" name="t_no" value="<?= e($r['t_no']) ?>"><input type="hidden" name="action" value="close">
                                <button class="btn btn-xs btn-navy" title="Close ticket" data-testid="act-close-<?= e($r['t_no']) ?>" style="width:100%"><i class="fa-solid fa-lock"></i> Close</button>
                            </form>
                        <?php endif; ?>
                        <?php if ($r['status'] === 'Closed'): ?>
                            <form method="post" action="includes/ticket_action.php" style="width:100%" onsubmit="return confirmReopenTicket(event, this, 'Re-open <?= e($r['t_no']) ?>?')">
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
            <tr><td colspan="<?= $canAct ? 15 : 14 ?>" style="text-align:center;padding:30px;color:var(--c-text-2)">No tickets match the filter.</td></tr>
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
    color: #000000;
    background: #e0e7ff;
    border: 1px solid #c7d2fe;
    white-space: nowrap;
    line-height: 1.5;
}

/* ==============================
   Ticket status dashboard shell
   ============================== */
.ticket-status-shell {
    position: relative;
    overflow: hidden;
    padding: 12px 14px;
    zoom: .85;
    background:
        radial-gradient(circle at top left, rgba(59,130,246,.14), transparent 34%),
        radial-gradient(circle at top right, rgba(245,158,11,.14), transparent 28%),
        linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
}
.ticket-status-shell::before,
.ticket-status-shell::after {
    content: '';
    position: absolute;
    inset: auto auto -35% -12%;
    width: 220px;
    height: 220px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(59,130,246,.16) 0%, rgba(59,130,246,0) 70%);
    animation: floatGlow 10s ease-in-out infinite;
    pointer-events: none;
}
.ticket-status-shell::after {
    inset: -28% -10% auto auto;
    width: 180px;
    height: 180px;
    background: radial-gradient(circle, rgba(16,185,129,.15) 0%, rgba(16,185,129,0) 72%);
    animation-delay: -4s;
}
@keyframes floatGlow {
    0%, 100% { transform: translate3d(0,0,0) scale(1); opacity: .75; }
    50% { transform: translate3d(14px,-10px,0) scale(1.08); opacity: 1; }
}
.ticket-status-hero {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    justify-content: space-between;
    flex-wrap: wrap;
    margin-bottom: 10px;
}
.ticket-status-kicker {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 7px 12px;
    border-radius: 999px;
    background: linear-gradient(135deg, rgba(59,130,246,.12), rgba(34,197,94,.12));
    border: 1px solid rgba(59,130,246,.16);
    color: #1e3a8a;
    font-size: 11.5px;
    font-weight: 800;
    letter-spacing: .5px;
    text-transform: uppercase;
}
.ticket-status-hero h3 {
    margin: 6px 0 4px;
    font-size: 18px;
    color: #0a1f44;
    letter-spacing: -.3px;
}
.ticket-status-hero p {
    margin: 0;
    max-width: 720px;
    color: #475569;
    font-size: 13px;
    line-height: 1.6;
}
.ticket-status-metric {
    min-width: 170px;
    padding: 10px 14px;
    border-radius: 18px;
    background: linear-gradient(135deg, #0a1f44, #1e3a8a 50%, #2563eb);
    color: #fff;
    box-shadow: 0 18px 35px -20px rgba(37,99,235,.65);
    position: relative;
    overflow: hidden;
}
.ticket-status-metric::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,.22), transparent);
    transform: translateX(-120%);
    animation: shimmer 4.8s ease-in-out infinite;
}
@keyframes shimmer {
    0%, 25% { transform: translateX(-120%); }
    55%, 100% { transform: translateX(120%); }
}
.ticket-status-metric span,
.ticket-status-metric small {
    position: relative;
    z-index: 1;
    display: block;
}
.ticket-status-metric span {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .5px;
    opacity: .84;
}
.ticket-status-metric strong {
    position: relative;
    z-index: 1;
    display: block;
    font-size: 26px;
    line-height: 1;
    margin: 6px 0 4px;
}
.ticket-status-metric small {
    font-size: 12px;
    opacity: .88;
}
.ticket-status-grid {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: repeat(6, minmax(0, 1fr));
    gap: 8px;
    margin-bottom: 10px;
}
.status-pill {
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    border-radius: 18px;
    border: 1px solid rgba(148,163,184,.22);
    background: linear-gradient(180deg, rgba(255,255,255,.95), rgba(248,250,252,.92));
    color: #0f172a;
    text-decoration: none;
    box-shadow: 0 14px 32px -24px rgba(15,23,42,.35);
    transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease, background .22s ease;
}
.status-pill::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,.35), transparent 45%, rgba(255,255,255,.15));
    opacity: .6;
    pointer-events: none;
}
.status-pill:hover {
    transform: translateY(-3px) scale(1.01);
    box-shadow: 0 18px 36px -20px rgba(15,23,42,.45);
    border-color: rgba(59,130,246,.3);
}
.status-pill.active {
    color: #fff;
    border-color: transparent;
    transform: translateY(-2px);
    box-shadow: 0 18px 42px -18px rgba(37,99,235,.55);
}
.status-pill.tone-open.active { background: linear-gradient(135deg, #0ea5e9, #2563eb 55%, #1d4ed8); }
.status-pill.tone-pending.active { background: linear-gradient(135deg, #f59e0b, #fb7185); }
.status-pill.tone-attend.active { background: linear-gradient(135deg, #7c3aed, #06b6d4); }
.status-pill.tone-solved.active { background: linear-gradient(135deg, #10b981, #22c55e); }
.status-pill.tone-closed.active { background: linear-gradient(135deg, #475569, #0f172a); }
.status-pill.tone-all.active { background: linear-gradient(135deg, #1e3a8a, #0f766e); }
.pill-icon {
    width: 38px;
    height: 38px;
    flex: 0 0 38px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    color: #fff;
    background: linear-gradient(135deg, #0a1f44, #2563eb);
    box-shadow: inset 0 1px 0 rgba(255,255,255,.18);
}
.status-pill.tone-pending .pill-icon { background: linear-gradient(135deg, #f59e0b, #fb7185); }
.status-pill.tone-attend .pill-icon { background: linear-gradient(135deg, #7c3aed, #06b6d4); }
.status-pill.tone-solved .pill-icon { background: linear-gradient(135deg, #10b981, #22c55e); }
.status-pill.tone-closed .pill-icon { background: linear-gradient(135deg, #64748b, #0f172a); }
.status-pill.tone-all .pill-icon { background: linear-gradient(135deg, #1e3a8a, #0f766e); }
.status-pill.active .pill-icon { background: rgba(255,255,255,.18); box-shadow: inset 0 1px 0 rgba(255,255,255,.28); }
.pill-copy {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    gap: 1px;
    min-width: 0;
    flex: 1;
}
.pill-label {
    font-size: 13px;
    font-weight: 800;
    letter-spacing: -.1px;
}
.pill-hint {
    font-size: 11px;
    color: #64748b;
}
.status-pill.active .pill-hint { color: rgba(255,255,255,.82); }
.pill-count {
    position: relative;
    z-index: 1;
    min-width: 34px;
    padding: 6px 9px;
    border-radius: 999px;
    background: rgba(15,23,42,.05);
    color: #0a1f44;
    font-size: 12px;
    font-weight: 800;
    text-align: center;
}
.status-pill.active .pill-count {
    background: rgba(255,255,255,.18);
    color: #fff;
}
.ticket-status-locked {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    flex-wrap: wrap;
    padding: 14px 16px;
    border-radius: 16px;
    background: linear-gradient(135deg, rgba(15,23,42,.04), rgba(59,130,246,.06));
    border: 1px dashed rgba(59,130,246,.25);
    color: #334155;
    margin-bottom: 14px;
}
.ticket-status-locked b { color: #0a1f44; }
.ticket-status-locked i { color: #2563eb; margin-right: 6px; }
.ticket-status-locked span { font-size: 12px; color: #64748b; }
.ticket-status-actions {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    flex-wrap: wrap;
}
.ticket-actions-row {
    display: grid;
    grid-template-columns: auto minmax(0, 1fr) auto;
    align-items: center;
    gap: 10px 14px;
    width: 100%;
}
.ticket-pagination-center {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
}
.ticket-search-form {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    width: 100%;
    justify-content: flex-end;
}
.ticket-search-form input[type=search] {
    width: min(36vw, 360px);
    min-width: 240px;
    padding: 10px 12px;
    border-radius: 999px;
    border: 1px solid rgba(37,99,235,.18);
    background: linear-gradient(180deg, #ffffff, #f8fbff);
    box-shadow: inset 0 1px 2px rgba(15,23,42,.04);
    transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
}
.ticket-search-form input[type=search]:focus {
    outline: none;
    border-color: rgba(37,99,235,.45);
    box-shadow: 0 0 0 4px rgba(59,130,246,.12);
    transform: translateY(-1px);
}
.ticket-pagination {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    align-items: end;
    gap: 0;
    padding: 10px 0 0;
    margin: 0;
    border-top: 1px solid rgba(148,163,184,.18);
}
.ticket-pagination-summary {
    color: #475569;
    font-size: 12px;
    font-weight: 600;
    margin: 0;
    line-height: 1;
    padding-bottom: 2px;
    text-align: center;
}
.ticket-page-links {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    justify-content: center;
    align-items: end;
    padding: 0;
    width: 100%;
}
.ticket-page-size-form {
    display: inline-flex;
    align-items: end;
    gap: 8px;
    flex-wrap: wrap;
    margin: 0;
}
.ticket-page-size-form label {
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: #1e3a8a;
}
.ticket-page-size-form select {
    min-width: 96px;
    padding: 8px 12px;
    border-radius: 999px;
    border: 1px solid rgba(37,99,235,.18);
    background: #fff;
    color: #0f172a;
    font-weight: 700;
}
.page-chip {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 60px;
    height: 38px;
    padding: 0 12px;
    border-radius: 999px;
    border: 1px solid rgba(37,99,235,.18);
    background: linear-gradient(180deg, #ffffff, #f8fbff);
    color: #1e3a8a;
    font-size: 12px;
    font-weight: 800;
    text-decoration: none;
    box-shadow: 0 10px 22px -18px rgba(15,23,42,.35);
}
.page-chip.active {
    background: linear-gradient(135deg, #1e3a8a, #2563eb);
    border-color: transparent;
    color: #fff;
}
.page-chip.disabled {
    pointer-events: none;
    opacity: .45;
}

@media (max-width: 1280px) {
    .ticket-status-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}
@media (max-width: 768px) {
    .tickets-table-wrap {
        min-height: 0;
        max-height: none;
        margin-bottom: 4px;
    }
    .ticket-status-shell {
        padding: 14px;
        zoom: 1;
    }
    .ticket-status-hero h3 {
        font-size: 17px;
    }
    .ticket-status-grid {
        grid-template-columns: 1fr;
    }
    .status-pill {
        padding: 12px;
    }
    .ticket-search-form input[type=search] {
        width: 100%;
        min-width: 0;
    }
    .ticket-status-actions {
        align-items: stretch;
    }
    .ticket-search-form {
        width: 100%;
        margin-left: 0;
    }
    .ticket-search-form .btn,
    .ticket-status-actions .btn {
        flex: 1 1 auto;
    }
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

/* ==============================
   Ticket Confirm Modal
   ============================== */
#ticketConfirmModal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15,23,42,.55);
    z-index: 61;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
#ticketConfirmModal .smodal-inner {
    background: #fff;
    border-radius: 16px;
    width: 100%;
    max-width: 520px;
    padding: 24px 26px;
    box-shadow: 0 30px 70px -18px rgba(15,23,42,.5);
    position: relative;
    overflow: hidden;
}
#ticketConfirmModal .smodal-inner .smodal-accent {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 6px;
    background: linear-gradient(180deg,#7c3aed,#a78bfa);
}
#ticketConfirmModal .smodal-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 10px;
}
#ticketConfirmModal .smodal-header .smodal-icon {
    width: 42px;
    height: 42px;
    border-radius: 11px;
    background: linear-gradient(135deg,#7c3aed,#a855f7);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}
#ticketConfirmModal .smodal-header h3 {
    margin: 0;
    color: #0a1f44;
    font-size: 17px;
    font-weight: 800;
}
#ticketConfirmModal .smodal-header .smodal-close {
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
#ticketConfirmModal .smodal-body {
    font-size: 13.5px;
    color: #334155;
    line-height: 1.6;
}
#ticketConfirmModal .smodal-footer {
    display: flex;
    gap: 8px;
    justify-content: flex-end;
    margin-top: 14px;
}
#ticketConfirmModal .smodal-footer .btn {
    min-width: 96px;
}

/* ==============================
   Responsive Table
   ============================== */
.tickets-table-wrap {
    overflow-x: hidden;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    zoom: .7;
    background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    border: 1px solid rgba(148,163,184,.18);
    border-radius: 18px;
    box-shadow: 0 16px 36px -26px rgba(15,23,42,.34);
    min-height: calc(100vh - 430px);
    max-height: calc(100vh - 300px);
    margin-bottom: 0;
}

.tickets-table {
    font-size: 13px;
    width: 100%;
    min-width: 0;
    border: 2px solid rgba(30,64,175,.36);
    border-collapse: separate;
    border-spacing: 0;
    table-layout: fixed;
    white-space: normal;
    box-shadow: 0 0 0 1px rgba(255,255,255,.8) inset, 0 10px 28px -22px rgba(30,64,175,.45);
}

.tickets-table th,
.tickets-table td {
    padding: 10px 9px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    word-break: normal;
    overflow-wrap: normal;
    hyphens: none;
}
                <td style="max-width:340px;min-width:240px;font-size:12.5px;font-weight:500;color:#000000;white-space:normal;word-break:normal;overflow-wrap:break-word;hyphens:auto;line-height:1.4"><?= e($r['problem']) ?></td>

.tickets-table thead th {
    background: linear-gradient(135deg, #0a1f44 0%, #1e3a8a 55%, #2563eb 100%);
    color: #ffffff;
    font-weight: 700;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .45px;
    text-align: center;
    padding-top: 13px;
    padding-bottom: 13px;
    border-bottom: 1px solid rgba(255,255,255,.22);
    border-right: 1px solid rgba(255,255,255,.18);
    position: sticky;
    top: 0;
    z-index: 3;
}
.tickets-table thead th:first-child { border-top-left-radius: 16px; }
.tickets-table thead th:last-child { border-top-right-radius: 16px; border-right: 0; }
.tickets-table tbody td {
    padding: 10px 9px;
    border-bottom: 1px solid #cfd9e6;
    border-right: 1px solid #d6e0ec;
    color: #000000;
    text-align: center;
    vertical-align: top;
    background: #ffffff;
    transition: transform .16s ease, background-color .16s ease, box-shadow .16s ease;
    transform-origin: center center;
}
.tickets-table tbody td:last-child { border-right: 0; }
.tickets-table tbody tr:nth-child(even) td { background: #f9fbff; }
.tickets-table tbody tr:hover td { background: #eef6ff; }
.tickets-table tbody td:hover {
    transform: scale(1.50);
    position: relative;
    z-index: 2;
    box-shadow: 0 12px 24px -18px rgba(15,23,42,.25);
}
.tickets-table tbody td:nth-child(14):hover,
.tickets-table tbody td:last-child:hover {
    transform: none;
    position: static;
    z-index: auto;
    box-shadow: none;
}
.tickets-table tbody tr:last-child td { border-bottom: 0; }
.tickets-table td:nth-child(1) { color: #000000; font-weight: 800; }
.tickets-table td:nth-child(9) {
    color: #dc2626;
    font-weight: 600;
    white-space: normal;
    overflow: visible;
    text-overflow: clip;
    word-break: break-word;
    overflow-wrap: anywhere;
    line-height: 1.45;
}
.tickets-table td.solution-cell {
    max-width: 240px;
    font-size: 12px;
    color: #16a34a;
    text-align: center;
    white-space: normal;
    overflow: visible;
    text-overflow: clip;
    word-break: break-word;
    overflow-wrap: anywhere;
    vertical-align: middle;
}
.tickets-table td:nth-child(7) {
    text-align: center;
    white-space: normal;
    overflow: visible;
    text-overflow: clip;
    word-break: break-word;
    overflow-wrap: anywhere;
}
.tickets-table td:nth-child(10) .eng-badges {
    justify-content: center;
}
.tickets-table td:last-child div {
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: min(160px, 100%);
    margin: 0 auto;
}
.tickets-table td:last-child .btn {
    width: 100%;
    min-height: 38px;
    padding: 10px 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border-radius: 12px;
    font-weight: 700;
}
.tickets-table td:last-child form {
    width: 100%;
    display: flex;
    justify-content: center;
}
.tickets-table td:last-child {
    text-align: center;
    vertical-align: middle;
}

.tickets-table th:nth-child(14),
.tickets-table td:nth-child(14) {
    width: 100px;
    min-width: 100px;
    max-width: 100px;
    text-align: center;
}

.tickets-table th:nth-child(5),
.tickets-table td:nth-child(5) {
    width: 92px;
    min-width: 92px;
    max-width: 92px;
    text-align: center;
}

.tickets-table th:nth-child(2),
.tickets-table td:nth-child(2) {
    width: 72px;
    min-width: 72px;
    max-width: 72px;
    text-align: center;
}

.tickets-table td:nth-child(2) .row-avatar {
    transition: transform .18s ease, box-shadow .18s ease;
    transform-origin: center center;
    display: inline-block;
}

.tickets-table td:nth-child(2):hover .row-avatar {
    transform: scale(2.50);
    box-shadow: 0 10px 18px -14px rgba(15,23,42,.45);
}

.tickets-table td:nth-child(2):hover {
    font-weight: 800;
}


/* Tablet and below */
@media (max-width: 1200px) {
    .tickets-table th,
    .tickets-table td {
        padding: 8px 6px;
        font-size: 12px;
        zoom: 1;
    }
    
    /* Hide less important columns */
    .tickets-table th:nth-child(5), /* Phone */
    .tickets-table th:nth-child(6), /* Asset */
    .tickets-table th:nth-child(7), /* Printer */
    .tickets-table th:nth-child(8), /* Category */
    .tickets-table th:nth-child(11), /* Solution */
    .tickets-table th:nth-child(12), /* Raised */
    .tickets-table td:nth-child(4),
    .tickets-table td:nth-child(5),
    .tickets-table td:nth-child(6),
    .tickets-table td:nth-child(7),
    .tickets-table td:nth-child(8),
    .tickets-table td:nth-child(11),
    .tickets-table td:nth-child(12) {
        display: none;
    }
}

@media (max-width: 992px) {
    .tickets-table th:nth-child(13), /* Updated */
    .tickets-table td:nth-child(13) {
        display: none;
        margin-bottom: 0;
}

/* Mobile */
@media (max-width: 768px) {
    .tickets-table th,
    .tickets-table td {
        padding: 6px 4px;
        font-size: 11px;
    }
    
    /* Hide more columns on mobile */
    .tickets-table th:nth-child(2), /* Photo */
    .tickets-table th:nth-child(4), /* Dept */
    .tickets-table th:nth-child(5), /* Phone */
    .tickets-table th:nth-child(6), /* Asset */
    .tickets-table th:nth-child(7), /* Printer */
    .tickets-table th:nth-child(8), /* Category */
    .tickets-table th:nth-child(10), /* Engineer */
    .tickets-table th:nth-child(11), /* Solution */
    .tickets-table th:nth-child(12), /* Raised */
    .tickets-table th:nth-child(13), /* Updated */
    .tickets-table td:nth-child(2),
    .tickets-table td:nth-child(4),
    .tickets-table td:nth-child(5),
    .tickets-table td:nth-child(6),
    .tickets-table td:nth-child(7),
    .tickets-table td:nth-child(8),
    .tickets-table td:nth-child(10),
    .tickets-table td:nth-child(11),
    .tickets-table td:nth-child(12),
    .tickets-table td:nth-child(13) {
        display: none;
    }
    
    .btn.btn-xs {
        padding: 4px 8px;
        font-size: 10px;
    }

    .tickets-table td:last-child div {
        gap: 4px;
    }
}

@media (max-width: 560px) {
    .tickets-table th:nth-child(3), /* User */
    .tickets-table td:nth-child(3) {
        display: table-cell;
    }
    .tickets-table thead th:nth-child(1),
    .tickets-table thead th:nth-child(14),
    .tickets-table tbody td:nth-child(1),
    .tickets-table tbody td:nth-child(14) {
        display: table-cell;
    }
    .tickets-table th:nth-child(9),
    .tickets-table td:nth-child(9) {
        max-width: none;
    }
    .tickets-table td:last-child .btn {
        font-size: 10px;
    }
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

<div id="ticketConfirmModal">
    <div class="smodal-inner">
        <div class="smodal-accent"></div>
        <div class="smodal-header">
            <div class="smodal-icon"><i class="fa-solid fa-circle-question"></i></div>
            <div>
                <h3 id="ticketConfirmTitle">Please confirm</h3>
                <p id="ticketConfirmSub" style="margin:3px 0 0;color:#475569;font-size:12px">Are you sure?</p>
            </div>
            <button type="button" class="smodal-close" onclick="closeTicketConfirm()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="smodal-body">
            <div id="ticketConfirmMessage"></div>
        </div>
        <div class="smodal-footer">
            <button type="button" class="btn btn-sm btn-secondary" onclick="closeTicketConfirm()">Cancel</button>
            <button type="button" class="btn btn-sm btn-primary" id="ticketConfirmOkBtn">OK</button>
        </div>
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

var ticketConfirmOnOk = null;
var ticketConfirmPrevOverflow = '';

function openTicketConfirm(title, message, onOk) {
    ticketConfirmOnOk = (typeof onOk === 'function') ? onOk : null;
    ticketConfirmPrevOverflow = document.body.style.overflow;
    document.body.style.overflow = 'hidden';
    document.getElementById('ticketConfirmTitle').textContent = title || 'Please confirm';
    document.getElementById('ticketConfirmSub').textContent = title || 'Please confirm';
    document.getElementById('ticketConfirmMessage').textContent = message || 'Are you sure?';
    var modal = document.getElementById('ticketConfirmModal');
    modal.style.display = 'flex';
}

function closeTicketConfirm() {
    var modal = document.getElementById('ticketConfirmModal');
    modal.style.display = 'none';
    document.body.style.overflow = ticketConfirmPrevOverflow;
    ticketConfirmOnOk = null;
}

document.getElementById('ticketConfirmOkBtn').addEventListener('click', function(){
    var callback = ticketConfirmOnOk;
    closeTicketConfirm();
    if (typeof callback === 'function') callback();
});

function confirmTicketAction(event, form, message) {
    if (event && typeof event.preventDefault === 'function') event.preventDefault();
    openTicketConfirm('Please confirm', message || 'Are you sure?', function(){
        form.submit();
    });
    return false;
}

function confirmReopenTicket(event, form, message) {
    if (event && typeof event.preventDefault === 'function') event.preventDefault();
    openTicketConfirm('Please confirm', message || 'Re-open this ticket?', function(){
        form.submit();
    });
    return false;
}

function hideSidebarForTicketTabs() {
    var side = document.getElementById('appSidebar');
    var body = document.body;
    if (!side) return;

    if (window.matchMedia('(max-width:900px)').matches) {
        side.classList.remove('open');
    } else {
        body.classList.add('sidebar-collapsed');
        try {
            localStorage.setItem('iskot_sidebar', 'collapsed');
        } catch(e){}
    }
}

(function(){
    document.querySelectorAll('.ticket-status-grid a.status-pill, .ticket-page-links a.page-chip').forEach(function(link){
        link.addEventListener('click', function(){
            hideSidebarForTicketTabs();
        });
    });
})();

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
