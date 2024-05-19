<?php require 'inc/head.php';?>

<div class="container mt-3 mb-3">
    <div class="row align-items-center">
        <div class="col"><h1>Labels
	<a href="label_add.php" type="button" class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-square-plus"></i>&nbsp;Add</a>
	</h1></div>
        <div class="col-auto">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Labels</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
		  <table id="table-assets" class="table table-striped table-hover" style="width:auto;">
			<thead class="table-secondary">
			  <tr>
				<th>Label</th>
				<th style="text-align: center;">Assets</th>
				<th>Options</th>
			  </tr>
			</thead>
			<tbody>
			<?php
			  $sql = "SELECT l.idlabels, l.label, l.description, uc.fullname AS createdby, uu.fullname AS updatedby, 
			  COUNT(alj.idassets) AS asset_count,
			  CASE 
				  WHEN l.createdon = '0000-00-00' THEN 'unknown'
				  ELSE DATE_FORMAT(l.createdon, '%d %M %Y')
			  END AS createdon,
			  CASE 
				  WHEN l.updatedon = '0000-00-00' THEN 'unknown'
				  ELSE DATE_FORMAT(l.updatedon, '%d %M %Y @ %H:%i')
			  END AS updatedon
			  FROM assetcache.labels AS l
			  LEFT JOIN asset_label_junction AS alj ON l.idlabels = alj.idlabels
			  LEFT JOIN assets AS a ON alj.idassets = a.idassets
			  LEFT JOIN users AS uc ON l.createdby = uc.userid
			  LEFT JOIN users AS uu ON l.updatedby = uu.userid
			  WHERE (l.deletedon = '0000-00-00' OR l.deletedon IS NULL) 
			  AND (a.deletedon = '0000-00-00' OR a.deletedon IS NULL)  
			  GROUP BY l.idlabels, l.label, l.description, uc.fullname, uu.fullname, createdon, updatedon
			  ORDER BY l.label ASC";
			  
			  $result = $mysqli->query($sql);

				if ($result !== false && $result->num_rows > 0) {
					// Process the results
					while ($row = $result->fetch_assoc()) {
					?>
					<tr>
						<td><a href="label_show.php?id=<?php echo $row["idlabels"] ?>"><?php echo $row["label"] ?></a></td>
						<td style="text-align: center;"><?php echo $row["asset_count"] ?></td>
						<td>
							<a href="label_edit.php?id=<?php echo $row["idlabels"] ?>" type="button" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-pen-to-square"></i></a>
							<a href="label_delete.php?id=<?php echo $row["idlabels"] ?>" type="button" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-trash-can"></i></a>	
						</td>
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
					"emptyTable": "There are currently no labels to display..."
				},
					"stateSave": false,
					"autoWidth": false,
					columns: [ null, { width: '100px' }, { width: '100px' }],
					"order": [ 0, "asc" ],
					"lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
					"iDisplayLength": 25,
					"searching": true,
					"paging": true,
					"ordering": true,
					"info": true,
					"responsive": true 
				});
			});
			</script>

  
<?php require 'inc/foot.php';?>