<?php
/**
 * ISKot Admin sidebar — mirrors every drawer item from the legacy
 * ISKotAdminHome.php while keeping the modern collapsible UI.
 *
 * Plus a Super-Admin-only "Manage Admins" entry that is hidden from
 * every other ISKot Admin account.
 */
$tab = $_GET['AdminTab'] ?? $_GET['ISKotAdminTab'] ?? 'Dashboard';
function _ac($t, $current){ return $t === $current ? 'active' : ''; }
function _ag($items, $current){ return in_array($current, $items, true) ? 'open' : ''; }

// Counts (safe even when a table is missing)
$total_cart        = safe_count($link, "SELECT COUNT(*) FROM request_master WHERE Status='Pending'");
$total_pending     = safe_count($link, "SELECT COUNT(*) FROM complain_register WHERE status='Pending'");
$total_attend      = safe_count($link, "SELECT COUNT(*) FROM complain_register WHERE status='Attend'");
$total_solved      = safe_count($link, "SELECT COUNT(*) FROM complain_register WHERE status='Solved'");
$total_sugg_unread = safe_count($link, "SELECT COUNT(*) FROM suggestions WHERE is_read=0");
$call_notification = $total_pending + $total_attend + $total_solved;
$is_super          = is_super_admin();
$total_admins      = safe_count($link, "SELECT COUNT(*) FROM iskotadmin_login");

$grp_tickets = ['CallGenerateByAdmin','All_Calls','Pending_Calls','Attend_Calls','Solved_Calls','Closed_Calls',
                'CallReport','AdminCallReport','asset_wise_report','user_wise_report','View_Calls',
                'AdminViewCall','sec_wise_report','ChangeCallReport','RemoveCall'];
$grp_cart    = ['CartridgeReqGenerate','CartridgePendingRequest','CartridgeIssue','CartridgeWiseIssue',
                'CartridgeStock','PrinterCartridgeNew','UpdatePrinterCartridgeNew','AddNewCartridge',
                'ReceivedCartridgeStock','PrinterWiseCartridgeStock','UpdateCartridgeNew',
                'UpdateCartridgeDetails','RemoveCartridgeReq'];
$grp_hw      = ['Hardware_Details','PC_Printer_Details','NewEntry','ChangeHardwareDetails','AddtoMS','Issue',
                'Hardware_total_list','verified_asset_list','Hardware_Storage_List','CCTV_List',
                'Hardware_Details_iskot','Hardware_Details_wo','Hardware_Details_standby',
                'software_list','internet_list','OS_list','AssetNoList','ChangeAssetNo',
                'Remove_Hardware','writeOffAsset','rfid_update'];
$grp_users   = ['AddNewUser','ManageUsers','EditUser','PrintEmployee','BulkImport','Search_Employee',
                'Update_Employee_Req','UpdateUserDetails','UpdateContactDetails','UserPasswordChange',
                'RemoveUser'];
$grp_engg    = ['AddEngineer','EngineerList','RemoveEngineer','EditEngineer','ActDeactEngg','P_Engineer'];
$grp_others  = ['UpdateMainPage','SanitizeReq','SvrBackupTime'];
?>

