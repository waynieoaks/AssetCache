<?php require 'inc/head.php';?>

<div class="container mt-3 mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h1>Create User</h1>
        </div>
        <div class="col-auto">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="users.php">Users</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<?php
$page_section = 'user';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the insert script
    require 'inc/mysqli-insert.php';
}

// Fetch list of usernames to check against, excluding the current username
$sql_usernames = "SELECT LOWER(p.username) AS username FROM users p";
$result_usernames = $mysqli->query($sql_usernames);

$existingUsernames = [];
if ($result_usernames !== false && $result_usernames->num_rows > 0) {
    // Process the results
    while ($usernameRow = $result_usernames->fetch_assoc()) {
        $existingUsernames[] = $usernameRow['username'];
    }
}

// Fetch list of emails to check against, excluding the current username
$sql_emails = "SELECT LOWER(p.email) AS email FROM users p";
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
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username"
                        data-parsley-maxlength="45"
                        data-parsley-minlength="5"
                        data-parsley-pattern="/^[a-zA-Z0-9.,!#$%&'*+\-=\^_~|/?{}()]*$/" 
                        data-parsley-maxlength-message="Please enter no more than 45 characters." 
                        data-parsley-minlength-message="Please enter more than 4 characters."
                        data-parsley-pattern-message="Please use only letters, numbers or allowed characters: ., ,, !, #, $, %, &, ', *, +, -, =, ^, _, ~, |, /, ?, {, }, (, )."  
                        data-parsley-uniqueusername 
                        required>
                </div>
                <div class="mb-3">
                    <label for="fullname" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="fullname" name="fullname"
                        data-parsley-maxlength="45" 
                        data-parsley-minlength="5"
                        data-parsley-pattern="/^[a-zA-Z0-9.,!#$%&'*+\-=\^_~|/?{}() ]*$/" 
                        data-parsley-maxlength-message="Please enter no more than 45 characters." 
                        data-parsley-minlength-message="Please enter more than 4 characters."
                        data-parsley-pattern-message="Please use only letters, numbers or allowed characters: ., ,, !, #, $, %, &, ', *, +, -, =, ^, _, ~, |, /, ?, {, }, (, ) and spaces."  
                        required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" 
                    data-parsley-type="email" 
                    data-parsley-uniqueemail
                    required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="password" name="password"
                    data-parsley-pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?&quot;:&#39;{}|<>])[A-Za-z\d!@#$%^&*(),.?&quot;:&#39;{}|<>]{8,}$"
                    data-parsley-pattern-message="Password must be at least 8 characters long, contain one lowercase letter, one uppercase letter, one digit, and one special character."
                        data-parsley-trigger="keyup"
                        required>
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
                <input type="hidden" name="createdby" value="<?php echo $session_userid; ?>">
                <input type="hidden" name="updatedby" value="<?php echo $session_userid; ?>">
                <button type="submit" class="btn btn-primary">Add</button>
                <a href="users.php" class="btn btn-outline-secondary float-end">Cancel</a>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">

    $(function () {
        // Custom validator to check if the username is unique
        window.Parsley.addValidator('uniqueusername', {
            validateString: function(value) {
                var lowerCaseValue = value.toLowerCase();
                return !existingUsernames.includes(lowerCaseValue);
            },
            messages: {
                en: 'This username already exists. Please choose another one.'
            }
        });

        // Custom validator to check if the email is unique
        window.Parsley.addValidator('uniqueemail', {
            validateString: function(value) {
                var lowerCaseValue = value.toLowerCase();
                return !existingEmails.includes(lowerCaseValue);
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
  
<?php require 'inc/foot.php';?>