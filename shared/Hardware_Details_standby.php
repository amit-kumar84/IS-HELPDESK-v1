 

<h2 height="73" colspan="2" style="text-align:center; font-size: 20px;text-shadow: 1px 1px 2px black, 0 0 25px blue, 0 0 5px darkblue; color:#B1FF10;">&nbsp; Standby Assets List &nbsp;</h2>
<div>
		<?php
			$query_sel = mysqli_query($link,"SELECT `CATG`, COUNT(CATG), CATG_TYPE FROM `hardware_master` WHERE USG='MS (STANDBY)' GROUP BY CATG");
			
			while($row = mysqli_fetch_assoc($query_sel))
			{
				echo "<pre><b>" .$row['CATG'] ."</b> (" .$row['CATG_TYPE'] .")" ." :" .$row['COUNT(CATG)'] ."</pre>" ;
			}
		?>

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
<br/><br/>
</div>

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

<div id="exportExcel">
<table width="100%" height="54" border="1" cellpadding="1" cellspacing="0" style="float:left;" id="table_func">
      <tbody>
        <tr style="text-align:center;" bgcolor="yellow">
          <td>S No</td>
          <td>Catg</td>
          <td>Catg type</td>
          <td>ID No</td>
          <td>M/C SL No</td>
          <td>Make</td>
          <td>Model</td>
          <td>MFG Year</td>
        </tr>
		<?php
		$s_no = 1 ;
		
		  $hardware_master_sel=mysqli_query($link,"SELECT * FROM `hardware_master` WHERE USG='MS (STANDBY)' ORDER BY DEPT, SEC, USERNAME");
									  
		  while($hardware_master_arr = mysqli_fetch_array($hardware_master_sel))
		  {
			  ?>
		
        <tr style="text-align: left; font-size:12px" id="row_hov">
          <td style="text-align: center;"><?php echo $s_no ; ?></td>
          <td><?php echo $hardware_master_arr["CATG"] ; ?></td>
          <td><?php echo $hardware_master_arr["CATG_TYPE"] ; ?></td>
          <td><?php echo $hardware_master_arr["HD_ID_NO"] ; ?></td>
          <td><?php echo $hardware_master_arr["MC_SL_NO"] ; ?></td>
          <td><?php echo $hardware_master_arr["MAKE"] ; ?></td>
          <td><?php echo $hardware_master_arr["MODEL"] ; ?></td>
          <td><?php echo $hardware_master_arr["MFG_YR"] ; ?></td>
        </tr>
		
		<?php
		$s_no++ ;
		  }
		  ?>  
      </tbody>
</table>
</div>
</div>