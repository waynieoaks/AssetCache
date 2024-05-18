<?php require 'inc/head.php';?>

<?php

if(isset($_GET['id'])) {
    $recordid = $_GET['id'];

    // Function to get all child locations
    function getAllChildLocations($parentId, $mysqli) {
        $childLocations = [];

        $sql = "SELECT idlocations FROM locations WHERE parent = $parentId AND (deletedon = '0000-00-00' OR deletedon IS NULL)";
        $result = $mysqli->query($sql);

        if ($result !== false && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $childLocations[] = $row['idlocations'];
                // Recursively find further child locations
                $childLocations = array_merge($childLocations, getAllChildLocations($row['idlocations'], $mysqli));
            }
        }

        return $childLocations;
    }

    function getBreadcrumbHierarchy($locationId, $mysqli) {
        $breadcrumbs = [];
        
        while ($locationId > 0) {
            $sql = "SELECT idlocations, location, parent FROM locations WHERE idlocations = $locationId";
            $result = $mysqli->query($sql);
            
            if ($result !== false && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $breadcrumbs[] = [
                    'id' => $row['idlocations'],
                    'location' => $row['location']
                ];
                $locationId = $row['parent'];
            } else {
                break;
            }
        }
        
        return array_reverse($breadcrumbs);
    }
    

    // Get all child locations
    $childLocationIds = getAllChildLocations($recordid, $mysqli);
    $childLocationIdsStr = implode(',', $childLocationIds);

    $sql = "SELECT l.idlocations, l.location, l.description, l.parent, uc.username AS createdby, p.idlocations AS pidlocations, p.location AS plocation, uu.username AS updatedby, 
                COUNT(a.idassets) AS asset_count,
                CASE 
                    WHEN l.createdon = '0000-00-00' THEN 'unknown'
                    ELSE DATE_FORMAT(l.createdon, '%d %M %Y')
                END AS createdon,
                CASE 
                    WHEN l.updatedon = '0000-00-00' THEN 'unknown'
                    ELSE DATE_FORMAT(l.updatedon, '%d %M %Y @ %H:%i')
                END AS updatedon
                FROM locations AS l
                LEFT JOIN assets AS a ON l.idlocations = a.location
                LEFT JOIN locations AS p ON l.parent = p.idlocations
                LEFT JOIN users AS uc ON l.createdby = uc.userid
                LEFT JOIN users AS uu ON l.updatedby = uu.userid
                WHERE (l.deletedon = '0000-00-00' OR l.deletedon IS NULL) 
                -- AND (a.deletedon = '0000-00-00' OR a.deletedon IS NULL)  
                AND l.idlocations = $recordid
                GROUP BY l.idlocations, l.location, l.description, uc.username, uu.username, createdon, updatedon";

    $result = $mysqli->query($sql);

    if ($result !== false && $result->num_rows > 0) {
        // Process the results
        while ($row = $result->fetch_assoc()) {
            ?>
            <div class="container mt-3 mb-3">
                <div class="row align-items-center">
                    <div class="col">
                <h1>View Location 
                    <a href="location_edit.php?id=<?php echo $row["idlocations"] ?>" type="button" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-pen-to-square"></i></a>
                    <a href="location_delete.php?id=<?php echo $row["idlocations"] ?>" type="button" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-trash-can"></i></a>
                    </h1></div>
                        <div class="col-auto">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                    <li class="breadcrumb-item"><a href="locations.php">Locations</a></li>
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
                            <div class="col"><strong>Location:</strong></div>
                            <div class="col"><span><?php echo $row['location']; ?></span></div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Description:</strong></div>
                            <div class="col"><span><?php echo $row['description']; ?></span></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col"><strong>Parent Location:</strong></div>
                            <div class="col">
                                <span>
                                    <?php 
                                    $breadcrumbs = getBreadcrumbHierarchy($row['parent'], $mysqli);
                                    if (count($breadcrumbs) > 0) {
                                        $breadcrumbLinks = [];
                                        foreach ($breadcrumbs as $breadcrumb) {
                                            $breadcrumbLinks[] = '<a href="location_show.php?id='.$breadcrumb['id'].'">'.$breadcrumb['location'].'</a>&nbsp;';
                                        }
                                        echo implode(' / ', $breadcrumbLinks);
                                    } else {
                                        echo "-- None --";
                                    }
                                    ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                    <hr>
                  <p class="text-center"><i>Created on <?php echo $row['createdon']; ?> by <?php echo ucfirst(strtolower($row['createdby'])); ?>
                    <br>Updated on <?php echo $row['updatedon']; ?> by <?php echo ucfirst(strtolower($row['updatedby'])); ?></i></p>
                    <hr>

                    <h4>Related Assets</h4>
                    <table id="table-assets-current" class="table table-striped table-hover" style="width:100%">
                        <thead class="table-secondary">
                        <tr>
                            <th>Asset ID</th>
                            <th>Asset Name</th>
                            <th>Value</th>
                            <th>Purchased</th>
                            <th>Warranty End</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $sql_assets_current = "SELECT 
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
                                    AND (a.disposed = 0 OR a.disposed IS NULL)
                                    AND a.location = $recordid";
                        
                        $result_assets_current = $mysqli->query($sql_assets_current);

                            if ($result_assets_current !== false && $result_assets_current->num_rows > 0) {
                                // Process the results
                                while ($row_asset = $result_assets_current->fetch_assoc()) {
                                ?>
                                <tr>
                                    <td><a href="asset_show.php?id=<?php echo $row_asset["idassets"] ?>"><?php echo $row_asset["number"] ?></a></td>
                                    <td><?php echo $row_asset["name"] ?></td>
                                    <td><?php echo $var_currency.$row_asset["purchase_price"] ?></td>
                                    <td><?php echo $row_asset["purchase_date"] ?></td>
                                    <td><?php echo $row_asset["warranty_status"] ?></td>
                                </tr>
                                
                                <?php    
                                }
                            } 
                        ?>
                        </tbody>
                    </table>

                    <h4>Related Child Assets</h4>
                    <table id="table-assets-child" class="table table-striped table-hover" style="width:100%">
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
                        if (!empty($childLocationIds)) {
                            $sql_assets_child = "SELECT 
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
                                            AND (a.disposed = 0 OR a.disposed IS NULL)
                                            AND a.location IN ($childLocationIdsStr)";
                            
                            $result_assets_child = $mysqli->query($sql_assets_child);

                                if ($result_assets_child !== false && $result_assets_child->num_rows > 0) {
                                    // Process the results
                                    while ($row_asset = $result_assets_child->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <td><a href="asset_show.php?id=<?php echo $row_asset["idassets"] ?>"><?php echo $row_asset["number"] ?></a></td>
                                        <td><?php echo $row_asset["name"] ?></td>
                                        <td><a href="location_show.php?id=<?php echo $row_asset["idlocation"] ?>"><?php echo $row_asset["location"] ?></a></td>
                                        <td><?php echo $var_currency.$row_asset["purchase_price"] ?></td>
                                        <td><?php echo $row_asset["purchase_date"] ?></td>
                                        <td><?php echo $row_asset["warranty_status"] ?></td>
                                    </tr>
                                    
                                    <?php    
                                    }
                                } 
                            }
                        ?>
                        </tbody>
                    </table>

                    <script type="text/javascript" defer="defer">
                    $(document).ready(function(){
                        $('#table-assets-current').dataTable({
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

                        $('#table-assets-child').dataTable({
                            "language": {
                            "emptyTable": "There are currently no child assets to display..."
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
