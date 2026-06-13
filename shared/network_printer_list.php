 

<h2 height="73" colspan="2" style="text-align:center; font-size: 20px;text-shadow: 1px 1px 2px black, 0 0 25px blue, 0 0 5px darkblue; color:#B1FF10;">&nbsp; Network Printer List &nbsp;</h2>
<div>
		<?php
			$query_sel = mysqli_query($link,"SELECT `CATG`, `CATG_TYPE`, COUNT(`HD_ID_NO`) FROM `hardware_master` WHERE USG!='MS (STANDBY)' AND USG!='WO' AND `CATG`!='NETWORK' AND `CATG`!='MODEM' AND `USG` LIKE '%NET%' GROUP BY CATG");
			
			while($row = mysqli_fetch_assoc($query_sel))
			{
				echo "<pre><b>" .$row['CATG'] ."</b> (" .$row['CATG_TYPE'] .")" ." :" .$row['COUNT(`HD_ID_NO`)'] ."</pre>" ;
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
		  
		  
		 <tr><td colspan="9" style="text-align:center; font-size:25px; background-color:#EBEBEB;">LAPTOP & PC</td></tr>
		 
        <tr style="text-align:center;" bgcolor="yellow">
          <td>S No</td>
          <td>Department</td>
          <td>Section</td>
          <td>Staff No.</td>
          <td>User Name</td>
          <td>IP Address</td>
          <td>Asset ID</td>
          <td>Usage</td>
        </tr>
		<?php
		$s_no = 1 ;
		
		  $hardware_master_sel=mysqli_query($link,"SELECT * FROM `hardware_master` WHERE (`USG`='NET' || `USG` LIKE '%NET%')  && (`CATG`='LAPTOP' || `CATG`='PC') ORDER BY DEPT, SEC, CATG, IP_ADDRESS ");
									  
		  while($hardware_master_arr = mysqli_fetch_array($hardware_master_sel))
		  {
			  ?>
		
        <tr style="text-align: left; font-size:12px" id="row_hov">
          <td style="text-align: center;"><?php echo $s_no ; ?></td>
          <td><?php echo $hardware_master_arr["DEPT"] ; ?></td>
          <td><?php echo $hardware_master_arr["SEC"] ; ?></td>
          <td><?php echo $hardware_master_arr["STAFF_NO"] ; ?></td>
          <td><?php echo $hardware_master_arr["USERNAME"] ; ?></td>
          <td><?php echo $hardware_master_arr["IP_ADDRESS"] ; ?></td>
          <td><?php echo $hardware_master_arr["HD_ID_NO"] ; ?></td>
          <td><?php echo $hardware_master_arr["USG"] ; ?></td>
        </tr>
		
		<?php
		$s_no++ ;
		  }
		  ?> 
		  
		  
		 <tr><td colspan="9" height="20"></td></tr>
		 <tr><td colspan="9" style="text-align:center; font-size:25px; background-color:#EBEBEB;">KISOK</td></tr>
		 
<!--KIOSK-->
        <tr style="text-align:center;" bgcolor="yellow">
          <td>S No</td>
          <td>Department</td>
          <td>Section</td>
          <td>Staff No.</td>
          <td>User Name</td>
          <td>IP Address</td>
          <td>Asset ID</td>
          <td>Usage</td>
        </tr>
		<?php
		$s_no = 1 ;
		
		  $hardware_master_sel=mysqli_query($link,"SELECT * FROM `hardware_master` WHERE (`USG`='NET' || `USG` LIKE '%NET%')  && (`CATG`='KIOSK') ORDER BY  IP_ADDRESS");
									  
		  while($hardware_master_arr = mysqli_fetch_array($hardware_master_sel))
		  {
			  ?>
		
        <tr style="text-align: left; font-size:12px" id="row_hov">
          <td style="text-align: center;"><?php echo $s_no ; ?></td>
          <td><?php echo $hardware_master_arr["DEPT"] ; ?></td>
          <td><?php echo $hardware_master_arr["SEC"] ; ?></td>
          <td><?php echo $hardware_master_arr["STAFF_NO"] ; ?></td>
          <td><?php echo $hardware_master_arr["USERNAME"] ; ?></td>
          <td><?php echo $hardware_master_arr["IP_ADDRESS"] ; ?></td>
          <td><?php echo $hardware_master_arr["HD_ID_NO"] ; ?></td>
          <td><?php echo $hardware_master_arr["USG"] ; ?></td>
        </tr>
		
		<?php
		$s_no++ ;
		  }
		  ?> 
		  
		  
		 <tr><td colspan="9" height="20"></td></tr>
		 <tr><td colspan="9" style="text-align:center; font-size:25px; background-color:#EBEBEB;">Printer</td></tr>

<!--Printer-->
		  
        <tr style="text-align:center;" bgcolor="yellow">
          <td>S No</td>
          <td>Department</td>
          <td>Section</td>
          <td>Staff No.</td>
          <td>User Name</td>
          <td>IP Address</td>
          <td>Asset ID</td>
        </tr>
		  
		<?php
		$s_no = 1 ;
		
		  $hardware_master_sel=mysqli_query($link,"SELECT * FROM `hardware_master` WHERE (`USG`='NET' || `USG` LIKE '%NET%')  && (`CATG`='PRINTER') ORDER BY DEPT, SEC, STAFF_NO, IP_ADDRESS ");
									  
		  while($hardware_master_arr = mysqli_fetch_array($hardware_master_sel))
		  {
			  ?>
		
        <tr style="text-align: left; font-size:12px" id="row_hov">
          <td style="text-align: center;"><?php echo $s_no ; ?></td>
          <td><?php echo $hardware_master_arr["DEPT"] ; ?></td>
          <td><?php echo $hardware_master_arr["SEC"] ; ?></td>
          <td><?php echo $hardware_master_arr["STAFF_NO"] ; ?></td>
          <td><?php echo $hardware_master_arr["USERNAME"] ; ?></td>
          <td><?php echo $hardware_master_arr["IP_ADDRESS"] ; ?></td>
          <td><?php echo $hardware_master_arr["HD_ID_NO"] ; ?></td>
        </tr>
		
		<?php
		$s_no++ ;
		  }
		  ?> 
		  
		  
		 <tr><td colspan="9" height="20"></td></tr>
		 <tr><td colspan="9" style="text-align:center; font-size:25px; background-color:#EBEBEB;">FIREWALL</td></tr>
		 
<!--FIREWALL-->
        <tr style="text-align:center;" bgcolor="yellow">
          <td>S No</td>
          <td>Department</td>
          <td>Section</td>
          <td>Staff No.</td>
          <td>User Name</td>
          <td>IP Address</td>
          <td>Asset ID</td>
          <td>Usage</td>
        </tr>
		<?php
		$s_no = 1 ;
		
		  $hardware_master_sel=mysqli_query($link,"SELECT * FROM `hardware_master` WHERE (`USG`='NET' || `USG` LIKE '%NET%')  && (`CATG`='ROUTER') ORDER BY  IP_ADDRESS");
									  
		  while($hardware_master_arr = mysqli_fetch_array($hardware_master_sel))
		  {
			  ?>
		
        <tr style="text-align: left; font-size:12px" id="row_hov">
          <td style="text-align: center;"><?php echo $s_no ; ?></td>
          <td><?php echo $hardware_master_arr["DEPT"] ; ?></td>
          <td><?php echo $hardware_master_arr["SEC"] ; ?></td>
          <td><?php echo $hardware_master_arr["STAFF_NO"] ; ?></td>
          <td><?php echo $hardware_master_arr["USERNAME"] ; ?></td>
          <td><?php echo $hardware_master_arr["IP_ADDRESS"] ; ?></td>
          <td><?php echo $hardware_master_arr["HD_ID_NO"] ; ?></td>
          <td><?php echo $hardware_master_arr["USG"] ; ?></td>
        </tr>
		
		<?php
		$s_no++ ;
		  }
		  ?> 
		  
		  
		 <tr><td colspan="9" height="20"></td></tr>
		 <tr><td colspan="9" style="text-align:center; font-size:25px; background-color:#EBEBEB;">SWITCH</td></tr>

<!--Switch-->
		  
        <tr style="text-align:center;" bgcolor="yellow">
          <td>S No</td>
          <td>IP Address</td>
          <td>Asset ID</td>
        </tr>
		  
		<?php
		$s_no = 1 ;
		
		  $hardware_master_sel=mysqli_query($link,"SELECT * FROM `hardware_master` WHERE (`USG`='NET' || `USG` LIKE '%NET%')  && (`CATG`='SWITCH') ORDER BY IP_ADDRESS ");
									  
		  while($hardware_master_arr = mysqli_fetch_array($hardware_master_sel))
		  {
			  ?>
		
        <tr style="text-align: left; font-size:12px" id="row_hov">
          <td style="text-align: center;"><?php echo $s_no ; ?></td>
          <td><?php echo $hardware_master_arr["IP_ADDRESS"] ; ?></td>
          <td><?php echo $hardware_master_arr["HD_ID_NO"] ; ?></td>
        </tr>
		
		<?php
		$s_no++ ;
		  }
		  ?> 
		 <tr><td colspan="9" height="20"></td></tr>
		  
      </tbody>
</table>
</div>
</div>