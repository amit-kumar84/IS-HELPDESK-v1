

<div>
	<form id="form" action="" name="form1" method="POST">
	  <p>
		  <b style="color:green;">Select Cartridge Type : </b>
			<select name="cartridge_wise_search" id="call_timing_dept" required>
				<option style="color:#AFAFAF;" value="" selected disabled>Select Cartridge</option>
					<?php						
					$cartridge_name = mysqli_query($link,"SELECT DISTINCT(cartridge_no) FROM `request_master` ORDER BY cartridge_no DESC");
						while($cartridge_data_arr = mysqli_fetch_array($cartridge_name))
						{
					?>
					<option><?php echo $cartridge_data_arr["cartridge_no"] ; ?></option>
					<?php
						}
					?>
			</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="search" id="butt" value="Search">
	  </p>
  </form>
</div>
 
<div>
<script>
function openWin() {
    var divText = document.getElementById("print_id").outerHTML;
    var myWindow = window.open('', '', 'width=1024,height=600');
    var doc = myWindow.document;
    doc.open();
    doc.write(divText);
    doc.close();
	myWindow.print();
}
</script>
<label style="float:right;"><a href="#" id="butt" onclick="openWin()">Print</a>&nbsp;&nbsp;&nbsp;</label>

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
<br/><br/>
</div>

<div id="exportExcel">
<div id="print_id">
<table width="100%" height="54" border="1" cellpadding="1" cellspacing="0" style="float:left;" id="table_func">
	<tbody>
        <tr style="text-align:center;" bgcolor="yellow">
			<td>Ticket No</td>
			<td>Request Date</td>
			<td>Department</td>
			<td>Section</td>
			<td>Username</td>
			<td>PC No.</td>
			<td>Printer</td>
			<td>Color</td>
			<td>Issue Date</td>
			<td>Issued Qty</td>
		</tr>
		
		<?php
			extract($_POST);
			if(isset($search))
			{
				$query_sel=mysqli_query($link,"SELECT * FROM `request_master` WHERE `cartridge_no`='$cartridge_wise_search' ORDER BY substring(request_no,8,12) DESC");
				$total_row_count = mysqli_num_rows($query_sel);
				echo "<hr/>";
					echo "<b style='color:green; font-size:30px;'><center><u>" .$cartridge_wise_search ."</u></center></b>" ;
					echo "<b style='color:#3795F8;'>Total Result :</b> " .$total_row_count ."<br><br>" ; // print per page record 
					  while($cartridge_data_show_array=mysqli_fetch_array($query_sel))
					  {
		?>
		
        <tr style="text-align: left; font-size:10px;" id="row_hov">
			<td><?php echo $cartridge_data_show_array["request_no"] ; ?></td>
			<td><?php echo $cartridge_data_show_array["request_date"] ; ?></td>
			<td><?php echo $cartridge_data_show_array["department"] ; ?></td>
			<td><?php echo $cartridge_data_show_array["sec"] ; ?></td>
			<td><?php echo $cartridge_data_show_array["username"] ; ?></td>
			<td><?php echo $cartridge_data_show_array["pc_no"] ; ?></td>
			<td><?php echo $cartridge_data_show_array["printer_name"] ; ?></td>
			<td><?php echo $cartridge_data_show_array["color"] ; ?></td>
			<td><?php echo $cartridge_data_show_array["issue_date"] ; ?></td>
			<td style="text-align: center;"><?php echo $cartridge_data_show_array["issue_qty"] ; ?></td>
		</tr>
		<?php				
					}
			}
		?>
	</tbody>
</table>
</div>
</div>
