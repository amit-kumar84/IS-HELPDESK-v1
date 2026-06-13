			<?php
					error_reporting(0) ;
					include 'connection.php' ;
			?>
<html>
	<head>
	
		<?php mysqli_set_charset($link, 'utf8') ; ?>
	
		<meta charset="UTF-8">
		
		<title>ISKOT Help Desk</title>
		
		<link type="image/x-icon" href="images/bel.ico" rel="shortcut icon" />
	</head>
	
	<body>
		<div style="display:flex; justify-content:center; align-items:center; height:90px; width:100%;color:white; background-color:blue; font-size:45px; margin-top:0px; float:left;">
			<marquee scrollamount="20" direction="left">
				<div style="display:flex;justify-content:center;align-items:center;float:left;">
				
					<?php
					//	$d = "24" ;
						$d = date('d') ;
						$m = date('m') ;
						
						$find_dob = mysqli_query($link,"SELECT * FROM `emp_details` WHERE LEFT(`d_o_b`,2)=$d AND MID(`d_o_b`,4,2)=$m ");
						
						IF(mysqli_num_rows($find_dob)!=0)
						{						
						ECHO "जन्मदिन की शुभकामनाएं :-   " ;
						
							WHILE($emp_det_array = mysqli_fetch_array($find_dob))
							{
								$emp_id = $emp_det_array['staffid'] ;
								$emp_name = $emp_det_array['username'] ;
								$emp_h_name = $emp_det_array['hindi_name'] ;
					?> &nbsp
								<img src="Pictures\<?php echo $emp_id ;?>.JPG" alt=" <?php echo $emp_name ;?>'s Image Not Found" style=" height:60px; width:60px;"/>&nbsp; <?php echo $emp_h_name ." (" .$emp_id .")";?>
					<?php
							}
						}
						else
						{
							$month = date('F') ;
							
							switch($month)
							{
								case "January" :
									$M = "जनवरी" ;
								break ;
								case "February" :
									$M = "फरवरी" ;
								break ;
								case "March" :
									$M = "मार्च" ;
								break ;
								case "April" :
									$M = "अप्रैल" ;
								break ;
								case "May" :
									$M = "मई" ;
								break ;
								case "June" :
									$M = "जून" ;
								break ;
								case "July" : 
									$M = "जुलाई" ;
								break ;
								case "August" :
									$M = "अगस्त" ;
								break ;
								case "September" :
									$M = "सितंबर" ;
								break ;
								case "October" :
									$M = "अक्टूबर" ;
								break ;
								case "November" :
									$M = "नवम्बर" ;
								break ;
								case "December" :
									$M = "दिसम्बर" ;
								break ;
							}
							ECHO "आज " .date('d') ." " .$M ." " .date('Y') ." है |" ;
						}
					?>
				</div>
			</marquee>	
		</div>
	</body>
</html>

