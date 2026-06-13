

<div>
<script>
function openWin() {
    var divText = document.getElementById("table_func").outerHTML;
    var myWindow = window.open('', '', 'width=1024,height=600');
    var doc = myWindow.document;
    doc.open();
    doc.write(divText);
    doc.close();
	myWindow.print();
}
</script>
<label style="float:right;"><a href="#" id="butt" onclick="openWin()">Print</a>&nbsp;</label>

<span style="float:right;"><a href="#" id="butt">Export To Excel</a>&nbsp;</span>

	<script type="text/javascript" src="js/jquery-1.9.0.js"> </script>
	<script type="text/javascript">
	$(function(){
		$('span').click(function(){
			var url='data:application/vnd.ms-excel,' + encodeURIComponent($('#exportExcel').html()) 
			location.href=url
			return false
		})
	})
	</script>
</div>
<br/><br/>

<div id="exportExcel">
<table width="100%" height="54" border="1" cellpadding="1" cellspacing="0" style="float:left;" id="table_func">
	<tbody>
        <tr style="text-align:center; color:black;" bgcolor="yellow">
			<td>S. No</td>
			<td>Cartridge</td>
			<td>Printer Name</td>
			<td>Serial No.</td>
			<td>Part No.</td>
			<td>Color</td>
			<td>Stock</td>
			<td>Last Received Qty</td>
			<td>Last Received Date</td>
			<td>Last Issue Number</td>
			<td>Last Issue Date</td>
		</tr>
	<?php
		$s_no = 1 ;
		$cartridge_data = mysqli_query($link,"SELECT * FROM `cartridge_stock_list` ;");
		
		while($cartridge_data_show_array = mysqli_fetch_array($cartridge_data))
		{
			$Cartridge = $cartridge_data_show_array["cartridge_no"] ;
			$cartridge_data_fetch = mysqli_query($link,"SELECT * FROM `printer_cartridge_list` WHERE `cartridge_no`='$Cartridge' ");
			
	?>
        <tr style="text-align: center; font-size:12px;" id="row_hov">
			<td><?php echo $s_no ; ?></td>
			<td style="text-align:left;">
			<?php
			while($cartridge_data_arr = mysqli_fetch_array($cartridge_data_fetch))
			{
				echo $cartridge_data_arr['model'] ."<br/>" ;
			}
			?>
			</td>
			<td style="text-align:left;"><?php echo $Cartridge ; ?></td>
			
			<td style="color:brown;"><?php echo $cartridge_data_show_array["cartridge_serial_no"] ; ?></td>
			<td style="color:brown;"><?php echo $cartridge_data_show_array["part_no"] ; ?></td>
			<td><?php echo $cartridge_data_show_array["color"] ; ?></td>
			
			<td style="<?php if($cartridge_data_show_array["stock_qty"]==0){ echo " background-color:red; color:white;";} elseif($cartridge_data_show_array["stock_qty"]==1){echo "background-color:skyblue;";} ?>"><?php echo $cartridge_data_show_array["stock_qty"] ; ?></td>
			<td><?php echo $cartridge_data_show_array["last_received_qty"] ; ?></td>
			<td><?php echo $cartridge_data_show_array["last_received_date"] ; ?></td>
			<td><?php echo $cartridge_data_show_array["last_issue_no"] ; ?></td>
			<td><?php echo $cartridge_data_show_array["last_issue_date"] ; ?></td>
		</tr>
	<?php
		$s_no++ ;
		}
	?>
	</tbody>
</table>
</div>