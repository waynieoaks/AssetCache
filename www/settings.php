<?php require 'inc/head.php';?>

<div class="container mt-3 mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h1>Settings</h1>
        </div>
        <div class="col-auto">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Settings</li>
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

// Fetch currency options from the 'currencies' table
$sql_currencies = "SELECT currencyname, currencyvalue FROM currencies WHERE deletedon = '0000-00-00' OR deletedon IS NULL ORDER BY currencyname ASC";
$result_currencies = $mysqli->query($sql_currencies);

// Fetch the setting details
$sql = "SELECT s.idsettings, s.setting_currency, uu.username AS updatedby,
            CASE 
                WHEN s.updatedon = '0000-00-00' THEN 'unknown'
                ELSE DATE_FORMAT(s.updatedon, '%d %M %Y @ %H:%i')
            END AS updatedon
            FROM assetcache.settings AS s
            LEFT JOIN users AS uu ON s.updatedby = uu.userid";

$result = $mysqli->query($sql);

if ($result !== false && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    ?>
    <div class="container mt-3 mb-3">
        <div class="row">
            <div class="col-md-6">
                <form method="post" id="update-form" data-parsley-validate="" action="">
                    <div class="mb-3">
                        <?php if ($row['idsettings'] == 1) { ?>
                            <label for="setting_currency" class="form-label">Currency:</label>
                            <select class="form-select" id="setting_currency" name="setting_currency" required>
                                <?php while ($currency = $result_currencies->fetch_assoc()) { ?>
                                    <option value="<?php echo $currency['currencyname']; ?>" <?php if ($currency['currencyname'] == $row['setting_currency']) echo 'selected'; ?>>
                                        <?php echo $currency['currencyname']; ?> (<?php echo $currency['currencyvalue']; ?>)
                                    </option>
                                <?php } ?>
                            </select>
                        <?php } ?>
                    </div>
                    
                    <!-- Hidden field for recordset -->
                    <input type="hidden" name="tableid" value="settings">
                    <input type="hidden" name="autoid" value="idsettings">
                    <input type="hidden" name="updatedby" value="<?php echo $session_userid; ?>">
                    <input type="hidden" name="recordid" value="<?php echo $row['idsettings']; ?>">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="index.php" class="btn btn-outline-secondary float-end">Cancel</a>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(function () {
            $('#update-form').parsley().on('field:validated', function() {
                var ok = $('.parsley-error').length === 0;
                $('.bs-callout-info').toggleClass('hidden', !ok);
                $('.bs-callout-warning').toggleClass('d-none', ok);
            }).on('form:submit', function() {
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
    <p class="text-center">Updated on <?php echo $row['updatedon']; ?> by <?php echo ucfirst(strtolower($row['updatedby'])); ?></i></p>
    <hr>

<?php } ?>

<?php require 'inc/foot.php';?>
