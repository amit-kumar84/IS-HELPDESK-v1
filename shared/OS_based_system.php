

<h2 height="73" colspan="2" style="text-align:center; font-size: 20px;text-shadow: 1px 1px 2px black, 0 0 25px blue, 0 0 5px darkblue; color:#B1FF10;">&nbsp; OS Based System &nbsp;</h2>

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
	
	<div>
		<label style="float:right;"><a href="#" id="butt" onclick="openWin()">Print</a>&nbsp;</label>
		<br/><br/>
	</div>

	<div id="table_func">
		<?php
			$query_sel = mysqli_query($link,"SELECT `OS`, COUNT(OS), USG FROM `hardware_master` WHERE USERNAME!='ISKOT' AND USG!='MS (STANDBY)' AND USG!='WO' GROUP BY OS");
			$s_no = 1 ;
	
			while($row = mysqli_fetch_assoc($query_sel))
			{
				echo "<pre >" .$s_no .". " ."<b style='color:brown;'>" .$row['OS'] ."</b>" ." : <b>" .$row['COUNT(OS)'] ."</b></pre>" ;
				
				$s_no++ ;
			}
		?>
	</div>