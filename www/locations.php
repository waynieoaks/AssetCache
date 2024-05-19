<?php require 'inc/head.php';?>
  
<div class="container mt-3 mb-3">
    <div class="row align-items-center">
        <div class="col"><h1>Locations
    <a href="location_add.php" type="button" class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-square-plus"></i>&nbsp;Add</a>
    </h1></div>
        <div class="col-auto">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Locations</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<?php
    // SQL query to fetch locations along with asset count
    $sql = "SELECT 
                l.idlocations, 
                l.location,
                l.parent,
                COUNT(a.idassets) AS asset_count
            FROM 
                locations AS l 
            LEFT JOIN 
                assets AS a ON l.idlocations = a.location
            WHERE 
                (l.deletedon = '0000-00-00' OR l.deletedon IS NULL)
                AND (a.deletedon = '0000-00-00' OR a.deletedon IS NULL)
            GROUP BY 
                l.idlocations, l.location";

    // Execute query
    $result = $mysqli->query($sql);

    // Check if query execution was successful
    if ($result === false) {
        die("Error in SQL query: " . $mysqli->error);
    }

    // Fetch all rows from the result set as an associative array
    $locations = [];
    while ($row = $result->fetch_assoc()) {
        $locations[] = $row;
    }

    function buildTreeWithTotalAssets($locations, $parentId = 0) {
        $tree = [];
        foreach ($locations as $location) {
            if ($location['parent'] == $parentId) {
                $children = buildTreeWithTotalAssets($locations, $location['idlocations']);
                if ($children) {
                    $location['children'] = $children;
                }
                // Calculate total assets including children
                $location['total_assets'] = $location['asset_count'];
                if (!empty($children)) {
                    foreach ($children as $child) {
                        $location['total_assets'] += $child['total_assets'];
                    }
                }
                $tree[] = $location;
            }
        }
        return $tree;
    }

    // Modify the displayTree function to include the total assets column
    function displayTreeWithTotalAssets($tree, $indent = 0) {
        foreach ($tree as $node) {
            echo '<tr>';
            echo '<td>' . str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $indent) . ($indent > 0 ? '└─ ' : '') .'<a href="location_show.php?id='.$node["idlocations"].'" class="link-primary">'. $node['location'] . '</a></td>';
            echo '<td class="d-none d-md-table-cell" style="text-align: center;">' . (isset($node['asset_count']) ? $node['asset_count'] : '') . '</td>';
            echo '<td class="d-none d-md-table-cell" style="text-align: center;">' . (isset($node['total_assets']) ? $node['total_assets'] : '') . '</td>';
            echo '<td class="d-none d-md-table-cell" style="text-align: center;">';
            echo '<a href="location_edit.php?id='.$node["idlocations"].'" type="button" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-pen-to-square"></i></a>';
            echo ' <a href="location_delete.php?id='.$node["idlocations"].'" type="button" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-trash-can"></i></a>';
            echo '</td>';
            echo '</tr>';
            if (isset($node['children'])) {
                displayTreeWithTotalAssets($node['children'], $indent + 1);
            }
        }
    }

    // Build the tree structure with total assets
    $treeWithTotalAssets = buildTreeWithTotalAssets($locations);

    // Display the hierarchical structure with total assets
    echo '<table id="table-assets" class="table table-striped table-hover" style="width:auto;">';
    echo '<thead class="table-secondary"><tr><th>Location</th><th class="d-none d-md-table-cell" style="text-align: center;">Assets</th><th class="d-none d-md-table-cell" style="text-align: center;">Total Assets</th><th class="d-none d-md-table-cell">Options</th></tr></thead>';
    echo '<tbody>';
    displayTreeWithTotalAssets($treeWithTotalAssets);
    echo '</tbody>';
    echo '</table>';
?>

<script type="text/javascript" defer="defer">
    $(document).ready(function(){
        $('#table-assets').dataTable( {
            "language": {
                "emptyTable": "There are currently no locations to display..."
            },
            "stateSave": false,
            "autoWidth": false,
            "responsive": true,
            "columns": [
                null,
                { "className": "d-none d-md-table-cell" },
                { "className": "d-none d-md-table-cell" },
                { "className": "d-none d-md-table-cell", "width": "90px" }
            ],
            "order": [ 1, "asc" ],
            "searching": false,
            "paging": false,
            "ordering": false,
            "info": true,
        });
    });
</script>

<?php require 'inc/foot.php';?>
