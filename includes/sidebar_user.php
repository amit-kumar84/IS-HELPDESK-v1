<?php
$tab = $_GET['UserTab'] ?? 'DashBoard';
function _uc($t,$current){ return $t === $current ? 'active' : ''; }
?>
<nav>
    <div class="nav-section-title">Overview</div>
    <a class="nav-link-item <?= _uc('DashBoard',$tab) ?>" href="home.php?UserTab=DashBoard"><i class="fa-solid fa-gauge-high lead-ic"></i> Dashboard</a>
    <a class="nav-link-item <?= _uc('ChangePassword',$tab) ?>" href="home.php?UserTab=ChangePassword"><i class="fa-solid fa-key lead-ic"></i> Change Password</a>

    <div class="nav-section-title">Service Requests</div>
    <a class="nav-link-item <?= _uc('ComplainRegistrationForm',$tab) ?>" href="home.php?UserTab=ComplainRegistrationForm"><i class="fa-solid fa-circle-exclamation lead-ic"></i> Register Complaint</a>
    <a class="nav-link-item <?= _uc('CartridgeRequestForm',$tab) ?>" href="home.php?UserTab=CartridgeRequestForm"><i class="fa-solid fa-print lead-ic"></i> Cartridge Request</a>
    <a class="nav-link-item <?= _uc('CloseComplain',$tab) ?>" href="home.php?UserTab=CloseComplain"><i class="fa-solid fa-circle-check lead-ic"></i> Close Complaint</a>

    <div class="nav-section-title">My Activity</div>
    <a class="nav-link-item <?= _uc('CallEnquiry',$tab) ?>" href="home.php?UserTab=CallEnquiry"><i class="fa-solid fa-clipboard-list lead-ic"></i> My Complaints</a>
    <a class="nav-link-item <?= _uc('CartridgeEnquiry',$tab) ?>" href="home.php?UserTab=CartridgeEnquiry"><i class="fa-solid fa-receipt lead-ic"></i> Cartridge Orders</a>

    <?php if ($sid === '207512' || $sid === '209488'): ?>
        <a class="nav-link-item <?= _uc('Storage_hd',$tab) ?>" href="home.php?UserTab=Storage_hd"><i class="fa-solid fa-hard-drive lead-ic"></i> Storage Devices</a>
    <?php endif; ?>
    <?php if ($sid === '209488'): ?>
        <a class="nav-link-item <?= _uc('Search_emp',$tab) ?>" href="home.php?UserTab=Search_emp"><i class="fa-solid fa-magnifying-glass lead-ic"></i> Search Employee</a>
    <?php endif; ?>

    <div class="nav-section-title">IT Compliance</div>
    <a class="nav-link-item <?= _uc('NDA_Agreement',$tab) ?>" href="home.php?UserTab=NDA_Agreement"><i class="fa-solid fa-file-signature lead-ic"></i> NDA Agreement</a>
</nav>
