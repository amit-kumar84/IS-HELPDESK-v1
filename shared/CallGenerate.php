

<h2 height="73" colspan="2" style="text-align:center; font-size: 36px;text-shadow: 1px 1px 2px black, 0 0 25px blue, 0 0 5px darkblue; color:#B1FF10;">&nbsp; Call Generate &nbsp;</h2>

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
				  $category = 'VDI' ;
			  }
			  else if($call_catg == 6 )
			  {
				  $category = $other_catg ;
			  }
			  else
			  {
				
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
				
				else if($problem_on == 'Lease Line')
				{
					$Problem_sys = 'Lease Line' ;
				}
				else if($problem_on == 'Broadband Line')
				{
					$Problem_sys = 'Broadband Line' ;
				}
				
				else if($problem_on == 'VDI')
				{
					$Problem_sys = 'VDI' ;
				}
				
				else if($problem_on == 'Other')
				{
					$Problem_sys = $other_prob_on ;
				}
				else
				{
					
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
		
	if(mysqli_query($link,"INSERT INTO `complain_register`(`t_no`, `r_DateTime`, `dept`,`sec`, `user_name`, `Staff_no`, `phone_no`, `pc_no`, `printer`, `problem_on`, `problem_type`, `problem`, `support_engg`, `solution`, `s_DateTime`, `status`) VALUES
	('$ticket_g_no','$regis_dt','$udept','$usec','$uname','$ustaffno','$uphoneno','$upc_no','$printer_no','$Problem_sys','$category','$uprob','','','','Pending')"))
		{
			echo "<meta http-equiv='refresh' content='0'>";
			echo '<script language="javascript">' .'alert("Query Has Been Submitted!")' .'</script>';
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
          <td><input name="ustaffno" type="text" style="background-color:white;" required="required" id="ustaffno" value="<?php echo $search_result['staffid'] ; ?>" readonly size="30"></td>
        </tr>
        <tr>
          <td width="34%" height="35">Name</td>
          <td width="66%"><input name="uname" type="text" style="background-color:white;" required="required" value="<?php echo $search_result['username'] ; ?>" id="uname" readonly  size="30"></td>
        </tr>
        <tr>
          <td height="36">Department<sup style="color:red;">*</sup></td>
          <td><input name="udept" type="text" required="required" value="<?php echo $search_result['deptt'] ; ?>" id="udept" placeholder=" Department" size="30"></td>
        </tr>
        <tr>
          <td height="36">Section<sup style="color:red;">*</sup></td>
          <td><input name="usec" type="text" required="required" value="<?php echo $search_result['sec'] ; ?>" id="usec" placeholder=" Section" size="30"></td>
        </tr>
        <tr>
          <td height="36">Phone Number<sup style="color:red;">*</sup></td>
          <td><input name="uphoneno" type="text" required="required" value="<?php echo $search_result['ip_phone'] ; ?>" id="uphoneno" placeholder=" Phone Number" size="30"></td>
        </tr>
        <tr>
          <td height="31">Problem Type<sup style="color:red;">*</sup></td>
          <td style="border:2px;">
          <select value="" name="call_catg" id="call_catg" onchange='check_call_catg(this.value);' required="required">
            <option style="color:#AFAFAF;" value="" selected="" disabled>Select a category of your Problem</option>
            <option value="1">Hardware</option>
            <option value="2">Software</option>
            <option value="3">Network</option>
            <option value="6">Server</option>
            <option value="4">Virus</option>
			
            <option value="5">Other</option>
            </select>
			<input type="text" name="other_catg" value="" id="inputbox1" style='display:none; margin-top:10px; border-color:blue;' size="30" Placeholder=" Problem Type"/>			
          </td>
        </tr>
        <tr>
          <td height="32">Problem On<sup style="color:red;">*</sup></td>
          <td style="border:2px;">
            <select name="problem_on" id="problem_on" onchange='check_prob_on(this.value);' required="required">
              <option style="color:#AFAFAF;" value="" selected="" disabled>Select a category of your Machine</option>
              <!-- Fetch data from Problem On... -->
				<?php
					$prob_on_data_fetch = mysqli_query($link,"SELECT * FROM `Problem_On`");
					while($prob_on_data_arr = mysqli_fetch_array($prob_on_data_fetch))
					{
					?>
					<option><?php echo $prob_on_data_arr["Problem_On"] ; ?></option>
				<?php
					}
					?>
					<option value="Lease Line">Lease Line</option>
					<option value="Broadband Line">Broadband Line</option>
					<option value="Other">Other</option>
            </select>
			<input type="text" name="other_prob_on" value="" id="inputbox2" style='display:none; margin-top:10px; border-color:blue;' size="30" Placeholder=" Problem On"/>
		  </td>
		  </tr>
        <tr>
          <td height="32">PC / Laptop/VDI Number<sup style="color:red;">*</sup></td>
          <td><span style="border:2px;">
            <select name="upc_no" id="call_catg2" onchange='check_PC(this.value);' required="required">
              <option style="color:#AFAFAF;" value="" selected="" disabled>Select your PC/Laptop/VDI</option>
			  	<!-- Fetch data from Master Data... -->
				<?php
					$master_data_fetch=mysqli_query($link,"SELECT `HD_ID_NO` FROM `hardware_master` WHERE (`STAFF_NO`='$stff_no') AND (`CATG`='PC' OR `CATG`='LAPTOP' OR `CATG`='VDI' ) ");
					while($master_data_arr=mysqli_fetch_array($master_data_fetch))
					{
						echo "<option>" .$master_data_arr["HD_ID_NO"] ."</option>" ;
					}
					?>
					<option value="Other">Other</option>		
            </select>
<!--***********************************************************************************************************************************************************-->			
			<select name="other_upc_no" value="" id="inputbox3" style='display:none; margin-top:10px; border-color:blue;' Placeholder=" PC No.">
              <option style="color:#AFAFAF;" value="" selected disabled></option>
			  	<!-- Fetch data from Master Data... -->
				<?php
					$master_data_fetch_full = mysqli_query($link,"SELECT `HD_ID_NO` FROM `hardware_master` WHERE `CATG`='PC' OR `CATG`='VDI' OR `CATG`='LAPTOP' OR `CATG`='NETWORK' ");
					while($master_data_arr_full = mysqli_fetch_array($master_data_fetch_full))
					{
					?>
					<option><?php echo $master_data_arr_full["HD_ID_NO"] ; ?></option>
				<?php
					}
					?>
				    
			
            </select>
<!--***********************************************************************************************************************************************************-->	
          </span></td>
        </tr>
        <tr>
          <td height="32">Printer</td>
          <td><span style="border:2px;">
            <select name="printer_no" id="pcNo" onchange='check_Printer(this.value)'>
              <option style="color:#AFAFAF;" value="" selected disabled>Select your Printer</option>
			  	<!-- Fetch data from Master Data... -->
				<?php
					$master_data_fetch = mysqli_query($link,"SELECT * FROM `hardware_master` WHERE `STAFF_NO`='$stff_no' AND `CATG`='PRINTER' ORDER BY MODEL ASC");
					while($master_data_arr = mysqli_fetch_array($master_data_fetch))
					{
					?>			  
					<option><?php echo $master_data_arr["MODEL"] ; ?></option>
			  	<?php
					}
					?>
					<option value="Other">Other</option>
            </select> <i style="color:red; font-size:12px;"><sup>***</sup>Select only if required</i>
<!--***********************************************************************************************************************************************************-->
			<select name="other_printer_no" value="" id="inputbox4" style='display:none; margin-top:10px; border-color:blue;' Placeholder=" Printer No.">
              <option style="color:#AFAFAF;" value="" selected disabled></option>
			  	<!-- Fetch data from Master Data... -->
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
<!--***********************************************************************************************************************************************************-->
          </span>
		  </td>
        </tr>
        <tr>
          <td height="71">Problem<sup style="color:red;">*</sup></td>
          <td><textarea name="uprob" cols="31" rows="5" maxlength="150" required id="uprob" placeholder=" Enter Your Query Here..."></textarea></td>
        </tr>
		
        <tr>
          <td colspan="2" style="text-align: center">
		  <input type="reset" name="reset" id="butt" value="Reset">
		  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		  <input type="submit" name="sub" id="butt" value="Submit"></td>
        </tr>
        <tr>
          <td colspan="2" style="text-align: center">&nbsp;</td>
        </tr>
      </tbody>
    </table>	
  </form>
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
