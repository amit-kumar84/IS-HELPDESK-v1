

<h2 height="73" colspan="2" style="text-align:center; font-size: 36px;text-shadow: 1px 1px 2px black, 0 0 25px blue, 0 0 5px darkblue; color:#B1FF10;">&nbsp;Non-disclosure Agreement (NDA) &nbsp;</h2>

<?php
	error_reporting(0);
	include("connection.php");
	
	extract($_POST);
	if(isset($sub))
	{
		$query_sel = mysqli_query($link,"SELECT * FROM `emp_details` WHERE `staffid`='$sid'");
		$emp_det = mysqli_fetch_array($query_sel) ;
		 
		$emp_id = $emp_det["staffid"] ;
		$emp_name = $emp_det["username"] ;
		$agree = $IAgree ;
		
		if(mysqli_query($link,"INSERT INTO `nda_agreemnt`(`staff_no`, `username`, `agree`) VALUES ('$emp_id', '$emp_name', '$agree')"))
		{
			echo "<meta http-equiv='refresh' content='0'>";
			echo '<script language="javascript">' .'alert("Your NDA agreement Has been submitted!")' .'</script>';
		}
	}

?>


<form id="form" name="form" method="POST">

	<div style="float:left; width:1022px; min-height:450px; border:1px solid brown; font-family:Arial, Helvetica, sans-serif;">

		<div style="float:left; text-align:justify; Height:370px; border-bottom:1px solid brown; overflow-x: auto;">
			
			<div style="padding:15px;">
				<img src="images/Bharat_Electronics_logo.png" alt="BEL Kotdwara" style="float:left; width:200px;"/>
				
				<center><h2><u>IT Access User Agreement (NDA)</u></h2></center><br/><br/>
				
				<label style="float:right">Date: <b><?php echo Date('d-m-Y') ; ?></b></label><br/>
				
					<p style=" text-indent:50px;">Good security is non-negotiable. Bharat Electronics Limited deals with a range of confidential material, the public exposure of which could potentially cause embarrassment, litigation, reputation loss and commercial damage to the Company. In addition, unauthorized persons gaining access to systems could create serious problems, including inappropriate transactions and impeding staff doing their legitimate activities, resulting in substantial cost to the Company. Therefore, as a condition of access to Bharat Electronics Limited's IT systems and data, all users must agree to abide by the following declaration at all times:</p>
					<br/>
					<center><h3><u>Declaration</u></h3></center>
					
					<ol type="1">
						<li>Without exception, I will never disclose my Bharat Electronics Limited login password to anyone.</li><br/>
						
						<li>I will never allow anyone else to access resources using my login (the only exception being if I am actually watching everything they do whilst they are using my login session).</li></br>
						
						<li>I will take particular care to prevent any possibility of anyone else learning, guessing or using my password. This means: </li><br/>
						
							<ol type="a">
								<li>I will commit my password to memory and never write it down anywhere.</li><br/>
								<li>I will choose a password that is difficult to guess (i.e. does not contain names, birth dates or phone numbers).</li><br/>
								<li>I will take care not to allow anyone to see me entering my password.</li><br/>
								<li>I will never leave a PC unattended and unlocked whilst logged in with my username, for any period of time, no matter how short. I will either logout or lock the session before leaving.</li><br/>
								<li>I will take special care when using web access outside the office, in particular to ensure when finished that I have properly logged out and completely closed all browser windows.</li><br/>
							</ol>
						<li>I will take responsibility for the safe-keeping and confidentiality of all Company data in my possession, in particular:</li><br/>
							
							<ol type="a">
								<li>I will ensure that all important data is safely stored on a Bharat Electronics Limited server, never solely on my PC.</li><br/>
								<li>USB memory keys, floppy disks and CDs are easy to lose and as a result expose the data they contain. Therefore, I will be vigilant to safeguard them and to not store unencrypted Company confidential material on them.</li><br/>
								<li>I will take particular care not to download any Bharat Electronics Limited Confidential or restricted files when using non-Bharat Electronics Limited computers, especially when using public internet cafes.</li><br/>
							</ol>
						
						<li>I will never attempt to circumvent security or anti-virus protection mechanisms</li><br/>
						
						<li>I will respect software licensing and Copy of Bharat Electronics Limited requirements. I will not load, store or use any Copy Bharat Electronics Limited material without ensuring legally recognized rights to do so are in effect.</li><br/>
						
						<li>I have read and understood the Bharat Electronics Limited Inc. Global Employee Use of computing Resources Policies and will abide by their requirements at all times. I will also abide by all additional official IT policies and directives that may be published by the company from time to time.</li><br/>
						
						<li>I acknowledge I will be held responsible and accountable for any consequence of my failure to diligently honor my obligations above.</li><br/> 
					</ol>
						<br/>
							<?php
								error_reporting(0);
								include("connection.php");
								 $query_sel=mysqli_query($link,"SELECT * FROM `emp_details` WHERE `staffid`='$sid'");
								 $emp_det=mysqli_fetch_array($query_sel) ;
							?>
							
						Staff No. :	<b><?php echo $emp_id = $emp_det["staffid"] ; ?></b><br/>
						Name of the Employee: <b><?php echo $emp_name = $emp_det["username"] ; ?></b>
						<br/><br/>
						Date: <?php echo date('d-m-Y') ; ?>
						 <br/><br/><br/>
						<label style="float:right; margin-right:5px;"><i>Format Part no.: 9914 745 441 41(R0)</i></label><br/><br/>
			</div>
		</div>

		<br/><br/>
		<?php
			$check_emp = mysqli_query($link,"SELECT * FROM `nda_agreemnt` WHERE `staff_no`='$sid'");
			$check_emp_count = mysqli_num_rows($check_emp);
			$check_emp_c = $check_emp_count ; 
			 
			
		if($check_emp_c =="0")
		{
		?>
			<input style="float:left;" type="checkbox" name="IAgree" Value="I Agree" required> I agree to the above statements, terms and conditions.
			<br/>
			<center><input type="submit" name="sub" id="butt" value="Submit"></td></center>
		<?php
		}
		else
		{
		?>
			You already agreed to the above statements, terms and conditions.
		<?php
		}
		
		?>
		
	</div>
</form>