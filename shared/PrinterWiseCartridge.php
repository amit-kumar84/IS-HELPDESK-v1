

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
        <tr style="text-align:center; color:black;">
			<td colspan="3" bgcolor="yellow"><b>Printer</b></td>
			<td colspan="3" bgcolor="yellow"><b>Cartridge</b></td>
		</tr>
        <tr style="text-align:center; color:black;">
			<td bgcolor="Yellow">Printer's Name</td>
			<td bgcolor="yellow">Qty.</td>
			<td bgcolor="yellow">Total</td>
			
			<td bgcolor="yellow">Cartridge's Name</td>
			<td bgcolor="yellow">Color</td>
			<td bgcolor="yellow">Qty.</td>
		</tr>
	<?php
		$cartridge_data = mysqli_query($link,"SELECT * FROM `cartridge_stock_list` ;");
		
		while($cartridge_data_show_array = mysqli_fetch_array($cartridge_data))
		{
			$Cartridge = $cartridge_data_show_array["cartridge_no"] ;
	?>
        <tr style="text-align: center; font-size:12px;" id="row_hov">
			<td style="text-align:left;">
				<?php
					$cartridge_data_fetch = mysqli_query($link,"SELECT * FROM `printer_cartridge_list` WHERE `cartridge_no`='$Cartridge' ");
					
					while($cartridge_data_arr = mysqli_fetch_array($cartridge_data_fetch))
					{
						echo $cartridge_data_arr['model'] ."<br/>" ;
					}
				?>
			</td>
			
			<td>
				<?php
					$cartridge_data_fetch2 = mysqli_query($link,"SELECT * FROM `printer_cartridge_list` WHERE `cartridge_no`='$Cartridge' ");
					
					while($cartridge_data_arr2 = mysqli_fetch_array($cartridge_data_fetch2))
					{
						echo $cartridge_data_arr2['printer_qty'] ."<br/>" ;
					}
				?>
			</td>

			<td>
				<?php
					$cartridge_data_fetch3 = mysqli_query($link,"SELECT * FROM `printer_cartridge_list` WHERE `cartridge_no`='$Cartridge' ");
					
					$printer_array_val = 0;
					
					while($cartridge_data_arr3 = mysqli_fetch_array($cartridge_data_fetch3))
					{
						$printer_array = $cartridge_data_arr3['printer_qty'] ;
						$printer_array_val = $printer_array_val + $printer_array ;
					}
					echo  $printer_array_val ;
				?>
			</td>
			
			<td style="text-align:left;">
				<?php echo $Cartridge ; ?>
			</td>
			
			<td>
				<?php echo $cartridge_data_show_array["color"] ; ?>
			</td>
			
			<td style="<?php if($cartridge_data_show_array["stock_qty"]==0){ echo " background-color:red; color:white;";} elseif($cartridge_data_show_array["stock_qty"]==1){echo "background-color:skyblue;";} ?>"><?php echo $cartridge_data_show_array["stock_qty"] ; ?></td>
		</tr>
	<?php				
		}
	?>
	</tbody>
</table>
</div>