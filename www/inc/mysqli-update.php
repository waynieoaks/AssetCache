<?php 

// DEBUG
  // echo '<pre>';
  // print_r($_POST);
  // echo '</pre>';

  // Get the form data 
  $formData = [];
  foreach ($_POST as $key => $value) {
	  $formData[$key] = $value;
  }

// Check if the required fields are present in the form data
if (isset($formData['tableid']) && isset($formData['autoid']) && isset($formData['recordid'])) {
    $sql_tableid = $formData['tableid'];
    $sql_autoid = $formData['autoid'];
    $sql_recordid = $formData['recordid'];
    
    // Initialize an empty array to store key-value pairs for the SET clause
    $setClauses = [];

    // Loop through the form data to build the SET clauses
    foreach ($formData as $key => $value) {
        // Exclude fields that are not relevant for the SET clause
        if ($key != 'tableid' && $key != 'autoid' && $key != 'recordid' && $key != 'confirm_password') {
          if ($key == 'password') {
            // This is the password field
            if ($value != '') {
             //Hash the password
              $sanitizedValue = password_hash($mysqli->real_escape_string($value), PASSWORD_DEFAULT);
              // Build the SET clause
              $key = "password_sha1";
              $setClauses[] = "`$key` = '$sanitizedValue'";
            }
          } else {
            // Sanitize the value to prevent SQL injection (you should use prepared statements instead)
            $sanitizedValue = $mysqli->real_escape_string($value);
            // Build the SET clause
            $setClauses[] = "`$key` = '$sanitizedValue'";
          }
        }
    }
    
    //DEBUG
      // echo '<pre>';
      // print_r($setClauses);
      // echo '</pre>';

    // Construct the SET portion of the SQL query
    $setClause = implode(", ", $setClauses);

    // Build the SQL update query
    $sql_update = "UPDATE $sql_tableid SET $setClause WHERE $sql_autoid = $sql_recordid";
    
    // Execute the SQL update query
    $result = $mysqli->query($sql_update);

    if ($result) {
		?>
		<div class="alert alert-success mt-3">
			Record updated successfully!
        </div>
		<?php
    } else {
		?>
		<div class="alert alert-danger mt-3">
			<strong>Error updating record: </strong> <?php echo $mysqli->error ?>
        </div>

		<?php
    }
} else {
	?>
		<div class="alert alert-danger mt-3">
			<strong>Error updating record: </strong> Required form data is missing.
        </div>

	<?php
}
?>