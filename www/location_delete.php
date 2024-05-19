<?php 
require 'inc/head.php';

// Start output buffering
ob_start();

if(isset($_GET['id'])) {

    // Step 1: Check if the record is valid
    $stmt = $mysqli->prepare("SELECT l.location FROM locations AS l WHERE l.idlocations = ? AND (l.deletedon = '0000-00-00' OR l.deletedon IS NULL)");
    $stmt->bind_param("i", $recordid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Record is valid
        $location = $result->fetch_assoc()['location'];
        $stmt->close();

        // Step 2: Fetch all descendants of the current location for child location dropdown
        function getAllDescendants($parentId, $mysqli) {
            $descendants = [];
            $stmt = $mysqli->prepare("SELECT idlocations FROM locations WHERE parent = ? AND (deletedon = '0000-00-00' OR deletedon IS NULL)");
            $stmt->bind_param("i", $parentId);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $descendants[] = $row['idlocations'];
                $descendants = array_merge($descendants, getAllDescendants($row['idlocations'], $mysqli));
            }
            $stmt->close();
            return $descendants;
        }

        $allDescendants = getAllDescendants($recordid, $mysqli);

        // Step 3: Check for assets assigned to this location
        $stmt_assets = $mysqli->prepare("SELECT 
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
                                        AND a.location = ?
                                        ORDER BY a.number ASC");
        $stmt_assets->bind_param("i", $recordid);
        $stmt_assets->execute();
        $result_assets = $stmt_assets->get_result();

        // Step 4: Check for child locations
        $stmt_children = $mysqli->prepare("SELECT l.idlocations, l.location FROM locations AS l WHERE parent = ? AND (deletedon = '0000-00-00' OR deletedon IS NULL)");
        $stmt_children->bind_param("i", $recordid);
        $stmt_children->execute();
        $result_children = $stmt_children->get_result();

        // Fetch all locations for the assets dropdown, excluding only the current location
        $stmt_locations_assets = $mysqli->prepare("SELECT idlocations, location FROM locations WHERE idlocations != ? AND (deletedon = '0000-00-00' OR deletedon IS NULL) ORDER BY location ASC");
        $stmt_locations_assets->bind_param("i", $recordid);
        $stmt_locations_assets->execute();
        $result_locations_assets = $stmt_locations_assets->get_result();

        // Fetch all locations for the child locations dropdown, excluding the current location and its descendants
        $stmt_locations_children = $mysqli->prepare("SELECT idlocations, location FROM locations WHERE idlocations != ? AND idlocations NOT IN (" . implode(',', $allDescendants) . ") AND (deletedon = '0000-00-00' OR deletedon IS NULL) ORDER BY location ASC");
        $stmt_locations_children->bind_param("i", $recordid);
        $stmt_locations_children->execute();
        $result_locations_children = $stmt_locations_children->get_result();

        // Determine if deletion is allowed
        $disableDelete = $result_assets->num_rows > 0 || $result_children->num_rows > 0;

        // Handle move all assets
        if (isset($_POST['move_all']) && isset($_POST['new_location_all']) && !empty($_POST['new_location_all'])) {
            $new_location_all = intval($_POST['new_location_all']);
            $stmt_move_all = $mysqli->prepare("UPDATE assets SET location = ? WHERE location = ?");
            $stmt_move_all->bind_param("ii", $new_location_all, $recordid);
            $stmt_move_all->execute();
            $stmt_move_all->close();
            header("Location: location_delete.php?id=$recordid");
            ob_end_flush();
            exit();
        }

        // Handle move selected assets
        if (isset($_POST['move_selected']) && isset($_POST['new_location'])) {
            foreach ($_POST['new_location'] as $asset_id => $new_location) {
                if (!empty($new_location)) {
                    $new_location = intval($new_location);
                    $stmt_move = $mysqli->prepare("UPDATE assets SET location = ? WHERE idassets = ?");
                    $stmt_move->bind_param("ii", $new_location, $asset_id);
                    $stmt_move->execute();
                    $stmt_move->close();
                }
            }
            header("Location: location_delete.php?id=$recordid");
            ob_end_flush();
            exit();
        }

        // Handle move all child locations
        if (isset($_POST['move_all_children']) && isset($_POST['new_parent_location_all']) && !empty($_POST['new_parent_location_all'])) {
            $new_parent_location_all = intval($_POST['new_parent_location_all']);
            $stmt_move_all_children = $mysqli->prepare("UPDATE locations SET parent = ? WHERE parent = ?");
            $stmt_move_all_children->bind_param("ii", $new_parent_location_all, $recordid);
            $stmt_move_all_children->execute();
            $stmt_move_all_children->close();
            header("Location: location_delete.php?id=$recordid");
            ob_end_flush();
            exit();
        }

        // Handle move selected child locations
        if (isset($_POST['move_selected_children']) && isset($_POST['new_parent_location'])) {
            foreach ($_POST['new_parent_location'] as $child_id => $new_parent_location) {
                if (!empty($new_parent_location)) {
                    $new_parent_location = intval($new_parent_location);
                    $stmt_move_child = $mysqli->prepare("UPDATE locations SET parent = ? WHERE idlocations = ?");
                    $stmt_move_child->bind_param("ii", $new_parent_location, $child_id);
                    $stmt_move_child->execute();
                    $stmt_move_child->close();
                }
            }
            header("Location: location_delete.php?id=$recordid");
            ob_end_flush();
            exit();
        }

        // Handle confirmation
        if(isset($_POST['confirm']) && !$disableDelete) {
            // Step 5: Update records in the database
            $stmt = $mysqli->prepare("UPDATE locations SET deletedon = CURRENT_DATE(), deletedby = ? WHERE idlocations = ?");
            $stmt->bind_param("si", $session_userid, $recordid);
            $stmt->execute();
            $stmt->close();

            // Redirect to locations.php after deletion
            header("Location: locations.php");
            ob_end_flush();
            exit();
        }
        ?>

        <div class="container mt-3 mb-3">
            <div class="row align-items-center">
                <div class="col">
                    <h1>Delete Location</h1>
                </div>
                <div class="col-auto">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                            <li class="breadcrumb-item"><a href="locations.php">Locations</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Delete</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="container mt-5">
            <div class="alert alert-primary">
                <strong>Are you sure?</strong> <br>This will delete the location '<?php echo htmlspecialchars($location); ?>'. 
            </div>

            <form method="post">
                <button type="submit" name="confirm" class="btn btn-danger" <?php if ($disableDelete) echo 'disabled'; ?>>DELETE</button>
                <a href="locations.php" class="btn btn-secondary">Go back</a>
            </form>

            <?php if ($disableDelete) { ?>
                <div class="alert alert-danger mt-3">
                    <strong>You cannot delete this location:</strong> It has assets or child locations assigned to it.
                </div>

                <?php if ($result_assets->num_rows > 0) { ?>
                    <div class="d-flex justify-content-between align-items-center">
                        <h3>Assigned Assets</h3>
                        <form method="post" class="d-flex align-items-center">
                            <select id="new_location_all" name="new_location_all" class="form-select me-2">
                                <option value="">Select Location</option>
                                <?php while ($loc = $result_locations_assets->fetch_assoc()) { ?>
                                    <option value="<?php echo $loc['idlocations']; ?>"><?php echo htmlspecialchars($loc['location']); ?></option>
                                <?php } ?>
                            </select>
                            <button type="submit" name="move_all" id="move_all_button" class="btn btn-sm btn-primary" disabled>Move All</button>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table id="table-assets" class="table table-striped table-hover mt-3">
                            <thead>
                                <tr>
                                    <th>Asset ID</th>
                                    <th class="d-none d-sm-table-cell">Asset Name</th>
                                    <th class="d-none d-lg-table-cell">Value</th>
                                    <th class="d-none d-lg-table-cell">Purchased</th>
                                    <th>New Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                <form method="post">
                                    <?php while ($asset = $result_assets->fetch_assoc()) { ?>
                                        <tr>
                                            <td><a href="asset_show.php?id=<?php echo htmlspecialchars($asset['idassets']); ?>"><?php echo htmlspecialchars($asset['number']); ?></a></td>
                                            <td class="d-none d-sm-table-cell"><?php echo htmlspecialchars($asset['name']); ?></td>
                                            <td class="d-none d-lg-table-cell"><?php echo $var_currency.htmlspecialchars($asset['purchase_price']); ?></td>
                                            <td class="d-none d-lg-table-cell"><?php echo htmlspecialchars($asset['purchase_date']); ?></td>
                                            <td>
                                                <select name="new_location[<?php echo $asset['idassets']; ?>]" class="form-select">
                                                    <option value="">Select Location</option>
                                                    <?php 
                                                    // Reset the result pointer and loop through locations again
                                                    $result_locations_assets->data_seek(0);
                                                    while ($loc = $result_locations_assets->fetch_assoc()) { 
                                                        echo '<option value="' . $loc['idlocations'] . '">' . htmlspecialchars($loc['location']) . '</option>';
                                                    } 
                                                    ?>
                                                </select>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td colspan="5" class="text-end">
                                            <button type="submit" name="move_selected" class="btn btn-primary">Move Selected Assets</button>
                                        </td>
                                    </tr>
                                </form>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>

                <?php if ($result_children->num_rows > 0) { ?>
                    <div class="d-flex justify-content-between align-items-center mt-5">
                        <h3>Child Locations</h3>
                        <form method="post" class="d-flex align-items-center">
                            <select id="new_parent_location_all" name="new_parent_location_all" class="form-select me-2">
                                <option value="">Select Location</option>
                                <?php 
                                // Reset the result pointer and loop through locations again
                                $result_locations_children->data_seek(0);
                                while ($loc = $result_locations_children->fetch_assoc()) { 
                                    echo '<option value="' . $loc['idlocations'] . '">' . htmlspecialchars($loc['location']) . '</option>';
                                } 
                                ?>
                            </select>
                            <button type="submit" name="move_all_children" id="move_all_children_button" class="btn btn-sm btn-primary" disabled>Move All</button>
                        </form>
                    </div>

                    <ul class="list-group mt-3">
                        <form method="post">
                            <?php while ($child = $result_children->fetch_assoc()) { ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><a href="location_show.php?id=<?php echo htmlspecialchars($child['idlocations']); ?>"><?php echo htmlspecialchars($child['location']); ?></a></span>
                                    <select name="new_parent_location[<?php echo $child['idlocations']; ?>]" class="form-select w-auto">
                                        <option value="">Select Location</option>
                                        <?php 
                                        // Reset the result pointer and loop through locations again
                                        $result_locations_children->data_seek(0);
                                        while ($loc = $result_locations_children->fetch_assoc()) { 
                                            echo '<option value="' . $loc['idlocations'] . '">' . htmlspecialchars($loc['location']) . '</option>';
                                        } 
                                        ?>
                                    </select>
                                </li>
                            <?php } ?>
                            <li class="list-group-item text-end">
                                <button type="submit" name="move_selected_children" class="btn btn-primary">Move Selected Child Locations</button>
                            </li>
                        </form>
                    </ul>
                <?php } ?>
            <?php } ?>
        </div>

        <?php
    } else {
        // Record not found or already deleted
        echo "Location not found or already deleted.";
    }
} else {
    // No ID provided
    echo "No record ID provided.";
}

require 'inc/foot.php';

// End output buffering and send output
ob_end_flush();
?>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        const newLocationAllDropdown = document.getElementById('new_location_all');
        const moveAllButton = document.getElementById('move_all_button');
        const newParentLocationAllDropdown = document.getElementById('new_parent_location_all');
        const moveAllChildrenButton = document.getElementById('move_all_children_button');

        newLocationAllDropdown.addEventListener('change', function () {
            moveAllButton.disabled = newLocationAllDropdown.value === "";
        });

        newParentLocationAllDropdown.addEventListener('change', function () {
            moveAllChildrenButton.disabled = newParentLocationAllDropdown.value === "";
        });
    });
</script>

<style>
    #move_all_button, #move_all_children_button {
        white-space: nowrap;
    }
</style>
