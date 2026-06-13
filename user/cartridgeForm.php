

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
		
	if(mysqli_query($link,"INSERT INTO `request_master`(`request_no`, `staff_no`, `username`, `department`, `sec`, `cartridge_no`,`color`, `printer_name`, `pc_no`, `ph_no`, `description`, `issue_qty`, `issue_date`, `request_date`, `Status`) VALUES
	('$crt_tikt_no','$staff_id','$username','$deptt','$sec','$cartridge_no', '$color', '$printer_name','$pc_no','$ph_no','$description','$issue_qty','','$request_date', 'Pending')"))
		{
			$stock_check_arr = mysqli_query($link,"SELECT * FROM `cartridge_stock_list` WHERE `cartridge_no`='$cartridge_no' AND `color`='$color'") ;
			$stock_check = mysqli_fetch_array($stock_check_arr) ;
			$stock_quant = $stock_check["stock_qty"] ;
			
			if($stock_quant>'0')
			{
				echo "<meta http-equiv='refresh' content='0'>";
				echo '<script language="javascript">' .'alert("Your Query Has Been Submitted!")' .'</script>';
			}
			else
			{
				echo "<meta http-equiv='refresh' content='0'>";
				echo '<script language="javascript">' .'alert("Cartridge is out of stock. Your Query has been submitted!")' .'</script>';
			}
		}
		else
		{
			echo "<meta http-equiv='refresh' content='0'>";
			echo '<script language="javascript">' .'alert("ERROR!")' .'</script>';
		}
	}
?>
	
	<!-- Fetch data from emp detail... -->
	<?php
	$emp_data=mysqli_query($link,"SELECT * FROM `emp_details` WHERE `staffid`='$sid'");
	$user_data_array=mysqli_fetch_array($emp_data);
	?>
<h2 height="73" colspan="2" style="text-align:center; font-size: 36px;text-shadow: 1px 1px 2px black, 0 0 25px blue, 0 0 5px darkblue; color:#B1FF10;"><img src="images/toner.png" height="50px" />&nbsp; Cartridge Request Form &nbsp; </h2>
	
	<p style="color:red;">
		<b>नोट :</b> निवेदन करने से पूर्व सभी प्रकार की जानकारीयों को जांच ले तथा उनकी पुष्टी कर लें |
	</p>
	<br/>
<form id="form1" name="form1" method="post">
<fieldset style="background-color:#F8F9F9; font-weight:bold; border-radius:20px;">
	
    <table width="600px" border="0" align="center" >
      <tbody>
		<tr>
			<td></td>
			<td></td>
		</tr>
        <tr>
          <td height="35">Staff Number</td>
          <td><input type="text" required="required" name="staff_id" value="<?php echo $user_data_array["staffid"] ;?>" readonly="readonly" placeholder=" Staff ID" size="30"></td>
        </tr>
        <tr>
          <td width="34%" height="35">Name</td>
          <td width="66%"><input type="text" required="required" name="username" value="<?php echo $user_data_array["username"] ?>" readonly="readonly" placeholder=" Enter Your Name" size="30"></td>
        </tr>
        <tr>
          <td height="36">Department<sup style="color:red;">*</sup></td>
          <td><input name="deptt" type="text" required="required" value="<?php echo $user_data_array["deptt"] ?>" id="udept" placeholder=" Department" size="30"></td>
        </tr>
        <tr>
          <td height="36">Section<sup style="color:red;">*</sup></td>
          <td><input name="sec" type="text" required="required"  value="<?php echo $user_data_array["sec"] ?>" placeholder=" Section" size="30"></td>
        </tr>
        <tr>
          <td height="36">Phone Number<sup style="color:red;">*</sup></td>
          <td><input name="ph_no" type="text" required="required" value="<?php echo $user_data_array["phone_no"] ?>" placeholder=" Phone Number" size="30"></td>
        </tr>
        <tr>
          <td height="32">PC / Laptop Number<sup style="color:red;">*</sup></td>
          <td><span style="border:2px;">
            <select name="pc_no" onchange='check_PC(this.value);' required="required">
              <option style="color:#AFAFAF;" value="" selected="" disabled>Select your PC/Laptop</option>
					<!-- Fetch data from Master Data... -->
				<?php
					$master_data=mysqli_query($link,"SELECT `HD_ID_NO` FROM `hardware_master` WHERE (`STAFF_NO`='$sid') AND (`CATG`='PC' OR `CATG`='Laptop')");
					while($master_data_array=mysqli_fetch_array($master_data))
					{
					?>
					<option><?php echo $master_data_array["HD_ID_NO"] ; ?></option>
				<?php
					}
					?>
					<option value="Other">Other</option>
			
            </select>
<!--***********************************************************************************************************************************************************-->			
			<select name="other_pc" value="" id="inputbox1" autofocus style='display:none; margin-top:10px; border-color:blue;' Placeholder=" PC">
              <option style="color:#AFAFAF;" value="" selected disabled></option>
			  	<!-- Fetch data from Master Data... -->
				<?php
					$master_data_fetch=mysqli_query($link,"SELECT `HD_ID_NO` FROM `hardware_master` WHERE `CATG`='PC' ");
					while($master_data_arr=mysqli_fetch_array($master_data_fetch))
					{
					?>
					<option><?php echo $master_data_arr["HD_ID_NO"] ; ?></option>
				<?php
					}
					?>
			
            </select>
<!--***********************************************************************************************************************************************************-->		
          </span></td>
          </span></td>
        </tr>
        <tr>
          <td height="32">Printer<sup style="color:red;">*</sup></td>
          <td><span style="border:2px;">
            <select name="printer_name" onchange='check_Printer(this.value)'  required="required">
              <option style="color:#AFAFAF;" value="" selected disabled>Select your Printer</option>
					<!-- Fetch data from Master Data... -->
				<?php
					$master_data = mysqli_query($link,"SELECT * FROM `hardware_master` WHERE `STAFF_NO`='$sid' AND `CATG`='PRINTER'");
					while($master_data_array = mysqli_fetch_array($master_data))
					{
					?>			  
					<option><?php echo $master_data_array["MODEL"] ; ?></option>
			  	<?php
					}
					?>
					<option value="Other">Other</option>
            </select>
<!--***********************************************************************************************************************************************************-->
			<select name="other_printer" value="" id="inputbox2" autofocus style='display:none; margin-top:10px; border-color:blue;' Placeholder=" Printer">
              <option style="color:#AFAFAF;" value="" selected disabled></option>
			  	<!-- Fetch data from Master Data... -->
				<?php
					$master_data_fetch = mysqli_query($link,"SELECT DISTINCT(MODEL) FROM `hardware_master` WHERE `CATG`='PRINTER'");
					while($master_data_arr = mysqli_fetch_array($master_data_fetch))
					{
					?>			  
					<option><?php echo $master_data_arr["MODEL"] ; ?></option>
			  	<?php
					}
					?>
            </select>
<!--***********************************************************************************************************************************************************-->	
          </span></td>
          </span></td>
        </tr>
        <tr>
          <td height="32">Color</td>
          <td><span style="border:2px;">
            <select name="color">
				<option style="color:#AFAFAF;" value="BLACK" selected>BLACK</option>
				<option value="YELLOW">YELLOW</option>
				<option value="CYAN">CYAN</option>
				<option value="MAGENTA">MAGENTA</option>
            </select>
          </span></td>
        </tr>
        <tr>
          <td height="71">Description</td>
          <td><textarea cols="31" rows="4" maxlength="150" name="description" placeholder=" Description if any..."></textarea><br/><br/></td>
        </tr>
		
        <tr>
          <td colspan="2" style="text-align: center">
		  <input type="reset" id="butt" value="Reset">
		  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		  <input type="submit" id="butt" name="submit" value="Submit"></td>
        </tr>
        <tr>
          <td colspan="2" style="text-align: center">&nbsp;</td>
        </tr>
      </tbody>
    </table>
</fieldset>	
</form>
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