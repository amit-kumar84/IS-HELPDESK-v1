

<?php

	date_default_timezone_set('Asia/Kolkata');
	$current_date = date('Y-m-d');
	
	extract($_POST);
	if(isset($search))
	{
		$data_fetch_hd=mysqli_query($link,"SELECT * FROM `hardware_master` WHERE `HD_ID_NO`='$hardware_no'");
		$hardware_data_arr=mysqli_fetch_array($data_fetch_hd);
	}
	?>

<div>
  <form id="form1" name="form1" method="post">
  <br/><br/>
	<b>BEL ID Number</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;
      <select name="hardware_no" style="width: 173px;" required="required">
          <option style="color:#AFAFAF;" value="" selected disabled> Select Machine</option>
		  
		  <!-- fetch all machine's details  -->
		  <?php
		  $master_data_fetch=mysqli_query($link,"SELECT `HD_ID_NO` FROM `hardware_master`");
		  while($master_data_arr=mysqli_fetch_array($master_data_fetch))
		  {
			  echo "<option>" .$master_data_arr["HD_ID_NO"] ."</option>" ;
			  }
			  ?>
      </select>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <input type="submit" name="search" id="butt" value="Search"><br/><br/>
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
</div>

				
	<div class="tr_hov">
		<table width="500px" border="1" align="center" cellpadding="1" cellspacing="0" id="table_func">
			<center><b style="color:red; font-size:25px;"><u><?php echo $hardware_data_arr["HD_ID_NO"] ; ?></u></b></center><br/>
			<tbody>
					<tr bgcolor="yellow">
						<td><center><strong>Title</strong></center></td>
						<td><center><strong>Description</strong></center></td>
					</tr>
					<tr>
						<td><strong>NAME</strong></td>
						<td><?php echo $hardware_data_arr["USERNAME"] ; ?></td>
					</tr>
					<tr>			
						<td><strong>STAFF NO</strong></td>
						<td><?php echo $hardware_data_arr["STAFF_NO"] ; ?></td>
					</tr>
					<tr>
						<td><strong>DEPARTMENT</strong></td>
						<td><?php echo $hardware_data_arr["DEPT"] ; ?></td>
					</tr>
					<tr>			
						<td><strong>SECTION</strong></td>
						<td><?php echo $hardware_data_arr["SEC"] ; ?></td>
					</tr>
					<tr style="background-color:#E1DDFA">
						<td><strong>USAGE</strong></td>
						<td><?php echo $hardware_data_arr["USG"] ; ?></td>
					</tr>
					<tr>
						<td><strong>CATEGORY</strong></td>
						<td><?php echo $hardware_data_arr["CATG"] ; ?></td>
					</tr>
					<tr>
						<td><strong>PROCCESSOR</strong></td>
						<td><?php echo $hardware_data_arr["PROC"] ; ?></td>
					</tr>
					<tr>
						<td><strong>CATEGORY TYPE<strong></td>
						<td><?php echo $hardware_data_arr["CATG_TYPE"] ; ?></td>
					</tr>
					<tr>
						<td><strong>COLOR</strong></td>
						<td><?php echo $hardware_data_arr["COLOR"] ; ?></td>
					</tr>
					<tr>
						<td><strong> ID NO</strong></td>
						<td><?php echo $hardware_data_arr["HD_ID_NO"] ; ?></td>
					</tr>
					<tr>
						<td><strong> RAM</strong></td>
						<td><?php echo $hardware_data_arr["RAM"] ; ?></td>
					</tr>
					<tr>
						<td><strong>SERIAL NO.</strong></td>
						<td><?php echo $hardware_data_arr["MC_SL_NO"] ; ?></td>
					</tr>
					<tr>
						<td><strong>RAM TYPE</strong></td>
						<td><?php echo $hardware_data_arr["RAM_TYPE"] ; ?></td>
					</tr>
					<tr>
						<td><strong>MFG Year</strong></td>
						<td><?php echo $hardware_data_arr["MFG_YR"] ; ?></td>
					</tr>
					<tr>
						<td><strong>HARD DISK</strong></td>
						<td><?php echo $hardware_data_arr["HDD"] ; ?></td>
					</tr>
					<tr>
						<td><strong>MODEL NO.</strong></td>
						<td><?php echo $hardware_data_arr["MODEL"] ; ?></td>
					</tr>
					<tr>
						<td><strong>IP ADDRESS</strong></td>
						<td><?php echo $hardware_data_arr["IP_ADDRESS"] ; ?></td>
					</tr>
					<tr>
						  <td><strong>MAKE MODEL</strong></td>
						  <td><?php echo $hardware_data_arr["MAKE"] ; ?></td>
					</tr>
					<tr>
						  <td><strong>OS</strong></td>
						  <td><?php echo $hardware_data_arr["OS"] ; ?></td>
					</tr>
					<tr>
						  <td><strong>PO NO</strong></td>
						  <td><?php echo $hardware_data_arr["PO_NO"] ; ?></td>
					</tr>
					<tr>
						  <td><strong>ASSET NO</strong></td>
						  <td><?php echo $hardware_data_arr["ASSET_NO"] ; ?></td>
					</tr>
					<tr>
						  <td><strong>SERVICE TYPE</strong></td>
						  <td><?php echo $hardware_data_arr["AMC_WAR_WO_WIP"] ; ?></td>
					</tr>
					<tr>
						<td><strong>PURCHASE DATE</strong></td>
						<td><?php echo $hardware_data_arr["warnty_valid_frm"] ; ?></td>
					</tr>
					<tr>
						<td><strong>WARANTY EXPIRY DATE</strong></td>
						<td><?php echo $hardware_data_arr["warnty_valid_upto"] ; ?></td>
					</tr>
			</tbody>
		</table>
	</div>
</div>

