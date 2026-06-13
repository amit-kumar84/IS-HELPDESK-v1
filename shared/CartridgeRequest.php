<style>
.tooltip{
	position: relative;
	display: inline-block;
	cursor: pointer;
}
.tooltip .tooltiptext{
	visibility: hidden;
	background-color:black;
	color: #fff ;
	text-align:center;
	border-radius:6px;
	padding: 5px 0;
/*position the tooltip*/
	position: absolute;
	z-index: 1;
}

.tooltip:hover .tooltiptext{
	visibility: visible;
}
</style>



<div>
<table width="100%" height="54" border="1" cellpadding="1" cellspacing="0" style="float:left;" id="table_func">
	<tbody>
        <tr style="text-align:center;" bgcolor="yellow">
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
			<td>Issue</td>
		</tr>
		<?php
			extract($_GET);
			$cartridge_data = mysqli_query($link,"SELECT * FROM `request_master` WHERE `Status`='Pending' ORDER BY substring(request_no,2,6) DESC, substring(request_no,7,12) DESC");
			
			$total_row_count = mysqli_num_rows($cartridge_data);
			echo "<b style='color:#3795F8;'>Total Pending Request :</b> " .$total_row_count ."<br><br>" ; // Print Pending Request
			
			while($cartridge_data_array = mysqli_fetch_array($cartridge_data))
			{
				$req_no = $cartridge_data_array["request_no"] ;
				$req_date = $cartridge_data_array["request_date"] ;
				$deprtment = $cartridge_data_array["department"] ;
				$section = $cartridge_data_array["sec"] ;
				$staff_no = $cartridge_data_array["staff_no"] ;
				$user = $cartridge_data_array["username"] ;
				$pc = $cartridge_data_array["pc_no"] ;
				$ph = $cartridge_data_array["ph_no"] ;
				$printer = $cartridge_data_array["printer_name"] ;
				$cart_no = $cartridge_data_array["cartridge_no"] ;
				$color = $cartridge_data_array["color"] ;
				$issue_qty = $cartridge_data_array["issue_qty"] ;
		?>
	<form id="form2" action="" name="form2" method="POST">
        <tr style="text-align: center; font-size:10px" id="row_hov">
			<td><?php echo $req_no ; ?></td>
			<td><?php echo $req_date ; ?></td>
			<td><?php echo $deprtment ; ?></td>
			<td><?php echo $section ; ?></td>
			<td><?php echo $staff_no ; ?></td>
			
            <td style="text-align:left;"><div class="tooltip"><?php echo $user ; ?>
					<span class="tooltiptext"><img src="Pictures\<?php echo $staff_no ;?>.JPG" alt="User Image Not Found!" height="120px" width="100px" /></span>
				</div>
			</td>
			<td><?php echo $pc ; ?></td>
			<td><?php echo $ph ; ?></td>
			<td style="text-align:left; padding-left:10px;">
				<select style="border:0px;" name="printer" required="required">
					  <option style="color:#AFAFAF;" value="<?php echo $printer ; ?>" selected><?php echo $printer ; ?></option>
						<!-- Fetch data from support engineer... -->
						<?php
							$cartridge_data_fetch = mysqli_query($link,"SELECT * FROM `printer_cartridge_list`");
							while($cartridge_data_arr = mysqli_fetch_array($cartridge_data_fetch))
							{
							?>
							<option><?php echo $cartridge_data_arr["model"] ; ?></option>
						<?php
							}
							?>
				</select>
			</td>
			<td>
			<select style="border:0px;" name="cartridge_no" required="required">
				  <option style="color:#AFAFAF;" value="<?php echo $cart_no ; ?>" selected><?php echo $cart_no ; ?></option>
					<!-- Fetch data from support engineer... -->
					<?php
						$cartridge_data_fetch = mysqli_query($link,"SELECT * FROM `cartridge_stock_list`");
						while($cartridge_data_arr = mysqli_fetch_array($cartridge_data_fetch))
						{
						?>
						<option><?php echo $cartridge_data_arr["cartridge_no"] ; ?></option>
					<?php
						}
						?>
				</select>
			</td>
			<td><select name="color" required>
					<option style="color:#AFAFAF;" selected><?php echo $color ; ?></option>
					<option value="BLACK">BLACK</option>
					<option value="YELLOW">YELLOW</option>
					<option value="CYAN">CYAN</option>
					<option value="MAGENTA">MAGENTA</option>
				</select>
			</td>
			<td><?php echo $issue_qty ; ?></td>
			<td>
				<button name="subm<?= $req_no ; ?>">Issue</button>
			</td>
		</tr>
	</form>
		<?php
		extract($_POST);
		if(isset($_POST["subm".$req_no]))
		{
			
			date_default_timezone_set('Asia/Kolkata');
			$issued_dt = date('d-m-Y h:i:s A');
			
			$stock_check_arr = mysqli_query($link,"SELECT * FROM `cartridge_stock_list` WHERE `cartridge_no`='$cartridge_no' AND `color`='$color'") ;
			$stock_check = mysqli_fetch_array($stock_check_arr) ;
			$stock_quant = $stock_check["stock_qty"] ;
			
			if($stock_quant>'0')
			{			
				if((mysqli_query($link,"UPDATE `request_master` SET `printer_name`='$printer', `cartridge_no`='$cartridge_no', `color`='$color', `issue_date`='$issued_dt', `Status`='Issued' WHERE `request_no`='$req_no'")) AND (mysqli_query($link,"UPDATE `cartridge_stock_list` SET `stock_qty`=stock_qty - 1,`last_issue_qty`='1',`last_issue_no`='$req_no',`last_issue_date`='$issued_dt' WHERE `cartridge_no`='$cartridge_no' AND `color`='$color' ")) )
					{
						echo "<meta http-equiv='refresh' content='0'>";
						echo '<script language="javascript">' .'alert("Cartridge Has Been Issued.")' .'</script>';
					}
				else
					{
					echo "<meta http-equiv='refresh' content='0'>";
					echo '<script language="javascript">' .'alert("Database Error!")' .'</script>';
					}
			}
			else
				{
				echo "<meta http-equiv='refresh' content='0'>";
				echo '<script language="javascript">' .'alert(" Cartridge is out of stock.")' .'</script>';
				}
		}
		?>		
		<?php				
			}
		?>
	</tbody>
</table>
</div>