

<?php
	error_reporting(0);
	include("connection.php");
	
	$sid = function_exists('current_user_id') ? current_user_id() : ($_SESSION['sid'] ?? '');
	extract($_POST);
	$emp_det = array();
	if (!empty($sid)) {
		$query_sel = mysqli_query($link,"SELECT * FROM `emp_details` WHERE `staffid`='$sid'");
		$emp_det = mysqli_fetch_array($query_sel) ?: array();
	}
	$ndaSuccess = false;
	$ndaAlreadyAccepted = false;
	$ndaMessage = '';
	if (!empty($sid)) {
		$check_emp = mysqli_query($link,"SELECT 1 FROM `nda_agreemnt` WHERE `staff_no`='$sid' LIMIT 1");
		$ndaAlreadyAccepted = mysqli_num_rows($check_emp) > 0;
	}
	if(isset($sub))
	{
		$emp_id = $emp_det["staffid"] ;
		$emp_name = $emp_det["username"] ;
		$agree = $IAgree ;
		if ($ndaAlreadyAccepted) {
			$ndaAlreadyAccepted = true;
			$ndaMessage = 'You already agreed to the above statements, terms and conditions.';
		} else if(mysqli_query($link,"INSERT INTO `nda_agreemnt`(`staff_no`, `username`, `agree`) VALUES ('$emp_id', '$emp_name', '$agree')"))
		{
			$ndaSuccess = true;
			$ndaMessage = 'Your NDA agreement has been submitted successfully.';
			$ndaAlreadyAccepted = true;
		}
	}

?>

