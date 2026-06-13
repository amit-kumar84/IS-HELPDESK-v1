<?php
$tab = $_GET['EngineerTab'] ?? 'Dashboard';
function _ec($t,$current){ return $t === $current ? 'active' : ''; }

$total_cart    = safe_count($link, "SELECT COUNT(*) FROM request_master WHERE Status='Pending'");
$total_pending = safe_count($link, "SELECT COUNT(*) FROM complain_register WHERE status='Pending'");
$total_attend  = safe_count($link, "SELECT COUNT(*) FROM complain_register WHERE status='Attend'");
$total_solved  = safe_count($link, "SELECT COUNT(*) FROM complain_register WHERE status='Solved'");
?>
<nav>
    <div class="nav-section-title">Overview</div>
    <a class="nav-link-item <?= _ec('Dashboard',$tab) ?>" href="Engineer_home.php?EngineerTab=Dashboard"><i class="fa-solid fa-gauge-high lead-ic"></i> Dashboard</a>
    <a class="nav-link-item <?= _ec('ChangePassword',$tab) ?>" href="Engineer_home.php?EngineerTab=ChangePassword"><i class="fa-solid fa-key lead-ic"></i> Change Password</a>

    <div class="nav-section-title">Calls</div>
    <a class="nav-link-item <?= _ec('CallGenerate',$tab) ?>" href="Engineer_home.php?EngineerTab=CallGenerate"><i class="fa-solid fa-plus lead-ic"></i> Generate Call</a>
    <a class="nav-link-item <?= _ec('All_Calls',$tab) ?>" href="Engineer_home.php?EngineerTab=All_Calls"><i class="fa-solid fa-list lead-ic"></i> All Calls</a>
    <a class="nav-link-item <?= _ec('Pending_Calls',$tab) ?>" href="Engineer_home.php?EngineerTab=Pending_Calls"><i class="fa-solid fa-hourglass-half lead-ic"></i> Unassigned <span class="nav-badge"><?= $total_pending ?></span></a>
    <a class="nav-link-item <?= _ec('Attend_Calls',$tab) ?>" href="Engineer_home.php?EngineerTab=Attend_Calls"><i class="fa-solid fa-spinner lead-ic"></i> In&nbsp;Progress <span class="nav-badge"><?= $total_attend ?></span></a>
    <a class="nav-link-item <?= _ec('Solved_Calls',$tab) ?>" href="Engineer_home.php?EngineerTab=Solved_Calls"><i class="fa-solid fa-circle-check lead-ic"></i> Solved <span class="nav-badge muted"><?= $total_solved ?></span></a>
    <a class="nav-link-item <?= _ec('View_Calls',$tab) ?>" href="Engineer_home.php?EngineerTab=View_Calls"><i class="fa-solid fa-calendar-days lead-ic"></i> Date Search</a>

    <div class="nav-section-title">Cartridges</div>
    <a class="nav-link-item <?= _ec('CartridgeReqGenerate',$tab) ?>" href="Engineer_home.php?EngineerTab=CartridgeReqGenerate"><i class="fa-solid fa-file-circle-plus lead-ic"></i> New Request</a>
    <a class="nav-link-item <?= _ec('CartridgePendingRequest',$tab) ?>" href="Engineer_home.php?EngineerTab=CartridgePendingRequest"><i class="fa-solid fa-clock lead-ic"></i> Pending Req. <span class="nav-badge"><?= $total_cart ?></span></a>

    <?php if ($sid === '620230'): ?>
        <div class="nav-section-title">Hardware</div>
        <a class="nav-link-item <?= _ec('Hardware_Details',$tab) ?>" href="Engineer_home.php?EngineerTab=Hardware_Details"><i class="fa-solid fa-server lead-ic"></i> Hardware List</a>
        <a class="nav-link-item <?= _ec('Issue',$tab) ?>" href="Engineer_home.php?EngineerTab=Issue"><i class="fa-solid fa-handshake lead-ic"></i> Issue To User</a>
        <a class="nav-link-item <?= _ec('RemoveCartridgeReq',$tab) ?>" href="Engineer_home.php?EngineerTab=RemoveCartridgeReq"><i class="fa-solid fa-trash lead-ic"></i> Remove Cartridge Req.</a>
    <?php endif; ?>

    <?php if ($sid === '620230' || $sid === '620229'): ?>
        <a class="nav-link-item <?= _ec('VerifiedAssetList',$tab) ?>" href="Engineer_home.php?EngineerTab=VerifiedAssetList"><i class="fa-solid fa-shield-halved lead-ic"></i> Verified Asset List</a>
    <?php endif; ?>

    <?php if ($sid === '620230' || $sid === '620212'): ?>
        <a class="nav-link-item <?= _ec('UserContactUpdate',$tab) ?>" href="Engineer_home.php?EngineerTab=UserContactUpdate"><i class="fa-solid fa-address-card lead-ic"></i> User Contact Update</a>
    <?php endif; ?>

    <div class="nav-section-title">Tools</div>
    <a class="nav-link-item <?= _ec('Search_Employee',$tab) ?>" href="Engineer_home.php?EngineerTab=Search_Employee"><i class="fa-solid fa-magnifying-glass lead-ic"></i> Search Employee</a>
</nav>
