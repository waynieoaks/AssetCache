<?php require 'inc/head.php';?>

<div class="container mt-3 mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h1>Create Location</h1>
        </div>
        <div class="col-auto">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="locations.php">Locations</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<?php

$page_section = 'location';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the insert script
    require 'inc/mysqli-insert.php';
}

// Fetch list of locations to check against, excluding the current location
$sql_locations = "SELECT l.idlocations, l.location, l.parent FROM locations l WHERE l.deletedon = '0000-00-00' OR l.deletedon IS NULL ORDER BY l.location ASC";
$result_locations = $mysqli->query($sql_locations);

$existingLocations = [];
if ($result_locations !== false && $result_locations->num_rows > 0) {
    // Process the results
    while ($locationRow = $result_locations->fetch_assoc()) {
        $existingLocations[] = $locationRow;
    }
}
?>

<script type="text/javascript">
    var existingLocations = <?php echo json_encode($existingLocations); ?>;
</script>

<div class="container mt-3 mb-3">
    <div class="row">
        <div class="col-md-6">
            <form method="post" id="update-form" data-parsley-validate="" action=""> 
                <div class="mb-3">
                    <label for="location" class="form-label">Location:</label>
                    <input type="text" class="form-control" id="location" name="location"
                        data-parsley-maxlength="45" 
                        data-parsley-pattern="/^[a-zA-Z0-9.,\/&() ]*$/" 
                        data-parsley-maxlength-message="Please enter no more than 45 characters." 
                        data-parsley-pattern-message="Please use only letters, numbers, ., /, &, (, and )." 
                        data-parsley-uniquelabel required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description:</label>
                    <textarea class="form-control" id="description" name="description"
                        data-parsley-pattern="/^[a-zA-Z0-9.,\/&() ]*$/"
                        data-parsley-pattern-message="Please use only letters, numbers, ., /, &, (, and )."
                    ></textarea>
                </div>
                <div class="mb-3">
                    <label for="parent" class="form-label">Parent Location:</label>
                    <select class="form-select" id="parent" name="parent" required>
                        <option value="0" selected>-- None --</option>
                        <?php
                        foreach ($existingLocations as $locationRow) {
                            $breadcrumbs = getBreadcrumbHierarchy($locationRow['idlocations'], $mysqli);
                            $breadcrumbLinks = [];
                            foreach ($breadcrumbs as $breadcrumb) {
                                $breadcrumbLinks[] = $breadcrumb['location'];
                            }
                            $hierarchicalLocation = implode(' / ', $breadcrumbLinks);
                           // $selected = ($locationRow['idlocations'] == $row['parent']) ? 'selected' : '';
                            echo '<option value="' . $locationRow['idlocations'] . '">' . htmlspecialchars($hierarchicalLocation) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <!-- Hidden field for recordset -->
                <input type="hidden" name="tableid" value="locations">
                <input type="hidden" name="autoid" value="idlocations">
                <input type="hidden" name="createdby" value="<?php echo $session_userid; ?>">
                <input type="hidden" name="updatedby" value="<?php echo $session_userid; ?>">
                <button type="submit" class="btn btn-primary">Add</button>
                <a href="location_show.php?id=<?php echo $recordid; ?>" class="btn btn-outline-secondary float-end">Cancel</a>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        // Custom validator to check if the label is unique
        window.Parsley.addValidator('uniquelabel', {
            validateString: function(value) {
                return !existingLabels.includes(value);
            },
            messages: {
                en: 'This location already exists. Please try another name.'
            }
        });

        $('#update-form').parsley().on('field:validated', function() {
            var ok = $('.parsley-error').length === 0;
            $('.bs-callout-info').toggleClass('hidden', !ok);
            $('.bs-callout-warning').toggleClass('d-none', ok);
        })
        .on('form:submit', function() {
            if ($('.parsley-error').length > 0) {
                return false; // Prevent form submission
            }
        });
    });
</script>

<div class="alert alert-danger bs-callout bs-callout-warning d-none mt-3">
    <strong>Unable to update: </strong> Please check the data you have entered and try again.
</div>

<?php
// Breadcrumb function
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
?>

<?php require 'inc/foot.php';?>
