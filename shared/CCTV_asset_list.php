 

<h2 height="73" colspan="2" style="text-align:center; font-size: 20px;text-shadow: 1px 1px 2px black, 0 0 25px blue, 0 0 5px darkblue; color:#B1FF10;">&nbsp; CCTV Asset List &nbsp;</h2>
<div>
		<?php
			$query_sel = mysqli_query($link,"SELECT `CATG`, COUNT(`CATG`) FROM `cctv` WHERE `NAME_LOCATION`!='MS (STANDBY)' && `NAME_LOCATION`!= 'REPAIRED' && `NAME_LOCATION`!='FAULTY' GROUP BY `CATG`");
			
			while($row = mysqli_fetch_assoc($query_sel))
			{
				echo "<pre><b>" .$row['CATG'] ."</b>" ." : " .$row['COUNT(`CATG`)'] ."</pre>" ;
			}
			
			ECHO "&nbsp;&nbsp;&nbsp;&nbsp;<b style='color:brown;'><u>Stand By</u></b>" ;
			
			$query_sel_SB = mysqli_query($link,"SELECT `CATG`, COUNT(`CATG`) FROM `cctv` WHERE `NAME_LOCATION`='MS (STANDBY)' GROUP BY `CATG`");
			
			while($row2 = mysqli_fetch_assoc($query_sel_SB))
			{
				echo "<pre><b>" .$row2['CATG'] ."</b>" ." : " .$row2['COUNT(`CATG`)'] ."</pre>" ;
			}
			
			ECHO "&nbsp;&nbsp;&nbsp;&nbsp;<b style='color:brown;'><u>Faulty / Repaired</u></b>" ;
			
			$query_sel = mysqli_query($link,"SELECT `CATG`, COUNT(`CATG`) FROM `cctv` WHERE `NAME_LOCATION`='FAULTY' || `NAME_LOCATION`='REPAIRED' GROUP BY `CATG`");
			
			while($row = mysqli_fetch_assoc($query_sel))
			{
				echo "<pre><b>" ."Faulty / Repaired" ."</b>" ." : " .$row['COUNT(`CATG`)'] ."</pre>" ;
			}
		?>
		<br/>

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
	  
<!--CCTV SERVER-->		 
		 <tr><td colspan="8" style="text-align:center; font-size:25px; background-color:#EBEBEB;">CCTV Server</td></tr>
		 
        <tr style="text-align:center;" bgcolor="yellow">
          <td>S No</td>
          <td>IP Address</td>
          <td>Name</td>
          <td>Make</td>
          <td>Model</td>
          <td colspan="3">Serial No.</td>
        </tr>
		<?php
		$s_no = 1 ;
		
		  $hardware_master_sel=mysqli_query($link,"SELECT * FROM `cctv` WHERE `CATG`='CCTV_SERVER' && `NAME_LOCATION`!= 'REPAIRED' && `NAME_LOCATION`!= 'MS (STANDBY)' ORDER BY IP_ADD ");
									  
		  while($hardware_master_arr = mysqli_fetch_array($hardware_master_sel))
		  {
			  ?>
		
        <tr style="text-align: left; font-size:12px" id="row_hov">
          <td style="text-align: center;"><?php echo $s_no ; ?></td>
          <td><?php echo $hardware_master_arr["IP_ADD"] ; ?></td>
          <td><?php echo $hardware_master_arr["NAME_LOCATION"] ; ?></td>
          <td><?php echo $hardware_master_arr["MAKE"] ; ?></td>
          <td><?php echo $hardware_master_arr["MODEL"] ; ?></td>
          <td colspan="3"><?php echo $hardware_master_arr["SL_NO"] ; ?></td>
        </tr>
		
		<?php
		$s_no++ ;
		  }
		  ?>
	  
