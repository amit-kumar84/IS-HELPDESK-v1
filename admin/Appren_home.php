<!doctype html>

	<?php
	error_reporting(0);
	include("connection.php");
	session_start();
	$sid=$_SESSION['sid'];
	$loginId=$_SESSION['login_as'];
	// for blank session
	if($sid=="" || $loginId!="Apprentice")
	{
	header("location:index.php?login_as=Apprentice");	
	}
	?>

<script type="text/javascript">
window.setTimeout("location=('logout.php');",15*60*1000); // 1 minute = 60*1000
</script>


<html>
<head>
<meta charset="utf-8">
<title>ISKOT Help Desk</title>

<link rel="stylesheet" type="text/css" href="css/style.css">
<link type="image/x-icon" href="images/bel.ico" rel="shortcut icon" />

</head>

<body>
	<div class="container">
		<div class="menu_bar">
			<ul style="list-style-type:none">
				<li>
					<a href="home.php?UserTab=DashBoard"><img src="images/Bharat_Electronics_logo.png" alt="BEL Kotdwara" width="100%"/></a>
						<br/><br/><br/>
				</li>
				<li>
					<div class="name_logout">
						<!-- Fetch data from emp detail... -->
						<?php
							$username_fetch=mysqli_query($link,"SELECT * FROM `appr_details` WHERE `staffid`='$sid'");
							$username_data_arr=mysqli_fetch_array($username_fetch);
						?>
							Welcome<br/>
								<?php echo $username_data_arr["username"] ?>
						<br/><br/>
							<a href="logout.php"><input type="button" name="logout" id="logout" value="Logout"></a>
						<br/><br/>
					</div>
			<!--	<li><a href="home.php?MenuTab=servey_ques"><button class="tablink">Survey<img src="images/new-icon2.gif" alt="NEW" width="25px"/></button></a></li>
				-->
				</ul>
			</div>	
	
	<div class="content">
		<!--  php switch to get the input from user by which they can call pages as according to their requirement-->
		<?php
		switch($_GET['MenuTab'])
		{
			case 'servey_ques': include("survey_questions2.php");
						break;
			default :	include("survey_questions2.php"); 
		}
		?>
		</div>
</div>
</body>
</html>