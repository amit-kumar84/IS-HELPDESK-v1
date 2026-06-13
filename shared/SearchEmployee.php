

<?php

if(isset($_POST['search']))
{
    $valueToSearch = $_POST['valueToSearch'];
    // search in all table columns
    // using concat mysql function
    $query = "SELECT * FROM `emp_details` WHERE CONCAT(`staffid`, `username`) LIKE '%".$valueToSearch."%' ORDER BY `cost_center`, `deptt`, `sec`, `staffid`, `username` ASC ";
    $search_result = filterTable($query);
    
}
 else {
    $query = "SELECT * FROM `emp_details` ORDER BY `cost_center`, `deptt`, `sec`, `staffid`, `username` ASC ";
    $search_result = filterTable($query);
}

// function to connect and execute the query
function filterTable($query)
{
    $connect = mysqli_connect("localhost", "root", "", "hardware_master");
    $filter_Result = mysqli_query($connect, $query);
    return $filter_Result;
}

?>
<div>
<form id="form1" action="#" name="form1" method="POST">
  <p><strong>Employee Name / Staff Number:
    <input name="valueToSearch" type="text" id="valueToSearch" autofocus placeholder=" Employee Name or Staff Number" size="30">
    <input name="search" type="submit" id="butt" formmethod="POST" value="Search">
    </strong>
  </p>
 
<div>
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
<span style="float:right;"><a href="#" id="butt" onclick="openWin()">Print</a>&nbsp;</span>
</div>
<br/><br/>

  <table width="100%" border="1" cellpadding="1" cellspacing="0" style="text-align:center; float:left;" id="table_func">
    <tbody>
      <tr style="background-color:yellow;font-size:12px; color:black;">
        <td>S. No.</td>
        <td>COST CENTER</td>
        <td>DEPT. (SEC)</td>
		
        <td>STAFF NO.</td>
        <td>IMAGE</td>
        <td>NAME</td>
        <td>DESIGNATION</td>
        <td>PH NO.</td>
		<td>IP PH NO.</td>
        <td>ASSET DETAILS</td>
      </tr>
      <!-- populate table from mysql database -->
	  <?php $s_no = 1 ; ?>
                <?php while($row = mysqli_fetch_array($search_result)):?>
	  <tr style="font-size:12px; text-align:left;">
        <td style="text-align:center;"><?php echo $s_no ;?></td>
        <td><?php echo $row['cost_center'];?></td>
        <td><?php echo $row['deptt'] ." (" .$row['sec'] .")" ; ?></td>
        <td><?php echo $row['staffid'];?></td>
        <td style="text-align:center;"><img src="Pictures\<?php echo $row['staffid'];?>.JPG" alt="User Image Not Found!" height="60px" width="50px" /></td>
		<td style="text-align:left;"><?php echo $row['username'] ; ?></div>
		</td>
        <td><?php echo $row['desg'] ;?></td>
        <td><?php echo $row['phone_no'] ;?></td>
        <td>
			<?php
				if($row['ip_phone']=="0")
				{
					echo "";
				}
				else
				{
					echo $row['ip_phone'] ;
				}
			?>
		</td>
		
<?php
		//variable for hold the staff id
		$data_fetch_var=$row['staffid'];
		
		// detail fetch only for PC
		$master_data_det_pc=mysqli_query($link,"SELECT * FROM `hardware_master` WHERE (`STAFF_NO`='$data_fetch_var') AND (`CATG`='PC')");
		echo "<td>" ;
		while($master_data_val_pc=mysqli_fetch_array($master_data_det_pc))
		{
        echo "<b style='color:green;'>PC : </b><b>" .$master_data_val_pc['HD_ID_NO'] ."</b>&nbsp;&nbsp;-&nbsp;&nbsp; " .$master_data_val_pc['MAKE'] ." (" .$master_data_val_pc['MODEL'] .")" ."<br/>" ;
		}
		
		// detail fetch only for Laptop
		$master_data_det_lap=mysqli_query($link,"SELECT * FROM `hardware_master` WHERE (`STAFF_NO`='$data_fetch_var') AND (`CATG`='Laptop')");
		while($master_data_val_lap=mysqli_fetch_array($master_data_det_lap))
		{
		echo "<b style='color:green;'>Laptop : </b><b>" .$master_data_val_lap['HD_ID_NO'] ."</b>&nbsp;&nbsp;-&nbsp;&nbsp; " .$master_data_val_lap['MAKE'] ." (" .$master_data_val_lap['MODEL'] .")" ."<br/>" ;
		}
		
		// details for webcam
		$master_data_det_WEB_CAM=mysqli_query($link,"SELECT * FROM `hardware_master` WHERE (`STAFF_NO`='$data_fetch_var') AND (`CATG`='WEB_CAM')");
		while($master_data_val_WEB_CAM=mysqli_fetch_array($master_data_det_WEB_CAM))
		{
		echo "<b style='color:green;'>WEB_CAM : </b><b>" .$master_data_val_WEB_CAM['HD_ID_NO'] ."</b>&nbsp;&nbsp;-&nbsp;&nbsp; " .$master_data_val_WEB_CAM['MAKE'] ." (" .$master_data_val_WEB_CAM['MODEL'] .")" ."<br/>" ;
		}
		
		
		
		// details for VDI
		$master_data_det_VDI=mysqli_query($link,"SELECT * FROM `hardware_master` WHERE (`STAFF_NO`='$data_fetch_var') AND (`CATG`='VDI')");
		while($master_data_val_VDI=mysqli_fetch_array($master_data_det_VDI))
		{
		echo "<b style='color:green;'>VDI : </b><b>" .$master_data_val_VDI['HD_ID_NO'] ."</b>&nbsp;&nbsp;-&nbsp;&nbsp; " .$master_data_val_VDI['MAKE'] ." (" .$master_data_val_VDI['MODEL'] .")" ."<br/>" ;
		}
		
		
		// detail fetch only for PRINTER
		$master_data_det_printer=mysqli_query($link,"SELECT * FROM `hardware_master` WHERE (`STAFF_NO`='$data_fetch_var') AND (`CATG`='PRINTER')");
		while($master_data_val_printer=mysqli_fetch_array($master_data_det_printer))
		{
		echo "<b style='color:green;'>Printer : </b><b>" .$master_data_val_printer['HD_ID_NO'] ."</b>&nbsp;&nbsp;-&nbsp;&nbsp; " .$master_data_val_printer['MAKE']  ." (" .$master_data_val_printer['MODEL'] .")" ."<br/>" ;
		}
		
		
		
		
		// detail fetch only for Storage Device
		$master_data_det_storage=mysqli_query($link,"SELECT * FROM `hardware_stroage_master` WHERE `STAFF_NO`='$data_fetch_var' ");
		while($master_data_val_storage=mysqli_fetch_array($master_data_det_storage))
		{
		echo "<b style='color:green;'>Storage Device : </b><b>" .$master_data_val_storage['HD_ID_NO'] ."</b>: " .$master_data_val_storage['CATG'] ."- " .$master_data_val_storage['MAKE'] ."-" .$master_data_val_storage['HD_ID_RECORD'] ."<br/>" ;
		}
		
		
		
		
		
		
		echo "</td>" ;
?>
		
      </tr>
	  
                <?php $s_no++ ; endwhile; ?>
    </tbody>
  </table>
</form>
</div>