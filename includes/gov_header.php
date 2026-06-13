<?php
/** GoI + BEL Kotdwar header — animated tricolor + digital clock */
?>
<div class="tricolor-strip"></div>
<div class="gov-bar">
    <div class="gov-left">
        <span class="flag-emoji">&#127470;&#127475;</span>
        <span>भारत सरकार &middot; <b style="color:#fff">GOVERNMENT OF INDIA</b></span>
        <span class="sep">|</span>
        <span>Ministry of Defence &middot; Public Sector Undertaking</span>
    </div>
    <div class="gov-right">
        <a href="#" onclick="document.body.style.fontSize='12px';return false;" title="Smaller">A-</a>
        <a href="#" onclick="document.body.style.fontSize='';return false;" title="Default">A</a>
        <a href="#" onclick="document.body.style.fontSize='15px';return false;" title="Larger">A+</a>
        <span style="color:#475569">|</span>
        <a href="#" title="हिंदी"><b>हिंदी</b></a>
    </div>
</div>
<header class="bel-header">
    <div class="bel-logo">
        <img src="images/Bharat_Electronics_logo.png" alt="BEL Logo">
    </div>
    <div class="bel-center">
        <h1>Bharat Electronics Limited</h1>
        <div class="sub">GOVERNMENT OF INDIA, Ministry of Defence &middot; Public Sector Undertaking, KOTDWAR</div>
        <div class="unit">IT HELPDESK PORTAL &middot; KOTDWAR UNIT</div>
    </div>
    <div class="bel-right">
        <div class="status-pill"><span class="dot"></span> SYSTEM ONLINE</div>
        <div class="bel-clock" id="belClock">
            <span class="digit" id="bcH">00</span>
            <span class="sep">:</span>
            <span class="digit" id="bcM">00</span>
            <span class="sep">:</span>
            <span class="digit" id="bcS">00</span>
            <span class="ampm" id="bcA">AM</span>
        </div>
        <div class="bel-date" id="belDate">--</div>
    </div>
</header>
<?php if (empty($hide_context)): ?>
<div class="context-bar">
    <div><i class="fa-solid fa-shield-halved"></i> &nbsp;<?= e($context_left ?? 'ONLINE IT HELPDESK · BEL KOTDWAR UNIT') ?></div>
    <div class="ctx-right"><?= e($context_right ?? 'Ministry of Defence · Public Sector Undertaking') ?></div>
</div>
<?php endif; ?>
<script>(function(){
    function pad(n){return n<10?'0'+n:''+n;}
    function tick(){
        var d = new Date();
        var h = d.getHours(), ap = h>=12 ? 'PM' : 'AM';
        h = h % 12; if (h===0) h = 12;
        var H = document.getElementById('bcH'); if(H) H.textContent = pad(h);
        var M = document.getElementById('bcM'); if(M) M.textContent = pad(d.getMinutes());
        var S = document.getElementById('bcS'); if(S) S.textContent = pad(d.getSeconds());
        var A = document.getElementById('bcA'); if(A) A.textContent = ap;
        var D = document.getElementById('belDate');
        if (D) D.textContent = d.toLocaleDateString('en-IN',{weekday:'short',day:'2-digit',month:'short',year:'numeric'});
    }
    tick(); setInterval(tick, 1000);
})();</script>