<nav>
    <?php if ($is_super): ?>
    <!-- ====================== SUPER ADMIN BAND ====================== -->
    <div class="super-admin-band" data-testid="super-admin-band">
        <span class="sa-crown"><i class="fa-solid fa-crown"></i></span>
        <div>
            <div class="sa-title">Super Admin</div>
            <div class="sa-id"><?= e(current_user_id()) ?></div>
        </div>
    </div>
    <?php endif; ?>

    <div class="nav-section-title">Overview</div>
    <a class="nav-link-item <?= _ac('Dashboard',$tab) ?>" href="Admin_Home.php?AdminTab=Dashboard" data-testid="nav-dashboard">
        <i class="fa-solid fa-gauge-high lead-ic"></i> Dashboard
    </a>
    <a class="nav-link-item <?= _ac('ChangePassword',$tab) ?>" href="Admin_Home.php?AdminTab=ChangePassword" data-testid="nav-change-password">
        <i class="fa-solid fa-key lead-ic"></i> Change Password
    </a>

    <?php if ($is_super): ?>
    <a class="nav-link-item super-link <?= _ac('ManageAdmins',$tab) ?>" href="Admin_Home.php?AdminTab=ManageAdmins" data-testid="nav-manage-admins">
        <i class="fa-solid fa-user-shield lead-ic"></i> Manage Admins
        <span class="nav-badge gold"><i class="fa-solid fa-crown"></i> <?= $total_admins ?></span>
    </a>
    <?php endif; ?>

    <div class="nav-section-title">Call / Ticket Register</div>
    <div class="nav-group <?= _ag($grp_tickets, $tab) ?>">
        <button class="nav-toggle" data-testid="nav-grp-tickets"><i class="fa-solid fa-ticket lead-ic"></i> Tickets
            <span class="nav-badge"><?= $call_notification ?></span>
            <i class="fa-solid fa-chevron-right chev"></i>
        </button>
        <div class="nav-children">
            <a class="nav-link-item <?= _ac('CallGenerateByAdmin',$tab) ?>"   href="Admin_Home.php?AdminTab=CallGenerateByAdmin"><i class="fa-solid fa-plus lead-ic"></i> Generate Ticket</a>
            <a class="nav-link-item <?= _ac('All_Calls',$tab) ?>"              href="Admin_Home.php?AdminTab=All_Calls"><i class="fa-solid fa-list lead-ic"></i> Ticket Status</a>
            <a class="nav-link-item <?= _ac('Pending_Calls',$tab) ?>"          href="Admin_Home.php?AdminTab=Pending_Calls"><i class="fa-solid fa-hourglass-half lead-ic"></i> Unassigned <span class="nav-badge"><?= $total_pending ?></span></a>
            <a class="nav-link-item <?= _ac('Attend_Calls',$tab) ?>"           href="Admin_Home.php?AdminTab=Attend_Calls"><i class="fa-solid fa-spinner lead-ic"></i> Unresolved <span class="nav-badge"><?= $total_attend ?></span></a>
            <a class="nav-link-item <?= _ac('Solved_Calls',$tab) ?>"           href="Admin_Home.php?AdminTab=Solved_Calls"><i class="fa-solid fa-circle-check lead-ic"></i> Solved <span class="nav-badge muted"><?= $total_solved ?></span></a>
            <a class="nav-link-item <?= _ac('Closed_Calls',$tab) ?>"           href="Admin_Home.php?AdminTab=Closed_Calls"><i class="fa-solid fa-lock lead-ic"></i> Closed</a>
            <a class="nav-link-item <?= _ac('AdminCallReport',$tab) ?>"        href="Admin_Home.php?AdminTab=AdminCallReport"><i class="fa-solid fa-chart-pie lead-ic"></i> Overall Status</a>
            <a class="nav-link-item <?= _ac('CallReport',$tab) ?>"             href="Admin_Home.php?AdminTab=CallReport"><i class="fa-solid fa-chart-line lead-ic"></i> Days-wise Report</a>
            <a class="nav-link-item <?= _ac('AdminViewCall',$tab) ?>"          href="Admin_Home.php?AdminTab=AdminViewCall"><i class="fa-solid fa-calendar-days lead-ic"></i> Date-wise Record</a>
            <a class="nav-link-item <?= _ac('sec_wise_report',$tab) ?>"        href="Admin_Home.php?AdminTab=sec_wise_report"><i class="fa-solid fa-sitemap lead-ic"></i> Section-wise Report</a>
            <a class="nav-link-item <?= _ac('asset_wise_report',$tab) ?>"      href="Admin_Home.php?AdminTab=asset_wise_report"><i class="fa-solid fa-laptop lead-ic"></i> Asset-wise Report</a>
            <a class="nav-link-item <?= _ac('user_wise_report',$tab) ?>"       href="Admin_Home.php?AdminTab=user_wise_report"><i class="fa-solid fa-user-tag lead-ic"></i> User-wise Report</a>
            <a class="nav-link-item <?= _ac('ChangeCallReport',$tab) ?>"       href="Admin_Home.php?AdminTab=ChangeCallReport"><i class="fa-solid fa-pen-to-square lead-ic"></i> Change Ticket</a>
            <a class="nav-link-item <?= _ac('RemoveCall',$tab) ?>"             href="Admin_Home.php?AdminTab=RemoveCall"><i class="fa-solid fa-trash lead-ic"></i> Remove Ticket</a>
        </div>
    </div>

    <div class="nav-section-title">Cartridge Register</div>
    <div class="nav-group <?= _ag($grp_cart, $tab) ?>">
        <button class="nav-toggle" data-testid="nav-grp-cartridges"><i class="fa-solid fa-print lead-ic"></i> Cartridges
            <span class="nav-badge"><?= $total_cart ?></span>
            <i class="fa-solid fa-chevron-right chev"></i>
        </button>
        <div class="nav-children">
            <a class="nav-link-item <?= _ac('CartridgeReqGenerate',$tab) ?>"      href="Admin_Home.php?AdminTab=CartridgeReqGenerate"><i class="fa-solid fa-file-circle-plus lead-ic"></i> Cartridge Form</a>
            <a class="nav-link-item <?= _ac('CartridgePendingRequest',$tab) ?>"   href="Admin_Home.php?AdminTab=CartridgePendingRequest"><i class="fa-solid fa-clock lead-ic"></i> Pending Req. <span class="nav-badge"><?= $total_cart ?></span></a>
            <a class="nav-link-item <?= _ac('CartridgeIssue',$tab) ?>"            href="Admin_Home.php?AdminTab=CartridgeIssue"><i class="fa-solid fa-receipt lead-ic"></i> Issue List</a>
            <a class="nav-link-item <?= _ac('CartridgeWiseIssue',$tab) ?>"        href="Admin_Home.php?AdminTab=CartridgeWiseIssue"><i class="fa-solid fa-filter lead-ic"></i> Cartridge-wise Issues</a>
            <a class="nav-link-item <?= _ac('CartridgeStock',$tab) ?>"            href="Admin_Home.php?AdminTab=CartridgeStock"><i class="fa-solid fa-boxes-stacked lead-ic"></i> Stock List</a>
            <a class="nav-link-item <?= _ac('ReceivedCartridgeStock',$tab) ?>"    href="Admin_Home.php?AdminTab=ReceivedCartridgeStock"><i class="fa-solid fa-truck-arrow-right lead-ic"></i> Update Stock</a>
            <a class="nav-link-item <?= _ac('PrinterCartridgeNew',$tab) ?>"       href="Admin_Home.php?AdminTab=PrinterCartridgeNew"><i class="fa-solid fa-plus lead-ic"></i> Add Printer-Cartridge</a>
            <a class="nav-link-item <?= _ac('UpdatePrinterCartridgeNew',$tab) ?>" href="Admin_Home.php?AdminTab=UpdatePrinterCartridgeNew"><i class="fa-solid fa-pen-to-square lead-ic"></i> Edit Printer-Cartridge</a>
            <a class="nav-link-item <?= _ac('AddNewCartridge',$tab) ?>"           href="Admin_Home.php?AdminTab=AddNewCartridge"><i class="fa-solid fa-plus-circle lead-ic"></i> Add New Cartridge</a>
            <a class="nav-link-item <?= _ac('UpdateCartridgeNew',$tab) ?>"        href="Admin_Home.php?AdminTab=UpdateCartridgeNew"><i class="fa-solid fa-pen lead-ic"></i> Edit Cartridge</a>
            <a class="nav-link-item <?= _ac('UpdateCartridgeDetails',$tab) ?>"    href="Admin_Home.php?AdminTab=UpdateCartridgeDetails"><i class="fa-solid fa-file-pen lead-ic"></i> Update Stock Details</a>
            <a class="nav-link-item <?= _ac('PrinterWiseCartridgeStock',$tab) ?>" href="Admin_Home.php?AdminTab=PrinterWiseCartridgeStock"><i class="fa-solid fa-list-check lead-ic"></i> Printer-wise Stock</a>
            <a class="nav-link-item <?= _ac('RemoveCartridgeReq',$tab) ?>"        href="Admin_Home.php?AdminTab=RemoveCartridgeReq"><i class="fa-solid fa-trash lead-ic"></i> Remove Cartridge Req.</a>
        </div>
    </div>

    <div class="nav-section-title">Hardware Details</div>
    <div class="nav-group <?= _ag($grp_hw, $tab) ?>">
        <button class="nav-toggle" data-testid="nav-grp-hardware"><i class="fa-solid fa-desktop lead-ic"></i> Hardware
            <i class="fa-solid fa-chevron-right chev"></i>
        </button>
        <div class="nav-children">
            <a class="nav-link-item <?= _ac('Hardware_total_list',$tab) ?>"      href="Admin_Home.php?AdminTab=Hardware_total_list"><i class="fa-solid fa-chart-simple lead-ic"></i> Total Asset Summary</a>
            <a class="nav-link-item <?= _ac('Hardware_Details',$tab) ?>"          href="Admin_Home.php?AdminTab=Hardware_Details"><i class="fa-solid fa-server lead-ic"></i> Hardware List</a>
            <a class="nav-link-item <?= _ac('verified_asset_list',$tab) ?>"       href="Admin_Home.php?AdminTab=verified_asset_list"><i class="fa-solid fa-shield-check lead-ic"></i> Verified Asset List</a>
            <a class="nav-link-item <?= _ac('Hardware_Storage_List',$tab) ?>"     href="Admin_Home.php?AdminTab=Hardware_Storage_List"><i class="fa-solid fa-warehouse lead-ic"></i> Storage List</a>
            <a class="nav-link-item <?= _ac('CCTV_List',$tab) ?>"                 href="Admin_Home.php?AdminTab=CCTV_List"><i class="fa-solid fa-video lead-ic"></i> CCTV Asset List</a>
            <a class="nav-link-item <?= _ac('Hardware_Details_iskot',$tab) ?>"    href="Admin_Home.php?AdminTab=Hardware_Details_iskot"><i class="fa-solid fa-building lead-ic"></i> ISKOT Asset List</a>
            <a class="nav-link-item <?= _ac('Hardware_Details_wo',$tab) ?>"       href="Admin_Home.php?AdminTab=Hardware_Details_wo"><i class="fa-solid fa-ban lead-ic"></i> Write-off Asset List</a>
            <a class="nav-link-item <?= _ac('Hardware_Details_standby',$tab) ?>"  href="Admin_Home.php?AdminTab=Hardware_Details_standby"><i class="fa-solid fa-pause lead-ic"></i> Standby Asset List</a>
            <a class="nav-link-item <?= _ac('software_list',$tab) ?>"             href="Admin_Home.php?AdminTab=software_list"><i class="fa-solid fa-code lead-ic"></i> Software List</a>
            <a class="nav-link-item <?= _ac('internet_list',$tab) ?>"             href="Admin_Home.php?AdminTab=internet_list"><i class="fa-solid fa-wifi lead-ic"></i> Internet User List</a>
            <a class="nav-link-item <?= _ac('OS_list',$tab) ?>"                   href="Admin_Home.php?AdminTab=OS_list"><i class="fa-brands fa-windows lead-ic"></i> OS-Based System</a>
            <a class="nav-link-item <?= _ac('PC_Printer_Details',$tab) ?>"        href="Admin_Home.php?AdminTab=PC_Printer_Details"><i class="fa-solid fa-print lead-ic"></i> PC / Printer Details</a>
            <a class="nav-link-item <?= _ac('NewEntry',$tab) ?>"                  href="Admin_Home.php?AdminTab=NewEntry"><i class="fa-solid fa-plus lead-ic"></i> Add Hardware</a>
            <a class="nav-link-item <?= _ac('ChangeHardwareDetails',$tab) ?>"     href="Admin_Home.php?AdminTab=ChangeHardwareDetails"><i class="fa-solid fa-pen-to-square lead-ic"></i> Edit Hardware</a>
            <a class="nav-link-item <?= _ac('AssetNoList',$tab) ?>"               href="Admin_Home.php?AdminTab=AssetNoList"><i class="fa-solid fa-list-ol lead-ic"></i> Asset No List</a>
            <a class="nav-link-item <?= _ac('ChangeAssetNo',$tab) ?>"             href="Admin_Home.php?AdminTab=ChangeAssetNo"><i class="fa-solid fa-arrows-turn-to-dots lead-ic"></i> Update Asset No</a>
            <a class="nav-link-item <?= _ac('AddtoMS',$tab) ?>"                   href="Admin_Home.php?AdminTab=AddtoMS"><i class="fa-solid fa-rotate-left lead-ic"></i> Return To MS</a>
            <a class="nav-link-item <?= _ac('Remove_Hardware',$tab) ?>"           href="Admin_Home.php?AdminTab=Remove_Hardware"><i class="fa-solid fa-trash lead-ic"></i> Remove Hardware</a>
            <a class="nav-link-item <?= _ac('writeOffAsset',$tab) ?>"             href="Admin_Home.php?AdminTab=writeOffAsset"><i class="fa-solid fa-circle-xmark lead-ic"></i> Asset Write-off</a>
            <a class="nav-link-item <?= _ac('rfid_update',$tab) ?>"               href="Admin_Home.php?AdminTab=rfid_update"><i class="fa-solid fa-id-card-clip lead-ic"></i> RFID Update</a>
            <a class="nav-link-item <?= _ac('Issue',$tab) ?>"                     href="Admin_Home.php?AdminTab=Issue"><i class="fa-solid fa-handshake lead-ic"></i> Issue To User</a>
        </div>
    </div>

    <div class="nav-section-title">User &amp; Engineer Management</div>
    <div class="nav-group <?= _ag($grp_users, $tab) ?>">
        <button class="nav-toggle" data-testid="nav-grp-users"><i class="fa-solid fa-users lead-ic"></i> User Detail
            <i class="fa-solid fa-chevron-right chev"></i>
        </button>
        <div class="nav-children">
            <a class="nav-link-item <?= _ac('Search_Employee',$tab) ?>"      href="Admin_Home.php?AdminTab=Search_Employee"><i class="fa-solid fa-magnifying-glass lead-ic"></i> User Search</a>
            <a class="nav-link-item <?= _ac('AddNewUser',$tab) ?>"           href="Admin_Home.php?AdminTab=AddNewUser"><i class="fa-solid fa-user-plus lead-ic"></i> Add New User</a>
            <a class="nav-link-item <?= _ac('BulkImport',$tab) ?>"           href="Admin_Home.php?AdminTab=BulkImport"><i class="fa-solid fa-file-import lead-ic"></i> Bulk Import (CSV)</a>
            <a class="nav-link-item <?= _ac('ManageUsers',$tab) ?>"          href="Admin_Home.php?AdminTab=ManageUsers"><i class="fa-solid fa-users-gear lead-ic"></i> Manage Users</a>
            <a class="nav-link-item <?= _ac('UpdateUserDetails',$tab) ?>"    href="Admin_Home.php?AdminTab=UpdateUserDetails"><i class="fa-solid fa-user-pen lead-ic"></i> Update User Details</a>
            <a class="nav-link-item <?= _ac('UpdateContactDetails',$tab) ?>" href="Admin_Home.php?AdminTab=UpdateContactDetails"><i class="fa-solid fa-address-card lead-ic"></i> Update Contact</a>
            <a class="nav-link-item <?= _ac('Update_Employee_Req',$tab) ?>"  href="Admin_Home.php?AdminTab=Update_Employee_Req"><i class="fa-solid fa-clipboard-check lead-ic"></i> Update Request</a>
            <a class="nav-link-item <?= _ac('UserPasswordChange',$tab) ?>"   href="Admin_Home.php?AdminTab=UserPasswordChange"><i class="fa-solid fa-key lead-ic"></i> Reset Password</a>
            <a class="nav-link-item <?= _ac('RemoveUser',$tab) ?>"           href="Admin_Home.php?AdminTab=RemoveUser"><i class="fa-solid fa-trash lead-ic"></i> Remove User</a>
        </div>
    </div>

    <div class="nav-group <?= _ag($grp_engg, $tab) ?>">
        <button class="nav-toggle" data-testid="nav-grp-engineers"><i class="fa-solid fa-user-gear lead-ic"></i> Engineers
            <i class="fa-solid fa-chevron-right chev"></i>
        </button>
        <div class="nav-children">
            <a class="nav-link-item <?= _ac('EngineerList',$tab) ?>"     href="Admin_Home.php?AdminTab=EngineerList"><i class="fa-solid fa-list lead-ic"></i> Engineer's List</a>
            <a class="nav-link-item <?= _ac('AddEngineer',$tab) ?>"      href="Admin_Home.php?AdminTab=AddEngineer"><i class="fa-solid fa-user-plus lead-ic"></i> Add Engineer</a>
            <a class="nav-link-item <?= _ac('P_Engineer',$tab) ?>"       href="Admin_Home.php?AdminTab=P_Engineer"><i class="fa-solid fa-user-check lead-ic"></i> Presence Engineer</a>
            <a class="nav-link-item <?= _ac('EditEngineer',$tab) ?>"     href="Admin_Home.php?AdminTab=EditEngineer"><i class="fa-solid fa-pen-to-square lead-ic"></i> Update Engineer Details</a>
            <a class="nav-link-item <?= _ac('ActDeactEngg',$tab) ?>"     href="Admin_Home.php?AdminTab=ActDeactEngg"><i class="fa-solid fa-toggle-on lead-ic"></i> Active / Deactive</a>
            <a class="nav-link-item <?= _ac('RemoveEngineer',$tab) ?>"   href="Admin_Home.php?AdminTab=RemoveEngineer"><i class="fa-solid fa-trash lead-ic"></i> Remove Engineer</a>
        </div>
    </div>

    <div class="nav-section-title">Communications</div>
    <a class="nav-link-item <?= _ac('Suggestions',$tab) ?>" href="Admin_Home.php?AdminTab=Suggestions" data-testid="nav-suggestions">
        <i class="fa-solid fa-lightbulb lead-ic"></i> User Suggestions
        <?php if ($total_sugg_unread > 0): ?><span class="nav-badge"><?= $total_sugg_unread ?></span><?php endif; ?>
    </a>
    <div class="nav-group <?= $tab === 'ContentManager' ? 'open' : '' ?>">
        <button class="nav-toggle"><i class="fa-solid fa-pen-ruler lead-ic"></i> Content Manager
            <i class="fa-solid fa-chevron-right chev"></i>
        </button>
        <div class="nav-children">
            <a class="nav-link-item <?= ($tab==='ContentManager' && ($_GET['section']??'news')==='news') ? 'active' : '' ?>"
               href="Admin_Home.php?AdminTab=ContentManager&section=news"><i class="fa-solid fa-newspaper lead-ic"></i> Latest News</a>
            <a class="nav-link-item <?= ($tab==='ContentManager' && ($_GET['section']??'')==='notice') ? 'active' : '' ?>"
               href="Admin_Home.php?AdminTab=ContentManager&section=notice"><i class="fa-solid fa-bullhorn lead-ic"></i> Notice Board</a>
            <a class="nav-link-item <?= ($tab==='ContentManager' && ($_GET['section']??'')==='form') ? 'active' : '' ?>"
               href="Admin_Home.php?AdminTab=ContentManager&section=form"><i class="fa-solid fa-cloud-arrow-down lead-ic"></i> Forms Download</a>
        </div>
    </div>
    <a class="nav-link-item <?= _ac('BannerSettings',$tab) ?>" href="Admin_Home.php?AdminTab=BannerSettings" data-testid="nav-banner-settings">
        <i class="fa-solid fa-tower-broadcast lead-ic"></i> Live Banner Settings
    </a>
    <a class="nav-link-item <?= _ac('UpdateMainPage',$tab) ?>" href="Admin_Home.php?AdminTab=UpdateMainPage" data-testid="nav-update-mainpage">
        <i class="fa-solid fa-house-laptop lead-ic"></i> Update Main Page
    </a>
    <a class="nav-link-item" href="javascript:void(0)" onclick="(function(){var w=Math.min(screen.availWidth,1400),h=Math.min(screen.availHeight,800);window.open('live_board.php','IS_LIVE_BOARD','width='+w+',height='+h+',left='+((screen.availWidth-w)/2)+',top='+((screen.availHeight-h)/2)+',resizable=yes,scrollbars=yes,toolbar=no,menubar=no,location=no,status=no');})();" data-testid="nav-live-board">
        <i class="fa-solid fa-tv lead-ic"></i> Open Live Board
        <span class="nav-badge" style="background:linear-gradient(135deg,#f59e0b,#facc15);color:#0a1f44">NEW</span>
    </a>

    <div class="nav-section-title">Others</div>
    <div class="nav-group <?= _ag($grp_others, $tab) ?>">
        <button class="nav-toggle" data-testid="nav-grp-others"><i class="fa-solid fa-ellipsis lead-ic"></i> Misc
            <i class="fa-solid fa-chevron-right chev"></i>
        </button>
        <div class="nav-children">
            <a class="nav-link-item <?= _ac('SanitizeReq',$tab) ?>"   href="Admin_Home.php?AdminTab=SanitizeReq"><i class="fa-solid fa-spray-can-sparkles lead-ic"></i> Sanitization</a>
            <a class="nav-link-item <?= _ac('SvrBackupTime',$tab) ?>" href="Admin_Home.php?AdminTab=SvrBackupTime"><i class="fa-solid fa-clock-rotate-left lead-ic"></i> Backup Timing</a>
        </div>
    </div>
