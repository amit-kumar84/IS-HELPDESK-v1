
	
<!-- ************************************************************************************* -->
	
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
  
<!-- ************************************************************************************* -->
<form name="form" method="POST" action="">
	<pre style="font-size:15px;">
	<b style="color:red;"><u><?php echo $msg ; ?></u></b>
	
	<b>Name</b> 		:	<?php echo $search_result['username'] ; ?>																							<br/>
	<b>Staff No</b> 	:	<input style="border:0px;" name="staff_no" type="text" value="<?php echo $search_result['staffid'] ; ?>" required readonly>			<br/>
	<b>Department</b>	:	<?php echo $search_result['deptt'] ; ?>																								<br/>
	<b>Section</b>		:	<?php echo $search_result['sec'] ; ?>																								<br/>
	<b>Phone No</b>	:	<?php echo $search_result['phone_no'] ; ?>																								<br/>
	<b>New Password</b>	:	<input name="new_pass" type="password" required id="np" placeholder=" New Password" size="30">										<br/>
	
				<input type="submit" name="subm" value="Reset Password"></td>
				
	</pre>
</form>

<?php
	extract($_POST);
	if(isset($subm))
	{
		  $np=md5($new_pass);
		if($staff_no != "")
		{
			mysqli_query($link,"UPDATE `emp_details` SET `staffpass`='$np' WHERE `staffid`='$staff_no' ") ;
			
			echo "<meta http-equiv='refresh' content='0'>";
			echo '<script language="javascript">' .'alert("Password has been changed!")' .'</script>';
		}
		else
		{
			echo "<meta http-equiv='refresh' content='0'>";
			echo '<script language="javascript">' .'alert("Field Error!")' .'</script>';
		}
	}
?>		