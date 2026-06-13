
	
<!-- ************************************************************************************* -->
	
<?php
	extract($_POST);
	if(isset($search))
	{
		$filter_Result=mysqli_query($link,"SELECT * FROM `emp_details` WHERE `staffid`='$valueToSearch'");
		$search_result=mysqli_fetch_array($filter_Result);
		$msg = "Please Confirm User Detail Before Change The Detail" ;
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
  
<!-- ************************************************************************************* -->
<form name="form" method="POST" action="">
	<pre style="font-size:15px;">
	<b style="color:red;"><u><?php echo $msg ; ?></u></b>
	
	<b>Name</b> 			:	<?php echo $search_result['username'] ; ?>																								<br/>
	<b>Staff No</b> 		:	<input style="border:0px;" name="staff_no" type="text" value="<?php echo $search_result['staffid'] ; ?>" required readonly>				<br/>
	<b>Cost Center</b>		:	<input name="cost_center" type="text" value="<?php echo $search_result['cost_center'] ; ?>">														<br/>
	<b>Department<sup style="color:red;">*</sup></b>		:	<input name="deptt" type="text" value="<?php echo $search_result['deptt'] ; ?>" required>														<br/>
	<b>Section<sup style="color:red;">*</sup></b>			:	<input name="sec" type="text" value="<?php echo $search_result['sec'] ; ?>" required>														<br/>
	<b>Phone No<sup style="color:red;">*</sup></b>		:	<input name="phone_no" type="text" value="<?php echo $search_result['phone_no'] ; ?>" required>														<br/>
	<b>IP Phone No</b>		:	<input name="ip_phone" type="text" value="<?php echo $search_result['ip_phone'] ; ?>">														<br/>
	<b>Designation</b>		:	<input name="desg" type="text" value="<?php echo $search_result['desg'] ; ?>">														<br/>
	<b>Employee Subgroup<sup style="color:red;">*</sup></b>	:	<select name="emp_sub"><option style="color:#AFAFAF;" value="<?php echo $search_result['Employee_Subgroup'] ; ?>" selected><?php echo $search_result['Employee_Subgroup'] ; ?></option>
					<option value="Executive">Executive</option>
					<option value="Non Executive">Non Executive</option>
					<option value="Technical Cadre">Technical Cadre</option>
				</select>															<br/>
	
				<input type="submit" name="subm" value="Update"></td>
	</pre>
</form>

<?php
	extract($_POST);
	if(isset($subm))
	{
		if($staff_no != "")
		{
			mysqli_query($link,"UPDATE `emp_details` SET `cost_center`='$cost_center', `sec`='$sec', `deptt`='$deptt', `phone_no`='$phone_no', `ip_phone`='$ip_phone', `desg`='$desg', `Employee_Subgroup`='$emp_sub' WHERE `staffid`='$staff_no' ") ;
			
			echo "<meta http-equiv='refresh' content='0'>";
			echo '<script language="javascript">' .'alert("User Details Has Been Updated!")' .'</script>';
		}
		else
		{
			echo "<meta http-equiv='refresh' content='0'>";
			echo '<script language="javascript">' .'alert("Field Error!")' .'</script>';
		}
	}
?>

				