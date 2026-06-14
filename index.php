<?php
error_reporting(0);
ob_start();
require_once 'includes/auth.php';
require_once 'connection.php';
$loginAs = $_GET['login_as'] ?? 'User';
// Backwards-compat: any old links pointing to 'SubAdmin' or 'Admin' fold into ISKotAdmin.
if ($loginAs === 'SubAdmin' || $loginAs === 'Admin') $loginAs = 'ISKotAdmin';

$roleMap = [
    'User'       => ['Employee Login',    'fa-user',        'Sign in to register complaints, request cartridges and track your tickets.'],
    'Engineer'   => ['Engineer Login',    'fa-user-gear',   'Attend assigned tickets, manage hardware and update statuses.'],
    'ISKotAdmin' => ['ISKot Admin Login', 'fa-user-shield', 'Master control for the entire IS-Kotdwar Helpdesk &mdash; tickets, cartridges, hardware, engineers and users.'],
];
$current = $roleMap[$loginAs] ?? $roleMap['User'];
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="author" content="Amit Kumar">
<meta name="copyright" content="© 2026 Amit Kumar. All Rights Reserved.">
<meta name="description" content="BEL Kotdwar IT Helpdesk Portal — developed by Amit Kumar">
<title>BEL Kotdwar &middot; IT Helpdesk &middot; <?= e($current[0]) ?></title>
<link rel="stylesheet" href="assets/css/app.css">
<link rel="stylesheet" href="assets/fa/all.min.css">
<link rel="stylesheet" href="assets/fa/fonts.css">
<link rel="shortcut icon" href="images/bel.ico">
</head>
<body class="landing-page">
<div class="landing">

<?php $hide_context = true; include 'includes/gov_header.php'; ?>

<section class="info-section" style="padding:14px 0;background:#f4f6fb;border-bottom:1px solid #e2e8f0;margin:0">
    <div class="info-inner" style="max-width:none;margin:0;padding:0">
        <?php
        // Load announcement and info cards for index page
        $news_rows   = @mysqli_query($link, "SELECT * FROM news_items WHERE is_active=1 ORDER BY sort_order ASC, id DESC");
        $form_rows   = @mysqli_query($link, "SELECT * FROM form_downloads WHERE is_active=1 ORDER BY sort_order ASC, id ASC");
        $__ann_q = @mysqli_query($link, "SELECT title, link FROM news_items WHERE is_active=1 AND is_new=1 ORDER BY sort_order ASC LIMIT 1");
        $ann     = $__ann_q ? mysqli_fetch_assoc($__ann_q) : null;
        ?>

        <?php if ($ann): ?>
        <div class="announcement-bar">
            <span class="lbl">Update / Announcement</span>
            <div class="scroll"><span><span class="new-flag">NEW</span> * <?= e($ann['title']) ?> &nbsp;&nbsp;&nbsp; <span class="new-flag">NEW</span> * <?= e($ann['title']) ?></span></div>
        </div>
        <?php endif; ?>
    </div>
</section>

