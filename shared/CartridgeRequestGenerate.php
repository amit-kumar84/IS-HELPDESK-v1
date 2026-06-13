

<h2 height="73" colspan="2" style="text-align:center; font-size: 36px;text-shadow: 1px 1px 2px black, 0 0 25px blue, 0 0 5px darkblue; color:#B1FF10;">&nbsp; Cartridge's Request Form &nbsp;</h2>

<div>
	<!-- Insert data into Complain Box  -->
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
	('$crt_tikt_no','$staff_id','$uname','$deptt','$sec','$cartridge_no', '$color', '$printer_name','$pc_no','$ph_no','$description','$issue_qty','','$request_date', 'Pending')"))
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
				echo '<script language="javascript">' .'alert("Your Query has been submitted but cartridge is out of stock!")' .'</script>';
			}
		}
		else
		{
			echo "<meta http-equiv='refresh' content='0'>";
			echo '<script language="javascript">' .'alert("ERROR!")' .'</script>';
		}
	}
?>
	
<!-- ************************************************************************************* -->
	
<?php
	extract($_POST);
	if(isset($search))
	{
		$filter_Result=mysqli_query($link,"SELECT * FROM `emp_details` WHERE `staffid`='$valueToSearch'");
		$search_result=mysqli_fetch_array($filter_Result);
		$stff_no= $search_result['staffid'] ;
	}
	?>
	<br/>
	<div>
		<form id="form1" name="form1" method="POST">
			<b>Staff Number :</b>
				<input name="valueToSearch" type="text" id="valueToSearch" required="required" value="" placeholder=" Staff Number" size="30">
					&nbsp;&nbsp;
				<input type="submit" name="search" id="butt" value="Search">
		</form>  
	</div>
  <br/>
  
<!-- ************************************************************************************* -->	
	
  <form id="form2" name="form2" method="post">
    <table width="600px" border="0" align="center" >
      <tbody>
	  <br/><br/>
        <tr>
          <td height="35">Staff Number</td>
          <td><input name="staff_id" type="text" style="background-color:white;" required="required" id="ustaffno" value="<?php echo $search_result['staffid'] ; ?>" readonly="readonly" size="30"></td>
        </tr>
        <tr>
          <td width="34%" height="35">Name</td>
          <td width="66%"><input name="uname" type="text" style="background-color:white;" required="required" value="<?php echo $search_result['username'] ; ?>" id="uname" readonly="readonly" size="30"></td>
        </tr>
        <tr>
          <td height="36">Department<sup style="color:red;">*</sup></td>
          <td><input name="deptt" type="text" required="required" value="<?php echo $search_result['deptt'] ; ?>" id="udept" placeholder=" Department" size="30"></td>
        </tr>
        <tr>
          <td height="36">Section<sup style="color:red;">*</sup></td>
          <td><input name="sec" type="text" required="required" value="<?php echo $search_result['sec'] ; ?>" id="usec" placeholder=" Section" size="30"></td>
        </tr>
        <tr>
          <td height="36">Phone Number<sup style="color:red;">*</sup></td>
          <td><input name="ph_no" type="text" required="required" value="<?php echo $search_result['phone_no'] ; ?>" id="uphoneno" placeholder=" Phone Number" size="30"></td>
        </tr>
        <tr>
          <td height="32">PC / Laptop Number<sup style="color:red;">*</sup></td>
          <td><span style="border:2px;">
            <select name="pc_no" id="call_catg2" onchange='check_PC(this.value);' required="required">
              <option style="color:#AFAFAF;" value="" selected="" disabled>Select your PC/Laptop</option>
			  	<!-- Fetch data from Master Data... -->
				<?php
					$master_data_fetch=mysqli_query($link,"SELECT `HD_ID_NO` FROM `hardware_master` WHERE (`STAFF_NO`='$stff_no') AND (`CATG`='PC' OR `CATG`='Laptop') ");
					while($master_data_arr=mysqli_fetch_array($master_data_fetch))
					{
						echo "<option>" .$master_data_arr["HD_ID_NO"] ."</option>" ;
					}
					?>
					<option value="Other">Other</option>		
            </select>
<!--***********************************************************************************************************************************************************-->			
			<select name="other_pc" value="" id="inputbox1" style='display:none; margin-top:10px; border-color:blue;' Placeholder=" PC No.">
              <option style="color:#AFAFAF;" value="" selected disabled></option>
			  	<!-- Fetch data from Master Data... -->
				<?php
					$master_data_fetch=mysqli_query($link,"SELECT `HD_ID_NO` FROM `hardware_master` WHERE `CATG`='PC' AND `USG`!='WO' ");
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
        </tr>
        <tr>
          <td height="32">Printer<sup style="color:red;">*</sup></td>
          <td><span style="border:2px;">
            <select name="printer_name" id="pcNo" onchange='check_Printer(this.value)' required="required">
              <option style="color:#AFAFAF;" value="" selected disabled>Select your Printer</option>
			  	<!-- Fetch data from Master Data... -->
				<?php
					$master_data_fetch = mysqli_query($link,"SELECT * FROM `hardware_master` WHERE `STAFF_NO`='$stff_no' AND `CATG`='PRINTER'");
					while($master_data_arr = mysqli_fetch_array($master_data_fetch))
					{
					?>			  
					<option><?php echo $master_data_arr["MODEL"] ; ?></option>
			  	<?php
					}
					?>
					<option value="Other">Other</option>
            </select>
<!--***********************************************************************************************************************************************************-->
			<select name="other_printer" value="" id="inputbox2" style='display:none; margin-top:10px; border-color:blue;' Placeholder=" Printer No.">
              <option style="color:#AFAFAF;" value="" selected disabled></option>
			  	<!-- Fetch data from Master Data... -->
				<?php
					$master_data_fetch = mysqli_query($link,"SELECT DISTINCT(MODEL) FROM `hardware_master` WHERE `CATG`='PRINTER' AND `USG`!='WO'");
					while($master_data_arr = mysqli_fetch_array($master_data_fetch))
					{
					?>			  
					<option><?php echo $master_data_arr["MODEL"] ; ?></option>
			  	<?php
					}
					?>
            </select>
<!--***********************************************************************************************************************************************************-->	
          </span>
		  </td>
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
		  <input type="reset" name="reset" id="butt" value="Reset">
		  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		  <input type="submit" id="butt" name="submit" value="Submit"></td>
        </tr>
        <tr>
          <td colspan="2" style="text-align: center">&nbsp;</td>
        </tr>
      </tbody>
    </table>	
  </form>
</div>
<script>
	function check_PC(val){
		var element=document.getElementById('inputbox1');
		if(val==''||val=='Other'){
			element.style.display='block';
			element.setAttribute("required","required");}
		else  
			element.style.display='none';
		}
	function check_Printer(val){
		var element=document.getElementById('inputbox2');
		if(val==''||val=='Other'){
			element.style.display='block';
			element.setAttribute("required","required");}
		else  
			element.style.display='none';
		}
</script> 
