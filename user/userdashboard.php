<?php
/** User self-profile dashboard */
require_once 'includes/photo.php';


$stmt = mysqli_prepare($link, "SELECT * FROM emp_details WHERE staffid = ?");
mysqli_stmt_bind_param($stmt, 's', $sid);
mysqli_stmt_execute($stmt);
$emp = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: [];

// User ticket counts
$stmt = mysqli_prepare($link, "SELECT
    SUM(status='Pending') p, SUM(status='Attend') a, SUM(status='Solved') s, SUM(status='Closed') c, COUNT(*) total
    FROM complain_register WHERE Staff_no = ?");
mysqli_stmt_bind_param($stmt, 's', $sid);
mysqli_stmt_execute($stmt);
$st = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: ['p'=>0,'a'=>0,'s'=>0,'c'=>0,'total'=>0];

// Hardware list
function hw($link, $sid, $catg){
    $stmt = mysqli_prepare($link, "SELECT HD_ID_NO, MC_SL_NO, MODEL FROM hardware_master WHERE STAFF_NO = ? AND CATG = ?");
    mysqli_stmt_bind_param($stmt, 'ss', $sid, $catg);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}
$pcs = hw($link, $sid, 'PC');
$laptops = hw($link, $sid, 'Laptop');
$vdis = hw($link, $sid, 'VDI');
$printers = hw($link, $sid, 'PRINTER');
?>

<div class="page-head">
    <div class="ic"><i class="fa-solid fa-id-badge"></i></div>
    <div>
        <h2>My Profile</h2>
        <div class="sub">Your details and assigned IT assets. To update any information, please contact M&amp;ES (43660 / 660).</div>
    </div>
    <div class="actions">
        <a href="home.php?UserTab=ComplainRegistrationForm" class="btn btn-sm"><i class="fa-solid fa-circle-exclamation"></i> Register Complaint</a>
        <a href="home.php?UserTab=CartridgeRequestForm" class="btn btn-sm btn-secondary"><i class="fa-solid fa-print"></i> Request Cartridge</a>
    </div>
</div>

<div class="stat-grid">
    <div class="stat brand"><div class="ic"><i class="fa-solid fa-ticket"></i></div><div class="label">My Tickets</div><div class="value"><?= (int)$st['total'] ?></div><div class="delta">All-time</div></div>
    <div class="stat warn"><div class="ic"><i class="fa-solid fa-hourglass-half"></i></div><div class="label">Pending</div><div class="value"><?= (int)$st['p'] ?></div><div class="delta">Awaiting engineer</div></div>
    <div class="stat brand"><div class="ic"><i class="fa-solid fa-spinner"></i></div><div class="label">In Progress</div><div class="value"><?= (int)$st['a'] ?></div><div class="delta">Being worked on</div></div>
    <div class="stat ok"><div class="ic"><i class="fa-solid fa-circle-check"></i></div><div class="label">Solved</div><div class="value"><?= (int)$st['s'] ?></div><div class="delta">Awaiting close</div></div>
</div>

<div style="display:grid;grid-template-columns:340px 1fr;gap:18px;align-items:start">
    <!-- Profile card -->
    <div class="card text-center">
        <div style="position:relative;display:inline-block;margin-bottom:10px">
            <?php
            $img = user_photo($sid);
            if ($img) {
                echo '<img src="' . e($img) . '" style="width:140px;height:160px;border-radius:14px;border:3px solid #dbeafe;object-fit:cover">';
            } else {
                echo '<div style="width:140px;height:160px;border-radius:14px;background:linear-gradient(135deg,#0a1f44,#1e3a8a);display:flex;align-items:center;justify-content:center;color:#fff;font-size:48px;font-weight:700;margin:0 auto">' . e(initials($emp['username'] ?? 'U')) . '</div>';
            }
            ?>
        </div>
        <div style="font-weight:700;font-size:16px;letter-spacing:-.2px"><?= e($emp['username'] ?? '') ?></div>
        <div class="text-muted" style="font-size:12.5px"><?= e($emp['desg'] ?? '') ?></div>
        <hr>
        <div style="text-align:left;font-size:13px;display:flex;flex-direction:column;gap:6px">
            <div><i class="fa-solid fa-id-card" style="width:18px;color:#94a3b8"></i> <b>Staff #</b> &nbsp;<?= e($emp['staffid'] ?? '') ?></div>
            <div><i class="fa-solid fa-building" style="width:18px;color:#94a3b8"></i> <b>Dept</b> &nbsp;<?= e($emp['deptt'] ?? '') ?></div>
            <div><i class="fa-solid fa-sitemap" style="width:18px;color:#94a3b8"></i> <b>Section</b> &nbsp;<?= e($emp['sec'] ?? '') ?></div>
            <div><i class="fa-solid fa-phone" style="width:18px;color:#94a3b8"></i> <b>Phone</b> &nbsp;<?= e(trim(($emp['ip_phone'] ?? '') . ' / ' . ($emp['phone_no'] ?? ''),' /')) ?></div>
            <div><i class="fa-solid fa-cake-candles" style="width:18px;color:#94a3b8"></i> <b>D.O.B.</b> &nbsp;<?= e($emp['d_o_b'] ?? '') ?></div>
            <div><i class="fa-solid fa-layer-group" style="width:18px;color:#94a3b8"></i> <b>Grade</b> &nbsp;<?= e($emp['grade'] ?? '') ?></div>
            <div><i class="fa-solid fa-coins" style="width:18px;color:#94a3b8"></i> <b>Cost Center</b> &nbsp;<?= e($emp['cost_center'] ?? '') ?></div>
        </div>
    </div>

    <!-- Assets -->
    <div class="card">
        <div class="card-title"><i class="fa-solid fa-laptop"></i> My IT Assets</div>
        <?php
        function asset_block($title, $icon, $rows) {
            echo '<div style="margin-bottom:14px"><div style="font-size:12.5px;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px"><i class="fa-solid ' . $icon . '" style="color:#1e3a8a"></i> &nbsp;' . $title . '</div>';
            $any = false;
            echo '<div style="display:flex;gap:8px;flex-wrap:wrap">';
            while ($r = mysqli_fetch_assoc($rows)) {
                $any = true;
                $label = $r['HD_ID_NO'] ?: $r['MODEL'];
                echo '<div style="background:#f1f5f9;border:1px solid #e2e8f0;border-radius:8px;padding:7px 12px;font-size:12px"><b>' . e($label) . '</b> &middot; <span style="color:#16a34a">SL: ' . e($r['MC_SL_NO']) . '</span></div>';
            }
            if (!$any) echo '<div class="text-muted" style="font-size:12px">No items assigned.</div>';
            echo '</div></div>';
        }
        asset_block('Desktop PCs', 'fa-desktop', $pcs);
        asset_block('Laptops',     'fa-laptop',  $laptops);
        asset_block('VDIs',        'fa-cloud',   $vdis);
        asset_block('Printers',    'fa-print',   $printers);
        ?>
    </div>
</div>

<?php include 'includes/info_widgets.php'; ?>