<section class="hero" style="margin:0;padding:20px;width:100%;background:#f4f6fb">
    <div class="hero-inner" style="display:grid;grid-template-columns:1.5fr 3.4fr 1.5fr;gap:20px;align-items:stretch;min-height:auto;margin:0 auto;padding:0;width:100%;max-width:1400px">
        <!-- LEFT: Forms Download -->
        <div class="info-card forms" style="height:auto;min-height:400px;display:flex;flex-direction:column;border:2px solid #ea7600;box-shadow:0 8px 32px -8px rgba(234,118,0,.35);transition:all .3s ease;animation:slideInLeft .6s ease-out;margin:0;padding:0;border-radius:0">
            <div class="ic-head" style="background:linear-gradient(135deg,#ea7600,#c2410c);padding:14px 16px;font-weight:700;display:flex;align-items:center;gap:10px"><i class="fa-solid fa-cloud-arrow-down" style="font-size:16px"></i> प्रपत्र डाउनलोड &middot; Forms Download</div>
            <div class="ic-body" style="flex:1;overflow-y:auto;background:#fafaf9;padding:14px 16px">
                <ul>
                <?php 
                $form_rows   = @mysqli_query($link, "SELECT * FROM form_downloads WHERE is_active=1 ORDER BY sort_order ASC, id ASC");
                while ($form_rows && ($f = mysqli_fetch_assoc($form_rows))):
                    $exists = is_file($f['file_path']);
                    $href = $f['file_path'];
                    $is_url = (strpos($f['file_path'], 'http') === 0);
                    
                    if ($is_url) {
                        $extra_attrs = 'target="_blank" rel="noopener"';
                    } elseif ($exists) {
                        $extra_attrs = 'target="_blank" rel="noopener"';
                    } else {
                        $extra_attrs = 'onclick="alert(\'Form file not yet uploaded by admin.\');return false"';
                    }
                ?>
                    <li style="padding:10px 12px;margin:6px 0;border-radius:8px;background:#fff;border-left:4px solid #ea7600;transition:all .2s ease;cursor:pointer;display:flex;align-items:flex-start;gap:12px;box-shadow:0 2px 8px rgba(15,23,42,.08)">
                        <i class="fa-solid <?= e($f['icon']) ?> lead" style="color:#ea7600;margin-top:2px;font-size:14px;min-width:20px;text-align:center"></i>
                        <div style="flex:1">
                            <a href="<?= e($href) ?>" <?= $extra_attrs ?> style="color:#0a1f44;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;transition:all .2s ease" onmouseover="this.style.color='#ea7600';this.style.textDecoration='underline'" onmouseout="this.style.color='#0a1f44';this.style.textDecoration='none'">
                                <?= e($f['title']) ?>
                                <?php if ($exists && !$is_url): ?><i class="fa-solid fa-download" style="font-size:11px;opacity:.7"></i><?php endif; ?>
                                <?php if ($is_url): ?><i class="fa-solid fa-arrow-up-right-from-square" style="font-size:11px;opacity:.7"></i><?php endif; ?>
                            </a>
                        </div>
                    </li>
                <?php endwhile; ?>
                </ul>
            </div>
        </div>

        <!-- CENTER: Hero / Login -->
        <div style="padding:20px;display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:center;height:auto">
            <div>
                <div class="eyebrow">भारत सरकार &middot; GOVERNMENT OF INDIA &middot; PSU</div>
                <h1>Secure IT Service Desk for <span style="color:#1e3a8a">BEL Kotdwar.</span></h1>
                <p>Register complaints, track tickets, manage cartridges, monitor hardware and supervise the entire support team &mdash; a single, professional portal for the Kotdwar unit.</p>

                <div class="role-switch" data-testid="role-switch">
                    <a href="index.php?login_as=User"       class="<?= $loginAs==='User' ? 'active' : '' ?>"       data-testid="role-user"><i class="fa-solid fa-user"></i> &nbsp;Employee</a>
                    <a href="index.php?login_as=Engineer"   class="<?= $loginAs==='Engineer' ? 'active' : '' ?>"   data-testid="role-engineer"><i class="fa-solid fa-user-gear"></i> &nbsp;Engineer</a>
                    <a href="index.php?login_as=ISKotAdmin" class="<?= $loginAs==='ISKotAdmin' ? 'active' : '' ?>" data-testid="role-admin"><i class="fa-solid fa-user-shield"></i> &nbsp;ISKot&nbsp;Admin</a>
                </div>

                <p style="font-size:12px;color:#64748b;margin-top:14px">For password issues contact <b style="color:#0a1f44">M&amp;ES (43660 / 660)</b>. New accounts are issued by the BEL administrator only.</p>
            </div>

            <div class="landing-card" data-testid="login-card" style="margin:0;height:fit-content">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:6px">
                    <div style="width:44px;height:44px;border-radius:10px;background:linear-gradient(135deg,#0a1f44,#1e3a8a);display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px">
                        <i class="fa-solid <?= e($current[1]) ?>"></i>
                    </div>
                    <div>
                        <h2><?= e($current[0]) ?></h2>
                        <div class="sub" style="margin:2px 0 0"><?= $current[2] ?></div>
                    </div>
                </div>

                <?php
                switch($loginAs){
                    case 'User':       include('auth/user_login.php');      break;
                    case 'Apprentice': include('auth/Appren_login.php');    break;
                    case 'Engineer':   include('auth/engineer_login.php');  break;
                    case 'ISKotAdmin': include('auth/ISKotAdminLogin.php'); break;
                    default:           include('auth/user_login.php');
                }
                ?>
            </div>
        </div>

        <!-- RIGHT: Latest News -->
        <div class="info-card news" style="height:auto;min-height:400px;display:flex;flex-direction:column;border:2px solid #138808;box-shadow:0 8px 32px -8px rgba(19,136,8,.35);transition:all .3s ease;animation:slideInRight .6s ease-out;margin:0;padding:0;border-radius:0">
            <div class="ic-head" style="background:linear-gradient(135deg,#138808,#0e6b07);padding:14px 16px;font-weight:700;display:flex;align-items:center;gap:10px"><i class="fa-solid fa-bullhorn" style="font-size:16px"></i> ताज़ा समाचार &middot; Latest News</div>
            <div class="ic-body" style="flex:1;overflow-y:auto;background:#f0fdf4;padding:14px 16px">
                <ul>
                <?php 
                $news_rows   = @mysqli_query($link, "SELECT * FROM news_items WHERE is_active=1 ORDER BY sort_order ASC, id DESC");
                while ($news_rows && ($n = mysqli_fetch_assoc($news_rows))): ?>
                    <li style="padding:10px 12px;margin:6px 0;border-radius:8px;background:#fff;border-left:4px solid #138808;transition:all .2s ease;cursor:pointer;display:flex;align-items:flex-start;gap:10px;box-shadow:0 2px 8px rgba(15,23,42,.08)">
                        <i class="fa-solid fa-circle lead" style="font-size:6px;margin-top:8px;color:#138808;min-width:14px;text-align:center"></i>
                        <div style="flex:1">
                            <?php if ($n['is_new']): ?><span class="new-pill" style="background:#ef4444;color:#fff;font-size:9.5px;font-weight:800;padding:2px 8px;border-radius:4px;margin-right:6px;letter-spacing:.5px;display:inline-block;animation:pulse 1.8s infinite">NEW</span><?php endif; ?>
                            <?php if (!empty($n['link']) && $n['link'] !== '#'): ?>
                                <a href="<?= e($n['link']) ?>" target="_blank" rel="noopener" style="color:#0a1f44;font-weight:600;text-decoration:none;display:block;transition:all .2s ease;margin-top:2px" onmouseover="this.style.color='#138808';this.style.textDecoration='underline'" onmouseout="this.style.color='#0a1f44';this.style.textDecoration='none'"><?= e($n['title']) ?></a>
                            <?php else: ?>
                                <span style="color:#0a1f44;font-weight:600;display:block;margin-top:2px"><?= e($n['title']) ?></span>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endwhile; ?>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="about-section">
    <div class="about-inner">
        <div>
            <h3>About BEL Kotdwar IT Helpdesk</h3>
            <p>The IT Helpdesk portal is the official internal service-management platform for the Kotdwar unit of <b>Bharat Electronics Limited</b> &mdash; a Navratna Public Sector Undertaking under the Ministry of Defence, Government of India.</p>
            <p>This portal centralises ticket registration, cartridge management, hardware asset tracking and engineer assignment across all sections of the unit, ensuring transparent, traceable and timely IT support for every employee.</p>
            <p>The system follows the <b>ISO 27001:2013 Information Security Management System</b> guidelines and is restricted to authenticated BEL personnel only.</p>
        </div>
        <div class="quick-facts">
            <div class="qf-title">Quick Facts</div>
            <ul>
                <li>&middot; Established: <b>1954</b></li>
                <li>&middot; Status: <b>Navratna PSU</b></li>
                <li>&middot; Ministry: <b>Defence (MoD), GoI</b></li>
                <li>&middot; HQ: <b>Bengaluru, Karnataka</b></li>
                <li>&middot; Unit: <b>Kotdwar, Uttarakhand</b></li>
                <li>&middot; ISO 27001:2013 &middot; ISMS Certified</li>
            </ul>
        </div>
    </div>
