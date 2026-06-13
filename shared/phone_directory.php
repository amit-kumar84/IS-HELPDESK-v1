 <!DOCTYPE html>
 
 <?php
	error_reporting(0);
	include("connection.php") ;
	extract($_POST);
	
 ?>
 
 <html>
	<head>
		<title>ISKOT Helpdesk</title>
		
		<meta charset="utf-8">
		
		<link rel="stylesheet" type="text/css" href="css/style.css" />
		<link type="image/x-icon" href="images/bel.ico" rel="shortcut icon" />
		
	</head>
	<body>
		
<div class="container_index">
			<!--*******************************************************************-->
										<!--Header-->
			

	<div class="header">
		<div class="logo">
			<a href="#"><img src="images/Bharat_Electronics_logo.png" alt="BEL Kotdwara" width="200px" height="80px" /></a>
		</div>
		
		<div class="header_title">
			<h1>ISKOT Help Desk</h1>
		</div>
		
		<div class="user_login">
			<!-- switch case for login_as USER/ADMIN/ENGINEER 'Login as' is a item by which the page is call -->
			<a href="index.php?login_as=User">User</a>
			|
			<a href="index.php?login_as=Engineer">Engineer</a>
			|
			<a href="index.php?login_as=SubAdmin">Admin</a>
			<br/><br/>
			<a href="http://bel.kot/"><img src="images/Home.png" alt="Home" width="150px" /></a>
		</div>
		<!-- marquee right to left-->
		<div class="marquee">
			<b class="marquee_tag">&nbsp; Update/Announcement :</b>
			
			<MARQUEE class="marquee_cntnt" onmouseover="this.stop()" onmouseout="this.start()" scrollamount="4" direction="left">
				<img src="images/new-icon.gif" alt="NEW" width="25px"/> <i style="color:red;">*</i><a style="text-decoration:none; color:green;" href="http://bel.kot/toppage1.htm"> Information Security Management System (ISMS) - ISO-27001:2013</a> &nbsp;&nbsp;
			<!--	<img src="images/new-icon.gif" alt="NEW" width="25px"/> <i style="color:red;">*</i>In Case of forgotten or lost password contact M&ES or 43660/660. -->
			</MARQUEE>
		</div>
		<!-- /. end of marquee right to left-->
	</div>
	
										<!--End of Header-->
			<!--*******************************************************************-->
			
			<div class="content" style="float:left; width:100%;">
				
				<div id="tagline_backlink">
					<hr/>
						<a href="index.php">Home</a> / <a href="#">Phonebook Directory</a> /
					<hr/>
				</div>
			
				<h2 style="text-align:center;">&nbsp; Phonebook Directory &nbsp;</h2>
				
				<table width="100%" height="54" border="1" cellpadding="1" cellspacing="0" id="table_func">
					<tbody>
						<tr style="text-align:center; background-color:yellow;">
						  <td>S. No.</td>
						  <td>Cost Center</td>
						  <td>Section</td>
						  <td>Staff Number</td>
						  <td>Name</td>
						  <td>Hindi Name</td>
						  <td>Designation</td>
						  <td>Phone Number</td>
						</tr>
						
						<?php
							$master_data_fetch=mysqli_query($link,"SELECT * FROM `emp_details` order by cost_center");
							$s_no = 1 ;
							while($master_data_arr=mysqli_fetch_array($master_data_fetch))
							{
						?>
						<tr>
							<td style="text-align:center;"><?php echo $s_no ; ?></td>
							<td><?php echo $master_data_arr['cost_center'] ; ?></td>
							<td><?php echo $master_data_arr['deptt'] ." (" .$master_data_arr['sec'] .")" ; ?></td>
							<td><?php echo $master_data_arr['staffid'] ; ?></td>
							<td><?php echo $master_data_arr['username'] ; ?></td>
							<td><?php echo $master_data_arr['hindi_name'] ; ?></td>
							<td><?php echo $master_data_arr['desg'] ; ?></td>
							<td style="text-align:center;"><?php echo $master_data_arr['ip_phone'] ."<br/>" .$master_data_arr['phone_no'] ; ?></td>
						</tr>
						<?php
						$s_no++ ;
							}
						?>
						
					</tbody>
				</table>
					
			</div>
		</div>
		
			<!--*******************************************************************-->
										<!--Footer-->
			<?php include 'footer.php';?>
										<!--End of Footer-->
			<!--*******************************************************************-->
		
		<br/><br/><br/><br/>
	</body>
 </html>