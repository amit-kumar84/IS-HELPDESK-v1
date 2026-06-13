

<h2 height="73" colspan="2" style="text-align:center; font-size: 20px;text-shadow: 1px 1px 2px black, 0 0 25px blue, 0 0 5px darkblue; color:#B1FF10;">&nbsp; Hardware Storage List &nbsp;</h2>
<div>

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
		 <tr><td colspan="10" style="text-align:center; font-size:25px; background-color:#EBEBEB;">Hard Disk</td></tr>
        <tr style="text-align:center;" bgcolor="yellow">
          <td>S No</td>
          <td>HD_ID_NO</td>
          <td>RECORD_ID_NO</td>
          <td>Dept</td>
          <td>Staff No.</td>
          <td>Username</td>
          <td>Catg</td>
          <td>M/C SL No</td>
          <td>Make</td>
          <td>Storage</td>
        </tr>
		<?php
		
		$s_no = 1 ;
		
		$hardware_master_sel=mysqli_query($link,"SELECT * FROM `hardware_stroage_master` WHERE `CATG`='HARD DRIVE'  ORDER BY `HD_ID_RECORD`");
		
		  while($hardware_master_arr = mysqli_fetch_array($hardware_master_sel))
		  {
			  ?>
		
        <tr style="text-align: left; font-size:12px" id="row_hov">
          <td style="text-align: center;"><?php echo $s_no ; ?></td>
		  <td><?php echo $hardware_master_arr["HD_ID_NO"] ; ?></td>
		  <td><?php echo $hardware_master_arr["HD_ID_RECORD"] ; ?></td>
          <td><?php echo $hardware_master_arr["DEPT"] ; ?></td>
          <td style="text-align:center;"><?php echo $hardware_master_arr["STAFF_NO"] ; ?></td>
          <td><?php echo $hardware_master_arr["USERNAME"] ; ?></td>
          <td><?php echo $hardware_master_arr["CATG"] ; ?></td>
          <td><?php echo $hardware_master_arr["MC_SL_NO"] ; ?></td>
          <td><?php echo $hardware_master_arr["MAKE"] ; ?></td>
          <td><?php echo $hardware_master_arr["STORAGE"] ; ?></td>
        </tr>
		
		<?php
		$s_no++ ;
		  }
		  ?>
		  
		 <tr><td colspan="10" height="20"></td></tr>
		 <tr><td colspan="10" style="text-align:center; font-size:25px; background-color:#EBEBEB;">Pen Drive</td></tr>
		 
        <tr style="text-align:center;" bgcolor="yellow">
          <td>S No</td>
          <td>HD_ID_NO</td>
          <td>RECORD_ID_NO</td>
          <td>Dept</td>
          <td>Staff No.</td>
          <td>Username</td>
          <td>Catg</td>
          <td>Make</td>
          <td colspan="3">Storage</td>
        </tr>
		  <?php
		
		$s_no = 1 ;
		
		$hardware_master_sel=mysqli_query($link,"SELECT * FROM `hardware_stroage_master` WHERE `CATG`='PENDRIVE'  ORDER BY `HD_ID_NO`");
		
		  while($hardware_master_arr = mysqli_fetch_array($hardware_master_sel))
		  {
			  ?>
		
        <tr style="text-align: left; font-size:12px" id="row_hov">
          <td style="text-align: center;"><?php echo $s_no ; ?></td>
		  <td><?php echo $hardware_master_arr["HD_ID_NO"] ; ?></td>
		  <td><?php echo $hardware_master_arr["HD_ID_RECORD"] ; ?></td>
          <td><?php echo $hardware_master_arr["DEPT"] ; ?></td>
          <td style="text-align:center;"><?php echo $hardware_master_arr["STAFF_NO"] ; ?></td>
          <td><?php echo $hardware_master_arr["USERNAME"] ; ?></td>
          <td><?php echo $hardware_master_arr["CATG"] ; ?></td>
          <td><?php echo $hardware_master_arr["MAKE"] ; ?></td>
          <td colspan="2"><?php echo $hardware_master_arr["STORAGE"] ; ?></td>
        </tr>
		
		<?php
		$s_no++ ;
		  }
		  ?>
		  
		 <tr><td colspan="10" height="20"></td></tr>
		 <tr><td colspan="10" style="text-align:center; font-size:25px; background-color:#EBEBEB;">Others</td></tr>
		 
        <tr style="text-align:center;" bgcolor="yellow">
          <td>S No</td>
          <td>HD_ID_NO</td>
          <td>RECORD_ID_NO</td>
          <td>Dept</td>
          <td>Staff No.</td>
          <td>Username</td>
          <td>Catg</td>
          <td colspan="3">Storage</td>
        </tr>
		  <?php
		
		$s_no = 1 ;
		
		$hardware_master_sel=mysqli_query($link,"SELECT * FROM `hardware_stroage_master` WHERE `CATG`!='PENDRIVE' && `CATG`!='HARD DRIVE' ORDER BY `HD_ID_NO`");
		
		  while($hardware_master_arr = mysqli_fetch_array($hardware_master_sel))
		  {
			  ?>
		
        <tr style="text-align: left; font-size:12px" id="row_hov">
          <td style="text-align: center;"><?php echo $s_no ; ?></td>
		  <td><?php echo $hardware_master_arr["HD_ID_NO"] ; ?></td>
		  <td><?php echo $hardware_master_arr["HD_ID_RECORD"] ; ?></td>
          <td><?php echo $hardware_master_arr["DEPT"] ; ?></td>
          <td style="text-align:center;"><?php echo $hardware_master_arr["STAFF_NO"] ; ?></td>
          <td><?php echo $hardware_master_arr["USERNAME"] ; ?></td>
          <td><?php echo $hardware_master_arr["CATG"] ; ?></td>
          <td colspan="3"><?php echo $hardware_master_arr["STORAGE"] ; ?></td>
        </tr>
		
		<?php
		$s_no++ ;
		  }
		  ?>
		 <tr><td colspan="10" height="20"></td></tr>
      </tbody>
</table>
</div>
</div>