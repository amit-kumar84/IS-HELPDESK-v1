<style>
.tooltip{
	position: relative;
	display: inline-block;
	cursor: pointer;
}
.tooltip .tooltiptext{
	visibility: hidden;
	background-color:White;
	color: Black ;
	border:1px solid black;
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

<h2 height="73" colspan="2" style="text-align:center; font-size: 36px;text-shadow: 1px 1px 2px black, 0 0 25px blue, 0 0 5px darkblue; color:#B1FF10;">&nbsp; Datewise Call Record &nbsp;</h2>
<hr/>
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

<span style="float:right;"><a href="#" id="butt">Export To Excel</a>&nbsp;</span>

	<script type="text/javascript" src="js/jquery-1.9.0.js"> </script>
	<script type="text/javascript">
	$(function(){
		$('span').click(function(){
			var url='data:application/vnd.ms-excel,' + encodeURIComponent($('#exportExcel').html()) 
			location.href=url
			return false
		})
	})
	</script>
<div>
	<form method="post">
	  <b>Date</b>&nbsp;:&nbsp;&nbsp;
	  
	  From&nbsp;<input type="date" date-format="dd/mm/yyyy" value="<?php echo date("Y-m-d");?>" name="from" required />&nbsp;
	  
	  To&nbsp;<input type="date" date-format="dd/mm/yyyy" value="<?php echo date("Y-m-d");?>"  max="<?php echo date("Y-m-d");?>" name="to" required />
	  
		<input name="search" type="submit" id="butt" value="Search"><br/></br>
	</form>
</div>

<div id="exportExcel">
<table width="100%" height="54" border="1" cellpadding="1" cellspacing="0" style="float:left;" id="table_func">
      <tbody>
        <tr style="text-align:center;" bgcolor="yellow">
          <td>S. No.</td>
          <td>Ticket No.</td>
          <td>Regis DateTime</td>
          <td>Department (Section)</td>
          <td>Staff No.</td>
          <td>Asset ID</td>
          <td>Printer</td>
         <td>Problem on</td>
		  <td>Problem type</td>
		   <td>Problem</td>
          <td>Engineer</td>
          <td>Solution</td>
          <td>Solution DateTime</td>
          <td>Remark</td>
          <td>Status</td>
        </tr>
	<?php
		$s_no=1;
		extract($_POST);
				
		if(isset($search))
		{
			/*date from*/
				
				$dt_from_y = date("Y", strtotime($from));
				$frm_yr = substr($dt_from_y,2);
				
				$dt_from_m = date("m", strtotime($from));
				$frm_m = $dt_from_m ;
				
				$dt_from_d = date("d", strtotime($from));
				$frm_d = $dt_from_d ;
				
				$dt_from = $frm_yr .$frm_m .$frm_d ;
			
			/*date to*/
				
				$dt_to_y = date("Y", strtotime($to));
				$to_yr = substr($dt_to_y,2);
				
				$dt_to_m = date("m", strtotime($to));
				$to_m = $dt_to_m ;
				
				$dt_to_d = date("d", strtotime($to));
				$to_d = $dt_to_d ;
				
				$dt_to = $to_yr .$to_m .$to_d ;
			
			
		  $query_sel = mysqli_query($link,"SELECT *, STR_TO_DATE(`r_DateTime`,'%d-%m-%Y %h:%i:%s %p') R_DT, STR_TO_DATE(`s_DateTime`,'%d-%m-%Y %h:%i:%s %p') S_DT FROM `complain_register` WHERE substring(t_no,1,6) BETWEEN '$dt_from' AND '$dt_to' ORDER BY substring(t_no,1,5) ASC, substring(t_no,8,12) ASC");
									  
		  $total_row_count = mysqli_num_rows($query_sel);
		  
			echo "<b>Total Record = </b>" .$total_row_count ."<br/>";
			
		    echo "<b>Duration = </b>  <span> &nbsp;" .$frm_yr ."-" .$frm_m ."-" .$frm_d ." To " .$to_yr ."-" .$to_m ."-" .$to_d ."</span><br/>" ; // print total call record 
			
			/*********************************************************************************************/
			
			
			$Check_downtime = mysqli_query($link,"SELECT * FROM `complain_register` WHERE (TIMEDIFF(STR_TO_DATE(`s_DateTime`,'%d-%m-%Y %h:%i:%s %p'), STR_TO_DATE(`r_DateTime`,'%d-%m-%Y %h:%i:%s %p'))>'04:00:00') AND substring(t_no,1,6) BETWEEN '$dt_from' AND '$dt_to' ORDER BY substring(t_no,1,5) ASC, substring(t_no,8,12) ASC") ;
			
			$total_cnt = mysqli_num_rows($Check_downtime);
			
			$downtime = 100-$total_cnt/$total_row_count*100;
			
			echo "<b>Call Uptime = <b/>" .number_format($downtime,2) ."<br/><br/>";
			
			/*********************************************************************************************/
		} 
		  while($call_arr=mysqli_fetch_array($query_sel))
		  {
	?>
		
        <tr style="text-align: left; font-size:10px" id="row_hov">
          <td style="text-align: center;"><?php echo $s_no ; ?></td>
          <td><?php echo $call_arr["t_no"] ; ?></td>
          <td><?php echo $call_arr["r_DateTime"] ; ?></td>
		  
          <td><?php echo $call_arr["dept"]." (" .$call_arr["sec"] .") " ; ?></td>
		  
		  <td>
			<div class="tooltip"><?php echo $call_arr["Staff_no"] ; ?>
				<span class="tooltiptext"><?php echo $call_arr["user_name"];?></span>
			</div>
		  </td>
          <td><?php echo $call_arr["pc_no"] ; ?></td>
          <td><?php echo $call_arr["printer"] ; ?></td>
         <td style="text-align:left;"><?php echo $call_arr["problem_on"] ; ?></td> 
		  <td style="text-align:left;"><?php echo $call_arr["problem_type"] ; ?></td> 
		  <td style="text-align:left;"><?php echo $call_arr["problem"] ; ?></td>
          <td><?php echo $call_arr["support_engg"] ; ?></td>
          <td style="text-align:left;"><?php echo $call_arr["solution"] ; ?></td>
          <td><?php echo $call_arr["s_DateTime"] ; ?></td>
          <td><?php echo $call_arr["remark"] ; ?></td>
          <td><?php echo $call_arr["status"] ; ?></td>
        </tr>
		
		<?php
		$s_no++ ;
		  }
		  ?>  
      </tbody>
</table>
</div>