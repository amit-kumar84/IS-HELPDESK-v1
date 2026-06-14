<?php
/**
 * Ticket / Call workflow handler.
 *
 *  Endpoint: includes/ticket_action.php (POST or GET)
 *
 *  Supported actions:
 *    - attend           (Pending → Attend by current actor)
 *    - solve            (Pending/Attend → Solved + solution text)
 *    - close            (any → Closed)
 *    - reopen           (Closed → Pending, clears engineer)
 *    - assign           (admin only — assigns ticket to chosen engineer by name)
 *    - status_update    (admin only — set status to any of Pending/Attend/Solved/Closed)
 *
 *  Allowed roles: ISKotAdmin (incl. legacy 'Admin') and Engineer.
 */
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/connection.php';

if (!logged_in() || !in_array(current_role(), ['ISKotAdmin','Admin','Engineer'], true)) {
    http_response_code(403);
    flash_set('danger', 'Not allowed.');
    header('Location: ../index.php');
    exit;
}

$is_admin = in_array(current_role(), ['ISKotAdmin','Admin'], true);

$t_no_raw   = $_POST['t_no']   ?? $_GET['t_no']   ?? '';
$t_no = [];
if (is_array($t_no_raw)) {
    foreach ($t_no_raw as $item) {
        $item = trim($item);
        if ($item !== '') $t_no[] = $item;
    }
} else {
    $item = trim($t_no_raw);
    if ($item !== '') $t_no[] = $item;
}
$action = trim($_POST['action'] ?? $_GET['action'] ?? '');
$actor  = $_SESSION['user_name'] ?? current_user_id();
$soln   = trim($_POST['solution'] ?? '');
/* Handle assignee as array (from checkboxes) or as single string */
$assignee_raw = $_POST['assignee'] ?? [];
if (is_array($assignee_raw)) {
    $filtered = [];
    foreach ($assignee_raw as $a) {
        $a = trim($a);
        if ($a !== '') $filtered[] = $a;
    }
    $assignee = implode(', ', $filtered);
} else {
    $assignee = trim($assignee_raw);
}
$new_status = trim($_POST['new_status'] ?? '');
$return = $_SERVER['HTTP_REFERER'] ?? '../Admin_Home.php?AdminTab=All_Calls';
$primary_t_no = $t_no[0] ?? '';

if (empty($t_no) || $action === '') {
    flash_set('danger', 'Missing ticket or action.');
    header('Location: ' . $return);
    exit;
}

$now = date('Y-m-d H:i:s');

