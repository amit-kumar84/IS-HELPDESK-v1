

<div>
<table width="100%" border="1" align="center" cellpadding="1" cellspacing="0" style="float:left;">
  <tbody>
    <tr style="text-align: center; background-color:yellow;">
	  <td width="10%">Ticket Number</td>
	  <td width="13%">Call DateTime</td>
      <td width="23%">Problem</td>
      <td width="23%">Solution</td>
      <td width="15%">Support Engineer</td>
      <td width="8%">Status</td>
      <td width="8%">Send</td>
    </tr>
	
	<?php
	extract($_GET);
	$sel=mysqli_query($link,"SELECT * FROM `complain_register` WHERE `Staff_no`='$sid' AND `status`='Pending' ORDER BY substring(t_no,8,13) DESC");
									  
	$total_row_count = mysqli_num_rows($sel);
	echo "<b>Total Pending Calls</b> : " .$total_row_count ."<br><br>" ; // print total pending call record 
		  
	while($arr=mysqli_fetch_array($sel))
		{
		$ticket_no = $arr["t_no"] ;
		$date_time = $arr["r_DateTime"] ;
		$user_prob = $arr["problem"] ;
		?>
			  
<form id="form1" action="" name="form1" method="POST">
	
	<tr style="text-align: center; font-size: 12px;" id="row_hov">
      <td><?php echo $ticket_no ; ?></td>
      <td><?php echo $date_time ; ?></td>
      <td style="text-align:left;"><?php echo $user_prob ; ?></td>
      <td style="text-align: center"><textarea cols="29" rows="1"  style="border:0px;" name="solution" size="28" required type="text" required="required" value="" id="solution" Placeholder=" Solution....." ></textarea></td>
	  
      <td style="text-align: center">
	  	<select style="border:0px;" name="support_engg" id="support_engg" required="required">
            <option style="color:#AFAFAF;" value="" selected="" disabled></option>
			<option value="Self" style="color:red;">Self</option>
			<!-- Fetch data fromsupport engineer... -->
			<?php
				$suppot_engg_data_fetch = mysqli_query($link,"SELECT * FROM `s_engg_login` WHERE `status`='0' AND `presence`='P' ORDER BY engg_name ASC  ");
				while($support_engg_data_arr = mysqli_fetch_array($suppot_engg_data_fetch))
				{
				?>
				<option><?php echo $support_engg_data_arr["engg_name"] ; ?></option>
			<?php
				}
				?>
		</select>
	  </td>
      <td style="text-align: center">
		<select style="border:0px;" name="status" id="status" required="required">
			<option style="color:#AFAFAF;" value="" selected="" disabled></option>
			<option value="Solved">Solved</option>
			<option value="Attend">Attend</option>
		</select>
			</td>
      <td style="text-align: center"><button name="subm<?= $ticket_no ; ?>">Send</button></td>
    </tr>
	</form>
	<?php
		extract($_POST);
		if(isset($_POST["subm".$ticket_no])) 
		{
		date_default_timezone_set('Asia/Kolkata');
		$complt_dt = date('d-m-Y h:i:s A');
		
		if(mysqli_query($link,"UPDATE `complain_register` SET `support_engg`='$support_engg', `solution`='$solution', `status`='$status', `s_DateTime`='$complt_dt' WHERE `t_no`='$ticket_no'"))
			{
			echo "<meta http-equiv='refresh' content='0'>";
			echo '<script language="javascript">' .'alert("Your query has been submitted.")' .'</script>';
			}
		else
			{
			echo '<script language="javascript">' .'alert("error!")' .'</script>';
			}
		}
		?>
	 <?php
		  }
		  ?>  
	
  </tbody>
</table>
</div>