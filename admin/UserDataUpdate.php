<head>
	<style>
	/* Style the tab */
	.tab_user {
		overflow: hidden;
		border: 1px solid #ccc;
		background-color: green;
	}

	/* Style the buttons inside the tab */
	.tab_user button {
		background-color: inherit;
		float: left;
		border: none;
		outline: none;
		cursor: pointer;
		padding: 14px 16px;
		transition: 0.3s;
		font-size: 17px;
		color:white;
	}

	/* Change background color of buttons on hover */
	.tab_user button:hover {
		background-color: white;
		color:black;
	}

	/* Create an active/current tablink class */
	tab_user button.active {
		background-color: #ccc;
	}

	/* Style the tab content */
	.tabcontent_user {
		display: none;
		padding: 6px 12px;
		border: 1px solid #ccc;
		border-top: none;
	}
	h3{
		color:green;
	}
	</style>
</head>	



<h2 height="73" colspan="2" style="text-align:center; font-size: 36px;text-shadow: 1px 1px 2px black, 0 0 25px blue, 0 0 5px darkblue; color:#B1FF10;">&nbsp;Update Your Profile &nbsp;</h2>

<?php
 $query_sel=mysqli_query($link,"SELECT * FROM `emp_details` WHERE `staffid`='$sid'");
 $emp_det=mysqli_fetch_array($query_sel) ;
?>

<!--*****************************************************************************************************************************************************************************-->

<div class="tab_user">
  <button class="tablinks_user" onclick="openCity(event, 'DeptSec')">Department & Section</button>
  <button class="tablinks_user" onclick="openCity(event, 'PhoneNo')">Phone Number</button>
  <button class="tablinks_user" onclick="openCity(event, 'PC')">PC</button>
  <button class="tablinks_user" onclick="openCity(event, 'Other')">Other</button>
</div>

<!--*****************************************************************************************************************************************************************************-->

<div id="DeptSec" class="tabcontent_user">
  <h3>Department & Section</h3>
  <p>
<form id="form1" name="form1" method="POST">
<br/><br/>
    <table width="600px" border="0" align="center" >
      <tbody>
		<tr/>
			<td>
				<b>नाम :</b>
			</td>
			<td>
				<input name="u_name" type="text" style="background-color:white; border:0px;" required="required" id="u_name" value="<?php echo $emp_det["username"] ; ?>" readonly size="30"><br/><br/>
			</td>
		</tr>
		<tr>
			<td>
				<b>स्टाफ नंबर :</b>
			</td>
			<td>
				<input name="ustaffno" type="text" style="background-color:white; border:0px;" required="required" id="ustaffno" value="<?php echo $emp_det["staffid"] ; ?>" readonly size="30"><br/><br/>
			</td>
		</tr>
		<tr>
			<td>
				<b>विभाग<sup style="color:red;">*</sup> :</b>
			</td>
			<td>
				<input name="udeptt" type="text" required="required" id="udeptt" value="<?php echo $emp_det["deptt"] ; ?>" size="25"><br/><br/>
			</td>
		</tr>
		<tr>
			<td>
				<b>अनुभाग<sup style="color:red;">*</sup> :</b>
			</td>
			<td>
				<input name="usec" type="text" required="required" id="usec" value="<?php echo $emp_det["sec"] ; ?>" size="25"><br/><br/>
			</td>
		</tr>
		<tr style="text-align:center;">
			<td colspan="2"><input type="submit" name="sub_dep_sec" id="butt" value="Submit"></td>
		</tr>
      </tbody>
	 </table>			
</pre>
</form>
  </p>
</div>
<?php
	extract($_POST);
	if(isset($sub_dep_sec))
	{
		date_default_timezone_set('Asia/Kolkata');		// default date time of india
		$request_date = date('d-m-Y h:i:s A');	
		
		// ticket num generate code	
		date_default_timezone_set('Asia/Kolkata');	// to set default date and time	
		$yer = date('Y');							// to get current year
		$yr = substr($yer,2); 						// Substring to get only last two digit of the year	
		$mnth = date('m');							// to get current month
		$dte = date('d');							// to get current date
		
		//Fetch last ID Number From Database to increment for next call
		$last_ticket_num = mysqli_query($link,"SELECT * FROM `update_user_ticket`");
		while($id_fetch_d = mysqli_fetch_array($last_ticket_num))
		{
			$randm_no_var = $id_fetch_d["ticket_num"] ;
		}
		$randm_no = $randm_no_var + 1 ;
		
		$crt_tikt_no = "U" .$yr .$mnth .$dte .$randm_no ;
	
		mysqli_query($link,"UPDATE `update_user_ticket` SET `req_number`='$crt_tikt_no',`ticket_num`='$randm_no'") ;
		
		if(mysqli_query($link,"INSERT INTO `user_update_data`(`req_no`, `staff_no`, `username`, `dept`, `sec`, `ph_no`, `pc`, `message`, `request_for`, `action_admin`) VALUES ('$crt_tikt_no', '$ustaffno', '$u_name' ,'$udeptt' , '$usec', '-', '-', '-', 'DEPT & SEC', 'Pending')"))
		{
			echo "<meta http-equiv='refresh' content='0'>";
			echo '<script language="javascript">' .'alert("Your Query Has Been Submitted! \n जांच होने के बाद एवं जानकारी सही पाये जाने पर आपके अनुरोध पर कार्य किया जाएगा |")' .'</script>';
		}
	}
