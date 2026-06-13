<?php
/** Print-friendly employee record */
require_once 'includes/photo.php';

$sid = trim($_GET['sid'] ?? '');
if ($sid === '') { echo '<div class="alert alert-danger">Missing sid parameter.</div>'; return; }

$stmt = mysqli_prepare($link, "SELECT * FROM emp_details WHERE staffid=? LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $sid);
mysqli_stmt_execute($stmt);
$emp = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$emp) { echo '<div class="alert alert-warning">Not found.</div>'; return; }

// Tickets for this user
$stmt = mysqli_prepare($link, "SELECT t_no, r_DateTime, problem_on, problem, status, support_engg FROM complain_register WHERE Staff_no=? ORDER BY t_no DESC LIMIT 50");
mysqli_stmt_bind_param($stmt, 's', $sid);
mysqli_stmt_execute($stmt);
$tix = mysqli_stmt_get_result($stmt);

// Hardware
$stmt = mysqli_prepare($link, "SELECT CATG, HD_ID_NO, MC_SL_NO, MODEL FROM hardware_master WHERE STAFF_NO=?");
mysqli_stmt_bind_param($stmt, 's', $sid);
mysqli_stmt_execute($stmt);
$hw = mysqli_stmt_get_result($stmt);
$photo = user_photo($sid);
?>

<div class="page-head">
    <div class="ic"><i class="fa-solid fa-print"></i></div>
    <div>
        <h2>Employee Record &middot; <?= e($emp['username']) ?></h2>
        <div class="sub">Printable employee details with assigned hardware and ticket history.</div>
    </div>
    <div class="actions">
        <button class="btn btn-sm" onclick="window.print()" data-testid="btn-print"><i class="fa-solid fa-print"></i> Print Record</button>
        <a href="Admin_Home.php?AdminTab=EditUser&sid=<?= urlencode($sid) ?>" class="btn btn-sm btn-secondary"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
    </div>
</div>

