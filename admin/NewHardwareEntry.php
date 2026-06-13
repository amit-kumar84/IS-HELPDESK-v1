

<?php
extract($_POST);
if(isset($sub))
{
	
		// for other option CATEGORY
			if($HD_type == 'Other')
			{
				$HD_type = $other_HD_type ;
			}
			else
			{
				$HD_type = $HD_type ;
			}
		// /. end ./for other option CATEGORY
	
if($sel=mysqli_query($link,"INSERT INTO `hardware_master`(`DEPT`, `SEC`, `USG`, `CATG`, `CATG_TYPE`, `HD_ID_NO`, `MC_SL_NO`, `MAKE`, `MODEL`, `STAFF_NO`, `USERNAME`, `MFG_YR`, `OS`, `COLOR`, `PROC`, `RAM`, `RAM_TYPE`, `HDD`, `DISPLAY`, `MOUSE`, `AMC_WAR_WO_WIP`, `IP_ADDRESS`, `PO_NO`, `ASSET_NO`, `warnty_valid_frm`, `warnty_valid_upto`,`ms_office_key`,`mac_address`)
	VALUES
('M&ES', 'MIS', 'MS (STANDBY)',  '$HD_type', '$HD_catg_type_4_printer', '$HD_ID_NO', '$HD_serialno', '$HD_make', '$HD_model_no', 'ISKOT', 'ISKOT', '$HD_mfg_year', '$HD_os', '$HD_color', '$HD_procc', '$HD_ram', '$HD_ramtype', '$HD_hdd', '', '', 'W', '$HD_ip_address', '$HD_po_no', '$HD_asset_no', '$p_date', '$w_exp_date','$ms_office_key','$mac_add')"))
	{
		echo "<meta http-equiv='refresh' content='0'>";
		echo '<script language="javascript">';
		echo 'alert("Hardware Has Been Added")';
		echo '</script>';
	}
	else
	{
		echo "<b style='color:red;'>BEL ID Number Already Exist or an unknown error.</b>";
	}
}
	?>
<div>
<form id="add_hw" name="hw_form" method="post">
  <table width="80%" border="0" align="center" cellpadding="1" cellspacing="0">
	
    <tbody>
	
    <tr>
      <td colspan="5" style="text-align: center; font-size: xx-large;"><strong><u>Description</u></strong></td>
    </tr>
	
    <tr>
      <td colspan="5" style="text-align: right">&nbsp;</td>
    </tr>
	
    <tr>
      <td><strong>CATEGORY<sup style="color:red;">*</sup></strong></td>
      <td><select name="HD_type" style="width: 173px;" onchange='check_hw_type(this.value);' required>
        <option style="color:#AFAFAF;" value="" selected disabled>Select a category</option>
        <option value="PC">PC</option>
		 <option value="VDI">VDI</option>
        <option value="LAPTOP">Laptop</option>
        <option value="PRINTER">Printer</option>
        <option value="SCANNER">Scanner</option>
        <option value="PROJECTOR">Projector</option>
        <option value="DIGITAL NOTICE BOARD">Digital Notice Board</option>
        <option value="VIDEO WALL">Video Wall</option>
        <option value="Other">Other</option>
        </select>
        <input type="text" name="other_HD_type" value="" id="inputbox1" style='display:none; margin-top:10px; border-color:blue;' size="20" Placeholder=" Hardware Name"/>
        <script>
			function check_hw_type(val){
				var element=document.getElementById('inputbox1');
				if(val==''||val=='Other'){
					element.style.display='block';
					element.setAttribute("required","required");}
				else  
					element.style.display='none';
				}
		</script></td>
      <td width="13%">&nbsp;</td>        
		<td><strong>CATEGORY TYPE</td>
        <td><input name="HD_catg_type_4_printer" type="text" placeholder=" AIO / Printer / Scanner / Etc."></td>
    </tr>
	
    <tr>
      <td width="20%"  ><strong> ID NO<sup style="color:red;">*</sup></strong></td>
      <td width="27%"  ><input name="HD_ID_NO" type="text" id="HD_ID_NO" placeholder=" BEL ID Number" required="required"></td>
      <td  >&nbsp; <br/><br/></td>
      
	  <td><strong>RAM</strong></td>
      <td width="21%"  ><input name="HD_ram" type="text" id="HD_ram" placeholder=" RAM"></td>
    </tr>
	
    <tr>
      <td><strong>SERIAL NO.<sup style="color:red;">*</sup></strong></td>
      <td><input name="HD_serialno" type="text" id="HD_serialno" placeholder=" Machine Serial Number" required="required"></td>
      <td>&nbsp;<br/><br/></td>
	  
      <td><strong>RAM TYPE</strong></td>
      <td><input name="HD_ramtype" type="text" id="HD_ramtype" placeholder=" RAM Type"></td>
    </tr>
	
    <tr>
      <td><strong>MFG Year<sup style="color:red;">*</sup></strong></td>
      <td><input name="HD_mfg_year" type="text" id="HD_mfg_year" placeholder=" Year" required="required"></td>
      <td>&nbsp;<br/><br/></td>
      
	  <td><strong>HARD DISK</strong></td>
      <td><input name="HD_hdd" type="text" id="HD_hdd" placeholder=" Storage Capacity"></td>
    </tr>
	
    <tr>
      <td><strong>MODEL NO.<sup style="color:red;">*</sup></strong></td>
      <td><input name="HD_model_no" type="text" id="HD_model_no" placeholder=" Model Number" required="required"></td>
      <td>&nbsp;<br/><br/></td>
		
		<td width="19%"><strong>PROCCESSOR</strong></td>
		<td><input name="HD_procc" type="text" id="HD_procc" placeholder=" Proccessor"></td>
    </tr>
	
      <tr>
        <td><strong>MAKE MODEL<sup style="color:red;">*</sup></strong></td>
        <td><input name="HD_make" type="text" id="HD_make" placeholder=" Company Name" required="required"></td>
      <td>&nbsp;<br/><br/></td>
		
        <td><strong>OS</strong></td>
        <td><input name="HD_os" type="text" id="HD_os" placeholder=" Operating System"></td>
      </tr>
	  
      <tr>
		
        <td><strong>PO NO<sup style="color:red;">*</sup></strong></td>
        <td><input name="HD_po_no" type="text" id="HD_po_no" placeholder=" PO Number" required="required"></td>
      <td>&nbsp;<br/><br/></td>
		
        <td><strong>COLOR</strong></td>
        <td><input name="HD_color" type="text" id="HD_color" placeholder=" Color"></td>
      </tr>
	  
      <tr>
		<td><strong>ASSET NO<sup style="color:red;">*</sup></strong></td>
        <td><input name="HD_asset_no" type="text" id="HD_asset_no" placeholder=" Asset Number" required="required"></td>
      <td>&nbsp;<br/><br/></td>
      
	  <td><strong>IP ADDRESS</strong></td>
      <td><input name="HD_ip_address" type="text" id="HD_ip_address" placeholder=" IP Address"></td>
      </tr>
	  
      <tr>
		<td><strong>MS Office Key<sup style="color:red;">*</sup></strong></td>
        <td><input name="ms_office_key" type="text" id="ms_office_key" placeholder=" MS Office Key" required="required"></td>
      <td>&nbsp;<br/><br/></td>
      
	  <td><strong>MAC ADDRESS</strong></td>
      <td><input name="mac_add" type="text" id="mac_add" placeholder=" MAC Address"></td>
      </tr>
	
      <tr>
		<td><strong>PURCHASE DATE<sup style="color:red;">*</sup></strong></td>
		<?php
		date_default_timezone_set('Asia/Kolkata');
		$current_date = date('Y-m-d');
		?>
        <td><input name="p_date" type="date" value="<?php echo $current_date ; ?>" max="<?php echo $current_date ; ?>" required="required" ><span class="validity"></span></td>
      <td>&nbsp;<br/><br/></td>
        
		<td><strong>WARANTY EXPIRY DATE<sup style="color:red;">*</sup></strong></td>
        <td><input name="w_exp_date" type="date" value="<?php echo $current_date ; ?>" min="<?php echo $current_date ; ?>" required="required"></td>
      </tr>
	  
      <tr>
        <td height="27" colspan="5" style="text-align: right">&nbsp;</td>
      </tr>
	  
      <tr>
        <td colspan="5" style="text-align: center"><input type="submit" name="sub" id="butt" value="Submit"></td>
      </tr>
	  
    </tbody>
  </table>
  </form>
</div>