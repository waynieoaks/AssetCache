<?php require 'inc/head.php';?>

<?php  
if(isset($_GET['id'])) {

$sql = "SELECT a.idassets, a.number, a.name, a.description, a.notes, a.location, l.location AS location_name, a.purchase_price, a.purchase_date, a.purchase_from, a.purchase_number, a.manufacturer, a.model_number, a.serial_number, a.lifetime_warranty, a.warranty_expires, a.warranty_details, a.disposed, a.disposal_to, 
a.disposal_date, a.disposal_notes, a.disposal_price, 
uc.fullname AS createdby, uu.fullname AS updatedby, 
    CASE 
        WHEN a.createdon = '0000-00-00' THEN 'unknown'
        ELSE DATE_FORMAT(a.createdon, '%d %M %Y')
    END AS createdon,
    CASE 
        WHEN a.updatedon = '0000-00-00' THEN 'unknown'
        ELSE DATE_FORMAT(a.updatedon, '%d %M %Y @ %H:%i')
    END AS updatedon
    FROM assets as a 
    LEFT JOIN users AS uc ON a.createdby = uc.userid
    LEFT JOIN users AS uu ON a.updatedby = uu.userid
    LEFT JOIN locations as l ON a.location = l.idlocations
    WHERE a.idassets = $recordid";

$result = $mysqli->query($sql);

if ($result !== false && $result->num_rows > 0) {
    // Process the results
    while ($row = $result->fetch_assoc()) {
        ?>

        <div class="container mt-3 mb-3">
            <div class="row align-items-center">
                <div class="col">
            <h1>View Asset 
                <a href="asset_edit.php?id=<?php echo $row["idassets"] ?>" type="button" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-pen-to-square"></i></a>
				<a href="asset_delete.php?id=<?php echo $row["idassets"] ?>" type="button" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-trash-can"></i></a>
                </h1></div>
                    <div class="col-auto">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="assets.php">Assets</a></li>
                                <li class="breadcrumb-item active" aria-current="page">View</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="container mt-3 mb-3">
              <div class="row">
                  <div class="col-md-6">
                    <div class="row">
                        <div class="col"><strong>Number:</strong></div>
                        <div class="col"><span><?php echo $row['number']; ?></span></div>
                    </div>
                    <div class="row">
                        <div class="col"><strong>Name:</strong></div>
                        <div class="col"><span><?php echo $row['name']; ?></span></div>
                    </div>
                    <div class="row">
                        <div class="col"><strong>Location:</strong></div>
                        <div class="col"><span><?php echo $row['location_name']; ?> <button class="btn btn-outline-primary btn-sm">Move</button></span></div>
                    </div>
                    <div class="row">
                        <div class="col"><strong>Manufacturer:</strong></div>
                        <div class="col"><span><?php echo $row['manufacturer']; ?></span></div>
                    </div>
                    <div class="row">
                        <div class="col"><strong>Model:</strong></div>
                        <div class="col"><span><?php echo $row['model_number']; ?></span></div>
                    </div>
                    <div class="row">
                        <div class="col"><strong>Serial:</strong></div>
                        <div class="col"><span><?php echo $row['serial_number']; ?></span></div>
                    </div>
                    <div class="row">
                        <div class="col"><strong>Description:</strong></div>
                        <div class="col"><span><?php echo $row['description']; ?></span></div>
                    </div>

                  </div>
                  <div class="col-md-6">
                    <div class="row">
                        <div class="col"><strong>Cost:</strong></div>
                        <div class="col"><span><?php echo $row['purchase_price']; ?></span></div>
                    </div>
                    <div class="row">
                        <div class="col"><strong>Purchased:</strong></div>
                        <div class="col"><span><?php echo $row['purchase_date']; ?></span></div>
                    </div>
                    <div class="row">
                        <div class="col"><strong>Supplier:</strong></div>
                        <div class="col"><span><?php echo $row['purchase_from']; ?></span></div>
                    </div>
                    <div class="row">
                        <div class="col"><strong>Purchase #:</strong></div>
                        <div class="col"><span><?php echo $row['purchase_number']; ?></span></div>
                    </div>   
                  </div>
              </div>
                <hr>
                <p class="text-center"><i>Created on <?php echo $row['createdon']; ?> by <?php echo $row['createdby']; ?>
                <br>Updated on <?php echo $row['updatedon']; ?> by <?php echo $row['updatedby']; ?></i></p>
                <hr>
                <div class="row">
                  <div class="col-md-6">
                    <div class="row">
                        <div class="col"><strong>Notes:</strong></div>
                        <div class="col"><span><?php echo $row['notes']; ?></span></div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="row">
                        <div class="col"><strong>Disposal date:</strong></div>
                        <div class="col"><span><?php echo $row['disposal_date']; ?></span></div>
                    </div>
                    <div class="row">
                        <div class="col"><strong>Disposal cost:</strong></div>
                        <div class="col"><span><?php echo $row['disposal_price']; ?></span></div>
                    </div>
                    <div class="row">
                        <div class="col"><strong>Disposed to:</strong></div>
                        <div class="col"><span><?php echo $row['disposal_to']; ?></span></div>
                    </div>
                    <div class="row">
                        <div class="col"><strong>Disposal notes:</strong></div>
                        <div class="col"><span><?php echo $row['disposal_notes']; ?></span></div>
                    </div>
                      
                  </div>
              </div>    
          </div>
<?php	
        }
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