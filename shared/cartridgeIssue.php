

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

	<?php
		$cartridge_data_show = mysqli_query($link,"SELECT * FROM `request_master` WHERE `Status`='Issued' ORDER BY substring(request_no,2,6) DESC, substring(request_no,7,12) DESC");
		
		$total_row_count = mysqli_num_rows($cartridge_data_show);		  
		echo "<b style='color:green;'>Total Issued Cartridge :</b> " .$total_row_count ; // TOTAL
		
		  echo "<hr id='hr_prprty'/>" ;
		  
		$cartridge_data_day = mysqli_query($link,"SELECT * FROM `request_master` WHERE `Status`='Issued' ORDER BY substring(request_no,2,6) DESC, substring(request_no,7,12) DESC");
		
		$total_row_count = mysqli_num_rows($cartridge_data_day);		  
		echo "<b style='color:#3795F8;'>Page's Total Issued Cartridge :</b> " .$total_row_count ."<br><br>" ; // LIMIT PER PAGE
	?>
	
<div id="exportExcel">
<table width="100%" height="54" border="1" cellpadding="1" cellspacing="0" style="float:left;" id="table_func">
	<tbody>
        <tr style="text-align:center;" bgcolor="yellow">
			<td>Ticket No</td>
			<td>Request Date</td>
			<td>Department</td>
			<td>Section</td>
			<td>Staff No</td>
			<td>Username</td>
			<td>PC No.</td>
			<td>Printer</td>
			<td>Cartridge</td>
			<td>Color</td>
			<td>Issue Date</td>
			<td>Issued Qty</td>
		</tr>
	<?php
		$cartridge_data = mysqli_query($link,"SELECT * FROM `request_master` WHERE `Status`='Issued' ORDER BY substring(request_no,2,6) DESC, substring(request_no,7,12) DESC");
		
		while($cartridge_data_show_array = mysqli_fetch_array($cartridge_data))
		{
	?>
        <tr style="text-align: left; font-size:10px;" id="row_hov">
			<td><?php echo $cartridge_data_show_array["request_no"] ; ?></td>
			<td><?php echo $cartridge_data_show_array["request_date"] ; ?></td>
			<td><?php echo $cartridge_data_show_array["department"] ; ?></td>
			<td><?php echo $cartridge_data_show_array["sec"] ; ?></td>
			<td><?php echo $cartridge_data_show_array["staff_no"] ; ?></td>
			<td><?php echo $cartridge_data_show_array["username"] ; ?></td>
			<td><?php echo $cartridge_data_show_array["pc_no"] ; ?></td>
			<td><?php echo $cartridge_data_show_array["printer_name"] ; ?></td>
			<td><?php echo $cartridge_data_show_array["cartridge_no"] ; ?></td>
			<td><?php echo $cartridge_data_show_array["color"] ; ?></td>
			<td><?php echo $cartridge_data_show_array["issue_date"] ; ?></td>
			<td style="text-align: center;"><?php echo $cartridge_data_show_array["issue_qty"] ; ?></td>
		</tr>
	<?php				
		}
	?>
	</tbody>
</table>
</div>