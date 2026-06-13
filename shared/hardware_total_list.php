 

 
<script>
function openWin() {
    var divText = document.getElementById("print").outerHTML;
    var myWindow = window.open('', '', 'width=1024,height=600');
    var doc = myWindow.document;
    doc.open();
    doc.write(divText);
    doc.close();
	myWindow.print();
}
</script>
<label style="float:right;"><a href="#" id="butt" onclick="openWin()">Print</a>&nbsp;</label>
<br/>
<div id="print">

<h2 height="73" colspan="2" style="text-align:center; font-size: 35px;text-shadow: 1px 1px 2px black, 0 0 25px blue, 0 0 5px darkblue; color:#B1FF10;">&nbsp; Total Assets Data &nbsp;</h2>

	<b>
	<?php
		date_default_timezone_set('Asia/Kolkata') ;
		echo date("l d, F Y h:i:s A") ;
	?>
		<br/><br/>
	</b>
<div>
	<table width="100%" border="1" cellpadding="1" cellspacing="0" style="float:left;" id="table_func">
		<tbody>
			<tr style="text-align:center; background-color:yellow;font-size:18px; color:black;">
				<td>Total Assets</td>
				<td>ISKOT Assets</td>
				<td>Issued Assets</td>
				<td>Standby Assets</td>
			</tr>
			
			<tr>
				
				<td style="padding:5px 10px 5px 15px;">
					<?php
						$query_sel = mysqli_query($link,"SELECT `CATG`, COUNT(CATG), CATG_TYPE FROM `hardware_master` WHERE USG!='WO' GROUP BY CATG");
						$s_no = 1 ;
						while($row = mysqli_fetch_assoc($query_sel))
						{
							echo "<pre >" .$s_no .". " ."<b style='color:brown;'>" .$row['CATG'] ."</b>" ." : <b style='float:right;'>" .$row['COUNT(CATG)'] ."</b></pre>" ;
							$s_no++ ;
						}
					?>
				</td>
				<td style="padding:5px 10px 5px 15px;">
					<?php
						$query_sel = mysqli_query($link,"SELECT `CATG`, COUNT(CATG), CATG_TYPE FROM `hardware_master` WHERE USERNAME='ISKOT' AND USG!='MS (STANDBY)' GROUP BY CATG");
						$s_no = 1 ;
						while($row = mysqli_fetch_assoc($query_sel))
						{
							echo "<pre >" .$s_no .". " ."<b style='color:brown;'>" .$row['CATG'] ."</b>" ." : <b style='float:right;'>" .$row['COUNT(CATG)'] ."</b></pre>" ;
							$s_no++ ;
						}
					?>
				</td>
				<td style="padding:5px 10px 5px 15px;">
					<?php
						$query_sel = mysqli_query($link,"SELECT `CATG`, COUNT(CATG), CATG_TYPE FROM `hardware_master` WHERE USERNAME!='ISKOT' AND USG!='MS (STANDBY)' AND USG!='WO' GROUP BY CATG");
						$s_no = 1 ;
						while($row = mysqli_fetch_assoc($query_sel))
						{
							echo "<pre >" .$s_no .". " ."<b style='color:brown;'>" .$row['CATG'] ."</b>" ." : <b style='float:right;'>" .$row['COUNT(CATG)'] ."</b></pre>" ;
							$s_no++ ;
						}
					?>
				</td>
				<td style="padding:5px 10px 5px 15px;">
					<?php
						$query_sel = mysqli_query($link,"SELECT `CATG`, COUNT(CATG), CATG_TYPE FROM `hardware_master` WHERE USG='MS (STANDBY)' GROUP BY CATG");
						$s_no = 1 ;
						while($row = mysqli_fetch_assoc($query_sel))
						{
							echo "<pre >" .$s_no .". " ."<b style='color:brown;'>" .$row['CATG'] ."</b>" ." : <b style='float:right;'>" .$row['COUNT(CATG)'] ."</b></pre>" ;
							$s_no++ ;
						}
					?>
				</td>
			</tr>
			
		 <tr><td colspan="4" height="30"></td></tr>
		 <tr><td colspan="4" style="text-align:center; font-size:25px; background-color:#EBEBEB;">CCTV Assets</td></tr>
			
			<tr style="text-align:center; background-color:yellow;font-size:18px; color:black;">
				<td>Total Assets</td>
				<td>ISKOT Assets</td>
				<td>Faulty / Repaired</td>
				<td>Standby Assets</td>
			</tr>
			<tr>
				<td style="padding:5px 10px 5px 15px;">
					<?php
						$query_sel = mysqli_query($link,"SELECT `CATG`, COUNT(`CATG`) FROM `cctv` GROUP BY `CATG`");
						$s_no = 1 ;
						while($row = mysqli_fetch_assoc($query_sel))
						{
							echo "<pre>" .$s_no .". " ."<b style='color:brown;'>" .$row['CATG'] ."</b>" ." : <b style='float:right;'>" .$row['COUNT(`CATG`)'] ."</b></pre>" ;
							$s_no++ ;
						}
					?>
				</td>
				<td style="padding:5px 10px 5px 15px;">
					<?php
						$query_sel = mysqli_query($link,"SELECT `CATG`, COUNT(`CATG`) FROM `cctv` WHERE `NAME_LOCATION`!='MS (STANDBY)' && `NAME_LOCATION`!='FAULTY' GROUP BY `CATG`");
						$s_no = 1 ;
						while($row = mysqli_fetch_assoc($query_sel))
						{
							echo "<pre>" .$s_no .". " ."<b style='color:brown;'>" .$row['CATG'] ."</b>" ." : <b style='float:right;'>" .$row['COUNT(`CATG`)'] ."</b></pre>" ;
							$s_no++ ;
						}
					?>
				</td>
				<td style="padding:5px 10px 5px 15px;"><?php
						$query_sel = mysqli_query($link,"SELECT `CATG`, COUNT(`CATG`) FROM `cctv` WHERE `NAME_LOCATION`='FAULTY' || `NAME_LOCATION`='REPAIRED' GROUP BY `CATG`");
						$s_no = 1 ;
						while($row = mysqli_fetch_assoc($query_sel))
						{
							echo "<pre>" .$s_no .". " ."<b style='color:brown;'>" .$row['CATG'] ."</b>" ." : <b style='float:right;'>" .$row['COUNT(`CATG`)'] ."</b></pre>" ;
							$s_no++ ;
						}
					?>
				</td>
				<td style="padding:5px 10px 5px 15px;">
					<?php
						$query_sel = mysqli_query($link,"SELECT `CATG`, COUNT(`CATG`) FROM `cctv` WHERE `NAME_LOCATION`='MS (STANDBY)' GROUP BY `CATG`");
						$s_no = 1 ;
						while($row = mysqli_fetch_assoc($query_sel))
						{
							echo "<pre>" .$s_no .". " ."<b style='color:brown;'>" .$row['CATG'] ."</b>" ." : <b style='float:right;'>" .$row['COUNT(`CATG`)'] ."</b></pre>" ;
							$s_no++ ;
						}
					?>
				</td>
			</tr>
		</tbody>
	</table>
	<div>
		<hr/>
		<br/><br/>
		<b style="background-color:#E8E8FF;">Information shown above is based on available data on central server.</b>
		<br/><br/>
		<b style="background-color:#E8E8FF;">This is a computer generated document no signature required</b>
	</div>
</div>
</div>