?>

<!--*****************************************************************************************************************************************************************************-->

<div id="PhoneNo" class="tabcontent_user">
  <h3>Phone Number</h3>
  <p>
  <form id="form" name="form" method="POST">
<br/><br/>
    <table width="600px" border="0" align="center" >
      <tbody>
		<tr/>
			<td>
				<b>नाम	:</b>
			</td>
			<td>
				<input name="u_name" type="text" style="background-color:white; border:0px;" required="required" id="u_name" value="<?php echo $emp_det["username"] ; ?>" readonly size="30"><br/><br/>
			</td>
		</tr>
		<tr>
			<td>
				<b>स्टाफ नंबर :</b>
			</td>
			<td>
				<input name="ustaffno" type="text" style="background-color:white; border:0px;" required="required" id="ustaffno" value="<?php echo $emp_det["staffid"] ; ?>" readonly size="30"><br/><br/>
			</td>
		</tr>
		<tr>
			<td>
				<b>फोन नंबर<sup style="color:red;">*</sup> :</b>
			</td>
			<td>
				<input name="uph_no" type="text" required="required" id="uph_no" value="<?php echo $emp_det["phone_no"] ; ?>" size="25"><br/><br/>
			</td>
		</tr>
		<tr style="text-align:center;">
			<td colspan="2"><input type="submit" name="sub_ph_no" id="butt" value="Submit"></td>
		</tr>
      </tbody>
	 </table>			
</pre>
</form>
  </p> 
</div>
<?php
	extract($_POST);
	if(isset($sub_ph_no))
	{
		date_default_timezone_set('Asia/Kolkata');		// default date time of india
		$request_date = date('d-m-Y h:i:s A');	
		
		// ticket num generate code	
		date_default_timezone_set('Asia/Kolkata');	// to set default date and time	
		$yer = date('Y');							// to get current year
		$yr = substr($yer,2); 						// Substring to get only last two digit of the year	
		$mnth = date('m');							// to get current month
		$dte = date('d');							// to get current date
		
		//Fetch last ID Number From Database to increment for next call
		$last_ticket_num = mysqli_query($link,"SELECT * FROM `update_user_ticket`");
		while($id_fetch_d = mysqli_fetch_array($last_ticket_num))
		{
			$randm_no_var = $id_fetch_d["ticket_num"] ;
		}
		$randm_no = $randm_no_var + 1 ;
		
		$crt_tikt_no = "U" .$yr .$mnth .$dte .$randm_no ;
	
		mysqli_query($link,"UPDATE `update_user_ticket` SET `req_number`='$crt_tikt_no',`ticket_num`='$randm_no'") ;
		
		if(mysqli_query($link,"INSERT INTO `user_update_data`(`req_no`, `staff_no`, `username`, `dept`, `sec`, `ph_no`, `pc`, `message`, `request_for`, `action_admin`) VALUES ('$crt_tikt_no', '$ustaffno', '$u_name' ,'-' , '-', '$uph_no', '-', '-', 'Phone No.', 'Pending')"))
		{
			echo "<meta http-equiv='refresh' content='0'>";
			echo '<script language="javascript">' .'alert("Your Query Has Been Submitted! \n जांच होने के बाद एवं जानकारी सही पाये जाने पर आपके अनुरोध पर कार्य किया जाएगा |")' .'</script>';
		}
	}
?>

<!--*****************************************************************************************************************************************************************************-->

<div id="PC" class="tabcontent_user">
  <h3>PC</h3>
  <p>
  
  <form id="form" name="form" method="POST">
