

<?php
extract($_POST);
if(isset($sub))
{
	$in_stock = mysqli_query($link,"SELECT * FROM `cartridge_stock_list` ");
	$cartridge_exist = mysqli_fetch_array($in_stock) ;
	$exist_cartridge = $cartridge_exist['cartridge_no'] ;
	
	if($exist_cartridge != $cartridge )
	{
			date_default_timezone_set('Asia/Kolkata');
			$stock_add_dt = date('Y-m-d');
			
		if($sel=mysqli_query($link,"INSERT INTO `cartridge_stock_list`(`cartridge_no`, `color`, `cartridge_serial_no`, `no_of_page`, `cost_of_toner`, `part_no`, `stock_qty`, `last_received_qty`, `last_received_date`) VALUES ('$cartridge', '$color', '$sl_no',  '$ttl_page_prnt', '$rate', '$part_number', '$recvd_QTY', '$recvd_QTY', '$stock_add_dt')"))
		{
			echo "<meta http-equiv='refresh' content='0'>";
			echo '<script language="javascript">';
			echo 'alert("New Cartridge Has Been Added")';
			echo '</script>';
		}
		else
		{
			echo "<b style='color:red;'>Unknown error.</b>";
		}
	}
	else
	{
			echo "<meta http-equiv='refresh' content='0'>";
			echo '<script language="javascript">';
			echo 'alert("Cartridge is already added")';
			echo '</script>';
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
      <td><input name="cartridge" type="text" placeholder=" Cartridge" required="required"><br/><br/></td>
    </tr>
    <tr>
        <td height="32"><strong> COLOR<strong><sup style="color:red;">*</sup></strong><br/><br/></td>
          <td><span style="border:2px;">
            <select name="color">
				<option style="color:#AFAFAF;" value="BLACK" selected>BLACK</option>
				<option value="YELLOW">YELLOW</option>
				<option value="CYAN">CYAN</option>
				<option value="MAGENTA">MAGENTA</option>
            </select><br/><br/>
        </span></td>
    </tr>
    <tr>
      <td width="30%"  ><strong> SERIAL NO<sup style="color:red;">*</sup></strong><br/><br/></td>
      <td><input name="sl_no" type="text" placeholder=" SERIAL NO" required="required"><br/><br/></td>
    </tr>
    <tr>
      <td><strong>TOTAL PAGE'S PRINT<br/><br/></td>
      <td><input name="ttl_page_prnt" type="text" placeholder=" Total page print"><br/><br/></td>
    </tr>
	
      <tr>
        <td><strong>RATE<sup style="color:red;">*</sup></strong><br/><br/></td>
        <td><input name="rate" type="text" placeholder=" Cartridge Rate" required="required"><br/><br/></td>
      </tr>
	  
      <tr>
        <td><strong>PART NUMBER<sup style="color:red;">*</sup></strong><br/><br/></td>
        <td><input name="part_number" type="text" maxlength="12" placeholder=" Part Number" required="required"><br/><br/></td>
      </tr>
    <tr>
      <td><strong>RECEIVED QUANTITY<sup style="color:red;">*</sup></strong><br/><br/></td>
      <td><input name="recvd_QTY" type="number" placeholder=" Received Quantity" required="required"><br/><br/></td>
    </tr>
	  
      <tr>
        <td colspan="5" style="text-align: center"><br/><br/><input type="submit" name="sub" id="butt" value="Submit"></td>
      </tr>
  </form>
	  
    </tbody>
  </table>
</div>