<div class="card" id="printArea">
    <div class="print-only" style="text-align:center;border-bottom:2px solid #0a1f44;padding-bottom:10px;margin-bottom:14px">
        <div style="font-size:18px;font-weight:800;color:#0a1f44">Bharat Electronics Limited &middot; Kotdwar</div>
        <div style="font-size:11px;color:#475569">Government of India, Ministry of Defence &middot; PSU</div>
        <div style="font-size:13px;font-weight:700;margin-top:6px">EMPLOYEE RECORD</div>
    </div>

    <div style="display:grid;grid-template-columns:160px 1fr;gap:20px;align-items:start">
        <div>
            <?php if ($photo): ?>
                <img src="<?= e($photo) ?>" style="width:140px;height:170px;object-fit:cover;border:2px solid #0a1f44;border-radius:6px">
            <?php else: ?>
                <div style="width:140px;height:170px;border:2px dashed #cbd5e1;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#94a3b8;font-size:11px">No Photo</div>
            <?php endif; ?>
        </div>
        <div>
            <table class="no-skin" style="width:100%;border-collapse:collapse;font-size:13px">
                <?php
                $kvs = [
                    'Staff Number' => $emp['staffid'],
                    'Name'         => $emp['username'],
                    'Department'   => $emp['deptt'],
                    'Section'      => $emp['sec'],
                    'Designation'  => $emp['desg'],
                    'Grade'        => $emp['grade'],
                    'Gender'       => $emp['gender'],
                    'Date of Birth'=> $emp['d_o_b'],
                    'IP Phone'     => $emp['ip_phone'],
                    'Phone'        => $emp['phone_no'],
                    'Cost Center'  => $emp['cost_center'],
                    'Subgroup'     => $emp['Employee_Subgroup'],
                ];
                foreach ($kvs as $k => $v):
                ?>
                <tr><td style="padding:6px 12px;font-weight:700;width:35%;background:#f8fafc;border:1px solid #e2e8f0"><?= e($k) ?></td><td style="padding:6px 12px;border:1px solid #e2e8f0"><?= e($v ?: '—') ?></td></tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <h3 style="margin:20px 0 6px;color:#0a1f44;font-size:14px;border-bottom:2px solid #ea7600;padding-bottom:4px"><i class="fa-solid fa-server"></i> Assigned IT Hardware</h3>
    <table style="width:100%;border-collapse:collapse;font-size:12.5px">
        <thead><tr><th style="padding:6px;border:1px solid #cbd5e1;background:#0a1f44;color:#fff">Type</th><th style="padding:6px;border:1px solid #cbd5e1;background:#0a1f44;color:#fff">Asset No</th><th style="padding:6px;border:1px solid #cbd5e1;background:#0a1f44;color:#fff">Serial No</th><th style="padding:6px;border:1px solid #cbd5e1;background:#0a1f44;color:#fff">Model</th></tr></thead>
        <tbody>
        <?php while ($h = mysqli_fetch_assoc($hw)): ?>
            <tr><td style="padding:5px;border:1px solid #cbd5e1"><?= e($h['CATG']) ?></td><td style="padding:5px;border:1px solid #cbd5e1"><?= e($h['HD_ID_NO']) ?></td><td style="padding:5px;border:1px solid #cbd5e1"><?= e($h['MC_SL_NO']) ?></td><td style="padding:5px;border:1px solid #cbd5e1"><?= e($h['MODEL']) ?></td></tr>
        <?php endwhile; ?>
        <?php if (mysqli_num_rows($hw) === 0): ?><tr><td colspan="4" style="padding:8px;color:#94a3b8;text-align:center">No hardware assigned.</td></tr><?php endif; ?>
        </tbody>
    </table>

    <h3 style="margin:20px 0 6px;color:#0a1f44;font-size:14px;border-bottom:2px solid #ea7600;padding-bottom:4px"><i class="fa-solid fa-ticket"></i> Ticket History (last 50)</h3>
    <table style="width:100%;border-collapse:collapse;font-size:12px">
        <thead><tr><th style="padding:6px;border:1px solid #cbd5e1;background:#0a1f44;color:#fff">Ticket #</th><th style="padding:6px;border:1px solid #cbd5e1;background:#0a1f44;color:#fff">Raised</th><th style="padding:6px;border:1px solid #cbd5e1;background:#0a1f44;color:#fff">Asset</th><th style="padding:6px;border:1px solid #cbd5e1;background:#0a1f44;color:#fff">Problem</th><th style="padding:6px;border:1px solid #cbd5e1;background:#0a1f44;color:#fff">Engineer</th><th style="padding:6px;border:1px solid #cbd5e1;background:#0a1f44;color:#fff">Status</th></tr></thead>
        <tbody>
        <?php while ($t = mysqli_fetch_assoc($tix)): ?>
            <tr><td style="padding:5px;border:1px solid #cbd5e1"><b><?= e($t['t_no']) ?></b></td><td style="padding:5px;border:1px solid #cbd5e1;font-size:11px"><?= e($t['r_DateTime']) ?></td><td style="padding:5px;border:1px solid #cbd5e1"><?= e($t['problem_on']) ?></td><td style="padding:5px;border:1px solid #cbd5e1"><?= e($t['problem']) ?></td><td style="padding:5px;border:1px solid #cbd5e1"><?= e($t['support_engg']) ?></td><td style="padding:5px;border:1px solid #cbd5e1"><?= e($t['status']) ?></td></tr>
        <?php endwhile; ?>
        <?php if (mysqli_num_rows($tix) === 0): ?><tr><td colspan="6" style="padding:8px;color:#94a3b8;text-align:center">No tickets raised.</td></tr><?php endif; ?>
        </tbody>
    </table>

    <div class="print-only" style="margin-top:30px;font-size:11px;color:#64748b;display:flex;justify-content:space-between">
        <span>Printed on: <?= e(date('d M Y, H:i')) ?></span>
        <span>BEL Kotdwar &middot; IT Helpdesk Portal</span>
    </div>
</div>
