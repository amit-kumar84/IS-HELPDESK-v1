    </div>
    <footer style="padding:16px 28px;border-top:1px solid var(--c-border);background:#fff;color:var(--c-text-2);font-size:12px;display:flex;justify-content:space-between;flex-wrap:wrap;gap:8px">
        <div>&copy; <?= date('Y') ?> <b style="color:#1e3a8a">Amit Kumar</b> &middot; All Rights Reserved &middot; BEL Kotdwar IT Helpdesk</div>
        <div>Version v7.1.0 &middot; Licensed Software</div>
        <div>Developed by <b style="color:#1e3a8a"><i class="fa-solid fa-code"></i> Amit Kumar</b></div>
    </footer>
</main>
</div>

<script>
// === In-app notification toast (replaces window.alert / window.confirm) ===
(function(){
    if (document.getElementById('iskotToastWrap')) return;
    var wrap = document.createElement('div');
    wrap.id = 'iskotToastWrap';
    document.body.appendChild(wrap);

    var ICONS = {
        success: 'fa-circle-check',
        danger : 'fa-circle-xmark',
        warning: 'fa-triangle-exclamation',
        info   : 'fa-circle-info',
        confirm: 'fa-circle-question'
    };
    var TITLES = {
        success: 'Success',
        danger : 'Error',
        warning: 'Warning',
        info   : 'Notice',
        confirm: 'Confirm'
    };

    function buildToast(opts){
        opts = opts || {};
        var type = opts.type || 'info';
        var t = document.createElement('div');
        t.className = 'iskot-toast ' + type;
        t.innerHTML =
            '<div class="ic"><i class="fa-solid ' + (ICONS[type]||'fa-circle-info') + '"></i></div>' +
            '<div class="body">' +
                '<div class="ttl">' + (opts.title || TITLES[type] || 'Notice') + '</div>' +
                '<div class="msg"></div>' +
                (opts.actions ? '<div class="acts">' +
                    '<button class="ok" type="button">' + (opts.okLabel || 'OK') + '</button>' +
                    '<button class="no" type="button">' + (opts.cancelLabel || 'Cancel') + '</button>' +
                '</div>' : '') +
            '</div>' +
            '<button class="cls" type="button" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>';
        t.querySelector('.msg').textContent = opts.message || '';
        wrap.appendChild(t);
        requestAnimationFrame(function(){ t.classList.add('show'); });

        function dismiss(){
            t.classList.remove('show');
            setTimeout(function(){ if (t.parentNode) t.parentNode.removeChild(t); }, 350);
        }
        t.querySelector('.cls').addEventListener('click', dismiss);

        if (!opts.actions) {
            var ms = (typeof opts.timeout === 'number') ? opts.timeout : 5000;
            if (ms > 0) setTimeout(dismiss, ms);
        }
        return { el: t, dismiss: dismiss };
    }

    window.iskotNotify = function(message, type, title){
        return buildToast({ message: message, type: type || 'info', title: title });
    };
    window.iskotConfirm = function(message, onOk, onCancel, title){
        var ref = buildToast({
            message: message, type: 'confirm', title: title || 'Please confirm',
            actions: true, timeout: 0
        });
        ref.el.querySelector('.acts .ok').addEventListener('click', function(){
            ref.dismiss(); if (typeof onOk === 'function') onOk();
        });
        ref.el.querySelector('.acts .no').addEventListener('click', function(){
            ref.dismiss(); if (typeof onCancel === 'function') onCancel();
        });
        return ref;
    };

    // Override window.alert globally so every legacy alert() pops the pretty toast.
    var nativeAlert = window.alert;
    window.alert = function(msg){
        var s = (msg === null || msg === undefined) ? '' : String(msg);
        // Pick a type from message text (best-effort).
        var type = 'info';
        var low  = s.toLowerCase();
        if (/success|added|updated|deleted|submitted|saved|sent|completed/.test(low)) type = 'success';
        else if (/error|invalid|failed|wrong|not found|denied|please.*resubmit/.test(low)) type = 'danger';
        else if (/warning|please|fill|required|missing|empty/.test(low)) type = 'warning';
        buildToast({ message: s, type: type });
    };
})();

// PHP flash messages already use .alert.* — also surface them via toast
(function(){
    document.querySelectorAll('.alert.alert-success,.alert.alert-danger,.alert.alert-warning,.alert.alert-info').forEach(function(el){
        var type = el.classList.contains('alert-success') ? 'success'
                 : el.classList.contains('alert-danger')  ? 'danger'
                 : el.classList.contains('alert-warning') ? 'warning' : 'info';
        if (window.iskotNotify) window.iskotNotify(el.innerText.trim(), type);
    });
})();

// Pretty-print helper: prints only the element with id="printArea" (CSS handles isolation).
window.printSection = function(id){
    if (id && id !== 'printArea') {
        // Mark the requested element as #printArea temporarily.
        var el = document.getElementById(id);
        if (!el) return window.print();
        var prevId = el.id;
        el.id = 'printArea';
        window.print();
        setTimeout(function(){ el.id = prevId; }, 1000);
    } else {
        window.print();
    }
};
</script>
<script>
// Live clock in topbar
(function(){
    var el = document.getElementById('topClock');
    if(!el) return;
    function tick(){
        var d = new Date();
        var t = d.toLocaleString('en-IN',{weekday:'short',day:'2-digit',month:'short',hour:'2-digit',minute:'2-digit'});
        el.innerHTML = '<i class="fa-regular fa-clock"></i> ' + t;
    }
    tick(); setInterval(tick, 30000);
})();

// Sidebar dropdown groups: click parent to toggle, auto-open if a child is .active
document.querySelectorAll('.nav-group > .nav-toggle').forEach(function(t){
    t.addEventListener('click', function(e){
        e.preventDefault();
        this.parentElement.classList.toggle('open');
    });
});
document.querySelectorAll('.nav-group').forEach(function(g){
    if (g.querySelector('.nav-children .active')) g.classList.add('open');
});
</script>
</body>
</html>