<!--CCTV Storage-->		 
		 <tr><td colspan="8" style="text-align:center; font-size:25px; background-color:#EBEBEB;">CCTV STORAGE</td></tr>
		 
        <tr style="text-align:center;" bgcolor="yellow">
          <td>S No</td>
          <td>IP Address</td>
          <td>Name</td>
          <td>Make</td>
          <td>Model</td>
          <td colspan="3">Serial No.</td>
        </tr>
		<?php
		$s_no = 1 ;
		
		  $hardware_master_sel=mysqli_query($link,"SELECT * FROM `cctv` WHERE `CATG`='CCTV_STORAGE' && `NAME_LOCATION`!= 'REPAIRED' && `NAME_LOCATION`!= 'MS (STANDBY)' && `NAME_LOCATION`!= 'REPAIRED' ORDER BY IP_ADD ");
									  
		  while($hardware_master_arr = mysqli_fetch_array($hardware_master_sel))
		  {
			  ?>
		
        <tr style="text-align: left; font-size:12px" id="row_hov">
          <td style="text-align: center;"><?php echo $s_no ; ?></td>
          <td><?php echo $hardware_master_arr["IP_ADD"] ; ?></td>
          <td><?php echo $hardware_master_arr["NAME_LOCATION"] ; ?></td>
          <td><?php echo $hardware_master_arr["MAKE"] ; ?></td>
          <td><?php echo $hardware_master_arr["MODEL"] ; ?></td>
          <td colspan="3"><?php echo $hardware_master_arr["SL_NO"] ; ?></td>
        </tr>
		
		<?php
		$s_no++ ;
		  }
		  ?>
	  
<!--CCTV Switch-->		 
		 <tr><td colspan="8" style="text-align:center; font-size:25px; background-color:#EBEBEB;">CCTV Switch</td></tr>
		 
        <tr style="text-align:center;" bgcolor="yellow">
          <td>S No</td>
          <td>IP Address</td>
          <td>Name</td>
          <td>Make</td>
          <td>Model</td>
          <td colspan="3" >Serial No.</td>
        </tr>
		<?php
		$s_no = 1 ;
		
		  $hardware_master_sel=mysqli_query($link,"SELECT * FROM `cctv` WHERE `CATG`='CCTV_SWITCH' && `NAME_LOCATION`!= 'REPAIRED' && `NAME_LOCATION`!= 'MS (STANDBY)' ORDER BY IP_ADD ");
									  
		  while($hardware_master_arr = mysqli_fetch_array($hardware_master_sel))
		  {
			  ?>
		
        <tr style="text-align: left; font-size:12px" id="row_hov">
          <td style="text-align: center;"><?php echo $s_no ; ?></td>
          <td><?php echo $hardware_master_arr["IP_ADD"] ; ?></td>
          <td><?php echo $hardware_master_arr["NAME_LOCATION"] ; ?></td>
          <td><?php echo $hardware_master_arr["MAKE"] ; ?></td>
          <td><?php echo $hardware_master_arr["MODEL"] ; ?></td>
          <td colspan="3" ><?php echo $hardware_master_arr["SL_NO"] ; ?></td>
        </tr>
		
		<?php
		$s_no++ ;
		  }
		  ?>
		 <tr><td colspan="8" height="20"></td></tr>
	  
<!--CCTV CAMERA-->		 
		 <tr><td colspan="8" style="text-align:center; font-size:25px; background-color:#EBEBEB;">CCTV CAMERA</td></tr>
		 
        <tr style="text-align:center;" bgcolor="yellow">
          <td>S No</td>
          <td>CAM ID</td>
          <td>IP Address</td>
          <td>Name</td>
          <td>Location</td>
          <td>Make</td>
          <td>Model</td>
          <td colspan="3">Serial No.</td>
        </tr>
		<?php
		$s_no = 1 ;
		
		  $hardware_master_sel=mysqli_query($link,"SELECT * FROM `cctv` WHERE `CATG`='CCTV_CAMERA' && `NAME_LOCATION`!= 'REPAIRED' && `NAME_LOCATION`!= 'MS (STANDBY)' && `NAME_LOCATION`!= 'FAULTY' ORDER BY IP_ADD ");
									  
		  while($hardware_master_arr = mysqli_fetch_array($hardware_master_sel))
		  {
			  ?>
		
        <tr style="text-align: left; font-size:12px" id="row_hov">
          <td style="text-align: center;"><?php echo $s_no ; ?></td>
          <td><?php echo $hardware_master_arr["CAM_ID"] ; ?></td>
          <td><?php echo $hardware_master_arr["IP_ADD"] ; ?></td>
          <td><?php echo $hardware_master_arr["NAME_LOCATION"] ; ?></td>
          <td><?php echo $hardware_master_arr["CAM_LOCATION"] ; ?></td>
          <td><?php echo $hardware_master_arr["MAKE"] ; ?></td>
          <td><?php echo $hardware_master_arr["MODEL"] ; ?></td>
          <td colspan="3"><?php echo $hardware_master_arr["SL_NO"] ; ?></td>
        </tr>
		
		<?php
		$s_no++ ;
		  }
		  ?>
		 <tr><td colspan="8" height="20"></td></tr>
		  	  
