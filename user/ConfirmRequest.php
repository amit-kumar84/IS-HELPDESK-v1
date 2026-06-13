<?php
//	$data_fetch=mysqli_query($link,"SELECT * FROM `complain_register` WHERE `Staff_no`='$sid' ORDER BY Staff_no DESC LIMIT 1");
//	$user_token_arr=mysqli_fetch_array($data_fetch);
//	$ticket_no = $user_token_arr["t_no"] ;

	$ticket_no = $_SESSION['token'] ;
	?>

<br/>
<br/>
Your Query Has Been Submitted.<br/>
Your Reference ID Number Is <span style="font-size:20px; color:green; font-weight:bold;" onMouseOver="this.style.color='red'" onMouseOut="this.style.color='green'"><?php echo $ticket_no ; ?></span>. Please Save This ID For Further Query.</br>
Click Here To <a href="logout.php" style="color:red; text-decoration:none; font-size:20px; font-weight:bold;" onMouseOver="this.style.color='green'" onMouseOut="this.style.color='red'">Logout</a>