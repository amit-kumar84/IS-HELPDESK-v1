<?php
require_once 'includes/auth.php';
ob_start();
require_once 'connection.php';
require_role('ISKotAdmin');

$sid = current_user_id();

// Pull ISKot Admin profile (with legacy admin_login fallback for older sessions).
$adminRow = [];
$stmt = mysqli_prepare($link, "SELECT adminName FROM iskotadmin_login WHERE adminid = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $sid);
mysqli_stmt_execute($stmt);
$adminRow = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: [];
if (!$adminRow) {
    $stmt = mysqli_prepare($link, "SELECT adminName FROM admin_login WHERE adminid = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 's', $sid);
    mysqli_stmt_execute($stmt);
    $adminRow = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: [];
}
$userName    = $adminRow['adminName'] ?? ($_SESSION['user_name'] ?? 'ISKot Administrator');
$userRoleLbl = is_super_admin() ? 'Super Admin' : 'ISKot Administrator';

// Accept both new (AdminTab) and legacy (ISKotAdminTab) query parameters.
$tab = $_GET['AdminTab'] ?? $_GET['ISKotAdminTab'] ?? 'Dashboard';

// Friendly page heading per tab
$titles = [
    'Dashboard' => ['Dashboard', 'Admin / Dashboard'],
    'CallGenerateByAdmin' => ['Generate Ticket', 'Tickets / New'],
    'All_Calls' => ['All Tickets', 'Tickets / All'],
    'Pending_Calls' => ['Unassigned Tickets', 'Tickets / Pending'],
    'Attend_Calls' => ['In-Progress Tickets', 'Tickets / Attending'],
    'Solved_Calls' => ['Solved Tickets', 'Tickets / Solved'],
    'Closed_Calls' => ['Closed Tickets', 'Tickets / Closed'],
    'CallReport' => ['Tickets Report', 'Reports / Tickets'],
    'asset_wise_report' => ['Asset-wise Report', 'Reports / Asset'],
    'user_wise_report' => ['User-wise Report', 'Reports / User'],
    'View_Calls' => ['Date-wise Ticket Search', 'Reports / Date'],
    'Hardware_Details' => ['Hardware Details', 'Hardware / List'],
    'PC_Printer_Details' => ['PC / Printer Details', 'Hardware / PC&Printer'],
    'NewEntry' => ['Add New Hardware', 'Hardware / Add'],
    'ChangeHardwareDetails' => ['Edit Hardware Details', 'Hardware / Edit'],
    'Issue' => ['Issue Hardware to User', 'Hardware / Issue'],
    'AddtoMS' => ['Return Hardware to MS', 'Hardware / Return'],
    'CartridgeReqGenerate' => ['Cartridge Request', 'Cartridges / New'],
    'CartridgePendingRequest' => ['Pending Cartridge Requests', 'Cartridges / Pending'],
    'CartridgeIssue' => ['Cartridge Issue List', 'Cartridges / Issues'],
    'CartridgeWiseIssue' => ['Cartridge-wise Issue List', 'Cartridges / Issues'],
    'CartridgeStock' => ['Cartridge Stock', 'Cartridges / Stock'],
    'PrinterCartridgeNew' => ['Add Printer-Cartridge', 'Cartridges / Add Printer'],
    'UpdatePrinterCartridgeNew' => ['Edit Printer-Cartridge', 'Cartridges / Edit Printer'],
    'AddNewCartridge' => ['Add New Cartridge', 'Cartridges / Add Stock'],
    'ReceivedCartridgeStock' => ['Received Cartridge Stock', 'Cartridges / Received'],
    'PrinterWiseCartridgeStock' => ['Printer-wise Cartridge Stock', 'Cartridges / Printer-wise'],
    'Suggestions' => ['User Suggestions', 'Communications / Suggestions'],
    'BulkImport' => ['Bulk Import Users', 'Users / Bulk Import'],
    'EditUser' => ['Edit Employee', 'Users / Edit'],
    'PrintEmployee' => ['Print Employee Record', 'Users / Print'],
    'AddNewUser' => ['Add New User', 'Users / Add'],
    'ManageUsers' => ['Manage Users', 'Users / Manage'],
    'Search_Employee' => ['Search Employee', 'Users / Search'],
    'Update_Employee_Req' => ['Employee Update Requests', 'Users / Update Req.'],
    'AddEngineer' => ['Add Engineer', 'Engineers / Add'],
    'EngineerList' => ['Engineer List', 'Engineers / List'],
    'EditEngineer' => ['Edit Engineer Details', 'Engineers / Edit'],
    'RemoveEngineer' => ['Remove Engineer', 'Engineers / Remove'],
    'ActDeactEngg' => ['Activate / Deactivate Engineer', 'Engineers / Status'],
    'P_Engineer' => ['Engineer Presence', 'Engineers / Presence'],
    'ChangePassword' => ['Change Password', 'Account / Password'],
    'ContentManager' => ['Content Manager', 'Communications / Content'],
    'BannerSettings' => ['Live Banner Settings', 'Communications / Live Banner'],

    // Super-admin only
    'ManageAdmins' => ['Manage ISKot Admins', 'Super Admin / Admins'],

    // Inherited from the legacy ISKot Admin dashboard
    'RemoveCall'              => ['Remove Ticket',                'Tickets / Remove'],
    'ChangeCallReport'        => ['Change Ticket Details',        'Tickets / Edit'],
    'sec_wise_report'         => ['Section-wise Ticket Report',   'Reports / Section'],
    'AdminViewCall'           => ['Date-wise Tickets Record',     'Reports / Date'],
    'Hardware_total_list'     => ['Total Asset Summary',          'Hardware / Summary'],
    'verified_asset_list'     => ['Verified Asset List',          'Hardware / Verified'],
    'Hardware_Storage_List'   => ['Hardware Storage List',        'Hardware / Storage'],
    'CCTV_List'               => ['CCTV Asset List',              'Hardware / CCTV'],
    'Hardware_Details_iskot'  => ['ISKOT Asset List',             'Hardware / ISKOT'],
    'Hardware_Details_wo'     => ['Write-off Asset List',         'Hardware / Write-off'],
    'Hardware_Details_standby'=> ['Standby Asset List',           'Hardware / Standby'],
    'software_list'           => ['Software List',                'Hardware / Software'],
    'internet_list'           => ['Internet User List',           'Hardware / Internet'],
    'OS_list'                 => ['OS-Based System List',         'Hardware / OS'],
    'AssetNoList'             => ['Asset No. List',               'Hardware / Asset No'],
    'ChangeAssetNo'           => ['Update Asset No.',             'Hardware / Update No'],
    'Remove_Hardware'         => ['Remove Hardware',              'Hardware / Remove'],
    'writeOffAsset'           => ['Asset Write-off',              'Hardware / Write-off'],
    'rfid_update'             => ['RFID Update',                  'Hardware / RFID'],
    'UpdateCartridgeNew'      => ['Edit Cartridge Details',       'Cartridges / Edit'],
    'UpdateCartridgeDetails'  => ['Update Cartridge Stock Details','Cartridges / Update Stock'],
    'RemoveCartridgeReq'      => ['Remove Cartridge Request',     'Cartridges / Remove Req'],
    'UpdateUserDetails'       => ['Update User Details',          'Users / Update'],
    'UpdateContactDetails'    => ['Update User Contact',          'Users / Contact'],
    'UserPasswordChange'      => ['Reset User Password',          'Users / Password'],
    'RemoveUser'              => ['Remove User',                  'Users / Remove'],
    'UpdateMainPage'          => ['Update Main Page Content',     'Others / Main Page'],
    'SanitizeReq'             => ['Sanitization Requests',        'Others / Sanitization'],
    'SvrBackupTime'           => ['Server Backup Timing',         'Others / Backup'],
];
[$pageHeading, $crumbs] = $titles[$tab] ?? ['Admin Panel', 'Admin'];
$pageTitle = 'ISKOT Admin · ' . $pageHeading;
$showGovHeader = ($tab === 'Dashboard');

include 'includes/header.php';
include 'includes/sidebar_admin.php';
include 'includes/topbar.php';
?>

<?php
switch ($tab) {
    case 'Dashboard':              include 'admin/admin_dashboard.php'; break;
    case 'CallGenerateByAdmin':    include 'shared/CallGenerate.php'; break;
    case 'All_Calls':              include 'shared/AllCalls.php'; break;
    case 'Pending_Calls':          include 'engineer/PendingCalls.php'; break;
    case 'Attend_Calls':           include 'engineer/AttendCalls.php'; break;
    case 'Solved_Calls':           include 'engineer/SolvedCalls.php'; break;
    case 'Closed_Calls':           include 'engineer/ClosedCalls.php'; break;
    case 'CallReport':             include 'shared/ReportCalls.php'; break;
    case 'AdminCallReport':        include 'shared/CallReport.php'; break;
    case 'asset_wise_report':      include 'shared/asset_wise_report.php'; break;
    case 'user_wise_report':       include 'shared/user_wise_report.php'; break;
    case 'View_Calls':             include 'shared/ViewCalls.php'; break;
    case 'Hardware_Details':       include 'shared/Hardware_Details.php'; break;
    case 'PC_Printer_Details':     include 'shared/PC_Printer_Details.php'; break;
    case 'NewEntry':               include 'admin/NewHardwareEntry.php'; break;
    case 'ChangeHardwareDetails':  include 'admin/ChangeHardwareDetails.php'; break;
    case 'Issue':                  include 'shared/HardwareIssueToUser.php'; break;
    case 'AddtoMS':                include 'shared/addToMS.php'; break;
    case 'ChangeCallReport':       include 'admin/ChangeCallReport.php'; break;
    case 'CartridgeReqGenerate':   include 'shared/CartridgeRequestGenerate.php'; break;
    case 'CartridgeIssue':         include 'shared/cartridgeIssue.php'; break;
    case 'CartridgeWiseIssue':     include 'shared/cartridgeWiseIssueList.php'; break;
    case 'CartridgePendingRequest':include 'shared/CartridgeRequest.php'; break;
    case 'CartridgeStock':         include 'shared/CartridgeStock.php'; break;
    case 'PrinterCartridgeNew':    include 'admin/PrinterCartridgeNewEntry.php'; break;
    case 'UpdatePrinterCartridgeNew': include 'admin/PrinterCartridgeUpdate.php'; break;
    case 'AddNewCartridge':        include 'admin/AddCartridgeStock.php'; break;
    case 'ReceivedCartridgeStock': include 'shared/ReceivedCartridgeStock.php'; break;
    case 'PrinterWiseCartridgeStock': include 'shared/PrinterWiseCartridge.php'; break;

    // User Management
    case 'AddNewUser':             include 'admin/AddNEwUser.php'; break;
    case 'ManageUsers':            include 'admin/ManageUsers.php'; break;
    case 'EditUser':               include 'admin/EditUser.php'; break;
    case 'PrintEmployee':          include 'admin/PrintEmployee.php'; break;
    case 'BulkImport':             include 'admin/BulkImportUsers.php'; break;
    case 'Suggestions':            include 'admin/Suggestions.php'; break;
    case 'Search_Employee':        include 'shared/SearchEmployee.php'; break;
    case 'Update_Employee_Req':    include 'admin/UserUpdateReq.php'; break;

    // Engineer Management
    case 'AddEngineer':            include 'admin/Add_Engineer.php'; break;
    case 'EngineerList':           include 'admin/Engineer_List.php'; break;
    case 'EditEngineer':           include 'admin/Edit_Engineer_Details.php'; break;
    case 'RemoveEngineer':         include 'admin/Remove_Engineer.php'; break;
    case 'ActDeactEngg':           include 'admin/ActDeactEngg.php'; break;
    case 'P_Engineer':             include 'engineer/Presence_Engineer.php'; break;

    case 'ChangePassword':         include 'admin/AdminChangePassword.php'; break;
    case 'ContentManager':         include 'admin/ContentManager.php'; break;
    case 'BannerSettings':         include 'admin/BannerSettings.php'; break;

    // ---- Super-Admin-only ----
    case 'ManageAdmins':           include 'admin/ManageAdmins.php'; break;

    // ---- Tabs inherited from the legacy ISKot Admin dashboard ----
    case 'RemoveCall':               include 'admin/RemoveCall.php'; break;
    case 'AdminViewCall':            include 'shared/ViewCalls.php'; break;
    case 'sec_wise_report':          include 'shared/section_wise_report.php'; break;
    case 'Hardware_total_list':      include 'shared/hardware_total_list.php'; break;
    case 'verified_asset_list':      include 'engineer/verifiedAssetList.php'; break;
    case 'Hardware_Storage_List':    include 'shared/hardware_storage_master.php'; break;
    case 'CCTV_List':                include 'shared/CCTV_asset_list.php'; break;
    case 'Hardware_Details_iskot':   include 'shared/Hardware_Details_ISKOT.php'; break;
    case 'Hardware_Details_wo':      include 'shared/Hardware_Details_wo.php'; break;
    case 'Hardware_Details_standby': include 'shared/Hardware_Details_standby.php'; break;
    case 'software_list':            include 'shared/SoftwareList.php'; break;
    case 'internet_list':            include 'shared/Internet_users_list.php'; break;
    case 'OS_list':                  include 'shared/OS_based_system.php'; break;
    case 'AssetNoList':              include 'shared/asset_no_details.php'; break;
    case 'ChangeAssetNo':            include 'admin/UpdateAssetNo.php'; break;
    case 'Remove_Hardware':          include 'admin/Remove_Hardware.php'; break;
    case 'writeOffAsset':            include 'admin/AssetToWriteOff.php'; break;
    case 'rfid_update':              include 'shared/rfid_update.php'; break;
    case 'UpdateCartridgeNew':       include 'admin/CartridgeUpdate.php'; break;
    case 'UpdateCartridgeDetails':   include 'admin/UpdateCartridgeDetails.php'; break;
    case 'RemoveCartridgeReq':       include 'admin/RemoveCartridgeReq.php'; break;
    case 'UpdateUserDetails':        include 'admin/Update_User_Details.php'; break;
    case 'UpdateContactDetails':     include 'engineer/user_contact_update.php'; break;
    case 'UserPasswordChange':       include 'user/User_Password_Change.php'; break;
    case 'RemoveUser':               include 'admin/RemoveUser.php'; break;
    case 'UpdateMainPage':           include 'admin/MainPageUpdate.php'; break;
    case 'SanitizeReq':              include 'sanitizeReq.php'; break;
    case 'SvrBackupTime':            include 'shared/server_backup_timing.php'; break;

    default:                       include 'admin/admin_dashboard.php';
}
?>

<?php include 'includes/footer_app.php'; ?>
