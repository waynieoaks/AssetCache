<?php require 'inc/head.php';?>

  
<div class="container mt-3 mb-3">
    <div class="row align-items-center">
        <div class="col"><h1>Assets
	<a href="asset_add.php" type="button" class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-square-plus"></i>&nbsp;Add</a>
	</h1></div>
        <div class="col-auto">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Assets</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
  
  <table id="table-assets" class="table table-striped table-hover" style="width:100%">
    <thead class="table-secondary">
      <tr>
        <th>Asset ID</th>
        <th>Asset Name</th>
		<th>Location</th>
        <th>Value</th>
		<th>Purchased</th>
		<th>Warranty End</th>
      </tr>
    </thead>
    <tbody>
	<?php
	  $sql = "SELECT 
				a.idassets, 
				a.number, 
				a.name, 
				a.location as idlocation,
				l.location,
				a.purchase_price, 
				CASE 
					WHEN a.purchase_date IS NULL OR a.purchase_date = '0000-00-00' THEN 'Unknown'
					ELSE a.purchase_date
				END AS purchase_date,
				CASE 
					WHEN a.lifetime_warranty = 1 THEN 'lifetime'
					WHEN a.warranty_expires IS NULL OR a.warranty_expires = '0000-00-00' THEN 'No warranty'
					ELSE a.warranty_expires
				END AS warranty_status
			FROM assets AS a 
			LEFT JOIN locations AS l ON a.location=l.idlocations
			WHERE a.deletedon = '0000-00-00' 
			AND (a.disposed = 0 OR a.disposed IS NULL)";
	  
	  $result = $mysqli->query($sql);

		if ($result !== false && $result->num_rows > 0) {
			// Process the results
			while ($row = $result->fetch_assoc()) {
			?>
			<tr>
				<td><a href="asset_show.php?id=<?php echo $row["idassets"] ?>"><?php echo $row["number"] ?></a></td>
				<td><?php echo $row["name"] ?></td>
				<td><a href="location_show.php?id=<?php echo $row["idlocation"] ?>"><?php echo $row["location"] ?></a></td>
				<td><?php echo $var_currency.$row["purchase_price"] ?></td>
				<td><?php echo $row["purchase_date"] ?></td>
				<td><?php echo $row["warranty_status"] ?></td>
			</tr>
			
			<?php	
			}
		} 
	  ?>
    </tbody>
  </table>
  
	<script type="text/javascript" defer="defer">
	$(document).ready(function(){
		$('#table-assets').dataTable( {
			"language": {
			"emptyTable": "There are currently no assets to display..."
		},
			"stateSave": false,
			"order": [ 0, "desc" ],
			"lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
			"iDisplayLength": 25,
			"autoWidth": true,
			"searching": true,
			"paging": true,
			"ordering": true,
			"info": true,
			"responsive": true 
		});
	});
	</script>
  
  
<?php require 'inc/foot.php';?>