</section>

<section class="features-section">
    <div class="features-inner">
        <div class="feature-card">
            <div class="ic"><i class="fa-solid fa-ticket"></i></div>
            <h4>Smart Ticketing</h4>
            <p>Raise, assign, escalate and resolve incidents with one-click status transitions and a complete audit trail.</p>
        </div>
        <div class="feature-card">
            <div class="ic"><i class="fa-solid fa-chart-pie"></i></div>
            <h4>Live Dashboards</h4>
            <p>Real-time counters for pending, in-progress and solved tickets, broken down by department, engineer and asset.</p>
        </div>
        <div class="feature-card">
            <div class="ic"><i class="fa-solid fa-user-shield"></i></div>
            <h4>Role-based Access</h4>
            <p>Distinct workflows for Employees, Engineers and ISKot Admins with photo-enabled profiles and secure session management.</p>
        </div>
    </div>
</section>

<footer class="landing-foot">
    <div class="left"><b>BEL Kotdwara</b> &middot; IT Helpdesk Portal v7.1 &middot; &copy; <?= date('Y') ?> <b>Amit Kumar</b> &middot; All Rights Reserved</div>
    <div class="right">Developed by <b style="color:#1e3a8a">Amit Kumar</b> &middot; Licensed Software &middot; सर्वाधिकार सुरक्षित</div>
</footer>
<div class="tricolor-strip thin"></div>

</div>
</body>
</html>
