

<div>
<table width="100%" height="54" border="1" cellpadding="1" cellspacing="0" style="float:left;" id="table_func">
	<tbody>
        <tr style="text-align:center;" bgcolor="yellow">
		<!--	<td>Printer Name</td> -->
			<td>Cartridge</td>
			<td>Color</td>
			<td>Stock</td>
			<td>Last Received Qty</td>
			<td>Last Received Date</td>
			<td>Update</td>
		</tr>
	<?php
		$cartridge_data = mysqli_query($link,"SELECT * FROM `cartridge_stock_list` ;");
		
		while($cartridge_data_show_array = mysqli_fetch_array($cartridge_data))
		{
			$Cartridge = $cartridge_data_show_array["cartridge_no"] ;
			$cartridge_data_fetch = mysqli_query($link,"SELECT * FROM `printer_cartridge_list` WHERE `cartridge_no`='$Cartridge' ");
	?>
<form id="form1" action="" name="form1" method="POST">
        <tr style="text-align: center; font-size:12px;" id="row_hov">
		<!--	<td style="text-align:left;">
			<?php
			while($cartridge_data_arr = mysqli_fetch_array($cartridge_data_fetch))
			{
				echo $cartridge_data_arr['model'] ."<br/>" ;
			}
			?>
			</td> -->
			<td><?php echo $Cartridge ; ?></td>
			<td><?php echo $cartridge_data_show_array["color"] ; ?></td>
			<td><input name="stock_qty" style="border:0px; text-align:center;" type="number" value="<?php echo $cartridge_data_show_array["stock_qty"] ; ?>" placeholder=" Stock Qty." required="required"></td>
			<td><input name="lst_recv_qty" style="border:0px; text-align:center;" type="number" value="<?php echo $cartridge_data_show_array["last_received_qty"] ; ?>" placeholder=" Received Qty." required="required"></td>
			<td><input name="lst_recv_date" style="border:0px; text-align:center;" type="date" value="<?php echo $cartridge_data_show_array["last_received_date"] ; ?>" placeholder=" Received Date" required="required"></td>
			<td style="text-align: center">
				<button name="subm<?= $Cartridge ; ?>">Update</button>
			</td>
		</tr>
</form>
	<?php
	extract($_POST);
	if(isset($_POST["subm".$Cartridge]))
	{
		if(mysqli_query($link,"UPDATE `cartridge_stock_list` SET `stock_qty`='$stock_qty',`last_received_qty`='$lst_recv_qty',`last_received_date`='$lst_recv_date' WHERE `cartridge_no`='$Cartridge'"))
			{
			echo "<meta http-equiv='refresh' content='0'>";
			}
	}
	?>		
	<?php				
		}
	?>
	</tbody>
</table>
</div>