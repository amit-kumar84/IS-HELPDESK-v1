<div>
	<h2 height="73" colspan="2" style="text-align:center; font-size: 36px;text-shadow: 1px 1px 2px black, 0 0 25px blue, 0 0 5px darkblue; color:#B1FF10;">&nbsp; Department / Section Wise Call Report &nbsp;</h2>

<!--***********************************************************************************************************************************************************************
															Search Total call according to department wise
*************************************************************************************************************************************************************************-->
  
	<form id="form" action="" name="form1" method="POST">
	  <p>
		  <b style="color:green;">Total Call Department wise : </b>
			<select name="dept_wise_search" id="call_timing_dept" required>
				<option style="color:#AFAFAF;" value="" selected disabled>Select department</option>
					<option value="ALL">All</option>
					<?php						
					$department_name_fetch = mysqli_query($link,"SELECT DISTINCT(dept) FROM `complain_register` ORDER BY dept ASC");
						while($department_name = mysqli_fetch_array($department_name_fetch))
						{
					?>
					<option><?php echo $department_name["dept"] ; ?></option>
					<?php
						}
					?>
			</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="sub" id="butt" value="Search">
	  </p>
  </form>
</div>

<div>
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
<label style="float:right;"><a href="#" id="butt" onclick="openWin()">Print</a>&nbsp;&nbsp;&nbsp;</label>
<span style="float:right;"><a href="#" id="butt">Export To Excel</a>&nbsp;</span>
<br/><br/>
</div>
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
<div id="exportExcel">
	<table width="100%" height="54" border="1" cellpadding="1" cellspacing="0" style="float:left;" id="table_func">
		<tbody>
			<tr style="text-align:center;" bgcolor="yellow">
			  <td>S. No.</td>
			  <td>Ticket No.</td>
			  <td>Regis DateTime</td>
			  <td>Dept.</td>
			  <td>Sec</td>
			  <td>Staff No.</td>
			  <td>Name</td>
			  <td>Phone</td>
			  <td>Problem</td>
			  <td>Engineer</td>
			  <td>Solution</td>
			  <td>Solution DateTime</td>
			  <td>Remark</td>
			  <td>Status</td>
			  <td>In Time</td>
			</tr>
		<?php
		$s_no=1;
			extract($_POST);
			if(isset($sub))
			{
				if($dept_wise_search=='ALL')
				{
					$query_sel=mysqli_query($link,"SELECT * FROM `complain_register` ORDER BY substring(t_no,1,5) DESC, substring(t_no,8,12) ");
					$total_row_count = mysqli_num_rows($query_sel);
						echo "<b style='color:#3795F8;'>Total Result :</b> " .$total_row_count ."<br><br>" ; // print per page call record 
				}
				else if($dept_wise_search=='')
				{
					echo "<b style='color:red;'><sup>*</sup>Please select department first.</b> <br/><br/>" ;
				}
				else if($dept_wise_search!='ALL' || $dept_wise_search!='')
				{
					$query_sel=mysqli_query($link,"SELECT * FROM `complain_register` WHERE `dept`='$dept_wise_search' ORDER BY substring(t_no,1,5) DESC, substring(t_no,8,12) ");
					$total_row_count = mysqli_num_rows($query_sel);
						echo "<b style='color:#3795F8;'>Total Result :</b> " .$total_row_count ."<br><br>" ; // print per page call record 
				}
				else
				{
					echo "0 Record Found!" ;
				}
					  while($call_arr=mysqli_fetch_array($query_sel))
					  {
		?>
			<tr style="text-align: center; font-size:10px" id="row_hov">
				<td><?php echo $s_no ; ?></td>
				<td><?php echo $call_arr["t_no"] ; ?></td>
				<td><?php echo $call_arr["r_DateTime"] ; ?></td>
				<td><?php echo $call_arr["dept"] ; ?></td>
				<td><?php echo $call_arr["sec"] ; ?></td>
				<td><?php echo $call_arr["Staff_no"] ; ?></td>
				<td style="text-align:left;"><?php echo $call_arr["user_name"] ; ?></td>
				<td><?php echo $call_arr["phone_no"] ; ?></td>
				<td style="text-align:left;"><?php echo $call_arr["problem"] ; ?></td>
				<td style="text-align:left;"><?php echo $call_arr["support_engg"] ; ?></td>
				<td style="text-align:left;"><?php echo $call_arr["solution"] ; ?></td>
				<td><?php echo $call_arr["s_DateTime"] ; ?></td>
				<td><?php echo $call_arr["remark"] ; ?></td>
				<td><?php echo $call_arr["status"] ; ?></td>
				<td><?php	?></td>
			</tr>
		<?php
		$s_no++ ;
			}
			}
		?>  
      </tbody>
</table>
<hr/><hr/>
	<!-- ./End of Search calls Details-->
</div>