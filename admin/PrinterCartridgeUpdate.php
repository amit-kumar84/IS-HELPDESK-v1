

<?php
	extract($_POST);
	if(isset($search))
	{
		$printer_model=mysqli_query($link,"SELECT * FROM `printer_cartridge_list` WHERE `model`='$model_search'");
		$printer_model_arr=mysqli_fetch_array($printer_model);
	}
	?>

<div>
  <form id="form1" name="form1" method="post">
  <br/><br/>
	<b>Printer Model</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;
      <select name="model_search" style="width: 173px;" required="required">
          <option style="color:#AFAFAF;" value="" selected disabled> Select Printer Model</option>
		  
		  <!-- fetch all machine's details  -->
		  <?php
		  $master_printer_model = mysqli_query($link,"SELECT `model` FROM `printer_cartridge_list`");
		  while($printer_model_data_arr=mysqli_fetch_array($master_printer_model))
		  {
			  echo "<option>" .$printer_model_data_arr["model"] ."</option>" ;
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
	if($model != "")
	{
		echo "<meta http-equiv='refresh' content='0'>";
		if(mysqli_query($link,"UPDATE `printer_cartridge_list` SET `model`='$model', `make`='$make', `cartridge_no`='$cartridge_no', `ttl_page_prnt`='$ttl_page_prnt', `rate`='$rate',`part_no`='$part_number' WHERE `model`='$model'" ))
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

<div>
<form name="print_cartdge_form" method="post">
  <table width="80%" border="0" align="center" cellpadding="1" cellspacing="0">
	
    <tbody>
	
    <tr>
      <td colspan="5" style="text-align: center; font-size: xx-large;"><strong><u>Description</u></strong></td>
    </tr>
	
    <tr>
      <td colspan="5" style="text-align: right">&nbsp;<br/><br/></td>
    </tr>
	
    <tr>
      <td width="20%"  ><strong> MODEL<sup style="color:red;">*</sup></strong><br/><br/></td>
      <td width="27%"  ><input name="model" type="text" value="<?php echo $printer_model_arr["model"] ; ?>" size="30" placeholder=" Printer Model" required="required" readonly style="border:0px;"><br/><br/></td>
    </tr>
	
    <tr>
      <td><strong>MAKE<sup style="color:red;">*</sup></strong><br/><br/></td>
      <td><input name="make" type="text" value="<?php echo $printer_model_arr["make"] ; ?>" placeholder=" Make" required="required"><br/><br/></td>
    </tr>
	
    <tr>
      <td><strong>CARTRIDGE NUMBER<sup style="color:red;">*</sup></strong><br/><br/></td>
      <td><input name="cartridge_no" type="text" value="<?php echo $printer_model_arr["cartridge_no"] ; ?>" placeholder=" Cartridge Number" required="required"><br/><br/></td>
    </tr>
	
    <tr>
      <td><strong>TOTAL PAGE'S PRINT<br/><br/></td>
      <td><input name="ttl_page_prnt" type="text" value="<?php echo $printer_model_arr["ttl_page_prnt"] ; ?>" placeholder=" Total page print"><br/><br/></td>
    </tr>
	
      <tr>
        <td><strong>RATE<sup style="color:red;">*</sup></strong><br/><br/></td>
        <td><input name="rate" type="text" value="<?php echo $printer_model_arr["rate"] ; ?>" placeholder=" Printer Rate" required="required"><br/><br/></td>
      </tr>
	  
      <tr>
        <td><strong>PART NUMBER<sup style="color:red;">*</sup></strong><br/><br/></td>
        <td><input name="part_number" type="text" maxlength="12" value="<?php echo $printer_model_arr["part_no"] ; ?>" placeholder=" Part Number" required="required"><br/><br/></td>
      </tr>
	  
      <tr>
          <td colspan="5" style="text-align: center"><input type="submit" id="butt" name="subm" id="butt3" value="Update"></td>
      </tr>
	  
    </tbody>
  </table>
  </form>
</div>