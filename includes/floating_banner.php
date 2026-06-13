<?php
/**
 * Floating Helpdesk Banner — live stats strip on every dashboard.
 *
 * • Pulls today + lifetime counters from complain_register.
 * • Each card renders only if banner_settings.is_enabled = 1.
 * • Card format: "today | total"  (e.g. "5 | 14,742").
 * • Star engineer = engineer with the most Solved tickets today,
 *   rendered as a compact animated ID-card.
 * • Auto-refreshes numbers every 60 s via AJAX (no full reload).
 * • Has an "Open in separate window" button to pop out the standalone
 *   Live Board for big-screen displays.
 *
 * Available variables (already set by parent page):
 *   $link  → mysqli connection
 *   safe_count() helper from includes/auth.php
 */

if (!isset($link)) return;

// Today key for matching DD-MM-YYYY style strings in complain_register
$__today = date('d-m-Y');

$banner_stats = [
    'total_calls'      => [
        'total' => safe_count($link, "SELECT COUNT(*) FROM complain_register"),
        'today' => safe_count($link, "SELECT COUNT(*) FROM complain_register WHERE r_DateTime LIKE '$__today%'"),
    ],
    'total_unassigned' => [
        'total' => safe_count($link, "SELECT COUNT(*) FROM complain_register WHERE status='Pending'"),
        'today' => safe_count($link, "SELECT COUNT(*) FROM complain_register WHERE status='Pending' AND r_DateTime LIKE '$__today%'"),
    ],
    'total_attend'     => [
        'total' => safe_count($link, "SELECT COUNT(*) FROM complain_register WHERE status='Attend'"),
        'today' => safe_count($link, "SELECT COUNT(*) FROM complain_register WHERE status='Attend' AND s_DateTime LIKE '$__today%'"),
    ],
    'total_solved'     => [
        'total' => safe_count($link, "SELECT COUNT(*) FROM complain_register WHERE status='Solved'"),
        'today' => safe_count($link, "SELECT COUNT(*) FROM complain_register WHERE status='Solved' AND s_DateTime LIKE '$__today%'"),
    ],
    'total_closed'     => [
        'total' => safe_count($link, "SELECT COUNT(*) FROM complain_register WHERE status='Closed'"),
        'today' => safe_count($link, "SELECT COUNT(*) FROM complain_register WHERE status='Closed' AND s_DateTime LIKE '$__today%'"),
    ],
    'today_incoming'   => [
        'total' => safe_count($link, "SELECT COUNT(*) FROM complain_register WHERE r_DateTime LIKE '$__today%'"),
        'today' => null,
    ],
    'today_attend'     => [
        'total' => safe_count($link, "SELECT COUNT(*) FROM complain_register WHERE status='Attend' AND s_DateTime LIKE '$__today%'"),
        'today' => null,
    ],
    'today_solved'     => [
        'total' => safe_count($link, "SELECT COUNT(*) FROM complain_register WHERE status='Solved' AND s_DateTime LIKE '$__today%'"),
        'today' => null,
    ],
    'today_closed'     => [
        'total' => safe_count($link, "SELECT COUNT(*) FROM complain_register WHERE status='Closed' AND s_DateTime LIKE '$__today%'"),
        'today' => null,
    ],
    'active_engineers' => [
        'total' => safe_count($link, "SELECT COUNT(*) FROM s_engg_login WHERE status='0'"),
        'today' => safe_count($link, "SELECT COUNT(*) FROM s_engg_login WHERE status='0' AND presence='P'"),
    ],
];

