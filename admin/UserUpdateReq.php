

<h2 height="73" colspan="2" style="text-align:center; font-size: 36px;text-shadow: 1px 1px 2px black, 0 0 25px blue, 0 0 5px darkblue; color:#B1FF10;">&nbsp;Update Data Request For User Profile &nbsp;</h2>


<div>
  <div>
	<table width="100%" height="54" border="1" cellpadding="1" cellspacing="0" style="float:left;" id="table_func">
      <tbody>
        <tr style="text-align: center" bgcolor="yellow">
          <td>Ticket Number</td>
          <td>Staff No.</td>
          <td>Name</td>
          <td>Request For</td>
          <td>Department</td>
          <td>Section</td>
          <td>Phone No.</td>
          <td>Message</td>
			<td colspan="2">PC</td>
          <td>Status</td>
			<td colspan="3">Action</td>
        </tr>
			<?php
			extract($_GET);
			$query_sel = mysqli_query($link,"SELECT * FROM `user_update_data` WHERE `action_admin`='Pending' ORDER BY `req_no` ASC");
			
			$total_row_count = mysqli_num_rows($query_sel);
			echo "<b>Total Pending Request : </b>" .$total_row_count ."<br><br>" ; // print total pending call record 
			
			while($req_arr = mysqli_fetch_array($query_sel))
				{
				$ticket_no = $req_arr["req_no"] ;
				$staff_num = $req_arr["staff_no"] ;
				$user_name = $req_arr["username"] ;
				$department = $req_arr["dept"] ;
				$section = $req_arr["sec"] ;
				$Ph_No = $req_arr["ph_no"] ;
				$Msg = $req_arr["message"] ;
				$PC = $req_arr["pc"] ;
				$PC_Action = $req_arr["action_for_pc"] ;
				$req_for = $req_arr["request_for"] ;
				$Status = $req_arr["action_admin"] ;
				?>
								  
	<form id="form1" action="" name="form1" method="POST">
								  
        <tr style="text-align:left; font-size:12px" id="row_hov">
<!-- data fetch from database -->
          <td value="" name="ticket_no"><?php echo $ticket_no; ?></td>
          <td><?php echo $staff_num ; ?></td>
          <td><?php echo $user_name ; ?></td>
          <td><?php echo $req_for ; ?></td>
          <td <?php if($department=="-"){ echo "style='text-align:center;'" ;}?> ><?php echo $department ; ?></td>
          <td <?php if($section=="-"){ echo "style='text-align:center;'" ;}?> ><?php echo $section ; ?></td>
          <td style="text-align:center;"><?php echo $Ph_No ; ?></td>
          <td <?php if($Msg=="-"){ echo "style='text-align:center;'" ;}?> ><?php echo $Msg ; ?></td>
          <td style="text-align:center;"><?php echo $PC ; ?></td>
          <td style="text-align: center; <?php if($PC_Action=="Add"){ echo "background-color:green; color:white;" ;} elseif($PC_Action=="Remove"){ echo "background-color:red; color:white;" ;} ?> " ><?php echo $PC_Action ; ?></td>
          <td><?php echo $Status ; ?></td>
		  
          <td style="text-align:center;">
		<!--	<button id="butt" name="subm<?= $ticket_no ; ?>">Update</button> ->
			<!--<img id="delete_but" src="images/accept.png" width="15px;" />-->
		  </td>
		  <td style="text-align:center;">
		<!--	<img id="delete_but" src="images/reject.png" width="15px;" /> -->
		  </td>
		  <td style="text-align:center;">
		<!--	<img id="delete_but" name="subm_del<?= $ticket_no ; ?>" src="images/delete.png" width="15px;" /> -->
		  </td>
		  		  
        </tr>
	</form>
	<?php
	extract($_POST);
	if(isset($_POST["subm".$ticket_no]))
	{
		date_default_timezone_set('Asia/Kolkata');
		$complt_dt = date('d-m-Y h:i:s A');
		
	//	if(mysqli_query($link,"UPDATE `user_update_data` SET `req_no`='', `staff_no`='' , `username`='', `dept`='', `sec`='', `ph_no`='', `pc`='', `message`='', `request_for`='', `action_for_pc`='', `action_admin`='' WHERE `req_no`='$ticket_no' "))
	//		{
	//		echo "<meta http-equiv='refresh' content='0'>";
	//		}
	}
	?>		
	<?php
	}
	?>  
	   </tbody>
	</table>
  </div>
</div>