switch ($action) {

    case 'attend':
        $stmt = mysqli_prepare($link, "UPDATE complain_register
            SET status='Attend', support_engg=?, s_DateTime=?
            WHERE t_no=? AND status='Pending'");
        mysqli_stmt_bind_param($stmt, 'sss', $actor, $now, $primary_t_no);
        $stmt->execute();
        $ok = mysqli_stmt_affected_rows($stmt) > 0;
        flash_set($ok ? 'success' : 'warning',
            $ok ? "Ticket $primary_t_no marked In-Progress (assigned to $actor)."
                : "Ticket $primary_t_no could not be moved to In-Progress.");
        break;

    case 'solve':
        $stmt = mysqli_prepare($link, "UPDATE complain_register
            SET status='Solved', solution=?, support_engg=COALESCE(NULLIF(support_engg,''), ?), s_DateTime=?
            WHERE t_no=? AND status IN ('Pending','Attend')");
        mysqli_stmt_bind_param($stmt, 'ssss', $soln, $actor, $now, $primary_t_no);
        $stmt->execute();
        $ok = mysqli_stmt_affected_rows($stmt) > 0;
        flash_set($ok ? 'success' : 'warning',
            $ok ? "Ticket $primary_t_no marked Solved."
                : "Ticket $primary_t_no could not be marked Solved.");
        break;

    case 'close':
        $stmt = mysqli_prepare($link, "UPDATE complain_register
            SET status='Closed',
                s_DateTime = IF(s_DateTime IS NULL OR s_DateTime='', ?, s_DateTime)
            WHERE t_no=? AND status IN ('Pending','Attend','Solved')");
        mysqli_stmt_bind_param($stmt, 'ss', $now, $primary_t_no);
        $stmt->execute();
        $ok = mysqli_stmt_affected_rows($stmt) > 0;
        flash_set($ok ? 'success' : 'warning',
            $ok ? "Ticket $primary_t_no closed." : "Could not close $primary_t_no.");
        break;

    case 'reopen':
        $stmt = mysqli_prepare($link, "UPDATE complain_register
            SET status='Pending', solution='', support_engg='', s_DateTime=''
            WHERE t_no=?");
        mysqli_stmt_bind_param($stmt, 's', $primary_t_no);
        $stmt->execute();
        flash_set('success', "Ticket $primary_t_no re-opened.");
        break;

    /* ============================================================
       NEW — Admin-only: assign a ticket to a specific engineer
       Moves Pending → Attend (or keeps current non-closed status)
       and stamps the chosen engineer's name into support_engg.
       ============================================================ */
    case 'assign':
        if (!$is_admin) {
            flash_set('danger', 'Only admins can re-assign tickets.');
            break;
        }
        if ($assignee === '') {
            flash_set('warning', 'No engineer selected.');
            break;
        }
        $placeholders = implode(',', array_fill(0, count($t_no), '?'));
        $sql = "UPDATE complain_register
            SET support_engg = ?,
                status       = CASE WHEN status='Pending' THEN 'Attend' ELSE status END,
                s_DateTime   = CASE WHEN s_DateTime IS NULL OR s_DateTime='' THEN ? ELSE s_DateTime END
            WHERE t_no IN ($placeholders) AND status <> 'Closed'";
        $stmt = mysqli_prepare($link, $sql);
        $params = array_merge([$assignee, $now], $t_no);
        $types = str_repeat('s', count($params));
        $refs = [];
        foreach ($params as $key => $value) {
            $refs[$key] = &$params[$key];
        }
        array_unshift($refs, $types);
        call_user_func_array([$stmt, 'bind_param'], $refs);
        $stmt->execute();
        $ok = mysqli_stmt_affected_rows($stmt) > 0;
        flash_set($ok ? 'success' : 'warning',
            $ok ? "Ticket(s) " . implode(', ', $t_no) . " assigned to $assignee."
                : "Could not re-assign selected ticket(s) (they may already be closed)."
); 
        break;

    /* ============================================================
       NEW — Admin-only: force status update to ANY state.
       Optional 'solution' / 'assignee' can also be set in one shot.
       ============================================================ */
    case 'status_update':
        if (!$is_admin) {
            flash_set('danger', 'Only admins can override status.');
            break;
        }
        $allowed_statuses = ['Pending','Attend','Solved','Closed'];
        if (!in_array($new_status, $allowed_statuses, true)) {
            flash_set('warning', 'Invalid status.');
            break;
        }
        // Build dynamic SQL based on what was provided
        $sets = ["status = ?"];
        $vals = [$new_status];
        $types = "s";
        if ($soln !== '') {
            $sets[] = "solution = ?";
            $vals[] = $soln;
            $types .= "s";
        }
        if ($assignee !== '') {
            $sets[] = "support_engg = ?";
            $vals[] = $assignee;
            $types .= "s";
        }
        // Stamp the action time
        $sets[] = "s_DateTime = ?";
        $vals[] = $now;
        $types .= "s";

        // If re-opening to Pending, clear engineer + solution
        if ($new_status === 'Pending') {
            $sets   = ["status = 'Pending'", "support_engg = ''", "solution = ''", "s_DateTime = ''"];
            $vals   = [];
            $types  = "";
        }

        $placeholders = implode(',', array_fill(0, count($t_no), '?'));
        $sql = "UPDATE complain_register SET " . implode(', ', $sets) . " WHERE t_no IN ($placeholders)";
        $vals = array_merge($vals, $t_no);
        $types .= str_repeat('s', count($t_no));

        $stmt = mysqli_prepare($link, $sql);
        $refs = [];
        foreach ($vals as $key => $value) {
            $refs[$key] = &$vals[$key];
        }
        array_unshift($refs, $types);
        call_user_func_array([$stmt, 'bind_param'], $refs);
        $stmt->execute();
        $ok = mysqli_stmt_affected_rows($stmt) > 0;
        flash_set($ok ? 'success' : 'warning',
            $ok ? "Ticket $primary_t_no status set to $new_status."
                : "No change for ticket $primary_t_no.");
        break;

    default:
        flash_set('danger', "Unknown action '$action'.");
}

header('Location: ' . $return);
exit;
