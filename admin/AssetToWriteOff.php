

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
		  $master_data_fetch=mysqli_query($link,"SELECT `HD_ID_NO` FROM `hardware_master` WHERE `USG`!='WO'");
		  while($master_data_arr=mysqli_fetch_array($master_data_fetch))
		  {
			  echo "<option>" .$master_data_arr["HD_ID_NO"] ."</option>" ;
			  }
			  ?>
      </select>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <input type="submit" name="search" id="butt" value="Search"><br/><br/><br/><br/><br/>
  </form>
 </div> 
 
 
<div>
 
 <?php
extract($_POST);
if(isset($subm))
{
	if($HD_ID_NO != "")
	{
		echo "<meta http-equiv='refresh' content='0'>";
		if(mysqli_query($link,"UPDATE `hardware_master` SET
								`STAFF_NO`='',`USERNAME`='', `DEPT`='M&ES', `SEC`='MIS', `USG`='WO' WHERE `HD_ID_NO`='$HD_ID_NO' " ))
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
	  <tr>
		<td><strong>NAME</strong></td>
        <td><?php echo $hardware_data_arr["USERNAME"] ; ?></td>
        <td>&nbsp;</td>
		<td><strong>STAFF NO</strong></td>
        <td><?php echo $hardware_data_arr["STAFF_NO"] ; ?></td>
	  </tr>
	  <tr>
		<td><strong>DEPARTMENT</strong></td>
        <td><?php echo $hardware_data_arr["DEPT"] ; ?></td>
        <td>&nbsp;<br/><br/><br/></td>
		<td><strong>SECTION</strong></td>
        <td><?php echo $hardware_data_arr["SEC"] ; ?></td>
	  </tr>
      <tr>
        <td><strong>CATEGORY</strong></td>
        <td><?php echo $hardware_data_arr["CATG"] ; ?></td>
        <td>&nbsp;<br/><br/></td>
        <td width="19%" ><strong>PROCCESSOR</strong></td>
        <td> <?php echo $hardware_data_arr["PROC"] ; ?></td>
      </tr>
	  
	  <tr>
		<td><strong>CATEGORY TYPE<strong></td>
        <td><?php echo $hardware_data_arr["CATG_TYPE"] ; ?></td>
        <td>&nbsp;<br/><br/></td>
        <td><strong>COLOR</strong></td>
        <td><?php echo $hardware_data_arr["COLOR"] ; ?></td>
	  </tr>
	  
      <tr>
        <td width="20%" ><strong> ID NO</strong></td>
        <td width="27%" ><input name="HD_ID_NO" type="text" id="HD_ID_NO" value="<?php echo $hardware_data_arr["HD_ID_NO"] ; ?>" style="border:0px;" required="required" readonly></td>
        <td>&nbsp;<br/><br/></td>
        <td><strong>RAM</strong></td>
        <td width="21%" ><?php echo $hardware_data_arr["RAM"] ; ?></td>
      </tr>
	  
      <tr>
        <td><strong>SERIAL NO.</strong></td>
        <td><?php echo $hardware_data_arr["MC_SL_NO"] ; ?></td>
        <td>&nbsp;<br/><br/></td>
        <td><strong>RAM TYPE</strong></td>
        <td><?php echo $hardware_data_arr["RAM_TYPE"] ; ?></td>
      </tr>
	  
      <tr>
        <td><strong>MFG Year</strong></td>
        <td><?php echo $hardware_data_arr["MFG_YR"] ; ?></td>
        <td>&nbsp;<br/><br/></td>
        <td><strong>HARD DISK</strong></td>
        <td><?php echo $hardware_data_arr["HDD"] ; ?></td>
      </tr>
	  
      <tr>
        <td><strong>MODEL NO.</strong></td>
        <td><?php echo $hardware_data_arr["MODEL"] ; ?></td>
        <td>&nbsp;<br/><br/></td>
        <td><strong>IP ADDRESS</strong></td>
        <td><?php echo $hardware_data_arr["IP_ADDRESS"] ; ?></td>
      </tr>
	  
      <tr>
        <td><strong>MAKE MODEL</strong></td>
        <td><?php echo $hardware_data_arr["MAKE"] ; ?></td>
		<td>&nbsp;<br/><br/></td>
        <td><strong>OS</strong></td>
        <td><?php echo $hardware_data_arr["OS"] ; ?></td>
      </tr>
		
      <tr>
        <td><strong>PO NO</strong></td>
        <td><?php echo $hardware_data_arr["PO_NO"] ; ?></td>
        <td>&nbsp;<br/><br/></td>
        <td><strong>ASSET NO</strong></td>
        <td><?php echo $hardware_data_arr["ASSET_NO"] ; ?></td>
      </tr>
		
      <tr>
        <td><strong>SERVICE TYPE</strong></td>
        <td><?php echo $hardware_data_arr["AMC_WAR_WO_WIP"] ; ?></td>
		<td>&nbsp;<br/><br/></td>
      </tr>
	
      <tr>
		<td><strong>PURCHASE DATE<sup style="color:red;">*</sup></strong></td>
        <td><?php echo $hardware_data_arr["warnty_valid_frm"] ; ?></td>
        <td>&nbsp;<br/><br/></td>
		<td><strong>WARANTY EXPIRY DATE<sup style="color:red;">*</sup></strong></td>
        <td><?php echo $hardware_data_arr["warnty_valid_upto"] ; ?></td>
      </tr>
	  
      <tr>
        <td height="27" colspan="5" >&nbsp;</td>
      </tr>
      <tr>
        <td colspan="5" style="text-align: center"><input type="submit" id="butt" name="subm" id="butt3" value="Write Off"></td>
      </tr>
      </tbody>
    </table>
  </form>
</div>
