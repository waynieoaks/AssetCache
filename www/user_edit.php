<?php require 'inc/head.php';?>

<div class="container mt-3 mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h1>Edit User</h1>
        </div>
        <div class="col-auto">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="users.php">Users</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit User</li>
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

// Fetch the user's current data
$sql = "SELECT P.username, P.fullname, P.email, uc.fullname AS createdby, uu.fullname AS updatedby,
            CASE 
                WHEN P.createdon = '0000-00-00' THEN 'unknown'
                ELSE DATE_FORMAT(P.createdon, '%d %M %Y')
            END AS createdon,
            CASE 
                WHEN P.updatedon = '0000-00-00' THEN 'unknown'
                ELSE DATE_FORMAT(P.updatedon, '%d %M %Y @ %H:%i')
            END AS updatedon,
            CASE 
                WHEN P.loggedon = '0000-00-00' THEN 'unknown'
                ELSE DATE_FORMAT(P.loggedon, '%d %M %Y @ %H:%i')
            END AS loggedon
            FROM users as P
            LEFT JOIN users AS uc ON P.createdby = uc.userid
            LEFT JOIN users AS uu ON P.updatedby = uu.userid 
            WHERE P.userid = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $recordid);
$stmt->execute();
$stmt->bind_result($username, $fullname, $email, $createdby, $updatedby, $createdon, $updatedon, $loggedon);
$stmt->fetch();
$stmt->close();

// Fetch list of usernames to check against, excluding the current username
$sql_usernames = "SELECT p.username FROM users p WHERE (p.deletedon = '0000-00-00' OR p.deletedon IS NULL) AND p.userid != $recordid";
$result_usernames = $mysqli->query($sql_usernames);

$existingUsernames = [];
if ($result_usernames !== false && $result_usernames->num_rows > 0) {
    // Process the results
    while ($usernameRow = $result_usernames->fetch_assoc()) {
        $existingUsernames[] = $usernameRow['username'];
    }
}

// Fetch list of emails to check against, excluding the current username
$sql_emails = "SELECT p.email FROM users p WHERE (p.deletedon = '0000-00-00' OR p.deletedon IS NULL) AND p.userid != $recordid";
$result_emails = $mysqli->query($sql_emails);

$existingEmails = [];
if ($result_emails !== false && $result_emails->num_rows > 0) {
    // Process the results
    while ($emailRow = $result_emails->fetch_assoc()) {
        $existingEmails[] = $emailRow['email'];
    }
}

?>

    <script type="text/javascript">
        var existingUsernames = <?php echo json_encode($existingUsernames); ?>;
        var existingEmails = <?php echo json_encode($existingEmails); ?>;
    </script>

<div class="container mt-3 mb-3">
    <div class="row">
        <div class="col-md-6">
            <form method="post" id="update-form" data-parsley-validate="" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label> <span><small>(Last logged on <?php echo $loggedon; ?>)</small></span>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>"
                        data-parsley-maxlength="45" 
                        data-parsley-pattern="/^[a-zA-Z0-9.,\/&()]*$/" 
                        data-parsley-maxlength-message="Please enter no more than 45 characters." 
                        data-parsley-pattern-message="Please use only letters, numbers, ., /, &, (, and )." 
                        data-parsley-uniqueusername required>
                </div>
                <div class="mb-3">
                    <label for="fullname" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>"
                        data-parsley-maxlength="45" 
                        data-parsley-pattern="/^[a-zA-Z0-9.,\/&() ]*$/" 
                        data-parsley-maxlength-message="Please enter no more than 45 characters." 
                        data-parsley-pattern-message="Please use only letters, numbers, ., /, &, (, and )." 
                        required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" 
                    data-parsley-type="email" 
                    required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">New Password</label>  <span><small>(Leave password fields blank to keep your current password)</small></span>
                    <input type="password" class="form-control" id="password" name="password"
                    data-parsley-pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?&quot;:&#39;{}|<>])[A-Za-z\d!@#$%^&*(),.?&quot;:&#39;{}|<>]{8,}$"
                    data-parsley-pattern-message="Password must be at least 8 characters long, contain one lowercase letter, one uppercase letter, one digit, and one special character."
                        data-parsley-trigger="keyup">
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                        data-parsley-validate-if-empty
                        data-parsley-equalto="#password"
                        data-parsley-required-if="#password"
                        data-parsley-error-message="Passwords need to match."
                        data-parsley-trigger="keyup">              
                </div>
                <!-- Hidden field for recordset -->
                <input type="hidden" name="tableid" value="users">
                <input type="hidden" name="autoid" value="userid">
                <input type="hidden" name="updatedby" value="<?php echo $session_userid; ?>">
                <input type="hidden" name="recordid" value="<?php echo $session_userid; ?>">
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">

    $(function () {
        // Custom validator to check if the username is unique
        window.Parsley.addValidator('uniqueusername', {
            validateString: function(value) {
                return !existingUsernames.includes(value);
            },
            messages: {
                en: 'This username already exists. Please choose another one.'
            }
        });

        // Custom validator to check if the email is unique
        window.Parsley.addValidator('uniqueemail', {
            validateString: function(value) {
                return !existingEmails.includes(value);
            },
            messages: {
                en: 'This email address is already in use. Please choose another one.'
            }
        });

        // Custom validator for conditional requirement
        window.Parsley.addValidator('requiredIf', {
            requirementType: 'string',
            validateString: function(value, requirement) {
                var target = $(requirement);
                if (target.length === 0) {
                    return true; // If the target element doesn't exist, validation passes.
                }
                return target.val().length === 0 || value.length > 0;
            },
            messages: {
                en: 'This field is required.'
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
<p class="text-center"><i>Created on <?php echo $createdon; ?> by <?php echo $createdby; ?>
    <br>Updated on <?php echo $updatedon; ?> by <?php echo $updatedby; ?></i></p>
<hr>


<?php require 'inc/foot.php';?>