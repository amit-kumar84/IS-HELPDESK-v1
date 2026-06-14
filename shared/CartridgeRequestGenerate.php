<style>
/* Modern Cartridge Request Form Styles */
.cartridge-container {
    max-width: 900px;
    margin: 20px auto;
    font-family: 'Segoe UI', 'Inter', Arial, sans-serif;
}

/* Notification Modal Styles */
.notification-modal {
	display: none;
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background: rgba(0, 0, 0, 0.5);
	backdrop-filter: blur(4px);
	-webkit-backdrop-filter: blur(4px);
	justify-content: center;
	align-items: center;
	z-index: 9999;
	opacity: 0;
	transition: opacity 0.3s ease;
}

.notification-modal.show {
	opacity: 1;
}

.notification-content {
	background: white;
	border-radius: 20px;
	padding: 40px;
	box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
	text-align: center;
	max-width: 420px;
	width: 90%;
	transform: scale(0.8);
	transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
	animation: modalBounce 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.notification-modal.show .notification-content {
	transform: scale(1);
}

@keyframes modalBounce {
	0% { transform: scale(0.5) translateY(-50px); opacity: 0; }
	50% { transform: scale(1.05); }
	100% { transform: scale(1); opacity: 1; }
}

.notification-icon {
	width: 80px;
	height: 80px;
	margin: 0 auto 20px;
	border-radius: 50%;
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 40px;
	font-weight: bold;
	color: white;
	animation: iconScale 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes iconScale {
	0% { transform: scale(0); }
	50% { transform: scale(1.1); }
	100% { transform: scale(1); }
}

.notification-icon.success-icon {
	background: linear-gradient(135deg, #138808 0%, #0a9833 100%);
	box-shadow: 0 8px 20px rgba(19, 136, 8, 0.3);
}

.notification-icon.warning-icon {
	background: linear-gradient(135deg, #ff9933 0%, #ff7700 100%);
	box-shadow: 0 8px 20px rgba(255, 153, 51, 0.3);
}

.notification-icon.error-icon {
	background: linear-gradient(135deg, #d32f2f 0%, #b71c1c 100%);
	box-shadow: 0 8px 20px rgba(211, 47, 47, 0.3);
}

.notification-title {
	color: #0a1f44;
	font-size: 24px;
	font-weight: 700;
	margin: 0 0 10px 0;
	letter-spacing: 0.3px;
}

.notification-message {
	color: #666;
	font-size: 15px;
	line-height: 1.6;
	margin: 0 0 25px 0;
	letter-spacing: 0.2px;
}

.notification-btn {
	padio: 12px 40px;
	background: linear-gradient(135deg, #ff9933 0%, #ea7600 100%);
	color: white;
	border: none;
	border-radius: 10px;
	font-size: 15px;
	font-weight: 600;
	cursor: pointer;
	transition: all 0.3s ease;
	box-shadow: 0 4px 15px rgba(255, 153, 51, 0.3);
	letter-spacing: 0.5px;
	padding: 12px 40px;
}

.notification-btn:hover {
	transform: translateY(-2px);
	box-shadow: 0 6px 20px rgba(255, 153, 51, 0.4);
	background: linear-gradient(135deg, #ffb347 0%, #ff9933 100%);
}

.notification-btn:active {
	transform: translateY(0);
	box-shadow: 0 2px 10px rgba(255, 153, 51, 0.3);
}

.cartridge-header {
    text-align: center;
    margin-bottom: 30px;
    animation: slideInDown 0.6s ease;
}

.cartridge-header h2 {
    font-size: 38px;
    font-weight: 800;
    margin: 0;
    background: linear-gradient(135deg, #ff9933 0%, #0a1f44 50%, #138808 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-shadow: none;
    letter-spacing: 1px;
}

.cartridge-header p {
    color: #666;
    font-size: 14px;
    margin-top: 8px;
}

/* Search Section */
.search-section {
    background: linear-gradient(135deg, rgba(255,153,51,0.1) 0%, rgba(10,31,68,0.1) 100%);
    padding: 20px;
    border-radius: 14px;
    margin-bottom: 25px;
    border: 2px solid rgba(255,153,51,0.3);
    animation: slideInUp 0.6s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.search-section h3 {
    color: #0a1f44;
    margin-top: 0;
    margin-bottom: 15px;
    font-size: 15px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    font-weight: 700;
}

.search-form-group {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    align-items: center;
}

.search-form-group input[type="text"] {
    flex: 1;
    min-width: 250px;
    padding: 12px 16px;
    border: 2px solid #e5edf6;
    border-radius: 10px;
    font-size: 14px;
    transition: all 0.3s ease;
    background: white;
    color: #0a1f44;
}

.search-form-group input[type="text"]:focus {
    outline: none;
    border-color: #ff9933;
    box-shadow: 0 0 0 4px rgba(255,153,51,0.15);
    background: #fff;
}

/* Form Section */
.form-card {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    border: 1px solid #e5edf6;
    animation: slideInUp 0.7s ease;
}

.form-section {
    margin-bottom: 28px;
}

.form-section:last-child {
    margin-bottom: 0;
}

.section-header {
    display: flex;
    align-items: center;
    margin-bottom: 18px;
    padding-bottom: 12px;
    border-bottom: 2px solid #f0f4f9;
}

.section-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    font-weight: bold;
    color: white;
    font-size: 18px;
}

.section-header-primary .section-icon {
    background: linear-gradient(135deg, #ff9933, #ea7600);
}

.section-header-secondary .section-icon {
    background: linear-gradient(135deg, #0a1f44, #1e3a8a);
}

.section-header-tertiary .section-icon {
    background: linear-gradient(135deg, #138808, #16a34a);
}

.section-header h3 {
    margin: 0;
    color: #0a1f44;
    font-size: 16px;
    font-weight: 700;
    letter-spacing: 0.3px;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 18px;
}

.form-row.full {
    grid-template-columns: 1fr;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
    color: #0a1f44;
    font-weight: 600;
    font-size: 14px;
    letter-spacing: 0.3px;
}

.required {
    color: #dc2626;
    margin-left: 4px;
    font-size: 16px;
}

.optional-badge {
    background: linear-gradient(135deg, #e0e7ff, #f3e8ff);
    color: #6366f1;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 11px;
    margin-left: 8px;
    font-weight: 700;
    letter-spacing: 0.5px;
}

.form-group input,
.form-group select,
.form-group textarea {
    padding: 12px 15px;
    border: 2px solid #e5edf6;
    border-radius: 10px;
    font-size: 14px;
    color: #0a1f44;
    background: white;
    font-family: inherit;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.form-group input::placeholder,
.form-group textarea::placeholder {
    color: #94a3b8;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #ff9933;
    box-shadow: 
        0 0 0 4px rgba(255,153,51,0.15),
        0 4px 12px rgba(255,153,51,0.25);
    background: #fefaf5;
    transform: translateY(-1px);
}

.form-group input:read-only,
.form-group input[readonly] {
    background: linear-gradient(135deg, #f8fafc, #f0f4f9);
    color: #64748b;
    border-color: #cbd5e1;
    cursor: not-allowed;
}

.form-group select {
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%230a1f44' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    padding-right: 35px;
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
    font-family: 'Segoe UI', Arial, sans-serif;
    line-height: 1.5;
}

.conditional-input {
    display: none;
    animation: slideInDown 0.3s ease;
}

.conditional-input.show {
    display: block;
}

.help-text {
    font-size: 12px;
    color: #94a3b8;
    margin-top: 6px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.help-text.required-badge {
    color: #dc2626;
    font-weight: 600;
}

/* Color Preview */
.color-options {
    display: flex;
    gap: 10px;
    margin-top: 10px;
    flex-wrap: wrap;
}

.color-swatch {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    cursor: pointer;
    border: 3px solid transparent;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    color: white;
    font-weight: bold;
}

.color-swatch:hover {
    transform: scale(1.1);
    border-color: #0a1f44;
}

.color-swatch.black {
    background: #000;
}

.color-swatch.yellow {
    background: #FFEB3B;
    color: #333;
}

.color-swatch.cyan {
    background: #00BCD4;
}

.color-swatch.magenta {
    background: #E91E63;
}

/* Action Buttons */
.form-actions {
    display: flex;
    gap: 14px;
    justify-content: center;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 2px solid #f0f4f9;
    flex-wrap: wrap;
}

.btn {
    padding: 13px 32px;
    border: 0;
    border-radius: 10px;
    font-weight: 700;
    font-size: 14px;
    letter-spacing: 0.5px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-width: 140px;
    justify-content: center;
}

.btn-submit {
    background: linear-gradient(135deg, #ff9933 0%, #ea7600 50%, #d97706 100%);
    color: white;
    box-shadow: 0 8px 20px rgba(255,153,51,0.35);
    position: relative;
    overflow: hidden;
}

.btn-submit::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s;
}

.btn-submit:hover::before {
    left: 100%;
}

.btn-submit:hover {
    transform: translateY(-3px);
    box-shadow: 0 14px 28px rgba(255,153,51,0.45);
    background: linear-gradient(135deg, #ff9933 0%, #ea7600 50%, #b45309 100%);
}

.btn-submit:active {
    transform: translateY(-1px);
}

.btn-reset {
    background: linear-gradient(135deg, #0891b2 0%, #06b6d4 50%, #0284c7 100%);
    color: white;
    box-shadow: 0 8px 20px rgba(8,145,178,0.35);
    position: relative;
    overflow: hidden;
}

.btn-reset::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s;
}

.btn-reset:hover::before {
    left: 100%;
}

.btn-reset:hover {
    transform: translateY(-3px);
    box-shadow: 0 14px 28px rgba(8,145,178,0.45);
    background: linear-gradient(135deg, #0891b2 0%, #06b6d4 50%, #0369a1 100%);
}

.btn-reset:active {
    transform: translateY(-1px);
}

/* Animations */
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
    .cartridge-header h2 {
        font-size: 28px;
    }
    
    .form-card {
        padding: 20px;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .search-form-group {
        flex-direction: column;
    }
    
    .search-form-group input[type="text"] {
        width: 100%;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
    }
    
    .color-options {
        justify-content: center;
    }
}
</style>

<div class="cartridge-container">
    <!-- Header -->
    <div class="cartridge-header">
        <h2>📦 Request Cartridge</h2>
        <p>Submit a request to get printer cartridges delivered to your location</p>
    </div>

    <!-- Insert data into Request Box  -->
    <?php
    extract($_POST);
    if(isset($submit)) {		
        // for other option pc/laptop
        if($pc_no == 'Other') {
            $pc_no = $other_pc;
        } else {
            $pc_no = $pc_no;
        }
        
        // for other option printer
        if($printer_name == 'Other') {
            $printer_name = $other_printer;
        } else {
            $printer_name = $printer_name;
        }
        
        date_default_timezone_set('Asia/Kolkata');
        $request_date = date('d-m-Y h:i:s A');	
        
        $issue_qty = '1';
        
        // ticket num generate code	
        date_default_timezone_set('Asia/Kolkata');
        $yer = date('Y');
        $yr = substr($yer,2);
        $mnth = date('m');
        $dte = date('d');
        
        //Fetch last ID Number From Database to increment for next call
        $last_ticket_num = mysqli_query($link,"SELECT * FROM `cartridge_ticket_no`");
        while($id_fetch_d = mysqli_fetch_array($last_ticket_num)) {
            $randm_no_var = $id_fetch_d["ticket_num"];
        }
        $randm_no = $randm_no_var + 1;
        
        $crt_tikt_no = "C" .$yr .$mnth .$dte .$randm_no;
        
        mysqli_query($link,"UPDATE `cartridge_ticket_no` SET `req_number`='$crt_tikt_no',`ticket_num`='$randm_no'");
        
        $cartridge_data = mysqli_query($link,"SELECT * FROM `printer_cartridge_list` WHERE `model`='$printer_name'");
        $cartridge_data_array = mysqli_fetch_array($cartridge_data);
        $cartridge_no = $cartridge_data_array["cartridge_no"];
        
        if(mysqli_query($link,"INSERT INTO `request_master`(`request_no`, `staff_no`, `username`, `department`, `sec`, `cartridge_no`,`color`, `printer_name`, `pc_no`, `ph_no`, `description`, `issue_qty`, `issue_date`, `request_date`, `Status`) VALUES
        ('$crt_tikt_no','$staff_id','$uname','$deptt','$sec','$cartridge_no', '$color', '$printer_name','$pc_no','$ph_no','$description','$issue_qty','','$request_date', 'Pending')")) {
            $stock_check_arr = mysqli_query($link,"SELECT * FROM `cartridge_stock_list` WHERE `cartridge_no`='$cartridge_no' AND `color`='$color'");
            $stock_check = mysqli_fetch_array($stock_check_arr);
            $stock_quant = $stock_check["stock_qty"];
            
            if($stock_quant > '0') {
                echo '<script language="javascript">window.pendingNotification = {type: "success", title: "Request Submitted Successfully!", message: "Your Ticket ID: ' . $crt_tikt_no . '", shouldRefresh: false};</script>';
            } else {
                echo '<script language="javascript">window.pendingNotification = {type: "warning", title: "Request Submitted!", message: "Cartridge is currently out of stock but will be issued when available.", shouldRefresh: false};</script>';
            }
        } else {
            echo '<script language="javascript">window.pendingNotification = {type: "error", title: "ERROR!", message: "Failed to submit request. Please try again.", shouldRefresh: false};</script>';
        }
    }
    ?>
    
    <!-- Search Form -->
    <?php
    extract($_POST);
    if(isset($search)) {
        $filter_Result=mysqli_query($link,"SELECT * FROM `emp_details` WHERE `staffid`='$valueToSearch'");
        $search_result=mysqli_fetch_array($filter_Result);
        $stff_no= $search_result['staffid'];
    }
    ?>
    
    <div class="search-section">
        <h3>👤 Step 1: Find Your Staff Record</h3>
        <form id="form1" name="form1" method="POST">
            <div class="search-form-group">
                <input type="text" name="valueToSearch" id="valueToSearch" required placeholder="Enter Staff Number (e.g., EMP123)" />
                <button type="submit" name="search" class="btn btn-submit">🔍 Search</button>
            </div>
        </form>  
    </div>
    
    <!-- Cartridge Request Form -->
    <form id="form2" name="form2" method="post">
        <div class="form-card">
            
            <!-- Personal Information Section -->
            <div class="form-section">
                <div class="section-header section-header-primary">
                    <div class="section-icon">👤</div>
                    <h3>Personal Information</h3>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>
                            Staff Number
                            <span class="required">*</span>
                        </label>
                        <input type="text" name="staff_id" id="ustaffno" required readonly value="<?php echo $search_result['staffid'] ?? ''; ?>" placeholder="Auto-filled from search" />
                    </div>
                    
                    <div class="form-group">
                        <label>
                            Name
                            <span class="required">*</span>
                        </label>
                        <input type="text" name="uname" id="uname" required readonly value="<?php echo $search_result['username'] ?? ''; ?>" placeholder="Auto-filled from search" />
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>
                            Department
                            <span class="required">*</span>
                        </label>
                        <input type="text" name="deptt" id="udept" required value="<?php echo $search_result['deptt'] ?? ''; ?>" placeholder="Enter Department" />
                    </div>
                    
                    <div class="form-group">
                        <label>
                            Section
                            <span class="required">*</span>
                        </label>
                        <input type="text" name="sec" id="usec" required value="<?php echo $search_result['sec'] ?? ''; ?>" placeholder="Enter Section" />
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>
                            Phone Number
                            <span class="required">*</span>
                        </label>
                        <input type="text" name="ph_no" id="uphoneno" required value="<?php echo $search_result['phone_no'] ?? ''; ?>" placeholder="Enter Phone/Extension Number" />
                    </div>
                </div>
            </div>
            
            <!-- Hardware Section -->
            <div class="form-section">
                <div class="section-header section-header-secondary">
                    <div class="section-icon">🖨️</div>
                    <h3>Hardware Details</h3>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>
                            PC / Laptop Number
                            <span class="required">*</span>
                        </label>
                        <select name="pc_no" id="call_catg2" onchange='check_PC(this.value);' required>
                            <option value="" disabled selected>Select Your Device</option>
                            <?php
                            $master_data_fetch=mysqli_query($link,"SELECT `HD_ID_NO` FROM `hardware_master` WHERE (`STAFF_NO`='$stff_no') AND (`CATG`='PC' OR `CATG`='Laptop')");
                            while($master_data_arr=mysqli_fetch_array($master_data_fetch)) {
                                echo "<option>" . htmlspecialchars($master_data_arr["HD_ID_NO"]) . "</option>";
                            }
                            ?>
                            <option value="Other">📝 Other (Not Listed)</option>
                        </select>
                        <select name="other_pc" id="inputbox1" class="conditional-input">
                            <option value="" disabled selected>Select From All Devices</option>
                            <?php
                            $master_data_fetch=mysqli_query($link,"SELECT `HD_ID_NO` FROM `hardware_master` WHERE `CATG`='PC' AND `USG`!='WO'");
                            while($master_data_arr=mysqli_fetch_array($master_data_fetch)) {
                                echo "<option>" . htmlspecialchars($master_data_arr["HD_ID_NO"]) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            Printer
                            <span class="required">*</span>
                        </label>
                        <select name="printer_name" id="pcNo" onchange='check_Printer(this.value)' required>
                            <option value="" disabled selected>Select Your Printer</option>
                            <?php
                            $master_data_fetch = mysqli_query($link,"SELECT * FROM `hardware_master` WHERE `STAFF_NO`='$stff_no' AND `CATG`='PRINTER'");
                            while($master_data_arr = mysqli_fetch_array($master_data_fetch)) {
                                echo "<option>" . htmlspecialchars($master_data_arr["MODEL"]) . "</option>";
                            }
                            ?>
                            <option value="Other">📝 Other</option>
                        </select>
                        <select name="other_printer" id="inputbox2" class="conditional-input">
                            <option value="" disabled selected>Select From All Printers</option>
                            <?php
                            $master_data_fetch = mysqli_query($link,"SELECT DISTINCT(MODEL) FROM `hardware_master` WHERE `CATG`='PRINTER' AND `USG`!='WO'");
                            while($master_data_arr = mysqli_fetch_array($master_data_fetch)) {
                                echo "<option>" . htmlspecialchars($master_data_arr["MODEL"]) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Cartridge Details Section -->
            <div class="form-section">
                <div class="section-header section-header-tertiary">
                    <div class="section-icon">🎨</div>
                    <h3>Cartridge Details</h3>
                </div>
                
                <div class="form-row full">
                    <div class="form-group">
                        <label>
                            Cartridge Color
                            <span class="required">*</span>
                        </label>
                        <select name="color" required onchange="highlightColor(this.value)">
                            <option value="BLACK" selected>⬛ BLACK</option>
                            <option value="YELLOW">🟨 YELLOW</option>
                            <option value="CYAN">🟦 CYAN</option>
                            <option value="MAGENTA">🟪 MAGENTA</option>
                        </select>
                        <div class="color-options">
                            <div class="color-swatch black" title="Black" onclick="selectColor('BLACK')">BLACK</div>
                            <div class="color-swatch yellow" title="Yellow" onclick="selectColor('YELLOW')">YEL</div>
                            <div class="color-swatch cyan" title="Cyan" onclick="selectColor('CYAN')">CYA</div>
                            <div class="color-swatch magenta" title="Magenta" onclick="selectColor('MAGENTA')">MAG</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Additional Information Section -->
            <div class="form-section">
                <div class="section-header section-header-primary">
                    <div class="section-icon">📝</div>
                    <h3>Additional Information</h3>
                </div>
                
                <div class="form-row full">
                    <div class="form-group">
                        <label>
                            Description
                            <span class="optional-badge">Optional</span>
                        </label>
                        <textarea name="description" placeholder="Add any notes or special instructions...&#10;Example: Urgent, or specific delivery instructions."></textarea>
                        <p class="help-text">💬 Max 150 characters</p>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="form-actions">
                <button type="reset" class="btn btn-reset">↺ Reset Form</button>
                <button type="submit" name="submit" class="btn btn-submit">✓ Submit Request</button>
            </div>
        </div>
    </form>
</div>

<!-- Custom Notification Modal -->
<div id="notificationModal" class="notification-modal">
	<div class="notification-content">
		<div id="notificationIcon" class="notification-icon success-icon">✓</div>
		<h3 id="notificationTitle" class="notification-title">Success</h3>
		<p id="notificationMessage" class="notification-message">Your action was successful</p>
		<button id="notificationOkBtn" class="notification-btn">OK</button>
	</div>
</div>
<script>
	// Handle conditional input display with modern animations
	function check_PC(val){
		var element = document.getElementById('inputbox1');
		if(val == '' || val == 'Other'){
			element.classList.add('show');
			element.setAttribute("required", "required");
		} else {
			element.classList.remove('show');
			element.removeAttribute("required");
		}
	}
	
	function check_Printer(val){
		var element = document.getElementById('inputbox2');
		if(val == '' || val == 'Other'){
			element.classList.add('show');
			element.setAttribute("required", "required");
		} else {
			element.classList.remove('show');
			element.removeAttribute("required");
		}
	}
	
	// Color selection functions
	function selectColor(color) {
		var select = document.querySelector('select[name="color"]');
		select.value = color;
		highlightColor(color);
	}
	
	function highlightColor(color) {
		// Reset all color swatches
		var swatches = document.querySelectorAll('.color-swatch');
		swatches.forEach(function(swatch) {
			swatch.style.borderColor = 'transparent';
		});
		
		// Highlight selected swatch
		var selectedSwatch = document.querySelector('.color-swatch.' + color.toLowerCase());
		if(selectedSwatch) {
			selectedSwatch.style.borderColor = '#0a1f44';
			selectedSwatch.style.boxShadow = '0 0 0 2px #0a1f44';
		}
	}
	
	// Enhanced Reset Button with Visual Feedback
	function setupResetButton() {
		var resetButtons = document.querySelectorAll('.btn-reset');
		resetButtons.forEach(function(btn) {
			btn.addEventListener('click', function(e) {
				// Get the form
				var form = btn.closest('form');
				if(form) {
					// Add animation effect
					btn.style.animation = 'none';
					setTimeout(() => {
						btn.style.animation = 'pulse 0.3s ease';
					}, 10);
					
					// Reset the form
					form.reset();
					
					// Clear any conditional inputs that were shown
					var conditionalInputs = form.querySelectorAll('.conditional-input');
					conditionalInputs.forEach(function(input) {
						input.classList.remove('show');
						input.removeAttribute("required");
					});
					
					// Reset select dropdowns
					var selects = form.querySelectorAll('select');
					selects.forEach(function(select) {
						select.selectedIndex = 0;
					});
					
					// Reset color selection to BLACK
					highlightColor('BLACK');
					
					// Show visual feedback
					btn.innerHTML = '✓ Form Reset!';
					btn.style.opacity = '0.8';
					
					setTimeout(() => {
						btn.innerHTML = '↺ Reset Form';
						btn.style.opacity = '1';
					}, 1500);
				}
			});
		});
	}
	
	// Add pulse animation for reset feedback
	var style = document.createElement('style');
	style.textContent = `
		@keyframes pulse {
			0%, 100% { transform: scale(1); }
			50% { transform: scale(0.95); }
		}
	`;
	document.head.appendChild(style);
	
	// Custom Notification Modal Function
	function showNotification(type, title, message, shouldRefresh = false, shouldResetForm = false) {
		const modal = document.getElementById('notificationModal');
		const icon = document.getElementById('notificationIcon');
		const titleEl = document.getElementById('notificationTitle');
		const messageEl = document.getElementById('notificationMessage');
		const okBtn = document.getElementById('notificationOkBtn');
		
		// Set content
		titleEl.textContent = title;
		messageEl.textContent = message;
		
		// Set styling based on type
		modal.className = 'notification-modal notification-' + type + ' show';
		
		if(type === 'success') {
			icon.innerHTML = '✓';
			icon.className = 'notification-icon success-icon';
		} else if(type === 'warning') {
			icon.innerHTML = '⚠';
			icon.className = 'notification-icon warning-icon';
		} else if(type === 'error') {
			icon.innerHTML = '✕';
			icon.className = 'notification-icon error-icon';
		}
		
		// Handle OK button
		okBtn.onclick = function() {
			modal.classList.remove('show');
			setTimeout(() => {
				modal.style.display = 'none';
				
				// Reset form if needed
				if(shouldResetForm) {
					const form2 = document.getElementById('form2');
					if(form2) form2.reset();
					const form1 = document.getElementById('form1');
					if(form1) form1.reset();
				}
				
				// Refresh page if needed
				if(shouldRefresh) {
					location.reload();
				}
			}, 300);
		};
		
		// Show modal
		modal.style.display = 'flex';
	}
	
	// Add subtle animations on page load
	document.addEventListener('DOMContentLoaded', function() {
		// Add fade-in animation to form elements
		const elements = document.querySelectorAll('.form-group input, .form-group select, .form-group textarea');
		elements.forEach((el, index) => {
			el.style.animation = `slideInUp 0.6s ease ${index * 0.05}s backwards`;
		});
		
		// Initialize color highlighting
		highlightColor('BLACK');
		
		// Show pending notification if exists
		if(window.pendingNotification) {
			const notif = window.pendingNotification;
			showNotification(notif.type, notif.title, notif.message, notif.shouldRefresh, true);
			delete window.pendingNotification;
		}
		
		// Setup reset button functionality
		setupResetButton();
	});
</script> 
