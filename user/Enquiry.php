

<div class="enquiry">
<!--	<script>
	function manual_insert(val){
				var element=document.getElementById('enq_number');
				if(val=='by_manual'){
					element.style.display='block';
					element.setAttribute("required","required");}
				else
					element.style.display='none';
				}
	</script>
	<form id="form1" name="search" action="#" method="POST"><br>
		<input type="radio" name="search" value="" id="all" checked>All  &nbsp;
		<input type="radio" name="search" value="by_manual" onchange='manual_insert(this.value);' id="t_no">
		Token Number &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

		<input type="submit" name="Search" id="enquiry" value="Search"><br><br>

		<input id="enq_number" name="enq_number" type="search"  placeholder=" Enter Token Number" size="25" style="display:none;"><br>
	</form>
-->


<h2 height="73" colspan="2" style="text-align:center; font-size: 36px;text-shadow: 1px 1px 2px black, 0 0 25px blue, 0 0 5px darkblue; color:#B1FF10;">&nbsp; Enquiry &nbsp;</h2>

<?php
		date_default_timezone_set('Asia/Kolkata');	// to set default date and time	
		$yer = date('Y');							// to get current year
		$yr = substr($yer,2); 						// Substring to get only last two digit of the year	
		$mnth = date('m');							// to get current month	
		$dte = date('d');							// to get current date
	
		$date_like = $yr .$mnth .$dte ;
?>
<b><sup style="color:red">*</sup>Today's Call</b><br/><br/>
	<table width="100%" border="1px" cellpadding="1" cellspacing="0" align="center" style="float:left;">
		<tbody>
	      <tr style="text-align: center; background-color:yellow;">
	        <td width="10%">Ticket Number</td>
	        <td width="13%">Call DateTime</td>
	        <td width="21%">Problem</td>
			<td width="11%">Engineer</td>
	        <td width="21%">Solution</td>
			<td width="13%">Solution DateTime</td>
	        <td width="5%">Status</td>
	        <td width="6%">ReOpen</td>
          </tr>
					<?php
							extract($_GET);
							//to get data from database
							$sel=mysqli_query($link,"SELECT * FROM `complain_register` WHERE `Staff_no`='$sid' AND `t_no` LIKE '%".$date_like."%' ORDER BY substring(t_no,1,5) DESC, substring(t_no,8,12) DESC");

							while($call_arr=mysqli_fetch_array($sel))
								{
								  $ticket_no = $call_arr["t_no"] ;
								  $date_time = $call_arr["r_DateTime"] ;
								  $user_prob = $call_arr["problem"] ;
								  $supprt_engg = $call_arr["support_engg"] ;
								  $solution = $call_arr["solution"] ;
								  $resolved_dt = $call_arr["s_DateTime"] ;
								  $status = $call_arr["status"] ;
									?>

								<form id="form1" action="" name="form1" method="POST">
									<tr style="text-align: center; font-size: 12px;" id="row_hov">
										<td><?php echo $ticket_no ; ?></td>
										<td><?php echo $date_time ; ?></td>
										<td style="text-align:left;"><?php echo $user_prob ; ?></td>
										<td><?php echo $supprt_engg ; ?></td>
										<td style="text-align:left;"><?php echo $solution ; ?></td>
										<td style="text-transform: uppercase;"><?php echo $resolved_dt ; ?></td>
										<td><?php echo $status ; ?></td>
										<td>
											<button name="subm<?= $ticket_no ; ?>">Open</button>
										</td>
									</tr>
								</form>
							<?php
							extract($_POST);
							if(isset($_POST["subm".$ticket_no])) 
							{
								if($status != "Pending")
									{
									if(mysqli_query($link,"UPDATE `complain_register` SET `status`='Pending' WHERE `t_no`='$ticket_no'"))
										{
										echo "<meta http-equiv='refresh' content='0'>";
										echo '<script language="javascript">' .'alert("Your Call Is Reopen!")' .'</script>';
										}
									else
										{
										echo '<script language="javascript">' .'alert("ERROR!")' .'</script>';
										}
									}
								else
									{
									echo '<script language="javascript">' .'alert("Call Is already in pending.")' .'</script>';
									}
							}
							?>			

					<?php
					}
					?>
		</tbody>
	</table>
			<hr/><hr/>
	
<!--**************************************************************************************************************************************************-->	
	<br/>
<b><sup style="color:red">*</sup>All Calls</b><br/><br/>
	<table width="100%" border="1px" cellpadding="1" cellspacing="0" align="center" style="float:left;">
		<tbody>
	      <tr style="text-align: center; background-color:yellow;">
	        <td>Ticket Number</td>
	        <td>Call DateTime</td>
	        <td>Problem</td>
	        <td>Asset ID</td>
			<td>Engineer</td>
	        <td>Solution</td>
			<td>Solution DateTime</td>
	        <td>Status</td>
          </tr>
					<?php
							extract($_GET);
							//to get data from database
							$sel=mysqli_query($link,"SELECT * FROM `complain_register` WHERE `Staff_no`='$sid' ORDER BY substring(t_no,1,5) DESC, substring(t_no,8,12) DESC");

							while($call_arr=mysqli_fetch_array($sel))
								{
								  $ticket_no = $call_arr["t_no"] ;
								  $date_time = $call_arr["r_DateTime"] ;
								  $user_prob = $call_arr["problem"] ;
								  $pc_no = $call_arr["pc_no"] ;
								  $supprt_engg = $call_arr["support_engg"] ;
								  $solution = $call_arr["solution"] ;
								  $resolved_dt = $call_arr["s_DateTime"] ;
								  $status = $call_arr["status"] ;
									?>

								<form id="form1" action="" name="form1" method="POST">
									<tr style="text-align: center; font-size: 12px;" id="row_hov">
										<td><?php echo $ticket_no ; ?></td>
										<td><?php echo $date_time ; ?></td>
										<td style="text-align:left;"><?php echo $user_prob ; ?></td>
										<td><?php echo $pc_no ; ?></td>
										<td><?php echo $supprt_engg ; ?></td>
										<td style="text-align:left;"><?php echo $solution ; ?></td>
										<td style="text-transform: uppercase;"><?php echo $resolved_dt ; ?></td>
										<td><?php echo $status ; ?></td>
									</tr>
								</form>

					<?php
					}
					?>
		</tbody>
	</table>
<!--**************************************************************************************************************************************************-->	
</div>