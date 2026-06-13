

<?php
extract($_POST);
if(isset($sub))
{
if($sel=mysqli_query($link,"INSERT INTO `printer_cartridge_list`(`model`, `make`, `cartridge_no`, `ttl_page_prnt`, `rate`, `part_no`) VALUES
																('$model', '$make', '$cartridge_no', '$ttl_page_prnt', '$rate', '$part_number')"))
	{
		echo "<meta http-equiv='refresh' content='0'>";
		echo '<script language="javascript">';
		echo 'alert("Printer-Cartridge Has Been Added")';
		echo '</script>';
	}
	else
	{
		echo "<b style='color:red;'>Model Already Exist or an unknown error.</b>";
	}
}
	?>
<div>
<form name="add_prntr_cartg" method="post">
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
      <td width="27%"  ><input name="model" type="text" placeholder=" Printer Model" required="required"><br/><br/></td>
    </tr>
	
    <tr>
      <td><strong>MAKE<sup style="color:red;">*</sup></strong><br/><br/></td>
      <td><input name="make" type="text" placeholder=" Make" required="required"><br/><br/></td>
    </tr>
	
    <tr>
      <td><strong>CARTRIDGE NUMBER<sup style="color:red;">*</sup></strong><br/><br/></td>
      <td><input name="cartridge_no" type="text" placeholder=" Cartridge Number" required="required"><br/><br/></td>
    </tr>
	
    <tr>
      <td><strong>TOTAL PAGE'S PRINT<br/><br/></td>
      <td><input name="ttl_page_prnt" type="text" placeholder=" Total page print"><br/><br/></td>
    </tr>
	
      <tr>
        <td><strong>RATE<sup style="color:red;">*</sup></strong><br/><br/></td>
        <td><input name="rate" type="text" placeholder=" Printer Rate" required="required"><br/><br/></td>
      </tr>
	  
      <tr>
        <td><strong>PART NUMBER<sup style="color:red;">*</sup></strong><br/><br/></td>
        <td><input name="part_number" type="text" maxlength="12" placeholder=" Part Number" required="required"><br/><br/></td>
      </tr>
	  
      <tr>
        <td colspan="5" style="text-align: center"><br/><br/><input type="submit" name="sub" id="butt" value="Submit"></td>
      </tr>
	  
    </tbody>
  </table>
  </form>
</div>