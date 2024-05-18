<?php require 'inc/head.php';?>

<div class="container mt-3 mb-3">
    <div class="row align-items-center">
        <div class="col"><h1>Users
	<a href="user_add.php" type="button" class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-square-plus"></i>&nbsp;Add</a>
	</h1></div>
        <div class="col-auto">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Users</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
		  <table id="table-users" class="table table-striped table-hover" style="width:auto;">
			<thead class="table-secondary">
			  <tr>
				<th>Username</th>
				<th>Full Name</th>
				<th>Email</th>
                <th>Options</th>
			  </tr>
			</thead>
			<tbody>
			<?php
			  $sql = "SELECT u.userid, u.username, u.fullname, u.email
			  FROM users AS u
			  WHERE u.deletedon = '0000-00-00' OR u.deletedon IS NULL";
			  
			  $result = $mysqli->query($sql);

				if ($result !== false && $result->num_rows > 0) {
					// Process the results
					while ($row = $result->fetch_assoc()) {
					?>
					<tr>
						<td><a href="user_show.php?id=<?php echo $row["userid"] ?>"><?php echo $row["username"] ?></a></td>
						<td><?php echo $row["fullname"] ?></td>
                        <td><?php echo $row["email"] ?></td>
						<td>
							<a href="user_edit.php?id=<?php echo $row["userid"] ?>" type="button" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-pen-to-square"></i></a>
							<a href="user_delete.php?id=<?php echo $row["userid"] ?>" type="button" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-trash-can"></i></a>	
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
				$('#table-users').dataTable( {
					"language": {
					"emptyTable": "There are currently no users to display..."
				},
					"stateSave": false,
					"autoWidth": false,
					columns: [ null, null, null, { width: '100px' }],
					"order": [ 0, "asc" ],
					//"lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
					//"iDisplayLength": 25,
					"searching": false,
					"paging": false,
					"ordering": false,
					"info": true,
					"responsive": true 
				});
			});
			</script>

  
<?php require 'inc/foot.php';?>