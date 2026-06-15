

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
<div>
	<!-- Insert data into Complain Box  -->
<?php
	extract($_POST);
	if(isset($sub))
	{
	
	// ticket num generate code	
		date_default_timezone_set('Asia/Kolkata');	// to set default date and time	
		$yer = date('Y');							// to get current year
		$yr = substr($yer,2); 						// Substring to get only last two digit of the year	
		$mnth = date('m');							// to get current month
		$dte = date('d');							// to get current date
		$type = $call_catg ;						// to get Call category type i.e hardware, software, virus, network, etc.
		
		//Fetch last ID Number From Database to increment for next call
		$last_id_num = mysqli_query($link,"SELECT `ticket_num` FROM `ticket_no` WHERE `token`=`token`");
		while($id_fetch = mysqli_fetch_array($last_id_num))
		{
			$randm_no_var = $id_fetch["ticket_num"] ;
		}
		$randm_no = $randm_no_var + 1 ;
		
		$ticket_g_no = $yr .$mnth .$dte .$type .$randm_no ;
	
	mysqli_query($link,"UPDATE `ticket_no` SET `req_number`='$ticket_g_no',`ticket_num`='$randm_no'") ;
	
	// /. end of ticket num generate code	
	
	
		date_default_timezone_set('Asia/Kolkata');
		$regis_dt = date('d-m-Y h:i:s A');
	
	// for other option call category 	
			  if($call_catg == 1 )
			  {
				  $category = 'Hardware' ;
			  }
			  else if($call_catg == 2 )
			  {
				  $category = 'Software' ;
			  }
			  else if($call_catg == 3 )
			  {
				  $category = 'Network' ;
			  }
			  else if($call_catg == 6 )
			  {
				  $category = 'Server' ;
			  }
			  else if($call_catg == 4 )
			  {
				  $category = 'Virus' ;
			  }
			  else if($call_catg == 5 )
			  {
				  $category = $other_catg ;
			  }
			  else
			  {
				  $category = 'Unidentify' ;
			  }
	// /. end ./ for other option call category 	
			  
	// for other option problem on
				if($problem_on == 'PC')
				{
					$Problem_sys = "PC" ;
				}
				else if($problem_on == 'Printer')
				{
					$Problem_sys = 'Printer' ;
				}
				else if($problem_on == 'Laptop')
				{
					$Problem_sys = 'Laptop' ;
				}
				else if($problem_on == 'SAP')
				{
					$Problem_sys = 'SAP' ;
				}
				else if($problem_on == 'Internet')
				{
					$Problem_sys = 'Internet' ;
				}
				else if($problem_on == 'Other')
				{
					$Problem_sys = $other_prob_on ;
				}
				else
				{
					$Problem_sys = 'Unidentify' ;
				}
	// /. end ./ for other option problem on
				
	// for other option pc/laptop
				if($upc_no == 'Other')
				{
					$upc_no = $other_upc_no ;
				}
				else
				{
					$upc_no = $upc_no ;
				}
	// /. end ./for other option pc/laptop
				
	// for other option printer
				if($printer_no == 'Other')
				{
					$printer_no = $other_printer_no ;
				}
				else
				{
					$printer_no = $printer_no ;
				}
	// /.end ./ for other option printer
		
	if(mysqli_query($link,"INSERT INTO `complain_register`(`t_no`, `r_DateTime`, `dept`, `sec`, `user_name`, `Staff_no`, `phone_no`, `pc_no`, `printer`, `problem_on`, `problem_type`, `problem`, `support_engg`, `solution`, `s_DateTime`, `status`) VALUES
	('$ticket_g_no', '$regis_dt', '$udept','$usec','$uname','$ustaffno','$uphoneno','$upc_no','$printer_no','$Problem_sys','$category', '$uprob','','','','Pending')"))
		{
			$_SESSION['token'] = $ticket_g_no;
			$successTicket = $ticket_g_no;
		}
	}
	?>
	
	<!-- Fetch data from emp detail... -->
	<?php
	$data_fetch=mysqli_query($link,"SELECT * FROM `emp_details` WHERE `staffid`='$sid'");
	$user_data_arr=mysqli_fetch_array($data_fetch);
	$successTicket = $successTicket ?? '';
	?>

	<?php if ($successTicket !== ''): ?>
	<div class="submit-success-screen compact-modal" id="complaintSuccessModal">
		<div class="submit-success-card">
			<div class="success-badge"><i class="fa-solid fa-check"></i></div>
			<div class="success-eyebrow">Ticket Submitted</div>
			<h2>Successfully recorded</h2>
			<p>Your complaint has been submitted and is now in the support queue.</p>
			<div class="success-ticket-wrap">
				<span>Your Ticket ID</span>
				<strong><?php echo htmlspecialchars($successTicket); ?></strong>
			</div>
			<div class="success-actions">
				<button type="button" class="success-btn" id="complaintSuccessOk">OK</button>
			</div>
		</div>
	</div>
	<script>
		(function(){
			var modal = document.getElementById('complaintSuccessModal');
			var okBtn = document.getElementById('complaintSuccessOk');
			if (!modal || !okBtn) return;
			function closeModal(){ modal.style.display = 'none'; }
			okBtn.addEventListener('click', closeModal);
		})();
	</script>
	<?php endif; ?>

	<section class="complaint-page">
		<section class="complaint-hero">
			<div class="complaint-hero-copy">
				<div class="complaint-kicker"><i class="fa-solid fa-circle-exclamation"></i> Service / New</div>
				<h2>Register a Complaint</h2>
				<p>Submit a polished, well-structured request with your work details, affected device, and clear issue summary so the support team can act quickly.</p>
				<div class="complaint-points">
					<div class="complaint-point"><i class="fa-solid fa-bolt"></i><span>Fast ticket generation</span></div>
					<div class="complaint-point"><i class="fa-solid fa-shield-halved"></i><span>Auto-prefilled employee info</span></div>
					<div class="complaint-point"><i class="fa-solid fa-wand-magic-sparkles"></i><span>Cleaner guided inputs</span></div>
				</div>
				<div class="complaint-hero-mini">
					<div class="hero-mini-item">
						<span>1</span>
						<strong>Pick the issue type</strong>
						<em>Use the most accurate category first.</em>
					</div>
					<div class="hero-mini-item">
						<span>2</span>
						<strong>Select the device</strong>
						<em>Choose the exact PC, laptop, or printer.</em>
					</div>
					<div class="hero-mini-item">
						<span>3</span>
						<strong>Describe clearly</strong>
						<em>Write what happened and when it started.</em>
					</div>
				</div>
			</div>
			<div class="complaint-hero-card">
				<div class="complaint-avatar-band">
					<div class="complaint-avatar"><i class="fa-solid fa-headset"></i></div>
					<div>
						<div class="complaint-avatar-label">Request profile</div>
						<div class="complaint-avatar-text">Clean, guided, and quick to submit</div>
					</div>
				</div>
				<div class="complaint-card-stat">
					<span>Logged in as</span>
					<strong><?php echo htmlspecialchars($user_data_arr["username"]); ?></strong>
				</div>
				<div class="complaint-card-stat">
					<span>Staff ID</span>
					<strong><?php echo htmlspecialchars($user_data_arr["staffid"]); ?></strong>
				</div>
				<div class="complaint-card-note">
					<i class="fa-solid fa-circle-info"></i>
					<span>Keep the issue description short, precise, and action-oriented for faster resolution.</span>
				</div>
			</div>
		</section>

		<section class="complaint-card">
			<div class="complaint-card-head">
				<div>
					<div class="section-tag"><i class="fa-solid fa-clipboard-list"></i> Complaint Details</div>
					<h3>Fill in the form below</h3>
					<p>Fields marked with an asterisk are required. The page keeps your employee details locked and prefilled for accuracy.</p>
				</div>
				<div class="complaint-card-badge">Secure entry</div>
			</div>

			<p class="complaint-warning"><b>नोट :</b> निवेदन करने से पूर्व सभी प्रकार की जानकारीयों को जांच ले तथा उनकी पुष्टी कर लें |</p>

			<form id="form1" name="form1" method="post" class="complaint-form">
				<div class="form-section-title"><span>Identity</span></div>
				<div class="form-grid">
					<div class="form-row is-readonly">
						<label for="ustaffno"><span class="label-icon"><i class="fa-solid fa-id-card"></i></span>Staff Number</label>
						<input name="ustaffno" type="text" required id="ustaffno" value="<?php echo htmlspecialchars($user_data_arr["staffid"]); ?>" readonly placeholder="Staff ID">
					</div>
					<div class="form-row is-readonly">
						<label for="uname"><span class="label-icon"><i class="fa-solid fa-user"></i></span>Name</label>
						<input name="uname" type="text" required value="<?php echo htmlspecialchars($user_data_arr["username"]); ?>" readonly id="uname" placeholder="Enter your name">
					</div>
					<div class="form-row">
						<label for="udept"><span class="label-icon"><i class="fa-solid fa-building-user"></i></span>Department <sup>*</sup></label>
						<input name="udept" type="text" required value="<?php echo htmlspecialchars($user_data_arr["deptt"]); ?>" id="udept" placeholder="Department">
					</div>
					<div class="form-row">
						<label for="usec"><span class="label-icon"><i class="fa-solid fa-diagram-project"></i></span>Section <sup>*</sup></label>
						<input name="usec" type="text" required value="<?php echo htmlspecialchars($user_data_arr["sec"]); ?>" id="usec" placeholder="Section">
					</div>
					<div class="form-row">
						<label for="uphoneno"><span class="label-icon"><i class="fa-solid fa-phone"></i></span>Phone Number <sup>*</sup></label>
						<input name="uphoneno" type="text" required value="<?php echo htmlspecialchars($user_data_arr["phone_no"]); ?>" id="uphoneno" placeholder="Phone Number">
					</div>
				</div>

				<div class="form-section-title"><span>Issue Routing</span></div>
				<div class="form-grid">
					<div class="form-row">
						<label for="call_catg"><span class="label-icon"><i class="fa-solid fa-layer-group"></i></span>Problem Type <sup>*</sup></label>
						<select value="" name="call_catg" id="call_catg" onchange='check_call_catg(this.value);' required>
							<option style="color:#AFAFAF;" value="" selected disabled>Select a category of your problem</option>
							<option value="1">🔧 Hardware</option>
							<option value="2">💻 Software</option>
							<option value="3">🌐 Network</option>
							<option value="6">🖥 Server</option>
							<option value="4">🛡 Virus</option>
							<option value="5">✨ Other</option>
						</select>
						<div class="field-legend">
							<span><i class="fa-solid fa-screwdriver-wrench"></i> Hardware</span>
							<span><i class="fa-solid fa-laptop-code"></i> Software</span>
							<span><i class="fa-solid fa-wifi"></i> Network</span>
							<span><i class="fa-solid fa-server"></i> Server</span>
						</div>
						<input type="text" name="other_catg" value="" id="inputbox1" autofocus style="display:none;" placeholder="Specify problem type">
					</div>
					<div class="form-row form-row-wide">
						<label for="problem_on"><span class="label-icon"><i class="fa-solid fa-desktop"></i></span>Problem On <sup>*</sup></label>
						<select name="problem_on" id="problem_on" onchange='check_prob_on(this.value);' required>
							<option style="color:#AFAFAF;" value="" selected disabled>Select a category of your machine</option>
							<?php
								$prob_on_data_fetch = mysqli_query($link,"SELECT * FROM `Problem_On`");
								while($prob_on_data_arr = mysqli_fetch_array($prob_on_data_fetch))
								{
									$problemOnName = trim($prob_on_data_arr["Problem_On"]);
									$problemOnIcon = '💠';
									switch (strtolower($problemOnName)) {
										case 'pc':
										case 'desktop':
											$problemOnIcon = '🖥️';
											break;
										case 'printer':
											$problemOnIcon = '🖨️';
											break;
										case 'laptop':
											$problemOnIcon = '💻';
											break;
										case 'internet':
										case 'network':
											$problemOnIcon = '🌐';
											break;
										case 'sap':
											$problemOnIcon = '📊';
											break;
										case 'server':
											$problemOnIcon = '🗄️';
											break;
										case 'other':
											$problemOnIcon = '✨';
											break;
									}
							?>
							<option><?php echo $problemOnIcon . ' ' . htmlspecialchars($problemOnName, ENT_QUOTES, 'UTF-8'); ?></option>
							<?php
								}
							?>
							<option value="Other">✨ Other</option>
						</select>
						<input type="text" name="other_prob_on" value="" id="inputbox2" autofocus style="display:none;" placeholder="Specify machine">
					</div>
					<div class="form-row form-row-wide">
						<label for="call_catg2"><span class="label-icon"><i class="fa-solid fa-laptop"></i></span>PC / Laptop Number <sup>*</sup></label>
						<select name="upc_no" id="call_catg2" onchange='check_PC(this.value);' required>
							<option style="color:#AFAFAF;" value="" selected disabled>Select your PC/Laptop</option>
							<?php
								$master_data_fetch=mysqli_query($link,"SELECT `HD_ID_NO` FROM `hardware_master` WHERE (`STAFF_NO`='$sid') AND (`CATG`='PC' OR `CATG`='LAPTOP' OR `CATG`='VDI')");
								while($master_data_arr=mysqli_fetch_array($master_data_fetch))
								{
							?>
							<option><?php echo $master_data_arr["HD_ID_NO"] ; ?></option>
							<?php
								}
							?>
							<option value="Other">Other</option>
						</select>
						<select name="other_upc_no" value="" id="inputbox3" style="display:none;" placeholder="PC No.">
							<option style="color:#AFAFAF;" value="" selected disabled></option>
							<?php
								$master_data_fetch = mysqli_query($link,"SELECT `HD_ID_NO` FROM `hardware_master` WHERE `CATG`='PC' OR `CATG`='LAPTOP' OR `CATG`='VDI'");
								while($master_data_arr=mysqli_fetch_array($master_data_fetch))
								{
							?>
							<option><?php echo $master_data_arr["HD_ID_NO"] ; ?></option>
							<?php
								}
							?>
						</select>
					</div>
					<div class="form-row form-row-wide">
						<label for="pcNo"><span class="label-icon"><i class="fa-solid fa-print"></i></span>Printer</label>
						<select name="printer_no" id="pcNo" onchange='check_Printer(this.value)'>
							<option style="color:#AFAFAF;" value="" selected disabled>Select your printer</option>
							<?php
								$master_data_fetch = mysqli_query($link,"SELECT * FROM `hardware_master` WHERE `STAFF_NO`='$sid' AND `CATG`='PRINTER' ORDER BY MODEL ASC");
								while($master_data_arr = mysqli_fetch_array($master_data_fetch))
								{
							?>
							<option><?php echo $master_data_arr["MODEL"] ; ?></option>
							<?php
								}
							?>
							<option value="Other">Other</option>
						</select>
						<i class="helper-note"><sup>***</sup>Select only if required</i>
						<select name="other_printer_no" value="" id="inputbox4" style="display:none;" placeholder="Printer No.">
							<option style="color:#AFAFAF;" value="" selected disabled></option>
							<?php
								$master_data_fetch = mysqli_query($link,"SELECT DISTINCT(MODEL) FROM `hardware_master` WHERE `CATG`='PRINTER' ORDER BY MODEL ASC");
								while($master_data_arr = mysqli_fetch_array($master_data_fetch))
								{
							?>
							<option><?php echo $master_data_arr["MODEL"] ; ?></option>
							<?php
								}
							?>
						</select>
					</div>
				</div>

				<div class="form-section-title"><span>Problem Description</span></div>
				<div class="form-grid">
					<div class="form-row form-row-wide">
						<label for="uprob"><span class="label-icon"><i class="fa-solid fa-pen-to-square"></i></span>Problem <sup>*</sup></label>
						<textarea name="uprob" cols="31" rows="4" maxlength="150" required id="uprob" placeholder="Describe the issue clearly"></textarea>
					</div>
				</div>

				<div class="complaint-actions">
					<div class="complaint-help">
						<i class="fa-solid fa-circle-info"></i>
						<span>Use a short issue summary and select <b>Other</b> only when the exact option is not listed. The form automatically keeps your profile details locked for safety.</span>
					</div>
					<div class="complaint-buttons">
						<input type="reset" name="reset" id="butt" value="Reset" class="btn-secondary">
						<input type="submit" name="sub" id="butt" value="Submit Request" class="btn-primary">
					</div>
				</div>
			</form>
		</section>
	</section>
</div>
<script>
	function check_call_catg(val){
		var element=document.getElementById('inputbox1');
		if(val==''||val=='5'){
			element.style.display='block';
			element.setAttribute("required","required");}
		else  
			element.style.display='none';
		}
	function check_prob_on(val){
		var element=document.getElementById('inputbox2');
		if(val==''||val=='Other'){
			element.style.display='block';
			element.setAttribute("required","required");}
		else  
			element.style.display='none';
		}
	function check_PC(val){
		var element=document.getElementById('inputbox3');
		if(val==''||val=='Other'){
			element.style.display='block';
			element.setAttribute("required","required");}
		else  
			element.style.display='none';
		}
	function check_Printer(val){
		var element=document.getElementById('inputbox4');
		if(val==''||val=='Other'){
			element.style.display='block';
			element.setAttribute("required","required");}
		else  
			element.style.display='none';
		}
</script>
<br/>