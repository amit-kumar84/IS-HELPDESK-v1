

<?php
	error_reporting(0);
	include("connection.php");
	session_start();
	$sid=$_SESSION['sid'];
	// for blank session
	if($sid=="")
	{
	header("location:index.php?login_as=User");	
	}
?>
<?php
	extract($_POST);
	if(isset($submit))
	{		
	// for other option pc/laptop
				if($pc_no == 'Other')
				{
					$pc_no = $other_pc ;
				}
				else
				{
					$pc_no = $pc_no ;
				}
	// /. end ./for other option pc/laptop
				
	// for other option printer
				if($printer_name == 'Other')
				{
					$printer_name = $other_printer ;
				}
				else
				{
					$printer_name = $printer_name ;
				}
	// /. end ./for other option printer
		
		date_default_timezone_set('Asia/Kolkata');		// default date time of india
		$request_date = date('d-m-Y h:i:s A');	
		
		$issue_qty = '1' ;			//by default request qty.
		
		// ticket num generate code	
		date_default_timezone_set('Asia/Kolkata');	// to set default date and time	
		$yer = date('Y');							// to get current year
		$yr = substr($yer,2); 						// Substring to get only last two digit of the year	
		$mnth = date('m');							// to get current month
		$dte = date('d');							// to get current date
		
		//Fetch last ID Number From Database to increment for next call
		$last_ticket_num = mysqli_query($link,"SELECT * FROM `cartridge_ticket_no`");
		while($id_fetch_d = mysqli_fetch_array($last_ticket_num))
		{
			$randm_no_var = $id_fetch_d["ticket_num"] ;
		}
		$randm_no = $randm_no_var + 1 ;
		
		$crt_tikt_no = "C" .$yr .$mnth .$dte .$randm_no ;
	
		mysqli_query($link,"UPDATE `cartridge_ticket_no` SET `req_number`='$crt_tikt_no',`ticket_num`='$randm_no'") ;
		
		$cartridge_data =mysqli_query($link,"SELECT * FROM `printer_cartridge_list` WHERE `model`='$printer_name' ") ;
		$cartridge_data_array = mysqli_fetch_array($cartridge_data) ;
		$cartridge_no = $cartridge_data_array["cartridge_no"] ;
		$cartNoticeType = '';
		$cartNoticeMessage = '';
		$cartTicketNo = '';
		
	if(mysqli_query($link,"INSERT INTO `request_master`(`request_no`, `staff_no`, `username`, `department`, `sec`, `cartridge_no`,`color`, `printer_name`, `pc_no`, `ph_no`, `description`, `issue_qty`, `issue_date`, `request_date`, `Status`) VALUES
	('$crt_tikt_no','$staff_id','$username','$deptt','$sec','$cartridge_no', '$color', '$printer_name','$pc_no','$ph_no','$description','$issue_qty','','$request_date', 'Pending')"))
		{
			$stock_check_arr = mysqli_query($link,"SELECT * FROM `cartridge_stock_list` WHERE `cartridge_no`='$cartridge_no' AND `color`='$color'") ;
			$stock_check = mysqli_fetch_array($stock_check_arr) ;
			$stock_quant = $stock_check["stock_qty"] ;
			$cartTicketNo = $crt_tikt_no;
			
			if($stock_quant>'0')
			{
				$cartNoticeType = 'success';
				$cartNoticeMessage = 'Your request has been submitted successfully.';
			}
			else
			{
				$cartNoticeType = 'warning';
				$cartNoticeMessage = 'Cartridge is out of stock, but your request has been submitted.';
			}
		}
		else
		{
				$cartNoticeType = 'danger';
				$cartNoticeMessage = 'ERROR! Your request could not be submitted.';
		}
	}
?>
	
	<!-- Fetch data from emp detail... -->
	<?php
	$emp_data=mysqli_query($link,"SELECT * FROM `emp_details` WHERE `staffid`='$sid'");
	$user_data_array=mysqli_fetch_array($emp_data);
	$cartNoticeType = $cartNoticeType ?? '';
	$cartNoticeMessage = $cartNoticeMessage ?? '';
	$cartTicketNo = $cartTicketNo ?? '';
	?>

	<?php if ($cartNoticeMessage !== ''): ?>
	<div class="submit-success-screen compact-modal" id="cartSuccessModal">
		<div class="submit-success-card <?= $cartNoticeType === 'warning' ? 'warning' : ($cartNoticeType === 'danger' ? 'danger' : '') ?>">
			<div class="success-badge">
				<i class="fa-solid <?= $cartNoticeType === 'danger' ? 'fa-triangle-exclamation' : ($cartNoticeType === 'warning' ? 'fa-circle-exclamation' : 'fa-check') ?>"></i>
			</div>
			<div class="success-eyebrow"><?= $cartNoticeType === 'warning' ? 'Attention' : ($cartNoticeType === 'danger' ? 'Error' : 'Ticket Submitted') ?></div>
			<h2><?= $cartNoticeType === 'danger' ? 'Submission issue' : 'Successfully recorded' ?></h2>
			<p><?= htmlspecialchars($cartNoticeMessage); ?></p>
			<?php if ($cartTicketNo !== ''): ?>
			<div class="success-ticket-wrap">
				<span>Your Request ID</span>
				<strong><?= htmlspecialchars($cartTicketNo); ?></strong>
			</div>
			<?php endif; ?>
			<div class="success-actions">
				<button type="button" class="success-btn" id="cartSuccessOk">OK</button>
			</div>
		</div>
	</div>
	<script>
		(function(){
			var modal = document.getElementById('cartSuccessModal');
			var okBtn = document.getElementById('cartSuccessOk');
			if (!modal || !okBtn) return;
			okBtn.addEventListener('click', function(){ modal.style.display = 'none'; });
		})();
	</script>
	<?php endif; ?>

	<section class="cart-page">
		<div class="cart-hero">
			<div class="cart-hero-copy">
				<div class="cart-kicker"><i class="fa-solid fa-print"></i> Service / Cartridge</div>
				<h2>Cartridge Request</h2>
				<p>Submit a clean and professional cartridge request with all device details prefilled for faster processing and better stock handling.</p>
				<div class="cart-points">
					<div class="cart-point"><i class="fa-solid fa-bolt"></i><span>Quick request</span></div>
					<div class="cart-point"><i class="fa-solid fa-clipboard-check"></i><span>Accurate stock mapping</span></div>
					<div class="cart-point"><i class="fa-solid fa-swatchbook"></i><span>Color-coded selection</span></div>
				</div>
				<div class="cart-steps">
					<div class="cart-step"><span>1</span><strong>Select device</strong><em>Choose the exact PC or laptop.</em></div>
					<div class="cart-step"><span>2</span><strong>Pick printer</strong><em>Use the installed model or type Other.</em></div>
					<div class="cart-step"><span>3</span><strong>Choose color</strong><em>Match the toner to your print need.</em></div>
				</div>
			</div>
			<div class="cart-hero-card">
				<div class="cart-avatar-band">
					<div class="cart-avatar"><i class="fa-solid fa-box-open"></i></div>
					<div>
						<div class="cart-avatar-label">Request profile</div>
						<div class="cart-avatar-text">Cartridge request made clear and easy</div>
					</div>
				</div>
				<div class="cart-stat">
					<span>Logged in as</span>
					<strong><?php echo htmlspecialchars($user_data_array["username"]); ?></strong>
				</div>
				<div class="cart-stat">
					<span>Staff ID</span>
					<strong><?php echo htmlspecialchars($user_data_array["staffid"]); ?></strong>
				</div>
				<div class="cart-note">
					<i class="fa-solid fa-circle-info"></i>
					<span>Make sure the printer model and color match the actual device to avoid request mismatch.</span>
				</div>
			</div>
		</div>

		<section class="cart-card">
			<div class="cart-card-head">
				<div>
					<div class="section-tag"><i class="fa-solid fa-rectangle-list"></i> Cartridge Details</div>
					<h3>Fill in the cartridge request</h3>
					<p>The form keeps your employee details locked and helps you choose the right device, printer, and toner color.</p>
				</div>
				<div class="cart-badge">Secure entry</div>
			</div>

			<p class="cart-warning"><b>नोट :</b> निवेदन करने से पूर्व सभी प्रकार की जानकारीयों को जांच ले तथा उनकी पुष्टी कर लें |</p>

			<form id="form1" name="form1" method="post" class="cart-form">
				<div class="form-section-title"><span>Identity</span></div>
				<div class="form-grid">
					<div class="form-row is-readonly">
						<label for="staff_id"><span class="label-icon"><i class="fa-solid fa-id-card"></i></span>Staff Number</label>
						<input type="text" required="required" name="staff_id" id="staff_id" value="<?php echo htmlspecialchars($user_data_array["staffid"]); ?>" readonly="readonly" placeholder="Staff ID">
					</div>
					<div class="form-row is-readonly">
						<label for="username"><span class="label-icon"><i class="fa-solid fa-user"></i></span>Name</label>
						<input type="text" required="required" name="username" id="username" value="<?php echo htmlspecialchars($user_data_array["username"]); ?>" readonly="readonly" placeholder="Enter your name">
					</div>
					<div class="form-row">
						<label for="udept"><span class="label-icon"><i class="fa-solid fa-building-user"></i></span>Department <sup>*</sup></label>
						<input name="deptt" type="text" required="required" value="<?php echo htmlspecialchars($user_data_array["deptt"]); ?>" id="udept" placeholder="Department">
					</div>
					<div class="form-row">
						<label for="sec"><span class="label-icon"><i class="fa-solid fa-diagram-project"></i></span>Section <sup>*</sup></label>
						<input name="sec" type="text" required="required" value="<?php echo htmlspecialchars($user_data_array["sec"]); ?>" id="sec" placeholder="Section">
					</div>
					<div class="form-row">
						<label for="ph_no"><span class="label-icon"><i class="fa-solid fa-phone"></i></span>Phone Number <sup>*</sup></label>
						<input name="ph_no" type="text" required="required" value="<?php echo htmlspecialchars($user_data_array["phone_no"]); ?>" id="ph_no" placeholder="Phone Number">
					</div>
				</div>

				<div class="form-section-title"><span>Device Mapping</span></div>
				<div class="form-grid">
					<div class="form-row form-row-wide">
						<label for="pc_no"><span class="label-icon"><i class="fa-solid fa-laptop"></i></span>PC / Laptop Number <sup>*</sup></label>
						<select name="pc_no" id="pc_no" onchange='check_PC(this.value);' required="required">
							<option style="color:#AFAFAF;" value="" selected disabled>Select your PC / Laptop</option>
							<?php
								$master_data=mysqli_query($link,"SELECT `HD_ID_NO` FROM `hardware_master` WHERE (`STAFF_NO`='$sid') AND (`CATG`='PC' OR `CATG`='Laptop')");
								while($master_data_array=mysqli_fetch_array($master_data))
								{
							?>
							<option>💻 <?php echo htmlspecialchars($master_data_array["HD_ID_NO"]); ?></option>
							<?php
								}
							?>
							<option value="Other">✨ Other</option>
						</select>
						<select name="other_pc" value="" id="inputbox1" autofocus style="display:none;" placeholder="PC">
							<option style="color:#AFAFAF;" value="" selected disabled></option>
							<?php
								$master_data_fetch=mysqli_query($link,"SELECT `HD_ID_NO` FROM `hardware_master` WHERE `CATG`='PC'");
								while($master_data_arr=mysqli_fetch_array($master_data_fetch))
								{
							?>
							<option><?php echo htmlspecialchars($master_data_arr["HD_ID_NO"]); ?></option>
							<?php
								}
							?>
						</select>
					</div>
					<div class="form-row form-row-wide">
						<label for="printer_name"><span class="label-icon"><i class="fa-solid fa-print"></i></span>Printer <sup>*</sup></label>
						<select name="printer_name" id="printer_name" onchange='check_Printer(this.value)' required="required">
							<option style="color:#AFAFAF;" value="" selected disabled>Select your printer</option>
							<?php
								$master_data = mysqli_query($link,"SELECT * FROM `hardware_master` WHERE `STAFF_NO`='$sid' AND `CATG`='PRINTER'");
								while($master_data_array = mysqli_fetch_array($master_data))
								{
							?>
							<option>🖨 <?php echo htmlspecialchars($master_data_array["MODEL"]); ?></option>
							<?php
								}
							?>
							<option value="Other">✨ Other</option>
						</select>
						<select name="other_printer" value="" id="inputbox2" autofocus style="display:none;" placeholder="Printer">
							<option style="color:#AFAFAF;" value="" selected disabled></option>
							<?php
								$master_data_fetch = mysqli_query($link,"SELECT DISTINCT(MODEL) FROM `hardware_master` WHERE `CATG`='PRINTER'");
								while($master_data_arr=mysqli_fetch_array($master_data_fetch))
								{
							?>
							<option><?php echo htmlspecialchars($master_data_arr["MODEL"]); ?></option>
							<?php
								}
							?>
						</select>
					</div>
				</div>

				<div class="form-section-title"><span>Print Settings</span></div>
				<div class="form-grid">
					<div class="form-row">
						<label for="color"><span class="label-icon"><i class="fa-solid fa-palette"></i></span>Color</label>
						<select name="color" id="color">
							<option style="color:#AFAFAF;" value="BLACK" selected>⬛ Black</option>
							<option value="YELLOW">🟨 Yellow</option>
							<option value="CYAN">🟦 Cyan</option>
							<option value="MAGENTA">🟪 Magenta</option>
						</select>
					</div>
					<div class="form-row form-row-wide">
						<label for="description"><span class="label-icon"><i class="fa-solid fa-pen-to-square"></i></span>Description</label>
						<textarea cols="31" rows="4" maxlength="150" name="description" id="description" placeholder="Describe the request if needed..."></textarea>
					</div>
				</div>

				<div class="cart-actions">
					<div class="cart-help">
						<i class="fa-solid fa-circle-info"></i>
						<span>Choose the exact cartridge source and the printer model so the request reaches the right stock record without delay.</span>
					</div>
					<div class="cart-buttons">
						<input type="reset" id="butt" value="Reset" class="btn-secondary">
						<input type="submit" id="butt" name="submit" value="Submit Request" class="btn-primary">
					</div>
				</div>
			</form>
		</section>
	</section>
<script>
	function check_PC(val){
		var element=document.getElementById('inputbox1');
		if(val==''||val=='Other'){
			element.style.display='block';
			element.setAttribute("required","required");
			}
		else  
			element.style.display='none';
		}
	function check_Printer(val){
		var element=document.getElementById('inputbox2');
		if(val==''||val=='Other'){
			element.style.display='block';
			element.setAttribute("required","required");
			}
		else  
			element.style.display='none';
		}
</script>
<br/>