

<h2 height="73" colspan="2" style="text-align:center; font-size: 20px;text-shadow: 1px 1px 2px black, 0 0 25px blue, 0 0 5px darkblue; color:#B1FF10;">&nbsp; Software List &nbsp;</h2>
<div>
  <div>
	<table width="100%" height="54" border="1" cellpadding="1" cellspacing="0" style="float:left;" id="table_func">
      <tbody>
        <tr style="text-align: center; font-size:20; font-weight:bold; "; bgcolor="yellow">
          <td>S. No.</td>
          <td>Software Name</td>
          <td>Type of Software</td>
          <td>PO No.</td>
          <td>Purchase year</td>
          <td>License</td>
        </tr>
		<?php
		extract($_GET);
		$query_sel = mysqli_query($link,"SELECT * FROM `softwarelist` ORDER BY `name` ASC");
		
		$s_no = 1 ;
		
		while($soft_list_arr = mysqli_fetch_array($query_sel))
			{
			$s_no ;
			$name = $soft_list_arr["name"] ;
			$type = $soft_list_arr["type"] ;
			$po_no = $soft_list_arr["po_no"] ;
			$pur_year = $soft_list_arr["pur_year"] ;
			$license = $soft_list_arr["license"] ;
		?>
		
		<tr style="text-align: left; font-size:15px" id="row_hov">
          <td style="text-align:center;"><?php echo $s_no ; ?></td>
          <td><?php echo $name ; ?></td>
          <td><?php echo $type ; ?></td>
          <td><?php echo $po_no ; ?></td>
          <td><?php echo $pur_year ; ?></td>
          <td><?php echo $license ; ?></td>
        </tr>
	
		<?php
		$s_no++ ;
			}
		?>
		
	   </tbody>
	</table>
  </div>
</div>
