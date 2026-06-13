<style>
.tooltip{
	position: relative;
	display: inline-block;
	cursor: pointer;
}
.tooltip .tooltiptext{
	visibility: hidden;
	background-color:black;
	color: #fff ;
	text-align:center;
	border-radius:6px;
	padding: 5px 0;
/*position the tooltip*/
	position: absolute;
	z-index: 1;
	width:200px;;
}

.tooltip:hover .tooltiptext{
	visibility: visible;
}
</style>



<script>
function openWin() {
    var divText = document.getElementById("table_func").outerHTML;
    var myWindow = window.open('', '', 'width=1024,height=600');
    var doc = myWindow.document;
    doc.open();
    doc.write(divText);
    doc.close();
	myWindow.print();
}
</script>
<label style="float:right;"><a href="#" id="butt" onclick="openWin()">Print</a>&nbsp;</label>

		<form id="form1" name="form1" method="POST">
			<b>Show</b>
				<select name="LimitDay" id="LimitDay" required="required">
					<option selected Disabled></option>
					<option value="30">30</option>
					<option value="60">60</option>
					<option value="90">90</option>
					<option value="120">120</option>
					<option value="150">150</option>
					<option value="180">180</option>
				</select>
				<b>Days Call Report</b>
					&nbsp;&nbsp;
				<input type="submit" name="search" id="butt" value="Search">
		</form>
		<?php
			extract($_POST);
			if(isset($search))
			{
				$LimitDay = $LimitDay ;
			}
			else
			{
				$LimitDay = "30" ;
			}
		?>

		<br/><br/>
		
<div id="exportExcel">
<table width="100%" height="54" border="1" cellpadding="1" cellspacing="0" style="float:left;" id="table_func">
      <tbody>
        <tr style="text-align:center;" bgcolor="yellow">
          <td>S. No.</td>
          <td>Ticket No.</td>
          <td>Regis DateTime</td>
          <td>Department (Section)</td>
          <td>Staff No.</td>
          <td>Problem</td>
          <td>Engineer</td>
          <td>Solution</td>
          <td>Solution DateTime</td>
          <td>Status</td>
          <td>Remark</td>
          <td>Downtime</td>
          <td>In Time</td>
        </tr>
		<?php
			$s_no=1;
		
			extract($_GET);
			
			$query_sel = mysqli_query($link,"SELECT *, STR_TO_DATE(`r_DateTime`,'%d-%m-%Y %h:%i:%s %p') R_DT, STR_TO_DATE(`s_DateTime`,'%d-%m-%Y %h:%i:%s %p') S_DT FROM `complain_register` WHERE STR_TO_DATE(`r_DateTime`,'%d-%m-%Y %h:%i:%s %p') BETWEEN DATE_SUB(NOW(), INTERVAL $LimitDay DAY) AND NOW() ORDER BY  substring(t_no,1,5) DESC, substring(t_no,8,12) DESC");
									  
			$total_row_count = mysqli_num_rows($query_sel);
			
			echo "<center><u style='font-size:20px;'>Total " .$total_row_count ." calls in last " .$LimitDay ." days</u></center><br/>" ; 
		  
		  while($call_arr=mysqli_fetch_array($query_sel))
		  {
		?>
		
        <tr style="text-align: center; font-size:10px" id="row_hov">
          <td><?php echo $s_no ; ?></td>
          <td><?php echo $call_arr["t_no"] ; ?></td>
          <td><?php echo $call_arr["r_DateTime"] ; ?></td>
		  
          <td style="text-align:left;"><?php echo $call_arr["dept"]." (" .$call_arr["sec"] .") " ; ?></td>
		  
          <td>
			<div class="tooltip"><?php echo $call_arr["Staff_no"] ; ?>
				<span class="tooltiptext"><?php echo $call_arr["user_name"];?></span>
			</div>
		  </td>
          <td style="text-align:left;"><?php echo $call_arr["problem"] ; ?></td>
          <td style="text-align:left;"><?php echo $call_arr["support_engg"] ; ?></td>
          <td style="text-align:left;"><?php echo $call_arr["solution"] ; ?></td>
          <td><?php echo $call_arr["s_DateTime"] ; ?></td>
          <td><?php echo $call_arr["status"] ; ?></td>
          <td><?php echo $call_arr["remark"] ; ?></td>
          <td><?php	
								
				if($call_arr['status']=="Closed")
				{
					$regis_Dt = $call_arr["R_DT"] ;			
					$solu_Dt = $call_arr["S_DT"] ;			
				
					$CallRegis = new DateTime($regis_Dt);
					$CallClose = new DateTime($solu_Dt);
				
					$interval = $CallRegis->diff($CallClose);
					
					echo $CallDuration =  $interval->format('%R%a Days %H:%I Hour') ;
				}
			?></td>
		
			<?php
				if($call_arr['status']=="Closed")
				{
					if($interval->format('%R%a %H:%I')>'+0 04:00')
					{
						$msg = "No" ;
					}
					else
					{
						$msg = "Yes" ;
					}
				}
				elseif($call_arr['status']=="Pending" || $call_arr['status']=="Solved" || $call_arr['status']=="Attend")
				{
					$msg = "N/A" ;
				}
			?>
          <td style="<?php if($msg=="No"){echo "background-color:red; color:white;";} elseif($msg=="Yes"){echo "background-color:green; color:white;";} elseif($msg=="N/A"){echo "background-color:black; color:white;";} ?>">
		  <?php
			echo $msg ;
		  ?>
		  </td>
        </tr>
		
		<?php
		$s_no++ ;
		  }
		  ?>  
      </tbody>
</table>
</div>