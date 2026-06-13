

<h2 height="73" colspan="2" style="text-align:center; font-size: 20px;text-shadow: 1px 1px 2px black, 0 0 25px blue, 0 0 5px darkblue; color:#B1FF10;">&nbsp; Hardware List &nbsp;</h2>
<div>
		<?php
			$query_sel = mysqli_query($link,"SELECT `CATG`, COUNT(CATG), CATG_TYPE FROM `hardware_master` WHERE USERNAME!='ISKOT' AND USG!='MS (STANDBY)' AND USG!='WO' AND CATG!='WEB_CAM' AND CATG!='PROJECTOR' GROUP BY CATG");
			
			while($row = mysqli_fetch_assoc($query_sel))
			{
				echo "<pre><b>" .$row['CATG'] ."</b> (" .$row['CATG_TYPE'] .")" ." :" .$row['COUNT(CATG)'] ."</pre>" ;
			}
		?>
		<br/>

<form id="form" action="" name="form1" method="POST">
	  <p>
		<b style="color:green;">Department : </b>
			<select name="dept_wise_search" id="call_timing_dept" required>
				<option style="color:#AFAFAF;">All</option>
					<?php						
					$department_name_fetch = mysqli_query($link,"SELECT DISTINCT(DEPT) FROM `hardware_master` ORDER BY DEPT ASC ");
						while($department_name = mysqli_fetch_array($department_name_fetch))
						{
					?>
					<option><?php echo $department_name["DEPT"] ; ?></option>
					<?php
						}
					?>
			</select>
			
		<b style="color:green;">Section : </b>
			<select name="sec_wise_search" id="call_timing_dept" required>
				<option style="color:#AFAFAF;">All</option>
					<?php						
					$sec_name_fetch = mysqli_query($link,"SELECT DISTINCT(SEC) FROM `hardware_master` ORDER BY SEC ASC ");
						while($sec_name = mysqli_fetch_array($sec_name_fetch))
						{
					?>
					<option><?php echo $sec_name["SEC"] ; ?></option>
					<?php
						}
					?>
			</select>
			
			<b style="color:green;">Category : </b>
			<select name="catg_wise_search" id="call_timing_dept" required>
				<option style="color:#AFAFAF;">All</option>
					<?php						
					$catg_name_fetch = mysqli_query($link,"SELECT DISTINCT(CATG) FROM `hardware_master` WHERE USG!='WO' && STAFF_NO!='ISKOT' && CATG!='WEB_CAM' AND CATG!='PROJECTOR' ORDER BY CATG ASC ");
						while($catg_name = mysqli_fetch_array($catg_name_fetch))
						{
					?>
					<option><?php echo $catg_name["CATG"] ; ?></option>
					<?php
						}
					?>
			</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="sub" id="butt" value="Search">
	  </p>
  </form>

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
          <td>Dept</td>
          <td>Sec</td>
          <td>Staff No.</td>
          <td>Username</td>
          <td>Usage</td>
          <td>Catg</td>
          <td>Catg type</td>
          <td>ID No</td>
          <td>Cost Center</td>
          <td>Asset Location</td>
          <td>IP Address</td>
          <td>M/C SL No</td>
          <td>Make</td>
          <td>Model</td>
          <td>MFG Yr</td>
          <td>Verified On</td>
        </tr>
		<?php
		$s_no = 1 ;
			extract($_POST);
			if(isset($sub))
			{
				// N N N
				if($dept_wise_search!="All" && $sec_wise_search!= "All" && $catg_wise_search!="All")
				{
		  $hardware_master_sel=mysqli_query($link,"SELECT * FROM `hardware_master` WHERE USG!='WO' AND USG!='MS (STANDBY)' AND USERNAME!='ISKOT' AND CATG!='WEB_CAM' AND CATG!='PROJECTOR' AND `DEPT`='$dept_wise_search' AND `SEC`='$sec_wise_search' AND `CATG`='$catg_wise_search' ORDER BY DEPT, SEC, USERNAME");
				}
				// Y N N
				else if($dept_wise_search=="All" && $sec_wise_search!= "All" && $catg_wise_search!="All")
				{
		  $hardware_master_sel=mysqli_query($link,"SELECT * FROM `hardware_master` WHERE USG!='WO' AND USG!='MS (STANDBY)' AND USERNAME!='ISKOT' AND CATG!='WEB_CAM' AND CATG!='PROJECTOR' AND `SEC`='$sec_wise_search' AND `CATG`='$catg_wise_search' ORDER BY DEPT, SEC, USERNAME");
				}
				// Y Y N
				else if($dept_wise_search=="All" && $sec_wise_search== "All" && $catg_wise_search!="All")
				{
		  $hardware_master_sel=mysqli_query($link,"SELECT * FROM `hardware_master` WHERE USG!='WO' AND USG!='MS (STANDBY)' AND USERNAME!='ISKOT' AND CATG!='WEB_CAM' AND CATG!='PROJECTOR' AND CATG!='PROJECTOR' AND `CATG`='$catg_wise_search' ORDER BY DEPT, SEC, USERNAME");
				}
				// Y N Y
				else if($dept_wise_search=="All" && $sec_wise_search!= "All" && $catg_wise_search=="All")
				{
		  $hardware_master_sel=mysqli_query($link,"SELECT * FROM `hardware_master` WHERE USG!='WO' AND USG!='MS (STANDBY)' AND USERNAME!='ISKOT' AND CATG!='WEB_CAM' AND CATG!='PROJECTOR' AND `SEC`='$sec_wise_search' ORDER BY DEPT, SEC, USERNAME");
				}
				// N Y Y
				else if($dept_wise_search!="All" && $sec_wise_search== "All" && $catg_wise_search=="All")
				{
		  $hardware_master_sel=mysqli_query($link,"SELECT * FROM `hardware_master` WHERE USG!='WO' AND USG!='MS (STANDBY)' AND USERNAME!='ISKOT' AND CATG!='WEB_CAM' AND CATG!='PROJECTOR' AND `DEPT`='$dept_wise_search' ORDER BY DEPT, SEC, USERNAME");
				}
				// N N Y
				else if($dept_wise_search!="All" && $sec_wise_search!= "All" && $catg_wise_search=="All")
				{
		  $hardware_master_sel=mysqli_query($link,"SELECT * FROM `hardware_master` WHERE USG!='WO' AND USG!='MS (STANDBY)' AND USERNAME!='ISKOT' AND CATG!='WEB_CAM' AND CATG!='PROJECTOR' AND `DEPT`='$dept_wise_search' AND `SEC`='$sec_wise_search' ORDER BY DEPT, SEC, USERNAME");
				}
				// N Y N
				else if($dept_wise_search!="All" && $sec_wise_search== "All" && $catg_wise_search!="All")
				{
		  $hardware_master_sel=mysqli_query($link,"SELECT * FROM `hardware_master` WHERE USG!='WO' AND USG!='MS (STANDBY)' AND USERNAME!='ISKOT' AND CATG!='WEB_CAM' AND CATG!='PROJECTOR' AND `DEPT`='$dept_wise_search' AND `CATG`='$catg_wise_search' ORDER BY DEPT, SEC, USERNAME");
				}
				// Y Y Y
				else if($dept_wise_search=='All' && $sec_wise_search=='All' && $catg_wise_search=='All')
				{
		  $hardware_master_sel=mysqli_query($link,"SELECT * FROM `hardware_master` WHERE USG!='WO' AND USG!='MS (STANDBY)' AND USERNAME!='ISKOT' AND CATG!='WEB_CAM' AND CATG!='PROJECTOR' ORDER BY DEPT, SEC, USERNAME");
				}
									  
		  while($hardware_master_arr = mysqli_fetch_array($hardware_master_sel))
		  {
			  ?>
		
        <tr style="text-align: left; font-size:10px" id="row_hov">
          <td style="text-align: center;"><?php echo $s_no ; ?></td>
			<?php
					$sid = $hardware_master_arr["STAFF_NO"] ;
				 $query_sel=mysqli_query($link,"SELECT * FROM `emp_details` WHERE `staffid`='$sid'");
				 $emp_det=mysqli_fetch_array($query_sel) ;
			?>
		  
		  
          <td><?php echo $emp_det["deptt"] ; ?></td>
          <td><?php echo $emp_det["sec"] ; ?></td>
		  
          <td><?php echo $hardware_master_arr["STAFF_NO"] ; ?></td>
          <td><?php echo $hardware_master_arr["USERNAME"] ; ?></td>
          <td><?php echo $hardware_master_arr["USG"] ; ?></td>
          <td><?php echo $hardware_master_arr["CATG"] ; ?></td>
          <td><?php echo $hardware_master_arr["CATG_TYPE"] ; ?></td>
		  <td><?php echo $hardware_master_arr["HD_ID_NO"] ; ?></td>
		  <td><?php echo $emp_det["cost_center"] ; ?></td>
		  <td><?php echo $hardware_master_arr["DEPT"] ." ( " .$hardware_master_arr["SEC"] ." ) " ; ?></td>
          <td><?php echo $hardware_master_arr["IP_ADDRESS"] ; ?></td>
          <td><?php echo $hardware_master_arr["MC_SL_NO"] ; ?></td>
          <td><?php echo $hardware_master_arr["MAKE"] ; ?></td>
          <td><?php echo $hardware_master_arr["MODEL"] ; ?></td>
          <td><?php echo $hardware_master_arr["MFG_YR"] ; ?></td>
          <td>
				<?php 
						if($hardware_master_arr["issued_on"]!=0)
						{
							echo $hardware_master_arr["issued_on"] ;
						}
						else
						{
							echo "" ;
						}
				?>
		  </td>
        </tr>
		
		<?php
		$s_no++ ;
		  }
			}
		  ?>  
      </tbody>
</table>
</div>
</div>