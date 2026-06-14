<style>
/* Modern Cartridge Request Table Styles */
.cartridge-requests-container {
    max-width: 1600px;
    margin: 20px auto;
    font-family: 'Segoe UI', 'Inter', Arial, sans-serif;
}

.table-header {
    margin-bottom: 25px;
    animation: slideInDown 0.6s ease;
}

.table-header h2 {
    font-size: 32px;
    font-weight: 800;
    margin: 0 0 8px 0;
    background: linear-gradient(135deg, #ff9933 0%, #0a1f44 50%, #138808 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.table-header p {
    color: #666;
    font-size: 14px;
    margin: 0;
}

.table-stats {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.stat-card {
    background: linear-gradient(135deg, rgba(255,153,51,0.1) 0%, rgba(255,153,51,0.05) 100%);
    border: 2px solid rgba(255,153,51,0.3);
    border-radius: 12px;
    padding: 15px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 200px;
}

.stat-icon {
    font-size: 24px;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #ff9933, #ea7600);
    color: white;
    border-radius: 8px;
    font-weight: bold;
}

.stat-text h3 {
    margin: 0;
    color: #0a1f44;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 700;
}

.stat-text p {
    margin: 4px 0 0 0;
    color: #ff9933;
    font-size: 20px;
    font-weight: 800;
}

.table-wrapper {
    background: white;
    border-radius: 14px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    border: 1px solid #e5edf6;
    overflow: hidden;
    animation: slideInUp 0.7s ease;
}

.table-responsive {
    overflow-x: auto;
}

.modern-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
}

.modern-table thead {
    background: linear-gradient(135deg, rgba(255,153,51,0.1) 0%, rgba(255,153,51,0.05) 100%);
    border-bottom: 2px solid #e5edf6;
}

.modern-table thead th {
    padding: 16px 14px;
    text-align: left;
    color: #0a1f44;
    font-weight: 700;
    font-size: 12px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    border-bottom: 2px solid #ff9933;
}

.modern-table tbody tr {
    border-bottom: 1px solid #f0f4f9;
    transition: all 0.2s ease;
}

.modern-table tbody tr:hover {
    background: linear-gradient(135deg, rgba(255,153,51,0.08) 0%, rgba(255,153,51,0.03) 100%);
    box-shadow: inset 0 0 0 1px rgba(255,153,51,0.1);
}

.modern-table tbody td {
    padding: 14px 14px;
    color: #0a1f44;
    font-size: 13px;
}

.modern-table tbody tr:last-child {
    border-bottom: none;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.badge-pending {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    color: #92400e;
    border: 1px solid #fcd34d;
}

.badge-issued {
    background: linear-gradient(135deg, #dcfce7, #bbf7d0);
    color: #166534;
    border: 1px solid #86efac;
}

.color-badge {
    display: inline-block;
    width: 24px;
    height: 24px;
    border-radius: 6px;
    border: 2px solid #cbd5e1;
    vertical-align: middle;
}

.color-badge.black {
    background: #000;
}

.color-badge.yellow {
    background: #FFC107;
    border-color: #FF9800 !important;
}

.color-badge.cyan {
    background: #00BCD4;
}

.color-badge.magenta {
    background: #E91E63;
}

.btn-group {
    display: flex;
    gap: 8px;
}

.btn-action {
    padding: 8px 14px;
    border: 0;
    border-radius: 6px;
    font-weight: 700;
    font-size: 11px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    background: linear-gradient(135deg, #ff9933, #ea7600);
    color: white;
    box-shadow: 0 4px 12px rgba(255,153,51,0.3);
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(255,153,51,0.4);
    background: linear-gradient(135deg, #ff9933, #d97706);
}

.btn-action:active {
    transform: translateY(0);
}

.user-thumbnail {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    border: 2px solid #e5edf6;
    cursor: pointer;
    transition: all 0.2s ease;
}

.user-thumbnail:hover {
    border-color: #ff9933;
    box-shadow: 0 0 0 3px rgba(255,153,51,0.2);
    transform: scale(1.05);
}

/* Tooltip Styles */
.tooltip {
    position: relative;
    display: inline-block;
    cursor: pointer;
}

.tooltip .tooltiptext {
    visibility: hidden;
    background-color: rgba(10,31,68,0.95);
    color: #fff;
    text-align: center;
    border-radius: 8px;
    padding: 8px;
    position: absolute;
    z-index: 100;
    bottom: 125%;
    left: 50%;
    transform: translateX(-50%);
    white-space: nowrap;
    font-size: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    animation: tooltipFade 0.3s ease;
}

.tooltip .tooltiptext img {
    width: 100px;
    height: 120px;
    border-radius: 6px;
    margin-top: 8px;
    display: block;
}

.tooltip:hover .tooltiptext {
    visibility: visible;
}

@keyframes tooltipFade {
    from { opacity: 0; transform: translateX(-50%) translateY(-5px); }
    to { opacity: 1; transform: translateX(-50%) translateY(0); }
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .table-responsive {
        overflow-x: auto;
    }
    
    .modern-table {
        font-size: 12px;
    }
    
    .modern-table thead th,
    .modern-table tbody td {
        padding: 10px 8px;
    }
    
    .table-stats {
        flex-direction: column;
    }
    
    .stat-card {
        min-width: auto;
    }
}
</style>

<div class="cartridge-requests-container">
    <div class="table-header">
        <h2>📋 Cartridge Requests Management</h2>
        <p>Manage and process pending cartridge requests</p>
    </div>
    
    <div class="table-stats">
        <div class="stat-card">
            <div class="stat-icon">⏳</div>
            <div class="stat-text">
                <h3>Pending Requests</h3>
                <p id="pending-count">0</p>
            </div>
        </div>
    </div>
    
    <div class="table-wrapper">
        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>🎫 Ticket No</th>
                        <th>📅 Request Date</th>
                        <th>🏢 Department</th>
                        <th>📍 Section</th>
                        <th>👤 Staff No</th>
                        <th>👥 Username</th>
                        <th>🖥️ PC No</th>
                        <th>📞 Phone</th>
                        <th>🖨️ Printer</th>
                        <th>📦 Cartridge</th>
                        <th>🎨 Color</th>
                        <th>📊 Qty</th>
                        <th>⚡ Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    extract($_GET);
                    $cartridge_data = mysqli_query($link,"SELECT * FROM `request_master` WHERE `Status`='Pending' ORDER BY substring(request_no,2,6) DESC, substring(request_no,7,12) DESC");
                    
                    $total_row_count = mysqli_num_rows($cartridge_data);
                    ?>
                    <script>
                        document.getElementById('pending-count').textContent = <?php echo $total_row_count; ?>;
                    </script>
                    <?php
                    
                    while($cartridge_data_array = mysqli_fetch_array($cartridge_data)) {
                        $req_no = $cartridge_data_array["request_no"];
                        $req_date = $cartridge_data_array["request_date"];
                        $deprtment = $cartridge_data_array["department"];
                        $section = $cartridge_data_array["sec"];
                        $staff_no = $cartridge_data_array["staff_no"];
                        $user = $cartridge_data_array["username"];
                        $pc = $cartridge_data_array["pc_no"];
                        $ph = $cartridge_data_array["ph_no"];
                        $printer = $cartridge_data_array["printer_name"];
                        $cart_no = $cartridge_data_array["cartridge_no"];
                        $color = strtolower($cartridge_data_array["color"]);
                        $issue_qty = $cartridge_data_array["issue_qty"];
                    ?>
                    <form id="form2" action="" name="form2" method="POST">
                        <tr>
                            <td><strong><?php echo htmlspecialchars($req_no); ?></strong></td>
                            <td><?php echo htmlspecialchars($req_date); ?></td>
                            <td><?php echo htmlspecialchars($deprtment); ?></td>
                            <td><?php echo htmlspecialchars($section); ?></td>
                            <td><?php echo htmlspecialchars($staff_no); ?></td>
                            <td>
                                <div class="tooltip">
                                    <?php echo htmlspecialchars($user); ?>
                                    <span class="tooltiptext">
                                        <img src="Pictures/<?php echo htmlspecialchars($staff_no); ?>.JPG" alt="User Image" onerror="this.style.display='none'" />
                                    </span>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($pc); ?></td>
                            <td><?php echo htmlspecialchars($ph); ?></td>
                            <td style="text-align: center;">
                                <select name="printer" required onchange="this.style.borderColor='#ff9933'">
                                    <option value="<?php echo htmlspecialchars($printer); ?>" selected><?php echo htmlspecialchars($printer); ?></option>
                                    <?php
                                    $cartridge_data_fetch = mysqli_query($link,"SELECT * FROM `printer_cartridge_list`");
                                    while($cartridge_data_arr = mysqli_fetch_array($cartridge_data_fetch)) {
                                        echo "<option>" . htmlspecialchars($cartridge_data_arr["model"]) . "</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                            <td style="text-align: center;">
                                <select name="cartridge_no" required onchange="this.style.borderColor='#ff9933'">
                                    <option value="<?php echo htmlspecialchars($cart_no); ?>" selected><?php echo htmlspecialchars($cart_no); ?></option>
                                    <?php
                                    $cartridge_data_fetch = mysqli_query($link,"SELECT * FROM `cartridge_stock_list`");
                                    while($cartridge_data_arr = mysqli_fetch_array($cartridge_data_fetch)) {
                                        echo "<option>" . htmlspecialchars($cartridge_data_arr["cartridge_no"]) . "</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                            <td style="text-align: center;">
                                <select name="color" class="color-select" required onchange="updateColorBadge(this)">
                                    <option value="<?php echo htmlspecialchars(strtoupper($color)); ?>" selected><?php echo htmlspecialchars(strtoupper($color)); ?></option>
                                    <?php 
                                    $color_upper = strtoupper($color);
                                    $available_colors = array('BLACK', 'YELLOW', 'CYAN', 'MAGENTA');
                                    foreach($available_colors as $col) {
                                        if($col !== $color_upper) {
                                            echo "<option value=\"$col\">$col</option>";
                                        }
                                    }
                                    ?>
                                </select>
                                <div style="margin-top: 4px;">
                                    <span class="color-badge <?php echo $color; ?>" title="<?php echo ucfirst($color); ?>"></span>
                                </div>
                            </td>
                            <td style="text-align: center;"><?php echo htmlspecialchars($issue_qty); ?></td>
                            <td style="text-align: center;">
                                <button type="submit" name="subm<?php echo htmlspecialchars($req_no); ?>" class="btn-action">✓ Issue</button>
                            </td>
                        </tr>
                    </form>
                    <?php
                    extract($_POST);
                    if(isset($_POST["subm".$req_no])) {
                        date_default_timezone_set('Asia/Kolkata');
                        $issued_dt = date('d-m-Y h:i:s A');
                        
                        $stock_check_arr = mysqli_query($link,"SELECT * FROM `cartridge_stock_list` WHERE `cartridge_no`='$cartridge_no' AND `color`='$color'");
                        $stock_check = mysqli_fetch_array($stock_check_arr);
                        $stock_quant = $stock_check["stock_qty"];
                        
                        if($stock_quant > '0') {			
                            if((mysqli_query($link,"UPDATE `request_master` SET `printer_name`='$printer', `cartridge_no`='$cartridge_no', `color`='$color', `issue_date`='$issued_dt', `Status`='Issued' WHERE `request_no`='$req_no'")) AND (mysqli_query($link,"UPDATE `cartridge_stock_list` SET `stock_qty`=stock_qty - 1,`last_issue_qty`='1',`last_issue_no`='$req_no',`last_issue_date`='$issued_dt' WHERE `cartridge_no`='$cartridge_no' AND `color`='$color'"))) {
                                echo "<meta http-equiv='refresh' content='0'>";
                                echo '<script language="javascript">alert("✅ Cartridge issued successfully for Ticket: ' . $req_no . '");</script>';
                            } else {
                                echo "<meta http-equiv='refresh' content='0'>";
                                echo '<script language="javascript">alert("❌ Database Error! Please try again.");</script>';
                            }
                        } else {
                            echo "<meta http-equiv='refresh' content='0'>";
                            echo '<script language="javascript">alert("⚠️ Cartridge is currently out of stock!");</script>';
                        }
                    }
                    ?>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function updateColorBadge(selectElement) {
    // Get the selected color value
    const selectedColor = selectElement.value.toLowerCase();
    
    // Find the color badge in the same row
    const colorBadge = selectElement.closest('td').querySelector('.color-badge');
    
    if(colorBadge) {
        // Remove all color classes
        colorBadge.className = 'color-badge';
        
        // Add the selected color class
        colorBadge.classList.add(selectedColor);
        
        // Update the title
        colorBadge.title = selectedColor.charAt(0).toUpperCase() + selectedColor.slice(1);
    }
    
    // Change border color on select
    selectElement.style.borderColor = '#ff9933';
}
</script>