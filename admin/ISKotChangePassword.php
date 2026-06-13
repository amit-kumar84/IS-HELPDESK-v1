

<?php
extract($_POST);
if(isset($sub))
{
  $sel=mysqli_query($link,"SELECT `adminpass` FROM `iskotadmin_login` WHERE `adminid`='$sid'");
  $arr=mysqli_fetch_array($sel);//fetch pass of user
  $op=md5($op);
  if($op==$arr['adminpass'])
  {
	  if($np==$cp)
	  {
		  $np=md5($np);
		  mysqli_query($link,"update iskotadmin_login set adminpass='$np' where adminid='$sid'");
			$msg="*Password successfully Changed.";
	  }
	  else
	  {
		$msg="*Please enter confirm password as same as new password.";  
	  }
  }
  else
  {
	$msg="*Old password is not correct.";  
  }
}
?>
<br/><br/><br/><br/><br/>

<h2 height="73" colspan="2" style="text-align:center; font-size: 36px;text-shadow: 1px 1px 2px black, 0 0 25px blue, 0 0 5px darkblue; color:#B1FF10;">&nbsp; Change Password &nbsp;</h2>

<div  style="background-color:#F8F9F9; font-weight:bold; border-radius:15px; border:1px solid black;">
<form name="form" method="post" action="">
<table align="center">
  <tr>
    <td colspan="2" style="color:red;"><b><?php echo $msg;?></b></td>
  </tr>
  <tr>
    <td width="287" height="35"><strong>Current Password</strong></td>
    <td width="290"><input name="op" type="password" required id="adminpasss" placeholder=" Current Password" size="30"></td>
  </tr>
  <tr>
    <td height="35"><strong>New Password</strong></td>
    <td><input name="np" min='8' type="password" required id="np" pattern="(?=.*\d)(?=.*\W+)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number, one uppercase, One lowercase or special Character and at least 8 or more characters" placeholder=" New Password" size="30"></td>
  </tr>
  <tr>
    <td height="31"><strong>Confirm Password</strong></td>
    <td><input name="cp" min='8' type="password" required id="cp" pattern="(?=.*\d)(?=.*\W+)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number, one uppercase, One lowercase or special Character and at least 8 or more characters" placeholder=" Confirm Password" size="30"></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" style="text-align: center"><input type="submit" name="sub" id="chng_pass" value="Submit"></td>
  </tr>
</table>
</form>
</div>