// Star engineer of the day (most Solved tickets today). Falls back to all-time leader if today is empty.
$star = null;
$starQ = @mysqli_query($link,
    "SELECT support_engg AS name, COUNT(*) AS c
     FROM complain_register
     WHERE status IN ('Solved','Closed')
       AND s_DateTime LIKE '$__today%'
       AND support_engg <> ''
     GROUP BY support_engg
     ORDER BY c DESC LIMIT 1");
if ($starQ && mysqli_num_rows($starQ) > 0) {
    $star = mysqli_fetch_assoc($starQ);
    $star['scope'] = 'today';
} else {
    $starQ = @mysqli_query($link,
        "SELECT support_engg AS name, COUNT(*) AS c
         FROM complain_register
         WHERE status IN ('Solved','Closed') AND support_engg <> ''
         GROUP BY support_engg
         ORDER BY c DESC LIMIT 1");
    if ($starQ && mysqli_num_rows($starQ) > 0) {
        $star = mysqli_fetch_assoc($starQ);
        $star['scope'] = 'all-time';
    }
}
$star_eid = $star_company = $star_field = $star_presence = '';
if ($star) {
    $eq = @mysqli_query($link, "SELECT enggid, company, support_field, presence FROM s_engg_login
                                WHERE engg_name='" . mysqli_real_escape_string($link, $star['name']) . "' LIMIT 1");
    if ($eq && ($er = mysqli_fetch_assoc($eq))) {
        $star_eid      = $er['enggid'];
        $star_company  = $er['company'];
        $star_field    = $er['support_field'];
        $star_presence = $er['presence'];
    }
}
$star_photo = '';
if ($star_eid !== '') {
    foreach ([
        'images/engineers/'.$star_eid.'.JPG','images/engineers/'.$star_eid.'.jpg','images/engineers/'.$star_eid.'.png',
        'Pictures/'.$star_eid.'.JPG','Pictures/'.$star_eid.'.jpg','Pictures/'.$star_eid.'.png'
    ] as $cand) {
        if (file_exists($cand)) { $star_photo = $cand; break; }
    }
}

// Pull which cards are enabled
$cards = [];
$cq = @mysqli_query($link, "SELECT * FROM banner_settings ORDER BY sort_order ASC");
if ($cq) while ($r = mysqli_fetch_assoc($cq)) {
    if (!$r['is_enabled']) continue;
    $cards[$r['setting_key']] = $r;
}

// Keep only core ticket cards on the floating banner to avoid clutter.
// Allowed keys should match the live board whitelist.
$allowed = ['total_calls', 'total_unassigned', 'total_attend', 'total_solved'];
foreach (array_keys($cards) as $k) {
    if ($k === 'star_engineer') continue; // keep star if enabled
    if (!in_array($k, $allowed, true)) unset($cards[$k]);
}

// Nothing enabled? Render nothing.
if (!$cards) return;
?>

<div class="float-banner" id="floatBanner" data-testid="float-banner">
    <div class="fb-head">
        <div class="fb-title">
            <i class="fa-solid fa-tower-broadcast"></i>
            <div class="fb-title-text">
                <b>Bharat Electronics Limited, Kotdwar</b>
                <span class="fb-sub">IS Helpdesk &middot; Live Board</span>
            </div>
            <span class="fb-date" id="fbDate" data-testid="fb-date"><?= e(date('D, d M Y')) ?></span>
            <span class="fb-clock" id="fbClock" data-testid="fb-clock">--:--:--</span>
        </div>
        <div class="fb-actions">
            <span class="fb-pill"><i class="fa-solid fa-circle pulse"></i> Live · auto-refresh 60s</span>
            <button type="button" class="fb-tbtn" id="fbRefresh" title="Refresh now" data-testid="fb-refresh"><i class="fa-solid fa-rotate"></i></button>
            <button type="button" class="fb-tbtn fb-popout" id="fbPopout" title="Open live board in separate window" data-testid="fb-popout"><i class="fa-solid fa-up-right-from-square"></i> Pop-out</button>
            <button type="button" class="fb-toggle" id="fbCollapse" title="Show / hide" data-testid="fb-collapse"><i class="fa-solid fa-chevron-down"></i></button>
        </div>
    </div>

    <div class="fb-body" id="fbBody">
        <?php foreach ($cards as $key => $cfg):
            if ($key === 'star_engineer') continue; // rendered separately
            $stat = $banner_stats[$key] ?? null;
            if (!$stat) continue;
            $total_n = (int)$stat['total'];
            $today_n = $stat['today'];
            $from = $cfg['color_from'] ?: '#1e3a8a';
            $to   = $cfg['color_to']   ?: '#3b82f6';
        ?>
            <div class="fb-card" style="background:linear-gradient(135deg, <?= e($from) ?>, <?= e($to) ?>)" data-key="<?= e($key) ?>" data-testid="fb-card-<?= e($key) ?>">
                <div class="fb-ic"><i class="fa-solid <?= e($cfg['icon']) ?>"></i></div>
                <div class="fb-meta">
                    <div class="fb-lbl"><?= e($cfg['label']) ?></div>
                    <div class="fb-val">
                        <?php if ($today_n !== null): ?>
                            <span class="today" data-fb-today><?= number_format($today_n) ?></span>
                            <span class="sep">|</span>
                            <span class="tot" data-fb-total><?= number_format($total_n) ?></span>
                        <?php else: ?>
                            <span class="today big" data-fb-total><?= number_format($total_n) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (isset($cards['star_engineer']) && $star):
            $cfg = $cards['star_engineer'];
            $from = $cfg['color_from']; $to = $cfg['color_to'];
        ?>
            <div class="fb-card star" data-key="star_engineer" data-testid="fb-card-star">
                <span class="fb-id-strap"></span>
                <span class="fb-id-clip"></span>
                <div class="fb-id-photo">
                    <?php if ($star_photo): ?>
                        <img src="<?= e($star_photo) ?>" alt="<?= e($star['name']) ?>">
                    <?php else: ?>
                        <div class="ph"><?= e(strtoupper(mb_substr($star['name'], 0, 2))) ?></div>
                    <?php endif; ?>
                    <span class="ring"></span><span class="ring"></span>
                </div>
                <div class="fb-id-info">
                    <div class="fb-id-row1">
                        <div class="fb-id-name" data-fb-star-name><?= e($star['name']) ?></div>
                        <span class="fb-id-scope <?= $star['scope']==='today'?'today':'alltime' ?>" data-fb-star-scope>
                            <i class="fa-solid fa-medal"></i> STAR · <?= $star['scope'] === 'today' ? 'TODAY' : 'ALL-TIME' ?>
                        </span>
                    </div>
                    <div class="fb-id-kv"><span class="k">ID</span><span class="v mono" data-fb-star-id><?= e($star_eid ?: '—') ?></span><span class="k" style="margin-left:10px">DESIGNATION</span><span class="v" data-fb-star-field><?= e($star_field ?: '—') ?></span></div>
                    <div class="fb-id-kv"><span class="k">COMPANY</span><span class="v" data-fb-star-company><?= e($star_company ?: '—') ?></span></div>
                    <div class="fb-id-stat">
                        <i class="fa-solid fa-trophy"></i>
                        <b data-fb-star-count><?= number_format((int)$star['c']) ?></b> tickets resolved
                        <span class="fb-id-status <?= $star_presence==='P'?'on':'off' ?>" data-fb-star-status>
                            <i class="fa-solid <?= $star_presence==='P'?'fa-circle-check':'fa-circle' ?>"></i>
                            <?= $star_presence === 'P' ? 'On Duty' : 'Off Duty' ?>
                        </span>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
(function () {
    var btn      = document.getElementById('fbCollapse');
    var body     = document.getElementById('fbBody');
    var banner   = document.getElementById('floatBanner');
    var popBtn   = document.getElementById('fbPopout');
    var refBtn   = document.getElementById('fbRefresh');
    var clockEl  = document.getElementById('fbClock');
    var dateEl   = document.getElementById('fbDate');

    if (!banner) return;

    // ---- collapse state restore ----
    try {
        if (localStorage.getItem('fb_collapsed') === '1') {
            banner.classList.add('collapsed');
            btn && btn.querySelector('i') && (btn.querySelector('i').className = 'fa-solid fa-chevron-up');
        }
    } catch(e){}

    btn && btn.addEventListener('click', function () {
        banner.classList.toggle('collapsed');
        var col = banner.classList.contains('collapsed');
        btn.querySelector('i').className = col ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down';
        try { localStorage.setItem('fb_collapsed', col ? '1' : '0'); } catch(e){}
    });

    // ---- live clock (1 s) ----
    function pad(n){ return String(n).padStart(2,'0'); }
    function tick() {
        var d = new Date();
        if (clockEl) clockEl.textContent = pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
        if (dateEl)  dateEl.textContent  = d.toLocaleDateString(undefined, { weekday: 'short', day: '2-digit', month: 'short', year: 'numeric' });
    }
    tick(); setInterval(tick, 1000);

    // ---- pop-out into separate resizable window ----
    popBtn && popBtn.addEventListener('click', function () {
        var w = Math.min(screen.availWidth, 1400), h = Math.min(screen.availHeight, 800);
        var l = (screen.availWidth  - w) / 2;
        var t = (screen.availHeight - h) / 2;
        window.open('live_board.php', 'IS_LIVE_BOARD',
            'width=' + w + ',height=' + h + ',left=' + l + ',top=' + t +
            ',resizable=yes,scrollbars=yes,toolbar=no,menubar=no,location=no,status=no');
    });

    // ---- AJAX auto-refresh every 60s ----
    function fmt(n){ return Number(n).toLocaleString('en-IN'); }
    function flash(card) {
        card.classList.add('fb-flash');
        setTimeout(function(){ card.classList.remove('fb-flash'); }, 700);
    }
    function refresh() {
        fetch('live_board_data.php', { credentials: 'same-origin', cache: 'no-store' })
            .then(function(r){ if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
            .then(function(d) {
                // cards
                (d.cards || []).forEach(function (c) {
                    var card = banner.querySelector('.fb-card[data-key="' + c.key + '"]');
                    if (!card) return;
                    var todayEl = card.querySelector('[data-fb-today]');
                    var totalEl = card.querySelector('[data-fb-total]');
                    var changed = false;
                    if (todayEl) {
                        var t = fmt(c.today);
                        if (todayEl.textContent !== t) { todayEl.textContent = t; changed = true; }
                    }
                    if (totalEl) {
                        var v = fmt(c.total);
                        if (totalEl.textContent !== v) { totalEl.textContent = v; changed = true; }
                    }
                    if (changed) flash(card);
                });

                // star engineer
                var sCard = banner.querySelector('.fb-card.star');
                if (sCard && d.star) {
                    var nm = sCard.querySelector('[data-fb-star-name]');
                    var ct = sCard.querySelector('[data-fb-star-count]');
                    var sp = sCard.querySelector('[data-fb-star-scope]');
                    var st = sCard.querySelector('[data-fb-star-status]');
                    var fd = sCard.querySelector('[data-fb-star-field]');
                    var cp = sCard.querySelector('[data-fb-star-company]');
                    var idEl = sCard.querySelector('[data-fb-star-id]');
                    if (nm) nm.textContent = d.star.name;
                    if (ct) ct.textContent = fmt(d.star.count);
                    if (idEl) idEl.textContent = d.star.enggid || '—';
                    if (sp) {
                        sp.innerHTML = '<i class="fa-solid fa-medal"></i> STAR · ' + (d.star.scope === 'today' ? 'TODAY' : 'ALL-TIME');
                        sp.className = 'fb-id-scope ' + (d.star.scope === 'today' ? 'today' : 'alltime');
                    }
                    if (st) {
                        var onDuty = d.star.presence === 'P';
                        st.className = 'fb-id-status ' + (onDuty ? 'on' : 'off');
                        st.innerHTML = '<i class="fa-solid ' + (onDuty ? 'fa-circle-check' : 'fa-circle') + '"></i> ' + (onDuty ? 'On Duty' : 'Off Duty');
                    }
                    if (fd) fd.textContent = d.star.support_field || '—';
                    if (cp) cp.textContent = d.star.company || '—';
                }
            })
            .catch(function(){ /* silent */ });
    }
    refBtn && refBtn.addEventListener('click', function(){ refBtn.classList.add('spin'); refresh(); setTimeout(function(){ refBtn.classList.remove('spin'); }, 800); });
    setInterval(refresh, 60 * 1000);
})();
</script>
