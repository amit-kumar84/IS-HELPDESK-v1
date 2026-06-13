<div>
	<h2 height="73" colspan="2" style="text-align:center; font-size: 36px;text-shadow: 1px 1px 2px black, 0 0 25px blue, 0 0 5px darkblue; color:#B1FF10;">&nbsp; Overall Call Report &nbsp;</h2>
	
<!--***********************************************************************************************************************************************************************
																Total Number Of Calls
*************************************************************************************************************************************************************************-->	
	<p>
	  <b style="color:green;">Total number of calls	:	</b><?php 
	   /* query for count total calls */
		$query_sel_4_count=mysqli_query($link,"SELECT * FROM `complain_register`");
		$total_row_count_4_count = mysqli_num_rows($query_sel_4_count);
		echo $total_row_count_4_count ; // print total call record 
		/* end query*/
	  ?>
		<pre>
			  <b style="color:green;">Closed Calls	:	</b><?php
				/* query for count closed calls */
				$query_sel_closed_Calls = mysqli_query($link,"SELECT * FROM `complain_register` WHERE `status`='Closed'");
				$total_row_count_closed_Calls = mysqli_num_rows($query_sel_closed_Calls);
				echo $total_row_count_closed_Calls ; // print solved call record 
				/* end query*/
				?>
				
			  <b style="color:red;">Pending Calls	:	</b><?php
				/* query for count pending calls */
				$query_sel_pending_Calls = mysqli_query($link,"SELECT * FROM `complain_register` WHERE `status`='Pending'");
				$total_row_count_pending_Calls = mysqli_num_rows($query_sel_pending_Calls);
				echo $total_row_count_pending_Calls ; // print pending call record 
				/* end query*/
				?>
				
			  <b style="color:orange;">Attend Calls	:	</b><?php
				/* query for count attend calls */
				$query_sel_attend_Calls = mysqli_query($link,"SELECT * FROM `complain_register` WHERE `status`='Attend'");
				$total_row_count_attend_Calls = mysqli_num_rows($query_sel_attend_Calls);
				echo $total_row_count_attend_Calls ; // print attend call record 
				/* end query*/
				?>
				
			  <b style="color:blue;">Solved Calls	:	</b><?php
				/* query for count solved calls */
				$query_sel_solved_Calls = mysqli_query($link,"SELECT * FROM `complain_register` WHERE `status`='Solved'");
				$total_row_count_solved_Calls = mysqli_num_rows($query_sel_solved_Calls);
				echo $total_row_count_solved_Calls ; // print solved call record 
				/* end query*/
				?>
		</pre>
	</p>
	<hr/>
	
	<!-- ./End Of total calls Details-->
	
<!--***********************************************************************************************************************************************************************
															Today's	Total Number Of Calls
*************************************************************************************************************************************************************************-->
	<?php
		date_default_timezone_set('Asia/Kolkata');	// to set default date and time	
		$yer = date('Y');							// to get current year
		$yr = substr($yer,2); 						// Substring to get only last two digit of the year	
		$mnth = date('m');							// to get current month	
		$dte = date('d');							// to get current date
	
		$date_like = $yr .$mnth .$dte ;
	?>
	
	<p>
	  <b style="color:green;">Today's total number of calls	:	</b><?php 
	   /* query for count total calls */
		$query_sel_4_count=mysqli_query($link,"SELECT * FROM `complain_register` WHERE `t_no` LIKE '%".$date_like."%'");
		$total_row_count_4_count = mysqli_num_rows($query_sel_4_count);
		echo $total_row_count_4_count ; // print total call record 
		/* end query*/
	  ?>
		<pre>
				  <b style="color:green;">Closed Calls	:	</b><?php
					/* query for count closed calls */
					$query_sel_closed_Calls = mysqli_query($link,"SELECT * FROM `complain_register` WHERE `status`='Closed' AND `t_no` LIKE '%".$date_like."%'");
					$total_row_count_closed_Calls = mysqli_num_rows($query_sel_closed_Calls);
					echo $total_row_count_closed_Calls ; // print solved call record 
					/* end query*/
					?>
					
				  <b style="color:red;">Pending Calls	:	</b><?php
					/* query for count pending calls */
					$query_sel_pending_Calls = mysqli_query($link,"SELECT * FROM `complain_register` WHERE `status`='Pending' AND `t_no` LIKE '%".$date_like."%'");
					$total_row_count_pending_Calls = mysqli_num_rows($query_sel_pending_Calls);
					echo $total_row_count_pending_Calls ; // print pending call record 
					/* end query*/
					?>
					
				  <b style="color:orange;">Attend Calls	:	</b><?php
					/* query for count attend calls */
					$query_sel_attend_Calls = mysqli_query($link,"SELECT * FROM `complain_register` WHERE `status`='Attend' AND `t_no` LIKE '%".$date_like."%'");
					$total_row_count_attend_Calls = mysqli_num_rows($query_sel_attend_Calls);
					echo $total_row_count_attend_Calls ; // print attend call record 
					/* end query*/
					?>
					
				  <b style="color:blue;">Solved Calls	:	</b><?php
					/* query for count solved calls */
					$query_sel_solved_Calls = mysqli_query($link,"SELECT * FROM `complain_register` WHERE `status`='Solved' AND `t_no` LIKE '%".$date_like."%'");
					$total_row_count_solved_Calls = mysqli_num_rows($query_sel_solved_Calls);
					echo $total_row_count_solved_Calls ; // print solved call record 
					/* end query*/
					?>
		</pre>
	</p>
  <hr/>
	
	<!-- ./End Of Today's calls Details-->
</div>