<!--FAULTY CCTV ITEM-->		 
		 <tr><td colspan="8" style="text-align:center; font-size:25px; background-color:#EBEBEB;">FAULTY CCTV ITEMS</td></tr>
		 
        <tr style="text-align:center;" bgcolor="yellow">
          <td>S No</td>
          <td>ID</td>
          <td>IP Address</td>
          <td>Name</td>
          <td>Make</td>
          <td>Model</td>
          <td colspan="3">Serial No.</td>
        </tr>
		<?php
		$s_no = 1 ;
		
		  $hardware_master_sel=mysqli_query($link,"SELECT * FROM `cctv` WHERE `NAME_LOCATION`= 'FAULTY' || `NAME_LOCATION`= 'REPAIRED' ORDER BY IP_ADD ");
									  
		  while($hardware_master_arr = mysqli_fetch_array($hardware_master_sel))
		  {
			  ?>
		
        <tr style="text-align: left; font-size:12px" id="row_hov">
          <td style="text-align: center;"><?php echo $s_no ; ?></td>
          <td><?php echo $hardware_master_arr["CAM_ID"] ; ?></td>
          <td><?php echo $hardware_master_arr["IP_ADD"] ; ?></td>
          <td><?php echo $hardware_master_arr["NAME_LOCATION"] ; ?></td>
          <td><?php echo $hardware_master_arr["MAKE"] ; ?></td>
          <td><?php echo $hardware_master_arr["MODEL"] ; ?></td>
          <td colspan="3"><?php echo $hardware_master_arr["SL_NO"] ; ?></td>
        </tr>
		
		<?php
		$s_no++ ;
		  }
		  ?>
		 <tr><td colspan="8" height="20"></td></tr>
		  
<!--CCTV STANDBY ASSET-->		 
		 <tr><td colspan="8" style="text-align:center; font-size:25px; background-color:#EBEBEB;">CCTV STAND BY</td></tr>
		 
        <tr style="text-align:center;" bgcolor="yellow">
          <td>S No</td>
          <td>CAM ID</td>
          <td>IP Address</td>
          <td>Category</td>
          <td>Name</td>
          <td>Make</td>
          <td>Model</td>
          <td>Serial No.</td>
        </tr>
		<?php
		$s_no = 1 ;
		
		  $hardware_master_sel=mysqli_query($link,"SELECT * FROM `cctv` WHERE `NAME_LOCATION`='MS (STANDBY)' ORDER BY IP_ADD ");
									  
		  while($hardware_master_arr = mysqli_fetch_array($hardware_master_sel))
		  {
			  ?>
		
        <tr style="text-align: left; font-size:12px" id="row_hov">
          <td style="text-align: center;"><?php echo $s_no ; ?></td>
          <td><?php echo $hardware_master_arr["CAM_ID"] ; ?></td>
          <td><?php echo $hardware_master_arr["IP_ADD"] ; ?></td>
          <td><?php echo $hardware_master_arr["CATG"] ; ?></td>
          <td><?php echo $hardware_master_arr["NAME_LOCATION"] ; ?></td>
          <td><?php echo $hardware_master_arr["MAKE"] ; ?></td>
          <td><?php echo $hardware_master_arr["MODEL"] ; ?></td>
          <td><?php echo $hardware_master_arr["SL_NO"] ; ?></td>
        </tr>
		
		<?php
		$s_no++ ;
		  }
		  ?>
      </tbody>
		 <tr><td colspan="8" height="20"></td></tr>
</table>
</div>
</div>