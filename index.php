<?php
error_reporting(0);
ob_start();
require_once 'includes/auth.php';
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

<section class="hero">
    <div class="hero-inner">
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

        <div class="landing-card" data-testid="login-card">
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
