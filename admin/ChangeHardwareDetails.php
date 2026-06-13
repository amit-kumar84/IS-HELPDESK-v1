

<?php
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
      <input type="submit" name="search" id="butt" value="Search"><br/><br/><br/><br/><br/>
  </form>
  
 <?php
extract($_POST);
if(isset($subm))
{
	if($HD_ID_NO != "")
	{
		echo "<meta http-equiv='refresh' content='0'>";
		if(mysqli_query($link,"UPDATE `hardware_master` SET
								`USG`='$HD_usg', `CATG`='$HD_type', `CATG_TYPE`='$HD_catg_type_4_printer', `MC_SL_NO`='$HD_serialno', `MAKE`='$HD_make', `MODEL`='$HD_model_no', `MFG_YR`='$HD_mfg_year', `OS`='$HD_os', `COLOR`='$HD_color', `PROC`='$HD_procc', `RAM`='$HD_ram', `RAM_TYPE`='$HD_ramtype', `HDD`='$HD_hdd', `DISPLAY`='$HD_display', `AMC_WAR_WO_WIP`='$HD_Service_type', `IP_ADDRESS`='$HD_ip_address', `PO_NO`='$HD_po_no', `ASSET_NO`='$HD_asset_no', `warnty_valid_frm`='$p_date', `warnty_valid_upto`='$w_exp_date', `mac_address`='$HD_mac_add' WHERE `HD_ID_NO`='$HD_ID_NO' " ))
		{
			echo "<meta http-equiv='refresh' content='0'>";
			echo '<script language="javascript">' .'alert("Update Successfully!")' .'</script>';
		}
		
		else
		{
			echo "<b style='color:red;'>Unknown Error!</b><br/></br>" ;
		}
	}
	else
	{
		echo "<b style='color:red;'>No query to update...</b><br/></br>" ;
	}
}
	?>

<form id="form2" name="form2" method="POST">
    <table width="100%" border="0" align="center" cellpadding="1" cellspacing="0">	  
      <tbody>
	  <tr style="color:green;">
		<td><strong>NAME</strong></td>
        <td><?php echo $hardware_data_arr["USERNAME"] ; ?></td>
        <td>&nbsp;</td>
		<td><strong>STAFF NO</strong></td>
        <td><?php echo $sid = $hardware_data_arr["STAFF_NO"] ; ?></td>
	  </tr>
	  <?php
			error_reporting(0);
			include("connection.php");
			 $query_sel=mysqli_query($link,"SELECT * FROM `emp_details` WHERE `staffid`='$sid'");
			 $emp_det=mysqli_fetch_array($query_sel) ;
	  ?>
	  
	  <tr style="color:green;">
		<td><strong>DEPARTMENT</strong></td>
        <td><?php echo $emp_det["deptt"] ; ?></td>
        <td>&nbsp;<br/><br/></td>
		<td><strong>SECTION</strong></td>
        <td><?php echo $emp_det["sec"] ; ?></td>
	  </tr>
	  <tr style="color:green;">
		<td><strong>ASSET LOCATION</strong></td>
        <td><?php echo $hardware_data_arr["DEPT"] ." &nbsp; &nbsp;&nbsp; (" .$hardware_data_arr["SEC"] .") " ; ?></td>
        <td>&nbsp;<br/><br/></td><td width="20%" ><strong> ID NO</strong></td>
        <td width="27%"><input name="HD_ID_NO" style="border:0; color:green" type="text" id="HD_ID_NO" value="<?php echo $hardware_data_arr["HD_ID_NO"] ; ?>" placeholder=" BEL ID Number" required="required" readonly></td>
	  </tr>
      <tr>
        <td><strong>CATEGORY</strong></td>
        <td>
		<select name="HD_type" style="width: 173px;">
          <option value="<?php echo $hardware_data_arr["CATG"] ; ?>" style="color:#AFAFAF;" selected><?php echo $hardware_data_arr["CATG"] ; ?></option>
          <option value="PC">PC</option>
          <option value="Laptop">Laptop</option>
          <option value="Printer">Printer</option>
          <option value="Scanner">Scanner</option>
          <option value="Projector">Projector</option>
        </select>
		</td>
        <td>&nbsp;<br/><br/></td>
        <td width="19%" ><strong>PROCCESSOR</strong></td>
        <td ><input name="HD_procc" type="text" id="HD_procc" value="<?php echo $hardware_data_arr["PROC"] ; ?>" placeholder=" Proccessor"></td>
      </tr>
	  
	  <tr>
		<td><strong>CATEGORY TYPE<strong></td>
        <td><input name="HD_catg_type_4_printer" type="text" value="<?php echo $hardware_data_arr["CATG_TYPE"] ; ?>" placeholder=" AIO / Printer / Scanner"></td>
        <td>&nbsp;<br/><br/></td>
          <td><strong>COLOR</strong></td>
          <td><span><input name="HD_color" type="text" id="HD_color" value="<?php echo $hardware_data_arr["COLOR"] ; ?>" placeholder=" Color"></span></td>
	  </tr>
	  
      <tr>
		<td><strong>USAGE</strong></td>
        <td><input name="HD_usg" type="text" id="HD_usg" value="<?php echo $hardware_data_arr["USG"] ; ?>" placeholder=" Usage"></td>
        <td>&nbsp;<br/><br/></td>
        <td><strong>RAM</strong></td>
        <td width="21%" ><input name="HD_ram" type="text" id="HD_ram" value="<?php echo $hardware_data_arr["RAM"] ; ?>" placeholder=" RAM"></td>
      </tr>
	  
      <tr>
        <td><strong>SERIAL NO.</strong></td>
        <td><input name="HD_serialno" type="text" id="HD_serialno" value="<?php echo $hardware_data_arr["MC_SL_NO"] ; ?>" placeholder=" Machine Serial Number"></td>
        <td>&nbsp;<br/><br/></td>
        <td><strong>RAM TYPE</strong></td>
        <td><input name="HD_ramtype" type="text" id="HD_ramtype" value="<?php echo $hardware_data_arr["RAM_TYPE"] ; ?>" placeholder=" RAM Type"></td>
      </tr>
	  
      <tr>
        <td><strong>MFG YEAR</strong></td>
        <td><input name="HD_mfg_year" type="year" id="HD_mfg_year" value="<?php echo $hardware_data_arr["MFG_YR"] ; ?>" placeholder=" Year"></td>
        <td>&nbsp;<br/><br/></td>
        <td><strong>HARD DISK</strong></td>
        <td><input name="HD_hdd" type="text" id="HD_hdd" value="<?php echo $hardware_data_arr["HDD"] ; ?>" placeholder=" Storage Capacity"></td>
      </tr>
	  
      <tr>
        <td><strong>MODEL NO.</strong></td>
        <td><input name="HD_model_no" type="text" id="HDmodelno" value="<?php echo $hardware_data_arr["MODEL"] ; ?>" placeholder=" Model Number"></td>
        <td>&nbsp;<br/><br/></td>
        <td><strong>IP ADDRESS</strong></td>
        <td><input name="HD_ip_address" type="text" id="HDipaddress" value="<?php echo $hardware_data_arr["IP_ADDRESS"] ; ?>" placeholder=" IP Address"></td>
      </tr>
	  
        <tr>
          <td><strong>MAKE MODEL</strong></td>
          <td><input name="HD_make" type="text" id="HD_make" value="<?php echo $hardware_data_arr["MAKE"] ; ?>" placeholder=" Company Name"></td>
        <td>&nbsp;<br/><br/></td>
          <td><strong>OS</strong></td>
          <td><input name="HD_os" type="text" id="HD_os" value="<?php echo $hardware_data_arr["OS"] ; ?>" placeholder=" Operating System"></td>
        </tr>
		
        <tr>
          <td><strong>PO NO</strong></td>
          <td><input name="HD_po_no" type="text" id="HD_po_no" value="<?php echo $hardware_data_arr["PO_NO"] ; ?>" placeholder=" PO Number"></td>
        <td>&nbsp;<br/><br/></td>
          <td><strong>ASSET NO</strong></td>
          <td><input name="HD_asset_no" type="text" id="HD_asset_no" value="<?php echo $hardware_data_arr["ASSET_NO"] ; ?>" placeholder=" Asset Number"></td>
        </tr>
		
        <tr>
          <td><strong>SERVICE TYPE</strong></td>
          <td><span >
            <select name="HD_Service_type" style="width: 173px;">
          	  <option style="color:#AFAFAF;" value="<?php echo $hardware_data_arr["AMC_WAR_WO_WIP"] ; ?>" required="required" selected><?php echo $hardware_data_arr["AMC_WAR_WO_WIP"] ; ?></option>
              <option value="A">A</option>
              <option value="W">W</option>
              <option value="WO">WO</option>
            </select>
          </span></td>
        <td>&nbsp;<br/><br/></td>
          <td><strong>MAC ADDRESS</strong></td>
          <td><input name="HD_mac_add" type="text" id="HD_mac_add" value="<?php echo $hardware_data_arr["mac_address"] ; ?>" placeholder=" MAC Address"></td>
        </tr>
	
      <tr>
		<td><strong>PURCHASE DATE<sup style="color:red;">*</sup></strong></td>
		<?php
		date_default_timezone_set('Asia/Kolkata');
		$current_date = date('Y-m-d');
		?>
        <td><input name="p_date" type="date" value="<?php echo $hardware_data_arr["warnty_valid_frm"] ; ?>" max="<?php echo $current_date ; ?>"><span class="validity"></span></td>
        <td>&nbsp;<br/><br/></td>
        
		<td><strong>WARANTY EXPIRY DATE<sup style="color:red;">*</sup></strong></td>
        <td><input name="w_exp_date" type="date" VALUE="<?php echo $hardware_data_arr["warnty_valid_upto"] ; ?>" min="<?php echo $current_date ; ?>"></td>
      </tr>
	  
        <tr>
          <td height="27" colspan="5" >&nbsp;</td>
        </tr>
        <tr>
          <td colspan="5" style="text-align: center"><input type="submit" id="butt" name="subm" id="butt3" value="Update"></td>
        </tr>
      </tbody>
    </table>
  </form>
</div>
