<?php
/**
 * Live Board — standalone, big-screen-friendly display of IS Helpdesk numbers.
 *
 * - Opens in its OWN browser window (popup) so users can park it on any
 *   screen / TV and resize freely with the mouse.
 * - Auto-refreshes the numbers every 60 s via AJAX (no full reload).
 * - Live clock with seconds + today's date.
 * - Header: "Bharat Electronics Limited, Kotdwar"  / sub: "IS Helpdesk · Live Board"
 * - All cards are controlled by Admin → Live Banner Settings (on / off).
 * - Star Engineer is rendered as a professional animated ID-card.
 *
 *  URL:  live_board.php?mode=full   (default)
 *        live_board.php?mode=strip  (single-row marquee strip — for top/bottom of screen)
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/connection.php';

if (!logged_in()) {
    header('Location: index.php');
    exit;
}

$mode = ($_GET['mode'] ?? 'full') === 'strip' ? 'strip' : 'full';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>BEL Kotdwar · IS Helpdesk · Live Board</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="shortcut icon" href="images/bel.ico" type="image/x-icon">
<link rel="stylesheet" href="assets/fa/all.min.css">
<link rel="stylesheet" href="assets/fa/fonts.css">
<link rel="stylesheet" href="css/live_board.css">
</head>
<body class="lb-body lb-mode-<?= e($mode) ?>" data-testid="live-board">

<!-- ====================== HEADER BAR ====================== -->
<header class="lb-header" id="lbHeader" data-testid="lb-header">
    <div class="lb-brand">
        <img src="images/bel.ico" alt="BEL" class="lb-logo" onerror="this.style.display='none'">
        <div class="lb-titles">
            <h1 class="lb-title">BHARAT ELECTRONICS LIMITED &middot; KOTDWAR</h1>
            <div class="lb-subtitle">
                <i class="fa-solid fa-tower-broadcast"></i>
                <span>IS Helpdesk &middot; Live Board</span>
                <span class="lb-pill"><span class="dot"></span> LIVE</span>
            </div>
        </div>
    </div>
    <div class="lb-clockwrap">
        <div class="lb-date" id="lbDate" data-testid="lb-date">—</div>
        <div class="lb-clock" id="lbClock" data-testid="lb-clock">--:--:--</div>
        <div class="lb-toolbar">
            <button type="button" class="lb-tbtn lb-tbtn-close" id="lbClose" title="Close window" data-testid="lb-close"><i class="fa-solid fa-xmark"></i></button>
        </div>
    </div>
</header>

<!-- ====================== CONTENT ====================== -->
<main class="lb-main">

    <!-- Star Engineer ID Card -->
    <section class="lb-star-wrap" id="lbStarWrap" data-testid="lb-star-wrap"></section>

    <!-- Stat cards grid -->
    <section class="lb-grid" id="lbGrid" data-testid="lb-grid"></section>

    <!-- Ticker (strip-mode only) -->
    <section class="lb-ticker" id="lbTicker" aria-hidden="true"></section>

</main>

<footer class="lb-footer">
    <div>
        <i class="fa-solid fa-circle-info"></i>
        Numbers auto-refresh every 60 seconds &middot;
        Last sync: <span id="lbSync" data-testid="lb-sync">…</span>
    </div>
    <div class="lb-footer-right">
        Information Services &middot; BEL Kotdwar
    </div>
</footer>

<!-- Loader -->
<div class="lb-loader" id="lbLoader"><div class="spinner"></div></div>

<script>
(function () {
    'use strict';

    const REFRESH_MS = 60 * 1000; // 60s
    const grid      = document.getElementById('lbGrid');
    const starWrap  = document.getElementById('lbStarWrap');
    const tickerEl  = document.getElementById('lbTicker');
    const clockEl   = document.getElementById('lbClock');
    const dateEl    = document.getElementById('lbDate');
    const syncEl    = document.getElementById('lbSync');
    const loader    = document.getElementById('lbLoader');
    const body      = document.body;

    // ---------- Clock (every 1 s) ----------
    function pad(n){ return String(n).padStart(2,'0'); }
    function tickClock() {
        const d = new Date();
        // 12-hour format with AM/PM
        let h = d.getHours();
        const ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12; h = h ? h : 12; // convert 0 -> 12
        clockEl.textContent = pad(h) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds()) + ' ' + ampm;
        // Friday, 30 Jan 2026
        dateEl.textContent  = d.toLocaleDateString(undefined, { weekday: 'long', day: '2-digit', month: 'short', year: 'numeric' });
    }
    tickClock();
    setInterval(tickClock, 1000);

    // ---------- Card template ----------
    function fmt(n){ return Number(n).toLocaleString('en-IN'); }
    function cardHTML(c) {
        const hasToday = c.today !== null && c.today !== undefined;
        const bg = 'linear-gradient(135deg,' + c.color_from + ',' + c.color_to + ')';
        return `
            <article class="lb-card" style="background:${bg}" data-testid="lb-card-${c.key}">
                <div class="lb-card-ic"><i class="fa-solid ${c.icon}"></i></div>
                <div class="lb-card-meta">
                    <div class="lb-card-label">${c.label}</div>
                    <div class="lb-card-value">
                        ${
                            hasToday
                            ? `<div class="lb-card-dual">
                                   <div class="lb-card-dual-item">
                                       <span class="lb-card-dual-label">${c.today_label || 'Today'}</span>
                                       <span class="lb-today" data-count="${c.today}">${fmt(c.today)}</span>
                                   </div>
                                   <span class="lb-sep">|</span>
                                   <div class="lb-card-dual-item">
                                       <span class="lb-card-dual-label">${c.total_label || 'Total'}</span>
                                       <span class="lb-total">${fmt(c.total)}</span>
                                   </div>
                               </div>
                               <div class="lb-sub">${c.subtext ? c.subtext : 'today · all-time'}</div>`
                            : `<span class="lb-today big" data-count="${c.total}">${fmt(c.total)}</span>
                               <div class="lb-sub">today</div>`
                        }
                    </div>
                </div>
                <div class="lb-card-shine"></div>
            </article>
        `;
    }

    function starHTML(s) {
        if (!s) return '';
        // join date in DD MMM YYYY
        let joined = '';
        if (s.joining_date && s.joining_date !== '0000-00-00') {
            try {
                const dj = new Date(s.joining_date);
                joined = dj.toLocaleDateString(undefined, { day:'2-digit', month:'short', year:'numeric' });
            } catch(e) {}
        }
        const photo = s.photo
            ? `<img src="${s.photo}" alt="${s.name}">`
            : `<div class="ph">${(s.name||'').substring(0,2).toUpperCase()}</div>`;

        return `
        <div class="lb-idcard" data-testid="lb-idcard">
            <div class="lb-idcard-strap"></div>
            <div class="lb-idcard-clip"></div>

            <div class="lb-idcard-top">
                <img src="images/bel.ico" alt="BEL" onerror="this.style.display='none'">
                <div>
                    <div class="org">BHARAT ELECTRONICS LIMITED</div>
                    <div class="unit">KOTDWAR &middot; INFORMATION SERVICES</div>
                </div>
                <div class="lb-idcard-badge"><i class="fa-solid fa-medal"></i> STAR</div>
            </div>

            <div class="lb-idcard-body">
                <div class="lb-idcard-photo">
                    ${photo}
                    <div class="lb-idcard-rings"><span></span><span></span><span></span></div>
                </div>
                <div class="lb-idcard-info">
                    <div class="row1">
                        <div class="name">${s.name || '—'}</div>
                        <span class="scope ${s.scope === 'today' ? 'today' : 'alltime'}">${s.scope === 'today' ? 'STAR · TODAY' : 'STAR · ALL-TIME'}</span>
                    </div>
                    <div class="kv"><span class="k">EMP ID</span><span class="v mono">${s.enggid || '—'}</span></div>
                    <div class="kv"><span class="k">DESIGNATION</span><span class="v">${s.support_field || '—'}</span></div>
                    <div class="kv"><span class="k">COMPANY</span><span class="v">${s.company || '—'}</span></div>
                    ${joined ? `<div class="kv"><span class="k">JOINED</span><span class="v">${joined}</span></div>` : ''}
                    <div class="lb-idcard-stat">
                        <i class="fa-solid fa-trophy"></i>
                        <b>${fmt(s.count)}</b> tickets resolved
                        <span class="status ${s.presence === 'P' ? 'on' : 'off'}">
                            <i class="fa-solid ${s.presence === 'P' ? 'fa-circle-check' : 'fa-circle'}"></i>
                            ${s.presence === 'P' ? 'On Duty' : 'Off Duty'}
                        </span>
                    </div>
                </div>
            </div>

            <div class="lb-idcard-foot">
                <div class="sig">Authorised by IS Helpdesk</div>
                <div class="bar"><span></span><span></span><span></span><span></span><span></span></div>
            </div>
        </div>`;
    }

    function tickerHTML(cards, star) {
        const parts = cards.map(c => {
            const v = (c.today !== null && c.today !== undefined) ? c.today + ' / ' + c.total : c.total;
            return `<span class="ti"><i class="fa-solid ${c.icon}"></i> ${c.label}: <b>${v}</b></span>`;
        });
        if (star) parts.unshift(`<span class="ti star"><i class="fa-solid fa-star"></i> Star Engineer: <b>${star.name}</b> · ${star.count} resolved</span>`);
        return parts.join('<span class="dot"></span>');
    }

    // ---------- Animated number increment ----------
    function animateNumbers(scope) {
        scope.querySelectorAll('[data-count]').forEach(el => {
            const target = +el.dataset.count;
            if (!isFinite(target)) return;
            const dur = 700;
            const start = performance.now();
            const initial = 0;
            function step(now) {
                const p = Math.min(1, (now - start) / dur);
                const v = Math.round(initial + (target - initial) * (1 - Math.pow(1 - p, 3)));
                el.textContent = v.toLocaleString('en-IN');
                if (p < 1) requestAnimationFrame(step);
            }
            requestAnimationFrame(step);
        });
    }

    // ---------- Fetch + render ----------
    let isFirst = true;
    async function load(silent) {
        if (!silent) loader.classList.add('on');
        try {
            const res = await fetch('live_board_data.php', { credentials: 'same-origin', cache: 'no-store' });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const data = await res.json();
            grid.innerHTML     = data.cards.map(cardHTML).join('');
            starWrap.innerHTML = starHTML(data.star);
            tickerEl.innerHTML = tickerHTML(data.cards, data.star);
            syncEl.textContent = new Date().toLocaleTimeString();
            if (isFirst) { animateNumbers(grid); isFirst = false; }
            else { grid.querySelectorAll('.lb-card').forEach(el => el.classList.add('flash')); setTimeout(() => grid.querySelectorAll('.lb-card').forEach(el => el.classList.remove('flash')), 700); }
        } catch (err) {
            syncEl.textContent = '⚠ ' + err.message;
        } finally {
            loader.classList.remove('on');
        }
    }
    load(false);
    setInterval(() => load(true), REFRESH_MS);

    // ---------- Toolbar buttons ----------
    document.getElementById('lbClose').addEventListener('click', () => { if (window.opener) window.close(); else history.back(); });

    // F11-style full screen on double-click of header
    document.getElementById('lbHeader').addEventListener('dblclick', () => {
        if (!document.fullscreenElement) document.documentElement.requestFullscreen?.();
        else document.exitFullscreen?.();
    });
})();
</script>
</body>
</html>
