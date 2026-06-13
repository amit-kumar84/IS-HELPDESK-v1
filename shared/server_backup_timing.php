


<h2 height="73" colspan="2" style="text-align:center; font-size: 36px;text-shadow: 1px 1px 2px black, 0 0 25px blue, 0 0 5px darkblue; color:#B1FF10;">&nbsp; Server Backup Timing &nbsp;</h2>

<table width="100%" height="54" border="1" cellpadding="1" cellspacing="0" style="float:left;" id="table_func">
      <tbody>
        <tr style="text-align:center;" bgcolor="yellow">
			<td>S. No.</td>
			<td>Server Name</td>
			<td>IP Address</td>
			<td>Backup Timimg</td>
			<td>Backup Location</td>
        </tr>
		<?php
			extract($_GET);
			
			$s_no = 1 ;
			
			$fetch_data = mysqli_query($link,"SELECT * FROM `server_backup_sheduled`");
			while($backup_data_arr = mysqli_fetch_array($fetch_data))
		  {
			?>
		
        <tr style="font-size:18px" id="row_hov">
			<td style="text-align:center;" ><?php echo $s_no ; ?></td>
			<td><?php echo $backup_data_arr['server_name'] ; ?></td>
			<td><?php echo $backup_data_arr['ip_add'] ; ?></td>
			<td STYLE="TEXT-ALIGN:CENTER;"><?php
												IF($backup_data_arr['timing']=='00:00:00')
												{
													echo '-' ;
												}
												ELSE
												{
													echo $backup_data_arr['timing'] ;
												}
											?>
			</td>
			<td><?php echo $backup_data_arr['backup_location'] ; ?></td>
        </tr>
		
		<?php
		$s_no++ ;
		  }
		  ?>  
      </tbody>
</table>