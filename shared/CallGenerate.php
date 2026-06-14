
<style>
/* Modern Call Generation Form Styles */
.call-generate-container {
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

.call-gen-header {
    text-align: center;
    margin-bottom: 30px;
    animation: slideInDown 0.6s ease;
}

.call-gen-header h2 {
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

.call-gen-header p {
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
    min-height: 120px;
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

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

/* Responsive Design */
@media (max-width: 768px) {
    .call-gen-header h2 {
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
}
</style>

<div class="call-generate-container">
    <!-- Header -->
    <div class="call-gen-header">
        <h2>🎫 Generate Support Ticket</h2>
        <p>Create a new support request and get assistance from our technical team</p>
    </div>

    <!-- Insert data into Complain Box  -->
    <?php
    extract($_POST);
    if(isset($sub)) {
        // ticket num generate code	
        date_default_timezone_set('Asia/Kolkata');
        $yer = date('Y');
        $yr = substr($yer,2);
        $mnth = date('m');
        $dte = date('d');
        $type = $call_catg;
        
        //Fetch last ID Number From Database to increment for next call
        $last_id_num = mysqli_query($link,"SELECT `ticket_num` FROM `ticket_no` WHERE `token`=`token`");
        while($id_fetch = mysqli_fetch_array($last_id_num)) {
            $randm_no_var = $id_fetch["ticket_num"];
        }
        $randm_no = $randm_no_var + 1;
        $ticket_g_no = $yr .$mnth .$dte .$type .$randm_no;
        
        mysqli_query($link,"UPDATE `ticket_no` SET `req_number`='$ticket_g_no',`ticket_num`='$randm_no'");
        
        date_default_timezone_set('Asia/Kolkata');
        $regis_dt = date('d-m-Y h:i:s A');
        
        // for other option call category 	
        if($call_catg == 1) { $category = 'Hardware'; }
        else if($call_catg == 2) { $category = 'Software'; }
        else if($call_catg == 3) { $category = 'Network'; }
        else if($call_catg == 6) { $category = 'Server'; }
        else if($call_catg == 4) { $category = 'Virus'; }
        else if($call_catg == 5) { $category = 'VDI'; }
        else if($call_catg == 6) { $category = $other_catg; }
        
        // for other option problem on
        if($problem_on == 'PC') { $Problem_sys = "PC"; }
        else if($problem_on == 'Printer') { $Problem_sys = 'Printer'; }
        else if($problem_on == 'Laptop') { $Problem_sys = 'Laptop'; }
        else if($problem_on == 'SAP') { $Problem_sys = 'SAP'; }
        else if($problem_on == 'Internet') { $Problem_sys = 'Internet'; }
        else if($problem_on == 'Lease Line') { $Problem_sys = 'Lease Line'; }
        else if($problem_on == 'Broadband Line') { $Problem_sys = 'Broadband Line'; }
        else if($problem_on == 'VDI') { $Problem_sys = 'VDI'; }
        else if($problem_on == 'Other') { $Problem_sys = $other_prob_on; }
        
        // for other option pc/laptop
        if($upc_no == 'Other') { $upc_no = $other_upc_no; }
        else { $upc_no = $upc_no; }
        
        // for other option printer
        if($printer_no == 'Other') { $printer_no = $other_printer_no; }
        else { $printer_no = $printer_no; }
        
        if(mysqli_query($link,"INSERT INTO `complain_register`(`t_no`, `r_DateTime`, `dept`,`sec`, `user_name`, `Staff_no`, `phone_no`, `pc_no`, `printer`, `problem_on`, `problem_type`, `problem`, `support_engg`, `solution`, `s_DateTime`, `status`) VALUES
        ('$ticket_g_no','$regis_dt','$udept','$usec','$uname','$ustaffno','$uphoneno','$upc_no','$printer_no','$Problem_sys','$category','$uprob','','','','Pending')")) {
            echo '<script language="javascript">window.pendingNotification = {type: "success", title: "Ticket Submitted Successfully!", message: "Your Ticket ID: ' . $ticket_g_no . '", shouldRefresh: false};</script>';
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
    
    <!-- Ticket Generation Form -->
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
                        <input type="text" name="ustaffno" id="ustaffno" required readonly value="<?php echo $search_result['staffid'] ?? ''; ?>" placeholder="Auto-filled from search" />
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
                        <input type="text" name="udept" id="udept" required value="<?php echo $search_result['deptt'] ?? ''; ?>" placeholder="Enter Department" />
                    </div>
                    
                    <div class="form-group">
                        <label>
                            Section
                            <span class="required">*</span>
                        </label>
                        <input type="text" name="usec" id="usec" required value="<?php echo $search_result['sec'] ?? ''; ?>" placeholder="Enter Section" />
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>
                            Phone Number
                            <span class="required">*</span>
                        </label>
                        <input type="text" name="uphoneno" id="uphoneno" required value="<?php echo $search_result['ip_phone'] ?? ''; ?>" placeholder="Enter Phone/Extension Number" />
                    </div>
                </div>
            </div>
            
            <!-- Problem Details Section -->
            <div class="form-section">
                <div class="section-header section-header-secondary">
                    <div class="section-icon">🔧</div>
                    <h3>Problem Details</h3>
                </div>
                
                <div class="form-row full">
                    <div class="form-group">
                        <label>
                            Problem Type
                            <span class="required">*</span>
                        </label>
                        <select name="call_catg" id="call_catg" onchange='check_call_catg(this.value);' required>
                            <option value="" disabled selected>Select Problem Category</option>
                            <option value="1">🖥️ Hardware</option>
                            <option value="2">💾 Software</option>
                            <option value="3">🌐 Network</option>
                            <option value="6">📡 Server</option>
                            <option value="4">⚠️ Virus</option>
                            <option value="5">📝 Other</option>
                        </select>
                        <input type="text" name="other_catg" id="inputbox1" class="conditional-input" placeholder="Specify Problem Type" />
                    </div>
                </div>
                
                <div class="form-row full">
                    <div class="form-group">
                        <label>
                            Problem On (Device Type)
                            <span class="required">*</span>
                        </label>
                        <select name="problem_on" id="problem_on" onchange='check_prob_on(this.value);' required>
                            <option value="" disabled selected>Select Device Type</option>
                            <?php
                            $prob_on_data_fetch = mysqli_query($link,"SELECT * FROM `Problem_On`");
                            while($prob_on_data_arr = mysqli_fetch_array($prob_on_data_fetch)) {
                                echo "<option>" . htmlspecialchars($prob_on_data_arr["Problem_On"]) . "</option>";
                            }
                            ?>
                            <option value="Lease Line">📞 Lease Line</option>
                            <option value="Broadband Line">📡 Broadband Line</option>
                            <option value="Other">📝 Other</option>
                        </select>
                        <input type="text" name="other_prob_on" id="inputbox2" class="conditional-input" placeholder="Specify Device Type" />
                    </div>
                </div>
            </div>
            
            <!-- Hardware Information Section -->
            <div class="form-section">
                <div class="section-header section-header-tertiary">
                    <div class="section-icon">💻</div>
                    <h3>Hardware Details</h3>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>
                            PC / Laptop / VDI Number
                            <span class="required">*</span>
                        </label>
                        <select name="upc_no" id="call_catg2" onchange='check_PC(this.value);' required>
                            <option value="" disabled selected>Select Your Device</option>
                            <?php
                            $master_data_fetch=mysqli_query($link,"SELECT `HD_ID_NO` FROM `hardware_master` WHERE (`STAFF_NO`='$stff_no') AND (`CATG`='PC' OR `CATG`='LAPTOP' OR `CATG`='VDI' )");
                            while($master_data_arr=mysqli_fetch_array($master_data_fetch)) {
                                echo "<option>" . htmlspecialchars($master_data_arr["HD_ID_NO"]) . "</option>";
                            }
                            ?>
                            <option value="Other">📝 Other (Not Listed)</option>
                        </select>
                        <select name="other_upc_no" id="inputbox3" class="conditional-input">
                            <option value="" disabled selected>Select From All Devices</option>
                            <?php
                            $master_data_fetch_full = mysqli_query($link,"SELECT `HD_ID_NO` FROM `hardware_master` WHERE `CATG`='PC' OR `CATG`='VDI' OR `CATG`='LAPTOP' OR `CATG`='NETWORK'");
                            while($master_data_arr_full = mysqli_fetch_array($master_data_fetch_full)) {
                                echo "<option>" . htmlspecialchars($master_data_arr_full["HD_ID_NO"]) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            Printer
                            <span class="optional-badge">Optional</span>
                        </label>
                        <select name="printer_no" id="pcNo" onchange='check_Printer(this.value)'>
                            <option value="" disabled selected>Select Your Printer</option>
                            <?php
                            $master_data_fetch = mysqli_query($link,"SELECT * FROM `hardware_master` WHERE `STAFF_NO`='$stff_no' AND `CATG`='PRINTER' ORDER BY MODEL ASC");
                            while($master_data_arr = mysqli_fetch_array($master_data_fetch)) {
                                echo "<option>" . htmlspecialchars($master_data_arr["MODEL"]) . "</option>";
                            }
                            ?>
                            <option value="Other">📝 Other</option>
                        </select>
                        <select name="other_printer_no" id="inputbox4" class="conditional-input">
                            <option value="" disabled selected>Select From All Printers</option>
                            <?php
                            $master_data_fetch = mysqli_query($link,"SELECT DISTINCT(MODEL) FROM `hardware_master` WHERE `CATG`='PRINTER' ORDER BY MODEL ASC");
                            while($master_data_arr = mysqli_fetch_array($master_data_fetch)) {
                                echo "<option>" . htmlspecialchars($master_data_arr["MODEL"]) . "</option>";
                            }
                            ?>
                        </select>
                        <p class="help-text">💡 Select only if you need printer support</p>
                    </div>
                </div>
            </div>
            
            <!-- Problem Description Section -->
            <div class="form-section">
                <div class="section-header section-header-primary">
                    <div class="section-icon">📝</div>
                    <h3>Problem Description</h3>
                </div>
                
                <div class="form-row full">
                    <div class="form-group">
                        <label>
                            Describe Your Issue
                            <span class="required">*</span>
                        </label>
                        <textarea name="uprob" id="uprob" required maxlength="500" placeholder="Please provide detailed information about your issue...&#10;Example: The computer won't start, or the printer is not responding.&#10;Maximum 500 characters."></textarea>
                        <p class="help-text">💬 Be specific to help us resolve faster. Max 500 characters.</p>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="form-actions">
                <button type="reset" class="btn btn-reset">↺ Reset Form</button>
                <button type="submit" name="sub" class="btn btn-submit">✓ Submit Ticket</button>
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
	function check_call_catg(val){
		var element = document.getElementById('inputbox1');
		if(val == '' || val == '5'){
			element.classList.add('show');
			element.setAttribute("required", "required");
		} else {
			element.classList.remove('show');
			element.removeAttribute("required");
		}
	}
	
	function check_prob_on(val){
		var element = document.getElementById('inputbox2');
		if(val == '' || val == 'Other'){
			element.classList.add('show');
			element.setAttribute("required", "required");
		} else {
			element.classList.remove('show');
			element.removeAttribute("required");
		}
	}
	
	function check_PC(val){
		var element = document.getElementById('inputbox3');
		if(val == '' || val == 'Other'){
			element.classList.add('show');
			element.setAttribute("required", "required");
		} else {
			element.classList.remove('show');
			element.removeAttribute("required");
		}
	}
	
	function check_Printer(val){
		var element = document.getElementById('inputbox4');
		if(val == '' || val == 'Other'){
			element.classList.add('show');
			element.setAttribute("required", "required");
		} else {
			element.classList.remove('show');
			element.removeAttribute("required");
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