</nav>

<style>
/* Super-Admin highlight band shown only when current admin is the Super Admin */
.super-admin-band{
    display:flex;align-items:center;gap:10px;
    margin:0 12px 14px;padding:10px 12px;border-radius:12px;
    background:linear-gradient(135deg,#fbbf24 0%,#f59e0b 50%,#fbbf24 100%);
    color:#1c1917;font-weight:700;letter-spacing:.3px;
    box-shadow:0 8px 18px -6px rgba(245,158,11,.45),inset 0 1px 0 rgba(255,255,255,.45);
    border:1px solid rgba(0,0,0,.06);
    position:relative;overflow:hidden;
}
.super-admin-band::before{
    content:"";position:absolute;inset:0;
    background:radial-gradient(120% 70% at 0% 0%, rgba(255,255,255,.55), transparent 60%);
    pointer-events:none;
}
.super-admin-band .sa-crown{
    width:32px;height:32px;border-radius:50%;background:#1c1917;color:#facc15;
    display:flex;align-items:center;justify-content:center;font-size:14px;
    box-shadow:inset 0 0 0 2px rgba(250,204,21,.6);
}
.super-admin-band .sa-title{font-size:11px;letter-spacing:1.2px;text-transform:uppercase;opacity:.85}
.super-admin-band .sa-id{font-size:14px;font-weight:800;font-family:'JetBrains Mono',ui-monospace,monospace;letter-spacing:.5px}

.nav-link-item.super-link{
    background:linear-gradient(90deg, rgba(250,204,21,.10), transparent);
    border-left:3px solid #facc15;
}
.nav-link-item.super-link.active{background:linear-gradient(90deg, rgba(250,204,21,.25), transparent)}
.nav-badge.gold{background:linear-gradient(135deg,#f59e0b,#facc15);color:#1c1917;font-weight:800}
</style>
