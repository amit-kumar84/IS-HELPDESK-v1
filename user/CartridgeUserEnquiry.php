

<div>

<table width="100%" height="54" border="1" cellpadding="1" cellspacing="0" style="float:left;" id="table_func">
	<tbody>
        <tr style="text-align:center; color:white;" bgcolor="black">
			<td>Request No.</td>
			<td>Request Date</td>
			<td>PC No.</td>
			<td>Printer</td>
			<td>Color</td>
			<td>Cartridge</td>
			<td>Issue Date</td>
			<td>Status</td>
		</tr>
	<?php
		$cartridge_data = mysqli_query($link,"SELECT * FROM `request_master`  WHERE `staff_no`='$sid'  ORDER BY substring(request_no,2,6) DESC, substring(request_no,7,12) DESC");
		while($cartridge_data_array = mysqli_fetch_array($cartridge_data))
		{
	?>
        <tr style="text-align: center; font-size:12px" id="row_hov">
			<td><?php echo $cartridge_data_array["request_no"] ; ?></td>
			<td><?php echo $cartridge_data_array["request_date"] ; ?></td>
			<td><?php echo $cartridge_data_array["pc_no"] ; ?></td>
			<td style="text-align:left; padding-left:10px;"><?php echo $cartridge_data_array["printer_name"] ; ?></td>
			<td><?php echo $cartridge_data_array["color"] ; ?></td>
			<td><?php echo $cartridge_data_array["cartridge_no"] ; ?></td>
			<td><?php echo $cartridge_data_array["issue_date"] ; ?></td>
			<td><?php echo $cartridge_data_array["Status"] ; ?></td>
		</tr>
	<?php				
		}
	?>
	</tbody>
</table>
</div>