<br/><br/>
    <table width="600px" border="0" align="center" >
      <tbody>
		<tr/>
			<td>
				<b>नाम	:</b>
			</td>
			<td>
				<input name="u_name" type="text" style="background-color:white; border:0px;" required="required" id="u_name" value="<?php echo $emp_det["username"] ; ?>" readonly size="30"><br/><br/>
			</td>
		</tr>
		<tr>
			<td>
				<b>स्टाफ नंबर :</b>
			</td>
			<td>
				<input name="ustaffno" type="text" style="background-color:white; border:0px;" required="required" id="ustaffno" value="<?php echo $emp_det["staffid"] ; ?>" readonly size="30"><br/><br/>
			</td>
		</tr>
		<tr>
			<td>
				<b>कम्प्यूटर सूची<sup style="color:red;">*</sup> :</b>
			</td>
			<td>
				<select name="upc_no" id="call_catg2" onchange='check_PC(this.value);' required="required">
				  <option style="color:#AFAFAF;" value="" selected="" disabled>Select your PC</option>
					<!-- Fetch data from Master Data... -->
					<?php
						$master_data_fetch=mysqli_query($link,"SELECT * FROM `hardware_master` WHERE (`STAFF_NO`='$sid') AND (`CATG`='PC') ");
						while($master_data_arr=mysqli_fetch_array($master_data_fetch))
						{
							echo "<option>" .$master_data_arr["HD_ID_NO"] ."</option>" ;
						}
						?>
						<option value="Other">Other</option>		
				</select>
				<!--***********************************************************************************************************************************************************-->			
				<select name="other_upc_no" value="" id="inputbox" style='display:none; margin-top:10px; border-color:blue;' Placeholder=" PC No.">
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
				<br/><br/>
			</td>
		</tr>
		<tr>
			<td>
				<b>कार्यवाही<sup style="color:red;">*</sup>	:</b>
			</td>
			<td>
				<select name="to_action" required="required">
					<option style="color:#AFAFAF;" value="" selected="" disabled>Action</option>
					<option value="Add">Add</option>
					<option value="Remove">Remove</option>
				</select><br/><br/>
			</td>
		</tr>
		<tr style="text-align:center;">
			<td colspan="2"><input type="submit" name="sub_pc" id="butt" value="Submit"></td>
		</tr>
      </tbody>
	 </table>			
</pre>
</form>
  </p>
</div>
<?php
	extract($_POST);
	if(isset($sub_pc))
	{
		
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
		
		date_default_timezone_set('Asia/Kolkata');		// default date time of india
		$request_date = date('d-m-Y h:i:s A');	
		
		// ticket num generate code	
		date_default_timezone_set('Asia/Kolkata');	// to set default date and time	
		$yer = date('Y');							// to get current year
		$yr = substr($yer,2); 						// Substring to get only last two digit of the year	
		$mnth = date('m');							// to get current month
		$dte = date('d');							// to get current date
		
		//Fetch last ID Number From Database to increment for next call
		$last_ticket_num = mysqli_query($link,"SELECT * FROM `update_user_ticket`");
		while($id_fetch_d = mysqli_fetch_array($last_ticket_num))
		{
			$randm_no_var = $id_fetch_d["ticket_num"] ;
		}
		$randm_no = $randm_no_var + 1 ;
		
		$crt_tikt_no = "U" .$yr .$mnth .$dte .$randm_no ;
	
		mysqli_query($link,"UPDATE `update_user_ticket` SET `req_number`='$crt_tikt_no',`ticket_num`='$randm_no'") ;
		
		if(mysqli_query($link,"INSERT INTO `user_update_data`(`req_no`, `staff_no`, `username`, `dept`, `sec`, `ph_no`, `pc`, `message`, `request_for`, `action_for_pc`, `action_admin`) VALUES ('$crt_tikt_no', '$ustaffno', '$u_name' ,'-' , '-', '-', '$upc_no', '-', 'PC', '$to_action', 'Pending')"))
		{
			echo "<meta http-equiv='refresh' content='0'>";
			echo '<script language="javascript">' .'alert("Your Query Has Been Submitted! \n जांच होने के बाद एवं जानकारी सही पाये जाने पर आपके अनुरोध पर कार्य किया जाएगा |")' .'</script>';
		}
	}
?>

<!--*****************************************************************************************************************************************************************************-->

<div id="Other" class="tabcontent_user">
  <h3>Other</h3>
  <p>
  
  <form id="form" name="form" method="POST">
