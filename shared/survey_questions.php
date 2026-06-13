
<!--
<form id="form1" name="form1" method="post">
		<?php
		  extract($_GET);
		  
			$data_fetch=mysqli_query($link,"SELECT * FROM `emp_details` WHERE `staffid`='$sid'");
			$user_data_arr=mysqli_fetch_array($data_fetch);
			
			$username = $user_data_arr["username"] ;
			$staff_number = $user_data_arr["staffid"] ;
			
			$test_count = mysqli_query($link,"SELECT * FROM `survey_answer` WHERE `staff_no`='$staff_number' ") ;
			$user_count = mysqli_num_rows($test_count);
			
			if($user_count != 1)
			{
		?>
<!--
		<pre style="color:blue";">
Name 		:	<?php echo $username ; ?><br/>
Staff No.	:	<?php echo $staff_number ; ?>
		</pre>
-->

		<b><i style="color:red;">Use Internet Explorer in Win 10, Win 8 & Use Google Chrome in Win 7, XP </i></b>
		
		<h2 height="73" colspan="2" style="text-align:center; font-size: 36px;text-shadow: 1px 1px 2px black, 0 0 25px blue, 0 0 5px darkblue; color:#B1FF10;">&nbsp; Canteen Service Survey &nbsp;</h2>
		 <?php
		  $s_no = 1;
		  $ans = 1 ;
		  $ques_sel = mysqli_query($link,"SELECT * FROM `survey_question`");
		  
		  while($ques_array=mysqli_fetch_array($ques_sel))
		  {
			?>
		
		<br/>
		
		<?php
			echo $s_no .". " .$ques_array["questions"] ."<br/><br/>";
		?>
		
			<input type="radio" name="answer<?php echo $ans ;?>" value="<?php echo $ques_array["opt1"] ; ?>" required="required"> <?php echo $ques_array["opt1"] ; ?><br/>
			<input type="radio" name="answer<?php echo $ans ;?>" value="<?php echo $ques_array["opt2"] ; ?>" required="required"> <?php echo $ques_array["opt2"] ; ?><br/>
			<input type="radio" name="answer<?php echo $ans ;?>" value="<?php echo $ques_array["opt3"] ; ?>" required="required"> <?php echo $ques_array["opt3"] ; ?><br/>
			<input type="radio" name="answer<?php echo $ans ;?>" value="<?php echo $ques_array["opt4"] ; ?>" required="required"> <?php echo $ques_array["opt4"] ; ?><br/><br/>
			
		<?php
		$s_no++ ;
		$ans++ ;
		  }
		  
		  echo $s_no .". " ."General remark/ suggestion: Is there any feedback you would like to give to improve the services of the canteen:" ."<br/><br/>";
		  echo "<textarea name='suggestion' rows='3' cols='90' placeholder=' Type your feedback Here... (not more than 300 words)' Required='Required'></textarea> <br/><br/>" ;
		  
		  ?>  
		  
		 <center> <input type="submit" name="sub" id="butt" value="Submit"> </center>
		 
</form>
		 <br/><br/>
		 
		 
		 <?php
				extract($_POST);
				if(isset($sub))
				{
						if(mysqli_query($link,"INSERT INTO `survey_answer`(`staff_no`, `ans1`, `ans2`, `ans3`, `ans4`, `ans5`, `ans6`)
												VALUES ('$staff_number', '$answer1', '$answer2', '$answer3', '$answer4', '$answer5', '$suggestion')"))
							{
								echo "<meta http-equiv='refresh' content='0'>";
								echo '<script language="javascript">' .'alert("Your survey has been submitted. Thank You!");' .'window.location="home.php?UserTab=DashBoard" ;' .'</script>';
							}
				//	}
					else
					{
							echo "<meta http-equiv='refresh' content='0'>";
							echo '<script language="javascript">' .'alert("Already Submitted!")' .'</script>';
						
					}
				}
			}
			else
			{
				echo "Your survey has been submitted. Thank You" ;
			}
				?>
				
				
-->