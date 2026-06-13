

<?php
extract($_POST);
if(isset($sub))
{
			date_default_timezone_set('Asia/Kolkata');
			$rcvd_dt = date('Y-m-d');
			
			$query_sel = mysqli_query($link,"SELECT `stock_qty` FROM `cartridge_stock_list` WHERE `cartridge_no`='$cartridge' AND `color`='$color' ") ;
			$stck_database_data = mysqli_fetch_array($query_sel) ;
			$stck_db_qty = $stck_database_data["stock_qty"] ;
			$stock_qty = $stck_recvd + $stck_db_qty;
			
if($sel=mysqli_query($link,"UPDATE `cartridge_stock_list` SET `stock_qty`='$stock_qty', `last_received_qty`='$stck_recvd', `last_received_date`='$rcvd_dt' WHERE `cartridge_no`='$cartridge' AND `color`='$color' "))
	{
		echo "<meta http-equiv='refresh' content='0'>";
		echo '<script language="javascript">';
		echo 'alert("Cartridge Stock Has Been Updated")';
		echo '</script>';
	}
	else
	{
		echo "<b style='color:red;'>unknown error.</b>";
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
      <td width="20%"><strong> CARTRIDGE<sup style="color:red;">*</sup></strong><br/><br/></td>
		<td/>
			<select name="cartridge" style="width: 173px;" required="required">
			  <option style="color:#AFAFAF;" value="" selected disabled> Select cartridge</option>
			  
			  <!-- fetch all machine's details  -->
			  <?php
			  $master_cartridge = mysqli_query($link,"SELECT DISTINCT(`cartridge_no`) FROM `cartridge_stock_list`");
			  while($master_cartridge_data_arr=mysqli_fetch_array($master_cartridge))
			  {
				  echo "<option>" .$master_cartridge_data_arr["cartridge_no"] ."</option>" ;
				  }
				  ?>
			</select><br/><br/>
		</td>
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
      <td><strong>STOCK RECEIVED<sup style="color:red;">*</sup></strong><br/><br/></td>
      <td><input name="stck_recvd" type="number" placeholder=" STOCK RECEIVED" required="required"><br/><br/></td>
    </tr>
	  
      <tr>
        <td colspan="5" style="text-align: center"><br/><br/><input type="submit" name="sub" id="butt" value="Submit"></td>
      </tr>
	  
    </tbody>
  </table>
  </form>
</div>