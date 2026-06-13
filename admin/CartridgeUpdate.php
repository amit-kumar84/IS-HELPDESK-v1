


<?php
	extract($_POST);
	if(isset($search))
	{
		$cartridge_model=mysqli_query($link,"SELECT * FROM `cartridge_stock_list` WHERE `cartridge_no`='$cartridge_search' AND `color`='$color_search'");
		if(mysqli_num_rows($cartridge_model)==0)
		{
			$msg = "**No record found" ;
		}
		else{
		$cartridge_model_arr=mysqli_fetch_array($cartridge_model);
		}
	}
	?>

<div>
  <form id="form1" name="form1" method="post">
  <br/><br/>
	<b>Cartridge</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;
      <select name="cartridge_search" style="width: 173px;" required="required">
          <option style="color:#AFAFAF;" value="" selected disabled> Select Cartridge</option>
		  
		  <!-- fetch all machine's details  -->
		  <?php
		  $master_cartridge_model = mysqli_query($link,"SELECT  DISTINCT(cartridge_no) FROM `cartridge_stock_list`");
		  while($cartridge_model_data_arr=mysqli_fetch_array($master_cartridge_model))
		  {
			  echo "<option>" .$cartridge_model_data_arr["cartridge_no"] ."</option>" ;
			  }
			  ?>
      </select>
	  <select name="color_search">
				<option value="BLACK" selected>BLACK</option>
				<option value="YELLOW">YELLOW</option>
				<option value="CYAN">CYAN</option>
				<option value="MAGENTA">MAGENTA</option>
            </select>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <input type="submit" name="search" id="butt" value="Search"><br/><br/><br/><br/><br/>
  </form>
 </div>
<b style="color:red;"><?php echo $msg ;?></b>


  
 <?php
extract($_POST);
if(isset($subm))
{
	if($cartridge != "")
	{
		echo "<meta http-equiv='refresh' content='0'>";
		if(mysqli_query($link,"UPDATE `cartridge_stock_list` SET `cartridge_serial_no`='$sl_no', `no_of_page`='$ttl_page_prnt', `cost_of_toner`='$rate', `part_no`='$part_number' WHERE `cartridge_no`='$cartridge' AND `color`='$color' "))
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
  <table width="80%" border="0" align="center" cellpadding="1" cellspacing="0">
	
    <tbody>
	
    <tr>
      <td colspan="5" style="text-align: center; font-size: xx-large;"><strong><u>Description</u></strong></td>
    </tr>
	
    <tr>
      <td colspan="5" style="text-align: right">&nbsp;<br/><br/></td>
    </tr>
	
<form name="add_prntr_cartg" method="post">
    <tr>
      <td width="30%"  ><strong> CARTRIDGE<sup style="color:red;">*</sup></strong><br/><br/></td>
      <td><input name="cartridge" type="text" value="<?php echo $cartridge_model_arr["cartridge_no"] ; ?>" size="40" readonly required="required" style="border:0px;"><br/><br/></td>
    </tr>
    <tr>
        <td height="32"><strong> COLOR<strong><sup style="color:red;">*</sup></strong><br/><br/></td>
          <td><input name="color" type="text" value="<?php echo $cartridge_model_arr["color"] ; ?>" size="40"readonly required="required" style="border:0px;"><br/><br/></td>
    </tr>
    <tr>
      <td width="30%"  ><strong> SERIAL NO<sup style="color:red;">*</sup></strong><br/><br/></td>
      <td><input name="sl_no" type="text" value="<?php echo $cartridge_model_arr["cartridge_serial_no"] ; ?>" placeholder=" SERIAL NO"><br/><br/></td>
    </tr>
    <tr>
      <td><strong>TOTAL PAGE'S PRINT<br/><br/></td>
      <td><input name="ttl_page_prnt" type="text" value="<?php echo $cartridge_model_arr["no_of_page"] ; ?>" placeholder=" Total page print"><br/><br/></td>
    </tr>
	
      <tr>
        <td><strong>RATE<sup style="color:red;">*</sup></strong><br/><br/></td>
        <td><input name="rate" type="text"  value="<?php echo $cartridge_model_arr["cost_of_toner"] ; ?>" placeholder=" Cartridge Rate"><br/><br/></td>
      </tr>
	  
      <tr>
        <td><strong>PART NUMBER<sup style="color:red;">*</sup></strong><br/><br/></td>
        <td><input name="part_number" type="text"  value="<?php echo $cartridge_model_arr["part_no"] ; ?>" maxlength="12" placeholder=" Part Number"><br/><br/></td>
      </tr>
	  
      <tr>
          <td colspan="5" style="text-align: center"><input type="submit" id="butt" name="subm" id="butt3" value="Update"></td>
      </tr>
  </form>
	  
    </tbody>
  </table>
</div>