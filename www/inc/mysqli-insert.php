<?php 
// Get the form data 
$formData = [];
foreach ($_POST as $key => $value) {
    $formData[$key] = $value;
}

// Check if the required fields are present in the form data
if (isset($formData['tableid']) && isset($formData['autoid'])) {
    $sql_tableid = $formData['tableid'];

    // Initialize arrays to store columns and values
    $columns = [];
    $values = [];

    // Loop through the form data to build the columns and values for the INSERT statement
    foreach ($formData as $key => $value) {
        // Exclude fields that are not relevant for the INSERT statement
        if ($key != 'tableid' && $key != 'autoid') {
            // Sanitize the value to prevent SQL injection (you should use prepared statements instead)
            $sanitizedValue = $mysqli->real_escape_string($value);
            // Add the column and value to their respective arrays
            $columns[] = "`$key`";
            $values[] = "'$sanitizedValue'";
        }
    }

    // Construct the columns and values portions of the SQL query
    $columnsClause = implode(", ", $columns);
    $valuesClause = implode(", ", $values);

    // Build the SQL insert query
    $sql_insert = "INSERT INTO $sql_tableid ($columnsClause) VALUES ($valuesClause)";
    
    // Execute the SQL insert query
    $result = $mysqli->query($sql_insert);

    if ($result) {
        // Get the ID of the newly created record
        $recordid = $mysqli->insert_id;
        ?>
        <div class="alert alert-success mt-3">
            Record created successfully! | <a href="<?php echo $page_section; ?>_show.php?id=<?php echo $recordid; ?>">View</a> | <a href="<?php echo $page_section; ?>_edit.php?id=<?php echo $recordid; ?>">Edit</a>
        </div>
        <?php
    } else {
        ?>
        <div class="alert alert-danger mt-3">
            <strong>Error creating record: </strong> <?php echo $mysqli->error ?>
        </div>
        <?php
    }
} else {
    ?>
    <div class="alert alert-danger mt-3">
        <strong>Error creating record: </strong> Required form data is missing.
    </div>
    <?php
}
?>
