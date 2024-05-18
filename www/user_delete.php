<?php 
require 'inc/head.php';?>

<?php
if(isset($_GET['id'])) {
    // Retrieve recordid from GET parameters
    $recordid = intval($_GET['id']); // Ensure $recordid is an integer

    // Step 1: Check if the record is valid
    $stmt = $mysqli->prepare("SELECT CONCAT(username, ' (', email, ')') AS user FROM users WHERE userid = ? AND (deletedon = '0000-00-00' OR deletedon IS NULL)");
    $stmt->bind_param("i", $recordid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Record is valid
        $user = $result->fetch_assoc()['user'];
        $stmt->close();
?>

<div class="container mt-3 mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h1>Delete User</h1>
        </div>
        <div class="col-auto">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="users.php">Users</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Delete</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

    <div class="container mt-5">
        <?php if ($recordid == $session_userid) { ?>
            <!-- Danger alert if user is trying to delete themselves -->
            <div class="alert alert-danger">
                <strong>Warning: Cannot delete yourself:</strong> <br><br>Seriously?<br>You want to delete <em>yourself</em>?<br>I mean, maybe you should... <br>However, I am not going to allow it!
            </div>
            <form method="post">
                <button type="submit" name="confirm" class="btn btn-danger" disabled>DELETE</button>
                <a href="users.php" class="btn btn-secondary">Go back</a>
            </form>
        <?php } else { ?>
            <!-- Confirmation alert if user is not deleting themselves -->
            <div class="alert alert-primary">
                <strong>Are you sure?</strong> <br>This will delete the user '<?php echo $user; ?>'. 
            </div>
            <form method="post">
                <button type="submit" name="confirm" class="btn btn-danger">DELETE</button>
                <a href="users.php" class="btn btn-secondary">Go back</a>
            </form>
        <?php } ?>
    </div>

    <?php
    } else {
        // Record not found or already deleted
        echo "User not found or already deleted.";
    }

    // Handle confirmation
    if(isset($_POST['confirm']) && $recordid != $session_userid) {
        // Step 2: Update records in the database
        $stmt = $mysqli->prepare("UPDATE users SET deletedon = CURRENT_DATE(), deletedby = ? WHERE userid = ?");
        $stmt->bind_param("si", $session_userid, $recordid);
        $stmt->execute();
        $stmt->close();

        // Redirect to users.php after deletion
        header("Location: users.php");
        exit();
    }
}
?>

<?php require 'inc/foot.php';?>
