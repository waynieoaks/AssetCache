<?php require 'inc/head.php';?>

  
<div class="mt-3 mb-3"><h1>Welcome</h1></div>
  <p>AssetCache is a no thrills asset inventory management system for home use. </p>
  
  <h3>Statistics</h3>
  
  <div class="container">
	<div class="row g-4">
		<div class="col-6 col-md-3">
		  <a href="assets.php" class="nav-link" style="pointer:cursor">
		  <div class="card mb-3 rounded text-bg-secondary">
			<div class="card-header d-flex justify-content-center"><i class="fa-solid fa-box"></i>&nbsp; Assets:</div>
			<div class="card-body">
			  <h3 class="card-title d-flex justify-content-center">
			  <?php
			  $sql = "SELECT COUNT(idassets) AS number FROM assets WHERE disposal_date IS NULL AND disposal_to IS NULL AND disposal_price=0 AND deletedon = '0000-00-00'";
			  
			  $result = $mysqli->query($sql);

				if ($result !== false && $result->num_rows > 0) {
					// Process the results
					while ($row = $result->fetch_assoc()) {
						echo $row["number"];
					}
				} else {
					echo "0";
				}
			  ?>  
			  </h3>
			</div>
		  </div>
		  </a>
		</div>
		<div class="col-6 col-md-3">
		  <a href="assets.php" class="nav-link" style="pointer:cursor">
		  <div class="card mb-3 rounded text-bg-secondary">
			<div class="card-header d-flex justify-content-center"><i class="fa-solid fa-money-bill-wave"></i>&nbsp; Value:</div>
			<div class="card-body">
			  <h3 class="card-title d-flex justify-content-center">
			  <?php
			  $sql = "SELECT ROUND(SUM(purchase_price), 2) AS value FROM assets WHERE disposal_date IS NULL AND disposal_to IS NULL AND disposal_price=0 AND deletedon = '0000-00-00'";
			  
			  $result = $mysqli->query($sql);

				if ($result !== false && $result->num_rows > 0) {
					// Process the results
					while ($row = $result->fetch_assoc()) {
						echo $var_currency.$row["value"];
					}
				} else {
					echo $var_currency."0.00";
				}
			  ?> 
			  </h3>
			</div>
		  </div>
		  </a>
		</div>
		<div class="col-6 col-md-3">
		  <a href="locations.php" class="nav-link" style="pointer:cursor">
		  <div class="card mb-3 rounded text-bg-secondary">
			<div class="card-header d-flex justify-content-center"><i class="fa-solid fa-location-dot"></i>&nbsp;Locations:</div>
			<div class="card-body">
			  <h3 class="card-title d-flex justify-content-center">
			  <?php
			  $sql = "SELECT COUNT(idlocations) AS number FROM locations WHERE deletedon = '0000-00-00'";
			  
			  $result = $mysqli->query($sql);

				if ($result !== false && $result->num_rows > 0) {
					// Process the results
					while ($row = $result->fetch_assoc()) {
						echo $row["number"];
					}
				} else {
					echo "0";
				}
			  ?>  
			  </h3>
			</div>
		  </div>
		  </a>
		</div>
		<div class="col-6 col-md-3">
		  <a href="labels.php" class="nav-link" style="pointer:cursor">
		  <div class="card mb-3 rounded text-bg-secondary">
			<div class="card-header d-flex justify-content-center"><i class="fa-solid fa-tag"></i>&nbsp; Labels:</div>
			<div class="card-body">
			  <h3 class="card-title d-flex justify-content-center">
			   <?php
			  $sql = "SELECT COUNT(idlabels) AS number FROM labels WHERE deletedon = '0000-00-00'";
			  
			  $result = $mysqli->query($sql);

				if ($result !== false && $result->num_rows > 0) {
					// Process the results
					while ($row = $result->fetch_assoc()) {
						echo $row["number"];
					}
				} else {
					echo "0";
				}
			  ?>
			  </h3>
			</div>
		  </div>
		  </a>
		</div>
	</div>
  </div>
  
  <h3>Recent Assets</h3>
  <table id="table-assets" class="table table-striped table-hover" style="width:100%">
    <thead class="table-secondary">
      <tr>
        <th>Asset ID</th>
        <th>Asset Name</th>
		<th>Location</th>
        <th>Value</th>
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
			a.purchase_price 
		FROM assets AS a 
		LEFT JOIN locations AS l ON a.location=l.idlocations
		WHERE a.disposal_date IS NULL 
			AND a.disposal_to IS NULL 
			AND a.disposal_price=0 
			AND a.deletedon = '0000-00-00'
		ORDER BY a.createdon, a.updatedon DESC
		LIMIT 5";
	  
	  $result = $mysqli->query($sql);

		if ($result !== false && $result->num_rows > 0) {
			// Process the results
			while ($row = $result->fetch_assoc()) {
			?>
			<tr>
				<td><a href="asset_show.php?id=<?php echo $row["idassets"] ?>"><?php echo $row["number"] ?></a></td>
				<td><?php echo $row["name"] ?></td>
				<td><?php echo $row["location"] ?></td>
				<td><?php echo $var_currency.$row["purchase_price"] ?></td>
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
			"order": [ 1, "desc" ],
			"autoWidth": true,
			"searching": false,
			"paging": false,
			"ordering": false,
			"info": false,
			"responsive": true 
		});
	});
	</script>

	<h3>Primary Locations</h3>
    <div class="container">
		<div class="row g-3">
			<?php
			  $sql = "WITH RECURSIVE LocationTree AS (
						SELECT idlocations, location, parent, deletedon
						FROM locations
						WHERE parent = 0 AND (deletedon = '0000-00-00' OR deletedon IS NULL) -- Select initial locations where parent is 0 and not deleted
						UNION ALL
						SELECT l.idlocations, l.location, l.parent, l.deletedon
						FROM locations l
						JOIN LocationTree lt ON l.parent = lt.idlocations
					)
					SELECT 
						lt.idlocations AS location_id, 
						lt.location AS location_name,
						COUNT(DISTINCT a.idassets) AS asset_count -- Count distinct assets
					FROM 
						LocationTree lt
					LEFT JOIN 
						assets a ON lt.idlocations = a.location OR a.location IN (
							SELECT idlocations FROM LocationTree WHERE parent = lt.idlocations
						) OR a.location IN (
							SELECT idlocations FROM LocationTree WHERE parent IN (
								SELECT idlocations FROM LocationTree WHERE parent = lt.idlocations
							)
						)
					WHERE 
						(a.deletedon = '0000-00-00' OR a.deletedon IS NULL)
						AND (a.disposed = 0 OR a.disposed IS NULL) -- Exclude disposed assets
						AND lt.parent = 0
					GROUP BY 
						lt.idlocations, lt.location";
			  
			  $result = $mysqli->query($sql);

				if ($result !== false && $result->num_rows > 0) {
					// Process the results
					while ($row = $result->fetch_assoc()) {
						?>
						<div class="col-md-4">
						<a href="location_show.php?id=<?php echo $row["location_id"] ?>" class="nav-link" style="pointer:cursor">
						  <div class="card mb-3 rounded text-bg-secondary">
							<div class="card-body">
							<!-- number -->
							<span class="badge rounded-pill bg-light text-dark float-end"><?php echo $row["asset_count"] ?></span>
							<!-- name -->
							  <Span><i class="fa-solid fa-location-dot"></i>&nbsp; <?php echo $row["location_name"] ?></span>
							</div>
						  </div>
						</a>
						</div>
						<?php
					}
				} else {
					?>
					<div class="alert alert-warning">
					  <strong>Warning!</strong> There are currently no locations to display...
					</div>
					<?php
						}
					  ?>
		</div>
	</div>
   
   <h3>Labels</h3>
   <?php
	  $sql = "SELECT 
				idlabels, 
				label 
			  FROM labels 
			  WHERE 
				deletedon = '0000-00-00'
			  ORDER BY label ASC";
	  
	  $result = $mysqli->query($sql);

		if ($result !== false && $result->num_rows > 0) {
			// Process the results
			while ($row = $result->fetch_assoc()) {
				?>
				<a href="label_show.php?id=<?php echo $row["idlabels"] ?>" class="btn btn-secondary btn-sm"><i class="fa-solid fa-location-dot"></i>&nbsp; <?php echo $row["label"] ?></a>		
				
				<?php
			}
		} else {
			?>
			<div class="alert alert-warning">
			  <strong>Warning!</strong> There are currently no labels to display...
			</div>
			<?php
		}
	  ?>
<?php require 'inc/foot.php';?>