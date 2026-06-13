

<!-- ****************************************************************************************************************************************************** -->
	
<?php
	extract($_POST);
	if(isset($search))
	{
		$filter_Result=mysqli_query($link,"SELECT * FROM `emp_details` WHERE `staffid`='$valueToSearch'");
		$search_result=mysqli_fetch_array($filter_Result);
		$msg = "Please Confirm User Detail Before Reset The User Password" ;
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
  <br/>
  <br/>
  <br/>
  
<!-- ****************************************************************************************************************************************************** -->

	  <?php
		extract($_POST);
		if(isset($sub))
		{
			$usr_staffid = $usr_staffid_val ;
				
			if($usr_staffid != "")
			{
				if($user_data_fetch = mysqli_query($link,"SELECT * FROM `emp_details` WHERE `staffid`='$usr_staffid' "))
				{
				$user_data_arr=mysqli_fetch_array($user_data_fetch) ;
				$u_user_name = $user_data_arr["username"] ;
				$s_deptt = $user_data_arr["deptt"] ;
				$s_sec = $user_data_arr["sec"] ;
				$usage = $u_usg ;
					if($u_usg == 'OTHER')
						{
							$usage = $other_u_usg ;
						}
					else
						{
							$usage = $u_usg ;
						}
						
					  if(mysqli_query($link,"UPDATE `hardware_master` SET `STAFF_NO`='$usr_staffid', `USERNAME`='$u_user_name', `DEPT`='$s_deptt', `SEC`='$s_sec', `USG`='$usage', `issued_on`=NOW()
					  WHERE `HD_ID_NO`='$Hardware_Name'"))
					  {
						echo "<meta http-equiv='refresh' content='0'>";
						echo '<script language="javascript">' .'alert("Update Successfully!")' .'</script>';
					  }
					  else
					  {
						 echo "Unknown Error!" ; 
					  }
				}
				else
				{
					echo "<b style='color:red;'>Fail to Update!<b/> <br/><br/>";
				}
			}
			else
			{
						echo "<meta http-equiv='refresh' content='0'>";
						echo '<script language="javascript">' .'alert("User not found!")' .'</script>';
			}
		}
		?>
<div>
<form name="form" method="POST" action="">
  <table width="32%" align="LEFT">
    <tbody>
      <tr>
        <td height="35" style="text-align: left"><strong>User Name</strong></td>
        <td><?php echo $search_result['username'] ; ?></td>
      </tr>
      <tr>
        <td height="35" style="text-align: left"><strong>Staff Number</strong></td>
        <td><input style="border:0;" name="usr_staffid_val" type="text" value="<?php echo $search_result['staffid'] ; ?>" required readonly></td>
      </tr>
      <tr>
        <td width="39%" height="36" style="text-align: left"><strong>BEL ID Number</strong></td>
        <td width="61%">
			<select name="Hardware_Name" style="width: 173px;" required>
				<option style="color:#AFAFAF;" value="" selected disabled>Select</option>
					<!-- Fetch data from Master Data... -->
					<?php
					extract($_POST);
					$master_data_fetch=mysqli_query($link,"SELECT `HD_ID_NO` FROM `hardware_master`");
					while($master_data_arr=mysqli_fetch_array($master_data_fetch))
					{
						echo "<option>" .$master_data_arr["HD_ID_NO"] ."</option>" ;
					}
					?>
		  </select>
        </td>
      </tr>
      <tr>
        <td height="35" style="text-align: left"><strong>Usage</strong></td>
		<td>
		<select name="u_usg" style="width: 173px;" onchange='check_u_usg(this.value);' required="required">
			<option style="color:#AFAFAF;" value="" selected disabled></option>
			<option value="SAP VDI">SAP VDI</option>
			<option value="SAP NON VDI">SAP NON VDI</option>
			<option value="NET VDI">NET VDI</option>
			<option value="NET NON VDI">NET NON VDI</option>
			<option value="TESTING">TESTING</option>
			<option value="DMRC">DMRC</option>
			<option value="STAND BY">STAND BY</option>
			<option value="TRAINING">TRAINING</option>
			<option value="VIDEO CONFRENCING">VIDEO CONFRENCING</option>
			<option value="OTHER">OTHER</option>
		</select>
			<input type="text" name="other_u_usg" value="" id="inputbox1" autofocus style='text-transform:uppercase; display:none; margin-top:10px; border-color:blue;' size="20" Placeholder=" USAGE"/>			
		</td>
      </tr>
	  
      <tr>
        <td height="28" colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" style="text-align: center"><input type="submit" name="sub" id="butt" value="Submit"></td>
      </tr>
    </tbody>
  </table>
  </form>
</div>
		
	<script>
		function check_u_usg(val){
			var element=document.getElementById('inputbox1');
			if(val==''||val=='OTHER'){
				element.style.display='Block';
				element.setAttribute("required","required");}
			else  
				element.style.display='none';
			}
	</script>
