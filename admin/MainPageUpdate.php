

<h2 height="73" colspan="2" style="text-align:center; font-size: 36px;text-shadow: 1px 1px 2px black, 0 0 25px blue, 0 0 5px darkblue; color:#B1FF10;">&nbsp;Main Page Update Information &nbsp;</h2>

<hr/>
<pre>
<b style="color:brown;">Current Message :</b>
		<textarea cols="100" rows="10" disabled style="background-color:white; color:red; border:0px;"><?php	$myfile = 	fopen("update/suggestion.txt", "r") or die("Unable to open file!");	echo fread($myfile,filesize("update/suggestion.txt"));	fclose($myfile); ?></textarea>
</pre>

<br/><br/>

<form id="form" action="" name="form" method="POST">
<pre>
<b>Message :</b>
		<textarea cols="50" rows="5" maxlength="150" name="description" placeholder=" Description if any..."></textarea>
		 
		<input type="submit" id="butt" name="submit" value="Save"></td>

</pre>
</form>



<?php
	extract($_POST);
	if(isset($submit))
	{		
			echo "<meta http-equiv='refresh' content='0'>";
			echo '<script language="javascript">' .'alert("Done.")' .'</script>';
			
		$myfile = fopen("update/suggestion.txt", "w") or die("Unable to open file!");
		fwrite($myfile, $description);
		fclose($myfile);
		
			
	}
?>