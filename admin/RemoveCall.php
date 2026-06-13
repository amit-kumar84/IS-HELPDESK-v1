


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
        <tr style="text-align: center" bgcolor="yellow">
          <td>Token No.</td>
          <td>Date</td>
          <td>Dept.</td>
          <td>Staff No.</td>
          <td>Name</td>
          <td>Phone</td>
          <td>PC</td>
          <td>Printer</td>
          <td>Category</td>
          <td>Problem</td>
          <td>Engineer</td>
          <td>Solution</td>
          <td>Date</td>
          <td>Status</td>
          <td></td>
        </tr>
		
			<?php
				extract($_POST);
				if(isset($delete))
				{
					if($t_no != "")
					{
						if(mysqli_query($link,"DELETE FROM `complain_register` WHERE `t_no`='$t_no'"))
						{
							echo "<meta http-equiv='refresh' content='0'>";
							echo '<script language="javascript">' .'alert("Delete Successfully!")' .'</script>';
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
			<td><input style="border:0px; width:90px;" name="t_no" type="text" id="t_no" value="<?php echo $search_result['t_no'] ; ?>" required="required" readonly></td>
			<td><?php echo $search_result['r_DateTime'] ; ?></td>
			<td><?php echo $search_result['dept'] ; ?></td>
			<td><?php echo $search_result['Staff_no'] ; ?></td>
			<td><?php echo $search_result['user_name'] ; ?></td>
			<td><?php echo $search_result['phone_no'] ; ?></td>
			<td><?php echo $search_result['pc_no'] ; ?></td>
			<td><?php echo $search_result['printer'] ; ?></td>
			<td><?php echo $search_result["problem_type"] ; ?></td>
			<td><?php echo $search_result["problem"] ; ?></td>
			<td><?php echo $search_result['support_engg'] ; ?></td>
			<td><?php echo $search_result['solution'] ; ?></td>
			<td><?php echo $search_result['s_DateTime'] ; ?></td>
			<td><?php echo $search_result['status'] ; ?></td>
			<td>
				<button name="delete">Delete</button>
			</td>
        </tr>
</form>
      </tbody>
	</table>
  </div>
</div>