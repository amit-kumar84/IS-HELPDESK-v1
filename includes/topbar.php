</aside>

<main class="app-main">
    <header class="app-topbar">
        <button
            type="button"
            class="menu-toggle"
            id="sidebarToggle"
            data-testid="sidebar-toggle"
            title="Show / hide sidebar"
            aria-label="Toggle sidebar">
            <i class="fa-solid fa-bars"></i>
        </button>
        <h1><?= e($pageHeading ?? '') ?></h1>
        <div class="topbar-actions">
            <span class="clock" id="topClock"></span>
        </div>
    </header>

    <div class="app-content">

<?php
$__flash = flash_get();
if ($__flash) {
    echo '<div class="alert alert-' . e($__flash['type']) . '"><i class="fa-solid fa-circle-info"></i> ' . e($__flash['msg']) . '</div>';
}
?>

<script>
(function(){
    // Hide / show sidebar — works on both desktop (collapse) and mobile (open).
    var btn  = document.getElementById('sidebarToggle');
    var side = document.getElementById('appSidebar');
    var body = document.body;
    if (!btn || !side) return;

    // Restore last state on desktop.
    try {
        if (window.matchMedia('(min-width:901px)').matches
            && localStorage.getItem('iskot_sidebar') === 'collapsed') {
            body.classList.add('sidebar-collapsed');
        }
    } catch(e){}

    btn.addEventListener('click', function(){
        if (window.matchMedia('(max-width:900px)').matches) {
            // Mobile: slide drawer in / out
            side.classList.toggle('open');
        } else {
            // Desktop: collapse / restore
            body.classList.toggle('sidebar-collapsed');
            try {
                localStorage.setItem(
                    'iskot_sidebar',
                    body.classList.contains('sidebar-collapsed') ? 'collapsed' : 'expanded'
                );
            } catch(e){}
        }
    });
})();
</script>
