<?php
error_reporting(0);
include("connection.php");
extract($_POST);
if(isset($sub))
{
	$sel=mysqli_query($link,"SELECT staffid, staffpass FROM appr_details WHERE staffid='$staffid'");
	$arr=mysqli_fetch_array($sel);
	$pass=md5($staffpass);
	if($staffid==$arr['staffid'] && $pass==$arr['staffpass'])
	{
		session_start();
		$_SESSION['sid']=$staffid;
		$_SESSION['login_as']="Apprentice";
		header("location:Appren_home.php");
	}
	else
	{
	$msg="Staff Number or password is not correct !";	
	}
}
?>

<div class="login">
<form name="form" method="post" action="">
    <table align="center">
      <tr>
        <td colspan="2" style="text-align: center; text-shadow:2px 2px #DCDCDC"><h1>Apprentice Login</h1><hr/></td>
      </tr>
      <tr>
        <td height="34" colspan="2" style="text-align: center; color:red;"><?php echo $msg ; ?></td>
      </tr>
      <tr>
        <td width="261" style="text-align: right"><strong>Staff Number :</strong><br/><br/></td>
        <td width="323" style="text-align: center"><input name="staffid" type="text" required id="staffid" placeholder=" Staff Number" size="30"><br/><br/></td>
      </tr>
      <tr>
        <td style="text-align: right"><strong>Password :</strong><br/><br/></td>
        <td style="text-align: center"><input name="staffpass" type="password" required id="staffpass" placeholder=" Password" size="30"><br/><br/></td>
      </tr>
      <tr>
        <td colspan="2" style="text-align: center"><input type="submit" name="sub" id="butt" value="Submit"><br/><br/></td>
      </tr>
    </table>
  </form>
 </div>