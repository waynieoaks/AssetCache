<?php 
require 'inc/head.php';?>

<?php
if(isset($_GET['id'])) {
    // Step 1: Check if the record is valid
    $stmt = $mysqli->prepare("SELECT label FROM assetcache.labels WHERE idlabels = ? AND (deletedon = '0000-00-00' OR deletedon IS NULL)");
    $stmt->bind_param("i", $recordid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Record is valid
        $label = $result->fetch_assoc()['label'];
        $stmt->close();
?>

<div class="container mt-3 mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h1>Delete Label</h1>
        </div>
        <div class="col-auto">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="labels.php">Labels</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Delete</li>
                </ol>
            </nav>
        </div>
    </div>
</div>


    <div class="container mt-5">
        <div class="alert alert-primary">
            <strong>Are you sure?</strong> <br>This will delete the label '<?php echo $label; ?>'. 
        </div>

        <form method="post">
            <button type="submit" name="confirm" class="btn btn-danger">DELETE</button>
            <a href="labels.php" class="btn btn-secondary">Go back</a>
        </form>
    </div>

    <?php
        } else {
            // Record not found or already deleted
            echo "Label not found or already deleted.";
        }

    // Handle confirmation
    if(isset($_POST['confirm'])) {
        // Step 2: Update records in the database

        $stmt = $mysqli->prepare("UPDATE labels SET deletedon = CURRENT_DATE(), deletedby = ? WHERE idlabels = ?");
        $stmt->bind_param("si", $session_userid, $recordid);
        $stmt->execute();
        $stmt->close();

    //    $stmt = $mysqli->prepare("UPDATE asset_label_junction SET deletedon = CURRENT_DATE(), deletedby = ? WHERE idlabels = ?");
    //    $stmt->bind_param("si", $session_userid, $recordid);
    //    $stmt->execute();
    //    $stmt->close();

        // Redirect to labels.php after deletion
        header("Location: labels.php");
        exit();
    }
}
?>

<?php require 'inc/foot.php';?>