<?php require 'inc/head.php';?>

<div class="container mt-3 mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h1>Create Label</h1>
        </div>
        <div class="col-auto">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="labels.php">Labels</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<?php
$page_section = 'label';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the insert script
    require 'inc/mysqli-insert.php';
}

// Fetch list of labels to check against
$sql = "SELECT l.label FROM labels l WHERE l.deletedon = '0000-00-00' OR l.deletedon IS NULL";
$result = $mysqli->query($sql);

$existingLabels = [];
if ($result !== false && $result->num_rows > 0) {
    // Process the results
    while ($row = $result->fetch_assoc()) {
        $existingLabels[] = $row['label'];
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
                    <input type="text" class="form-control" id="label" name="label"
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
                <!-- Hidden field for recordset -->
                <input type="hidden" name="tableid" value="labels">
                <input type="hidden" name="autoid" value="idlabels">
                <input type="hidden" name="createdby" value="<?php echo $session_userid; ?>">
                <input type="hidden" name="updatedby" value="<?php echo $session_userid; ?>">
                <button type="submit" class="btn btn-primary">Add</button>
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

<?php require 'inc/foot.php';?>
