<style>
.tooltip{
	position: relative;
	display: inline-block;
	cursor: pointer;
}
.tooltip .tooltiptext{
	visibility: hidden;
	background-color:white;
	border:1px solid black;
	color: black ;
	text-align:center;
/*position the tooltip*/
	position: absolute;
	z-index: 1;
}

.tooltip:hover .tooltiptext{
	visibility: visible;
}
</style>



<div>
<?php
	extract($_POST);
	if(isset($search))
	{
		$filter_Result=mysqli_query($link,"SELECT * FROM `complain_register` WHERE `t_no`='$valueToSearch'");
		$search_result=mysqli_fetch_array($filter_Result);
	}
	?>
	<div>
		<form id="form1" name="form1" method="POST">
			<b>Token Number :</b>
				<input name="valueToSearch" type="text" id="valueToSearch" required="required" value="" placeholder=" Enter Token Number" size="30">
					&nbsp;&nbsp;
				<input type="submit" name="search" id="butt" value="Search">
		</form>  
	</div>
  <br><br>
  <div>
	<table width="100%" border="1" cellpadding="1" cellspacing="0" style="float:left;">
      <tbody>
        <tr style="text-align: center;" bgcolor="yellow">
          <td>Token No.</td>
          <td>Category</td>
          <td>Problem</td>
          <td>Engineer</td>
          <td>Solution</td>
          <td>R_Date</td>
          <td>S_Date</td>
          <td>Status</td>
          <td></td>
        </tr>
		
			<?php
				extract($_POST);
				if(isset($update))
				{
					if($t_no != "")
					{
						if(mysqli_query($link,"UPDATE `complain_register` SET `problem_type`='$call_catg', `problem`='$problem',`support_engg`='$support_engg', `solution`='$solution', `status`='$status', `s_DateTime`='$DateTime' WHERE `t_no`='$t_no'"))
						{
							echo "<meta http-equiv='refresh' content='0'>";
							echo '<script language="javascript">' .'alert("Update Successfully!")' .'</script>';
						}
						else
						{
							echo "<meta http-equiv='refresh' content='0'>";
							echo '<script language="javascript">' .'alert("Please resubmit your query!")' .'</script>';
						}
					}
					else
					{
						echo "<meta http-equiv='refresh' content='0'>";
						echo '<script language="javascript">' .'alert("Invalid Data Entered!")' .'</script>';
					}
				}
				?>
<form id="form2" name="form2" method="POST">
        <tr style="font-size:12px; text-align: center">
			<td>
				<div class="tooltip">
					<input style="border:0px; width:90px;" name="t_no" type="text" id="t_no" value="<?php echo $search_result['t_no'] ; ?>" required="required" readonly>
					
					<span style=" text-align:left; width:100px; font-size:15px;" class="tooltiptext">
						<img src="Pictures\<?php echo $search_result["Staff_no"];?>.JPG" alt="User Image Not Found!" height="120px" width="100px"></img><br/>
						<b>Staff No.: </b><?php echo $search_result["Staff_no"];?><br/>
						<b>Name: </b><?php echo $search_result["user_name"];?><br/>
						<b>Dept: </b><?php echo $search_result['dept'] ; ?><br/>
						<b>Ph No: </b><?php echo $search_result['phone_no'] ; ?><br/>
						<b>PC No.: </b> <?php echo $search_result['pc_no'] ; ?><br/>
						<b>Printer: </b><?php echo $search_result['printer'] ; ?>
					</span>
				</div>
			</td>
			
			<td>
				<select name="call_catg" id="call_catg">
					<option style="color:#AFAFAF;" value="<?php echo $search_result["problem_type"] ; ?>" selected><?php echo $search_result["problem_type"] ; ?></option>
					<option value="Hardware">Hardware</option>
					<option value="VDI">VDI</option>
					<option value="Software">Software</option>
					<option value="Network">Network</option>
					<option value="Virus">Virus</option>
					<option value="Server">Server</option>
					<option value="Virus">Virus</option>
				</select>
			</td>
			<td>
			<textarea name="problem" cols="20" rows="2" id="textarea"><?php echo $search_result['problem'] ; ?></textarea>
			</td>
			<td>
			<select style="border:0px;" name="support_engg" id="support_engg">
				  <option style="color:#AFAFAF;" value="<?php echo $search_result['support_engg'] ; ?>" selected><?php echo $search_result['support_engg'] ; ?></option>
					<!-- Fetch data from support engineer... -->
					<?php
						$suppot_engg_data_fetch = mysqli_query($link,"SELECT * FROM `s_engg_login` WHERE `status`='0' AND `presence`='P' ORDER BY engg_name ASC ");
						while($support_engg_data_arr = mysqli_fetch_array($suppot_engg_data_fetch))
						{
						?>
						<option><?php echo $support_engg_data_arr["engg_name"] ; ?></option>
					<?php
						}
						?>
				</select>
			</td>
			<td>
			<textarea name="solution" cols="20" rows="2" id="textarea"><?php echo $search_result['solution'] ; ?></textarea>
			</td>
			
			<td><?php echo $search_result['r_DateTime'] ; ?></td>
			
			<td>
				<input style="border:0px; width:112px; font-size:10px;" name="DateTime" type="text" value="<?php echo $search_result['s_DateTime'] ; ?>">
			</td>
			<td>
				<select style="border:0px;" name="status" id="status">
					<option style="color:#AFAFAF;" value="<?php echo $search_result['status'] ; ?>" selected><?php echo $search_result['status'] ; ?></option>
					<option value="Pending">Pending</option>
					<option value="Solved">Solved</option>
					<option value="Attend">Attend</option>
				</select>
			</td>
			<td>
				<button name="update">Update</button>
			</td>
        </tr>
</form>
      </tbody>
	</table>
  </div>
</div>