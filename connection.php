<?php
/**
 * MySQL connection (XAMPP-compatible).
 *
 * Defaults match the XAMPP defaults (host=localhost, user=root, no password).
 * To override on dev/staging, set the environment variables:
 *   DB_HOST, DB_USER, DB_PASS, DB_NAME
 */

/* Suppress notices / warnings on legacy pages — XAMPP defaults to
 * display_errors=on which prints PHP warnings straight into the HTML
 * of forms and corrupts the UI. Only fatal errors remain visible. */
@ini_set('display_errors', '0');
@ini_set('display_startup_errors', '0');
@ini_set('html_errors', '0');
error_reporting(E_ERROR | E_PARSE);

$mysql_host = getenv('DB_HOST') ?: '127.0.0.1';
$username   = getenv('DB_USER') ?: 'root';
$password   = getenv('DB_PASS') ?: '';
$dbname     = getenv('DB_NAME') ?: 'hardware_master';

mysqli_report(MYSQLI_REPORT_OFF);
$link = mysqli_connect($mysql_host, $username, $password, $dbname);

if (!$link) {
    die(
        "<div style='font-family:Inter,Arial,sans-serif; max-width:560px; margin:80px auto;
                     padding:32px; background:#fff; border-radius:14px;
                     box-shadow:0 18px 48px -16px rgba(15,23,42,.18); text-align:center;'>
            <div style='font-size:48px;'>&#9888;</div>
            <h2 style='margin:8px 0 4px;color:#dc2626;'>Database Connection Failed</h2>
            <p style='color:#475569;margin:0;'>Please contact your administrator to resolve this issue.</p>
        </div>"
    );
}

mysqli_set_charset($link, 'utf8mb4');

/**
 * Auto-migration — creates the four CMS-related tables if a freshly imported
 * `hardware_master` doesn't yet have them. Idempotent and silent.
 */
@mysqli_query($link, "CREATE TABLE IF NOT EXISTS suggestions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_staff_id VARCHAR(50),
    user_name VARCHAR(150),
    user_dept VARCHAR(150),
    user_role VARCHAR(50),
    subject VARCHAR(255),
    message TEXT,
    is_read TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

@mysqli_query($link, "CREATE TABLE IF NOT EXISTS news_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    link VARCHAR(500) DEFAULT '#',
    is_new TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

@mysqli_query($link, "CREATE TABLE IF NOT EXISTS form_downloads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    file_path VARCHAR(500),
    icon VARCHAR(80) DEFAULT 'fa-file-pdf',
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

@mysqli_query($link, "CREATE TABLE IF NOT EXISTS notice_board (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    body TEXT,
    icon VARCHAR(80) DEFAULT 'fa-bullhorn',
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

@mysqli_query($link, "CREATE TABLE IF NOT EXISTS banner_settings (
    setting_key VARCHAR(50) PRIMARY KEY,
    is_enabled TINYINT(1) DEFAULT 1,
    label VARCHAR(120),
    icon VARCHAR(60),
    color_from VARCHAR(20),
    color_to   VARCHAR(20),
    sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Seed banner_settings if empty
$__bs_check = @mysqli_query($link, "SELECT COUNT(*) c FROM banner_settings");
$__bs_row   = $__bs_check ? mysqli_fetch_assoc($__bs_check) : ['c' => 1];
if ((int)($__bs_row['c'] ?? 1) === 0) {
    @mysqli_query($link, "INSERT INTO banner_settings VALUES
        ('total_calls',     1, 'Total Tickets',         'fa-ticket',           '#1e3a8a', '#3b82f6', 1),
        ('total_unassigned',1, 'Open / Unassigned',    'fa-hourglass-half',   '#b45309', '#f59e0b', 2),
        ('total_attend',    1, 'In Progress',           'fa-spinner',          '#7c3aed', '#a78bfa', 3),
        ('total_solved',    1, 'Total Solved',          'fa-circle-check',     '#15803d', '#22c55e', 4),
        ('total_closed',    1, 'Total Closed',          'fa-lock',             '#334155', '#64748b', 5),
        ('today_incoming',  1, 'Today · Incoming',      'fa-inbox',            '#0e7490', '#06b6d4', 6),
        ('today_attend',    1, 'Today · Attended',      'fa-person-running',   '#9a3412', '#f97316', 7),
        ('today_solved',    1, 'Today · Solved',        'fa-check-double',     '#166534', '#10b981', 8),
        ('today_closed',    1, 'Today · Closed',        'fa-flag-checkered',   '#475569', '#94a3b8', 9),
        ('active_engineers',1, 'Active Engineers',      'fa-user-gear',        '#1d4ed8', '#60a5fa', 10),
        ('star_engineer',   1, 'Star Engineer (Today)', 'fa-star',             '#a16207', '#facc15', 11)");
}

// Seed once if news_items is empty (first install)
$__seed_check = @mysqli_query($link, "SELECT COUNT(*) c FROM news_items");
$__seed_row   = $__seed_check ? mysqli_fetch_assoc($__seed_check) : ['c' => 1];
if ((int)($__seed_row['c'] ?? 1) === 0) {
    @mysqli_query($link, "INSERT INTO news_items (title, link, is_new, sort_order) VALUES
        ('IT Helpdesk Portal — newly redesigned UI launched.', '#', 1, 1),
        ('Cartridge requests now route to in-charge automatically.', '#', 1, 2),
        ('Annual asset verification drive starts next month.', '#', 0, 3)");
    @mysqli_query($link, "INSERT INTO form_downloads (title, file_path, icon, sort_order) VALUES
        ('New PC / Printer Request Form', 'forms/pc_request.pdf', 'fa-file-pdf', 1),
        ('Cartridge Request Form',        'forms/cartridge.pdf', 'fa-file-pdf', 2),
        ('Asset Transfer Form',           'forms/asset_transfer.pdf', 'fa-file-pdf', 3)");
    @mysqli_query($link, "INSERT INTO notice_board (title, body, icon, sort_order) VALUES
        ('Server Maintenance Window', 'Every Saturday 8:00 PM – 10:00 PM. Plan your work accordingly.', 'fa-server', 1),
        ('Password Policy',           'Change passwords every 90 days. Contact M&ES for resets.', 'fa-lock', 2),
        ('Phishing Awareness',        'Do not click suspicious links. Report to IT immediately.', 'fa-shield', 3)");
}