<section class="nda-page">
	<div class="nda-hero">
		<div class="nda-hero-copy">
			<div class="nda-kicker"><i class="fa-solid fa-file-signature"></i> Compliance / NDA</div>
			<h2>IT Access User Agreement</h2>
			<p>Read the agreement, confirm you understand the responsibilities, and accept it once. The form is designed for clarity, not clutter.</p>
			<div class="nda-points">
				<div class="nda-point"><i class="fa-solid fa-lock"></i><span>Password discipline</span></div>
				<div class="nda-point"><i class="fa-solid fa-server"></i><span>Data protection</span></div>
				<div class="nda-point"><i class="fa-solid fa-shield-halved"></i><span>Policy compliance</span></div>
			</div>
		</div>
		<div class="nda-hero-card">
			<div class="nda-avatar-band">
				<div class="nda-avatar"><i class="fa-solid fa-user-shield"></i></div>
				<div>
					<div class="nda-avatar-label">Agreement status</div>
					<div class="nda-avatar-text">One-time confirmation for IT access</div>
				</div>
			</div>
			<div class="nda-stat">
				<span>Employee</span>
				<strong><?php echo htmlspecialchars($emp_det["username"] ?? 'Employee'); ?></strong>
			</div>
			<div class="nda-stat">
				<span>Staff ID</span>
				<strong><?php echo htmlspecialchars($emp_det["staffid"] ?? $sid); ?></strong>
			</div>
			<div class="nda-note">
				<i class="fa-solid fa-circle-info"></i>
				<span>This agreement is recorded once for your login profile.</span>
			</div>
		</div>
	</div>

	<section class="nda-card">
		<div class="nda-card-head">
			<div>
				<div class="section-tag"><i class="fa-solid fa-file-contract"></i> Agreement Text</div>
				<h3>Review the declaration</h3>
				<p>Read the full declaration and scroll through the agreement before confirming.</p>
			</div>
			<div class="nda-badge"><?php echo date('d-m-Y'); ?></div>
		</div>

		<div class="nda-article">
			<div class="nda-brand-row">
				<img src="images/Bharat_Electronics_logo.png" alt="BEL Kotdwara" class="nda-logo" />
				<div>
					<div class="nda-title">IT Access User Agreement (NDA)</div>
					<div class="nda-subtitle">Bharat Electronics Limited</div>
				</div>
			</div>

			<p>Good security is non-negotiable. Bharat Electronics Limited deals with a range of confidential material, the public exposure of which could potentially cause embarrassment, litigation, reputation loss and commercial damage to the Company. In addition, unauthorized persons gaining access to systems could create serious problems, including inappropriate transactions and impeding staff doing their legitimate activities, resulting in substantial cost to the Company. Therefore, as a condition of access to Bharat Electronics Limited's IT systems and data, all users must agree to abide by the following declaration at all times:</p>

			<div class="nda-declaration-block">
				<div class="nda-section-title">Declaration</div>
				<ol type="1">
					<li>Without exception, I will never disclose my Bharat Electronics Limited login password to anyone.</li>
					<li>I will never allow anyone else to access resources using my login (the only exception being if I am actually watching everything they do whilst they are using my login session).</li>
					<li>I will take particular care to prevent any possibility of anyone else learning, guessing or using my password. This means:
						<ol type="a">
							<li>I will commit my password to memory and never write it down anywhere.</li>
							<li>I will choose a password that is difficult to guess.</li>
							<li>I will take care not to allow anyone to see me entering my password.</li>
							<li>I will never leave a PC unattended and unlocked whilst logged in with my username.</li>
							<li>I will take special care when using web access outside the office and will log out properly.</li>
						</ol>
					</li>
					<li>I will take responsibility for the safe-keeping and confidentiality of all Company data in my possession, in particular:</li>
					<li>I will never attempt to circumvent security or anti-virus protection mechanisms.</li>
					<li>I will respect software licensing and company material requirements.</li>
					<li>I have read and understood the Bharat Electronics Limited Inc. Global Employee Use of Computing Resources Policies and will abide by their requirements at all times.</li>
					<li>I acknowledge I will be held responsible and accountable for any consequence of my failure to diligently honor my obligations above.</li>
				</ol>
			</div>
		</div>

		<div class="nda-footer-row">
			<div class="nda-employee-meta">
				<div><span>Staff No.</span><strong><?php echo htmlspecialchars($emp_det["staffid"] ?? $sid); ?></strong></div>
				<div><span>Name</span><strong><?php echo htmlspecialchars($emp_det["username"] ?? 'Employee'); ?></strong></div>
				<div><span>Date</span><strong><?php echo date('d-m-Y'); ?></strong></div>
			</div>
			<div class="nda-sign-note"><i>Format Part no.: 9914 745 441 41(R0)</i></div>
		</div>

		<form id="form" name="form" method="POST" class="nda-form">
			<?php if ($ndaAlreadyAccepted): ?>
				<div class="nda-locked"><i class="fa-solid fa-circle-check"></i> You already agreed to the above statements, terms and conditions.</div>
			<?php else: ?>
				<label class="nda-checkbox">
					<input type="checkbox" name="IAgree" value="I Agree" required>
					<span>I agree to the above statements, terms and conditions.</span>
				</label>
				<div class="nda-actions">
					<input type="submit" name="sub" id="butt" value="Submit Agreement" class="btn-primary">
				</div>
			<?php endif; ?>
		</form>
	</section>
</section>

<?php if ($ndaMessage !== ''): ?>
<div class="submit-success-screen compact-modal" id="ndaSuccessModal">
	<div class="submit-success-card">
		<div class="success-badge"><i class="fa-solid <?php echo $ndaSuccess ? 'fa-check' : ($ndaAlreadyAccepted ? 'fa-circle-check' : 'fa-triangle-exclamation'); ?>"></i></div>
		<div class="success-eyebrow"><?php echo $ndaSuccess ? 'Agreement Saved' : ($ndaAlreadyAccepted ? 'Already Accepted' : 'Notice'); ?></div>
		<h2><?php echo $ndaSuccess ? 'NDA submitted' : ($ndaAlreadyAccepted ? 'No action needed' : 'NDA notice'); ?></h2>
		<p><?php echo htmlspecialchars($ndaMessage); ?></p>
		<div class="success-actions">
			<button type="button" class="success-btn" id="ndaSuccessOk">OK</button>
		</div>
	</div>
</div>
<script>
	(function(){
		var modal = document.getElementById('ndaSuccessModal');
		var okBtn = document.getElementById('ndaSuccessOk');
		if (!modal || !okBtn) return;
		okBtn.addEventListener('click', function(){ modal.style.display = 'none'; });
	})();
</script>
<?php endif; ?>