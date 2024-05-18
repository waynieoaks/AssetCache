<?php require 'inc/head.php';?>

<div class="container mt-3 mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h1>Edit Label</h1>
        </div>
        <div class="col-auto">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="labels.php">Labels</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<?php
// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the update script
    require 'inc/mysqli-update.php';
}

// Check if ID is set in the URL
if (isset($_GET['id'])) {
    //$recordid = intval($_GET['id']);

    // Fetch the label details
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
            AND l.idlabels = $recordid
            GROUP BY l.idlabels, l.label, l.description, uc.fullname, uu.fullname, createdon, updatedon";

    $result = $mysqli->query($sql);

    if ($result !== false && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Fetch list of labels to check against, excluding the current label
        $sql = "SELECT l.label FROM labels l WHERE (l.deletedon = '0000-00-00' OR l.deletedon IS NULL) AND l.idlabels != $recordid";
        $result = $mysqli->query($sql);

        $existingLabels = [];
        if ($result !== false && $result->num_rows > 0) {
            // Process the results
            while ($labelRow = $result->fetch_assoc()) {
                $existingLabels[] = $labelRow['label'];
            }
        }
        ?>

        <script type="text/javascript">
            var existingLabels = <?php echo json_encode($existingLabels); ?>;
        </script>

        <div class="container mt-3 mb-3">
            <div class="row">
                <div class="col-md-6">
                    <form method="post" id="update-form" data-parsley-validate="" action="">
                        <div class="mb-3">
                            <label for="label" class="form-label">Label:</label>
                            <input type="text" class="form-control" id="label" name="label" value="<?php echo htmlspecialchars($row['label']); ?>"
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
                            ><?php echo htmlspecialchars($row['description']); ?></textarea>
                        </div>
                        <!-- Hidden field for recordset -->
                        <input type="hidden" name="tableid" value="labels">
                        <input type="hidden" name="autoid" value="idlabels">
                        <input type="hidden" name="updatedby" value="<?php echo $session_userid; ?>">
                        <input type="hidden" name="recordid" value="<?php echo $recordid; ?>">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="label_delete.php?id=<?php echo $row["idlabels"]; ?>" type="button" class="btn btn-outline-danger">Delete</a>
                        <a href="label_show.php?id=<?php echo $recordid; ?>" class="btn btn-outline-secondary float-end">Cancel</a>
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
                        en: 'This label already exists. Please choose another one.'
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

        <hr>
        <p class="text-center"><i>Created on <?php echo $row['createdon']; ?> by <?php echo $row['createdby']; ?>
            <br>Updated on <?php echo $row['updatedon']; ?> by <?php echo $row['updatedby']; ?></i></p>
        <hr>

        <?php
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
