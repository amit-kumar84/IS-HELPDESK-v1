<?php
/**
 * Admin · Live Banner Settings
 *
 * On / off toggle switches for every metric card shown in the floating
 * IS-Helpdesk live banner. Admin can also re-order cards and edit their
 * label / colour gradient.
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['act'] ?? '';

    if ($act === 'toggle') {
        $key = $_POST['key'] ?? '';
        $stmt = mysqli_prepare($link, "UPDATE banner_settings SET is_enabled = 1 - is_enabled WHERE setting_key = ?");
        mysqli_stmt_bind_param($stmt, 's', $key);
        mysqli_stmt_execute($stmt);
        flash_set('success', 'Card visibility updated.');
    }
    elseif ($act === 'enable_all' || $act === 'disable_all') {
        $v = $act === 'enable_all' ? 1 : 0;
        mysqli_query($link, "UPDATE banner_settings SET is_enabled = $v");
        flash_set('success', $v ? 'All cards enabled.' : 'All cards disabled.');
    }
    elseif ($act === 'edit') {
        $key   = $_POST['key'] ?? '';
        $lbl   = trim($_POST['label']      ?? '');
        $icon  = trim($_POST['icon']       ?? '');
        $from  = trim($_POST['color_from'] ?? '');
        $to    = trim($_POST['color_to']   ?? '');
        $sort  = (int)($_POST['sort_order']?? 0);
        if ($key && $lbl) {
            $stmt = mysqli_prepare($link,
                "UPDATE banner_settings
                 SET label=?, icon=?, color_from=?, color_to=?, sort_order=?
                 WHERE setting_key=?");
            mysqli_stmt_bind_param($stmt, 'sssiss', $lbl, $icon, $from, $to, $sort, $key);
            // bind types: 5 strings + 1 int. Fix:
        }
        // Simpler: use direct escaped query
        $stmt2 = mysqli_prepare($link,
            "UPDATE banner_settings SET label=?, icon=?, color_from=?, color_to=?, sort_order=? WHERE setting_key=?");
        mysqli_stmt_bind_param($stmt2, 'ssssis', $lbl, $icon, $from, $to, $sort, $key);
        mysqli_stmt_execute($stmt2);
        flash_set('success', 'Card details updated.');
    }
    header('Location: Admin_Home.php?AdminTab=BannerSettings');
    exit;
}

$rows = mysqli_query($link, "SELECT * FROM banner_settings ORDER BY sort_order ASC");
$enabledCount = safe_count($link, "SELECT COUNT(*) FROM banner_settings WHERE is_enabled=1");
$totalCount   = safe_count($link, "SELECT COUNT(*) FROM banner_settings");
?>

<div class="page-head">
    <div class="ic"><i class="fa-solid fa-tower-broadcast"></i></div>
    <div>
        <h2>Live Banner · Card Controls</h2>
        <div class="sub">Toggle each statistic card on / off for the floating helpdesk banner. Changes appear instantly on every dashboard.</div>
    </div>
    <div class="actions">
        <span style="font-size:12px;color:#475569;align-self:center;margin-right:8px">
            <b style="color:#0a1f44"><?= $enabledCount ?></b> / <?= $totalCount ?> active
        </span>
        <form method="post" style="display:inline">
            <input type="hidden" name="act" value="enable_all">
            <button class="btn btn-sm" data-testid="bs-enable-all"><i class="fa-solid fa-eye"></i> Enable all</button>
        </form>
        <form method="post" style="display:inline">
            <input type="hidden" name="act" value="disable_all">
            <button class="btn btn-sm btn-secondary" data-testid="bs-disable-all" style="background:#f1f5f9;color:#0a1f44"><i class="fa-solid fa-eye-slash"></i> Disable all</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-title"><i class="fa-solid fa-toggle-on"></i> Card visibility</div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(330px,1fr));gap:12px">
        <?php while ($r = mysqli_fetch_assoc($rows)):
            $on = (int)$r['is_enabled'] === 1;
        ?>
            <div class="card" style="margin:0;border:2px solid <?= $on ? '#22c55e' : '#e2e8f0' ?>;padding:12px" data-testid="bs-row-<?= e($r['setting_key']) ?>">
                <div style="display:flex;align-items:center;gap:11px">
                    <div style="width:42px;height:42px;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px;background:linear-gradient(135deg,<?= e($r['color_from']) ?>,<?= e($r['color_to']) ?>);box-shadow:0 4px 10px -2px rgba(0,0,0,.2)">
                        <i class="fa-solid <?= e($r['icon']) ?>"></i>
                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="font-weight:700;color:#0a1f44;font-size:13.5px"><?= e($r['label']) ?></div>
                        <div style="font-size:11px;color:#64748b;font-family:'JetBrains Mono',monospace"><?= e($r['setting_key']) ?></div>
                    </div>
                    <!-- toggle -->
                    <form method="post" style="margin:0">
                        <input type="hidden" name="act" value="toggle">
                        <input type="hidden" name="key" value="<?= e($r['setting_key']) ?>">
                        <button type="submit" class="bs-switch <?= $on ? 'on' : '' ?>" data-testid="bs-toggle-<?= e($r['setting_key']) ?>" title="<?= $on ? 'Click to hide' : 'Click to show' ?>">
                            <span class="knob"></span>
                        </button>
                    </form>
                </div>

                <details style="margin-top:10px">
                    <summary style="cursor:pointer;font-size:11.5px;color:#1e3a8a;font-weight:600"><i class="fa-solid fa-sliders"></i> Edit label & colour</summary>
                    <form method="post" style="margin-top:10px;display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:12px">
                        <input type="hidden" name="act" value="edit">
                        <input type="hidden" name="key" value="<?= e($r['setting_key']) ?>">
                        <label style="grid-column:1/-1">Label
                            <input type="text" name="label" value="<?= e($r['label']) ?>" required>
                        </label>
                        <label>Icon (FA name)
                            <input type="text" name="icon" value="<?= e($r['icon']) ?>" placeholder="fa-ticket">
                        </label>
                        <label>Sort order
                            <input type="number" name="sort_order" value="<?= (int)$r['sort_order'] ?>">
                        </label>
                        <label>Colour from
                            <input type="color" name="color_from" value="<?= e($r['color_from']) ?>" style="height:34px;padding:2px">
                        </label>
                        <label>Colour to
                            <input type="color" name="color_to"   value="<?= e($r['color_to'])   ?>" style="height:34px;padding:2px">
                        </label>
                        <button class="btn btn-sm" type="submit" style="grid-column:1/-1"><i class="fa-solid fa-save"></i> Save</button>
                    </form>
                </details>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<style>
/* iOS-style toggle switch */
.bs-switch{
    width:48px;height:26px;background:#cbd5e1;border:0;border-radius:99px;cursor:pointer;
    position:relative;transition:background .25s;flex-shrink:0;
}
.bs-switch .knob{
    position:absolute;top:3px;left:3px;width:20px;height:20px;border-radius:50%;
    background:#fff;box-shadow:0 2px 6px rgba(0,0,0,.25);transition:left .22s cubic-bezier(.4,1.5,.55,1);
}
.bs-switch.on{background:#22c55e}
.bs-switch.on .knob{left:25px}
.bs-switch:hover{filter:brightness(1.05)}
</style>
