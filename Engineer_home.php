<?php
require_once 'includes/auth.php';
ob_start();
require_once 'connection.php';
require_role('Engineer');

$sid = current_user_id();
$tab = $_GET['EngineerTab'] ?? 'Dashboard';

// Pull engineer profile
$stmt = mysqli_prepare($link, "SELECT engg_name, support_field FROM s_engg_login WHERE enggid = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $sid);
mysqli_stmt_execute($stmt);
$engRow = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: [];
$userName    = $engRow['engg_name'] ?? ($_SESSION['user_name'] ?? 'Engineer');
$userRoleLbl = 'Engineer · ' . ($engRow['support_field'] ?? '');

$titles = [
    'Dashboard' => ['Dashboard', 'Engineer / Dashboard'],
    'CallGenerate' => ['Generate Call', 'Calls / New'],
    'All_Calls' => ['All Calls', 'Calls / All'],
    'Pending_Calls' => ['Unassigned Calls', 'Calls / Pending'],
    'Attend_Calls' => ['In-Progress Calls', 'Calls / Attending'],
    'Solved_Calls' => ['Solved Calls', 'Calls / Solved'],
    'View_Calls' => ['Date-wise Call Search', 'Calls / Search'],
    'CartridgeReqGenerate' => ['Cartridge Request', 'Cartridges / New'],
    'CartridgePendingRequest' => ['Pending Cartridge Requests', 'Cartridges / Pending'],
    'RemoveCartridgeReq' => ['Remove Cartridge Request', 'Cartridges / Remove'],
    'Issue' => ['Issue Hardware to User', 'Hardware / Issue'],
    'Hardware_Details' => ['Hardware Details', 'Hardware / List'],
    'VerifiedAssetList' => ['Verified Asset List', 'Assets / Verified'],
    'UserContactUpdate' => ['User Contact Update', 'Users / Contact'],
    'Search_Employee' => ['Search Employee', 'Tools / Search'],
    'ChangePassword' => ['Change Password', 'Account / Password'],
];
[$pageHeading, $crumbs] = $titles[$tab] ?? ['Engineer Panel', 'Engineer'];
$pageTitle = 'ISKOT Engineer · ' . $pageHeading;

include 'includes/header.php';
include 'includes/sidebar_engineer.php';
include 'includes/topbar.php';
?>

<?php
switch ($tab) {
    case 'Dashboard':              include 'engineer/engineer_dashboard.php'; break;
    case 'CallGenerate':           include 'shared/CallGenerate.php'; break;
    case 'All_Calls':              include 'shared/AllCalls.php'; break;
    case 'Pending_Calls':          include 'engineer/PendingCalls.php'; break;
    case 'Attend_Calls':           include 'engineer/AttendCalls.php'; break;
    case 'Solved_Calls':           include 'engineer/SolvedCalls.php'; break;
    case 'View_Calls':             include 'shared/ViewCalls.php'; break;
    case 'CartridgeReqGenerate':   include 'shared/CartridgeRequestGenerate.php'; break;
    case 'CartridgePendingRequest':include 'shared/CartridgeRequest.php'; break;
    case 'RemoveCartridgeReq':     include 'admin/RemoveCartridgeReq.php'; break;
    case 'Issue':                  include 'shared/HardwareIssueToUser.php'; break;
    case 'Hardware_Details':       include 'shared/Hardware_Details.php'; break;
    case 'VerifiedAssetList':      include 'engineer/verifiedAssetList.php'; break;
    case 'UserContactUpdate':      include 'engineer/user_contact_update.php'; break;
    case 'Search_Employee':        include 'shared/SearchEmployee.php'; break;
    case 'ChangePassword':         include 'engineer/E_ChangePassword.php'; break;
    default:                       include 'engineer/engineer_dashboard.php';
}
?>

<?php include 'includes/footer_app.php'; ?>
