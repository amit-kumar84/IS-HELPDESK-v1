


<div>
<?php
	extract($_POST);
	if(isset($search))
	{
		$filter_Result=mysqli_query($link,"SELECT * FROM `request_master` WHERE `request_no`='$valueToSearch'");
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
			<td>Ticket No</td>
			<td>Request Date</td>
			<td>Department</td>
			<td>Section</td>
			<td>Staff No.</td>
			<td>Username</td>
			<td>PC NO.</td>
			<td>PH NO.</td>
			<td>Printer</td>
			<td>Cartridge</td>
			<td>Color</td>
			<td>Required Qty</td>
			<td>Status</td>
			<td></td>
        </tr>
		
			<?php
				extract($_POST);
				if(isset($delete))
				{
					if($request_no != "")
					{
						if(mysqli_query($link,"DELETE FROM `request_master` WHERE `request_no`='$request_no'"))
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
			<td><input style="border:0px; width:90px;" name="request_no" type="text" id="request_no" value="<?php echo $search_result['request_no'] ; ?>" required="required" readonly></td>
			<td><?php echo $search_result['request_date'] ; ?></td>
			<td><?php echo $search_result['department'] ; ?></td>
			<td><?php echo $search_result['sec'] ; ?></td>
			<td><?php echo $search_result['staff_no'] ; ?></td>
			<td><?php echo $search_result['username'] ; ?></td>
			<td><?php echo $search_result['pc_no'] ; ?></td>
			<td><?php echo $search_result['ph_no'] ; ?></td>
			<td><?php echo $search_result['printer_name'] ; ?></td>
			<td><?php echo $search_result['cartridge_no'] ; ?></td>
			<td><?php echo $search_result['color'] ; ?></td>
			<td><?php echo $search_result['issue_qty'] ; ?></td>
			<td><?php echo $search_result['Status'] ; ?></td>
			<td>
				<button name="delete">Delete</button>
			</td>
        </tr>
</form>
      </tbody>
	</table>
  </div>
</div>