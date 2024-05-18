<?php require 'inc/head.php';?>

<?php

if(isset($_GET['id'])) {

  $sql = "SELECT l.idlabels, l.label, l.description, uc.username AS createdby, uu.username AS updatedby, 
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
            AND l.idlabels = $recordid
            GROUP BY l.idlabels, l.label, l.description, uc.username, uu.username, createdon, updatedon";

  $result = $mysqli->query($sql);

  if ($result !== false && $result->num_rows > 0) {
      // Process the results
      while ($row = $result->fetch_assoc()) {
          ?>
          <div class="container mt-3 mb-3">
            <div class="row align-items-center">
                <div class="col">
            <h1>View Label 
                <a href="label_edit.php?id=<?php echo $row["idlabels"] ?>" type="button" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-pen-to-square"></i></a>
				<a href="label_delete.php?id=<?php echo $row["idlabels"] ?>" type="button" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-trash-can"></i></a>
                </h1></div>
                    <div class="col-auto">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="labels.php">Labels</a></li>
                                <li class="breadcrumb-item active" aria-current="page">View</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
          
          <div class="container mt-3 mb-3">
              <div class="row">
                  <div class="col-md-6">
                    <div class="row">
                        <div class="col"><strong>Label:</strong></div>
                        <div class="col"><span><?php echo $row['label']; ?></span></div>
                    </div>
                    <div class="row">
                        <div class="col"><strong>Description:</strong></div>
                        <div class="col"><span><?php echo $row['description']; ?></span></div>
                    </div>

                  </div>
                  <div class="col-md-6">
                      
                  </div>
              </div>
                <hr>
              <p class="text-center"><i>Created on <?php echo $row['createdon']; ?> by <?php echo ucfirst(strtolower($row['createdby'])); ?>
                <br>Updated on <?php echo $row['updatedon']; ?> by <?php echo ucfirst(strtolower($row['updatedby'])); ?></i></p>
                <hr>

                <h4>Related Assets</h4>
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
                            LEFT JOIN asset_label_junction AS alj ON a.idassets = alj.idassets
                            WHERE a.deletedon = '0000-00-00' 
                            AND (a.disposed = 0 OR a.disposed IS NULL)
                            AND alj.idlabels = $recordid";
                    
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
          </div>
          <?php	
      }
    } else {
        ?>
        <div class="alert alert-danger mt-3">
            <strong>Record not found:</strong> Please check the record id and try again.
        </div>
        <?php
    }
    } else {
    ?>
    <div class="alert alert-danger mt-3">
        <strong>No record provided:</strong> Please check the record id and try again.
    </div>
    <?php
    }
    ?>
<?php require 'inc/foot.php';?>