<br/><br/>
    <table width="600px" border="0" align="center" >
      <tbody>
		<tr/>
			<td>
				<b>नाम	:</b>
			</td>
			<td>
				<input name="u_name" type="text" style="background-color:white; border:0px;" required="required" id="u_name" value="<?php echo $emp_det["username"] ; ?>" readonly size="30"><br/><br/>
			</td>
		</tr>
		<tr>
			<td>
				<b>स्टाफ नंबर :</b>
			</td>
			<td>
				<input name="ustaffno" type="text" style="background-color:white; border:0px;" required="required" id="ustaffno" value="<?php echo $emp_det["staffid"] ; ?>" readonly size="30"><br/><br/>
			</td>
		</tr>
		<tr>
			<td>
				<b>संदेश	:</b><sup style="color:red;">*</sup>
			</td>
			<td>
				<textarea name="uprob" cols="31" rows="4" maxlength="150" required id="uprob" placeholder=" Enter Your Query Here..."></textarea> <i style="color:red; font-size:12px;">120 Character Max</i><br/><br/>
			</td>
		</tr>
		<tr style="text-align:center;">
			<td colspan="2"><input type="submit" name="sub_message" id="butt" value="Submit"></td>
		</tr>
      </tbody>
	 </table>			
</pre>
</form>
  </p>
</div>

<?php
	extract($_POST);
	if(isset($sub_message))
	{
		date_default_timezone_set('Asia/Kolkata');		// default date time of india
		$request_date = date('d-m-Y h:i:s A');	
		
		// ticket num generate code	
		date_default_timezone_set('Asia/Kolkata');	// to set default date and time	
		$yer = date('Y');							// to get current year
		$yr = substr($yer,2); 						// Substring to get only last two digit of the year	
		$mnth = date('m');							// to get current month
		$dte = date('d');							// to get current date
		
		//Fetch last ID Number From Database to increment for next call
		$last_ticket_num = mysqli_query($link,"SELECT * FROM `update_user_ticket`");
		while($id_fetch_d = mysqli_fetch_array($last_ticket_num))
		{
			$randm_no_var = $id_fetch_d["ticket_num"] ;
		}
		$randm_no = $randm_no_var + 1 ;
		
		$crt_tikt_no = "U" .$yr .$mnth .$dte .$randm_no ;
	
		mysqli_query($link,"UPDATE `update_user_ticket` SET `req_number`='$crt_tikt_no',`ticket_num`='$randm_no'") ;
		
		if(mysqli_query($link,"INSERT INTO `user_update_data`(`req_no`, `staff_no`, `username`, `dept`, `sec`, `ph_no`, `pc`, `message`, `request_for`, `action_admin`) VALUES ('$crt_tikt_no', '$ustaffno', '$u_name' ,'-' , '-', '-', '-', '$uprob', 'Manual', 'Pending')"))
		{
			echo "<meta http-equiv='refresh' content='0'>";
			echo '<script language="javascript">' .'alert("Your Query Has Been Submitted! \n जांच होने के बाद एवं जानकारी सही पाये जाने पर आपके अनुरोध पर कार्य किया जाएगा |")' .'</script>';
		}
	}
?>

<!--*****************************************************************************************************************************************************************************-->

	<div class="msg_corner">
		<center><h2><u>अनुदेश</u></h2></center><hr/>
		<ul>
			<li>
				निवेदन करने के लिए केवल Google Chrome का ही उपयोग करें |
			</li>
				<br/>
			<li>
				निवेदन करने से पूर्व सभी प्रकार की जानकारीयों को जांच ले तथा उनकी पुष्टी कर लें |
			</li>
				<br/>
			<li>
				अतिरिक्त जानकारी हेतु प्रबंधन सेवाएँ अथवा 43660 पर संपर्क करें |
			</li>
				<br/>
		</ul>
	</div>

<script>
function openCity(evt, cityName) {
    var i, tabcontent_user, tablinks_user;
    tabcontent_user = document.getElementsByClassName("tabcontent_user");
    for (i = 0; i < tabcontent_user.length; i++) {
        tabcontent_user[i].style.display = "none";
    }
    tablinks_user = document.getElementsByClassName("tablinks_user");
    for (i = 0; i < tablinks_user.length; i++) {
        tablinks_user[i].className = tablinks_user[i].className.replace(" active", "");
    }
    document.getElementById(cityName).style.display = "block";
    evt.currentTarget.className += " active";
}
</script>

<script>
	function check_PC(val){
		var element=document.getElementById('inputbox');
		if(val==''||val=='Other'){
			element.style.display='block';
			element.setAttribute("required","required");}
		else  
			element.style.display='none';
		}
</script> 