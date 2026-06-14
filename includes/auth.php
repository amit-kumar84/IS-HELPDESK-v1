<?php
/**
 * Session / auth helpers shared by every page in the app.
 *
 * Call require_role('ISKotAdmin' | 'Engineer' | 'User') at the top of
 * protected pages. 'Admin' is treated as a legacy alias for 'ISKotAdmin'.
 *
 * "Super Admin" is the single account `iskot` in `iskotadmin_login` — it
 * is the only one allowed to manage other ISKot Admin accounts.
 */

/* -------------------------------------------------------------------
 * Production-style error handling (XAMPP defaults to display_errors=on
 * which prints "Undefined variable" warnings straight into the HTML
 * of legacy pages and corrupts forms). We suppress notices / warnings
 * universally — only fatal errors remain visible.
 * ------------------------------------------------------------------*/
@ini_set('display_errors', '0');
@ini_set('display_startup_errors', '0');
@ini_set('html_errors', '0');
error_reporting(E_ERROR | E_PARSE);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

function safe_count($link, $sql) {
    $r = @mysqli_query($link, $sql);
    if (!$r) return 0;
    $row = mysqli_fetch_row($r);
    return (int)($row[0] ?? 0);
}

function current_user_id() {
    return $_SESSION['sid'] ?? '';
}

function current_role() {
    return $_SESSION['login_as'] ?? '';
}

function logged_in() {
    return current_user_id() !== '' && current_role() !== '';
}

/**
 * The "Super Admin" is the single hard-wired account `iskot` —
 * it is the only ISKot Admin allowed to manage other ISKot Admin
 * accounts (add / update / change password / remove).
 */
function SUPER_ADMIN_ID() {
    // Allow the Super Admin ID to be overridden by a file in /memory/
    // This lets the Super Admin account ID be changed safely at runtime.
    $path = __DIR__ . '/../memory/super_admin_id';
    if (is_readable($path)) {
        $v = trim(@file_get_contents($path));
        if ($v !== '') return strtolower($v);
    }
    return 'iskot';
}

function is_super_admin() {
    return current_role() === 'ISKotAdmin'
        && strtolower(trim(current_user_id())) === SUPER_ADMIN_ID();
}

/**
 * Role gate. The admin-side dashboard now belongs to ISKotAdmin
 * (replaces the previous "Administrator" role). We still accept any
 * legacy 'Admin' session for backwards compatibility.
 */
function require_role($role) {
    $cur = current_role();
    $ok  = false;
    if ($role === 'Admin' || $role === 'ISKotAdmin') {
        $ok = ($cur === 'ISKotAdmin' || $cur === 'Admin');
    } else {
        $ok = ($cur === $role);
    }
    if (!logged_in() || !$ok) {
        $param = $role === 'Engineer' ? 'Engineer'
               : (($role === 'Admin' || $role === 'ISKotAdmin') ? 'ISKotAdmin' : 'User');
        header('Location: index.php?login_as=' . $param);
        exit;
    }
}

/** Guard a page so that only the Super Admin can open it. */
function require_super_admin() {
    require_role('ISKotAdmin');
    if (!is_super_admin()) {
        http_response_code(403);
        echo "<div style='padding:60px;text-align:center;font-family:Inter,Arial,sans-serif'>
                <i class='fa-solid fa-lock' style='font-size:48px;color:#dc2626'></i>
                <h2 style='margin:18px 0 8px;color:#0a1f44'>Restricted</h2>
                <p style='color:#475569'>This section is reserved for the <b>Super Admin</b> only.</p>
                <a href='Admin_Home.php' style='color:#1d4ed8;font-weight:600'>&larr; Back to Dashboard</a>
              </div>";
        exit;
    }
}

function initials($s) {
    $s = trim((string)$s);
    if ($s === '') return 'U';
    $parts = preg_split('/\s+/', $s);
    $out = '';
    foreach (array_slice($parts, 0, 2) as $p) {
        $out .= strtoupper(mb_substr($p, 0, 1));
    }
    return $out ?: 'U';
}

/**
 * Tiny flash-message helper (one-shot session messages used by
 * includes/ticket_action.php, includes/info_widgets.php, etc.).
 *
 *   flash_set('success', 'Ticket closed.');
 *   $f = flash_get();        // returns ['type'=>'success','msg'=>'...'] or null
 */
function flash_set($type, $msg) {
    $_SESSION['__flash'] = ['type' => $type, 'msg' => $msg];
}
function flash_get() {
    if (empty($_SESSION['__flash'])) return null;
    $f = $_SESSION['__flash'];
    unset($_SESSION['__flash']);
    return $f;
}
