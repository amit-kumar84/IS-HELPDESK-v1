

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
		  $master_data_fetch=mysqli_query($link,"SELECT `HD_ID_NO` FROM `hardware_master` WHERE `CATG`='PC' ||`CATG`='LAPTOP' || `CATG`='PRINTER' || `CATG`='PROJECTOR' ");
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
		if(mysqli_query($link,"UPDATE `hardware_master` SET `ASSET_NO`='$HD_asset_no' WHERE `HD_ID_NO`='$HD_ID_NO' " ))
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
    <table width="60%" border="0" align="center" cellpadding="1" cellspacing="0">	  
      <tbody>
		<tr>
			<td width="20%" ><strong> ID NO</strong><br/><br/></td>
			<td width="27%" ><input name="HD_ID_NO" type="text" id="HD_ID_NO" value="<?php echo $hardware_data_arr["HD_ID_NO"] ; ?>" placeholder=" BEL ID Number" required="required" readonly><br/><br/></td>
		</tr>
        <tr>
          <td><strong>ASSET NO</strong></td>
          <td><input name="HD_asset_no" type="text" id="HD_asset_no" value="<?php echo $hardware_data_arr["ASSET_NO"] ; ?>" placeholder=" Asset Number"></td>
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
