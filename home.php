<?php
require_once 'includes/auth.php';
require_once 'connection.php';
require_role('user');

$sid = current_user_id();
$tab = $_GET['UserTab'] ?? 'DashBoard';

// Pull profile
$stmt = mysqli_prepare($link, "SELECT username, desg, deptt FROM emp_details WHERE staffid = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $sid);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: [];
$userName    = $row['username'] ?? ($_SESSION['user_name'] ?? 'Employee');
$userRoleLbl = 'Employee · ' . ($row['deptt'] ?? '');

$titles = [
    'DashBoard' => ['My Profile', 'User / Dashboard'],
    'ComplainRegistrationForm' => ['Register Complaint', 'Service / New'],
    'CartridgeRequestForm' => ['Cartridge Request', 'Service / Cartridge'],
    'CloseComplain' => ['Close Complaint', 'Service / Close'],
    'CallEnquiry' => ['My Complaints', 'Activity / Complaints'],
    'CartridgeEnquiry' => ['My Cartridge Orders', 'Activity / Cartridges'],
    'ChangePassword' => ['Change Password', 'Account / Password'],
    'NDA_Agreement' => ['NDA Agreement', 'Compliance / NDA'],
    'Storage_hd' => ['Storage Device Data', 'Hardware / Storage'],
    'Search_emp' => ['Search Employee', 'Tools / Search'],
];
[$pageHeading, $crumbs] = $titles[$tab] ?? ['Dashboard', 'User'];
$pageTitle = 'ISKOT · ' . $pageHeading;

include 'includes/header.php';
include 'includes/sidebar_user.php';
include 'includes/topbar.php';
?>

<script>window.setTimeout(function(){location='logout.php'}, 15*60*1000);</script>

<?php
switch ($tab) {
    case 'DashBoard':              include 'user/userdashboard.php'; break;
    case 'ComplainRegistrationForm':include 'user/RequestForm.php'; break;
    case 'SendRequest':            include 'user/ConfirmRequest.php'; break;
    case 'CartridgeRequestForm':   include 'user/cartridgeForm.php'; break;
    case 'CloseComplain':          include 'user/Feedback.php'; break;
    case 'CallEnquiry':            include 'user/Enquiry.php'; break;
    case 'CartridgeEnquiry':       include 'user/CartridgeUserEnquiry.php'; break;
    case 'ChangePassword':         include 'user/ChangePassword.php'; break;
    case 'UpdateAssetNo':          include 'admin/UpdateAssetNo.php'; break;
    case 'AssetNoDetails':         include 'shared/asset_no_details.php'; break;
    case 'NDA_Agreement':          include 'user/NDAagreement.php'; break;
    case 'Storage_hd':             include 'shared/hardware_storage_master.php'; break;
    case 'Search_emp':             include 'shared/SearchEmployee.php'; break;
    case 'dataUpdate':             include 'admin/UserDataUpdate.php'; break;
    default:                       include 'user/userdashboard.php';
}
?>

<?php include 'includes/footer_app.php'; ?>
