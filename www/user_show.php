<?php require 'inc/head.php';?>

<?php

if(isset($_GET['id'])) {

  $sql = "SELECT P.userid, P.username, P.fullname, P.email, uc.fullname AS createdby, uu.fullname AS updatedby,
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
  WHERE P.userid = $recordid";

  $result = $mysqli->query($sql);

  if ($result !== false && $result->num_rows > 0) {
      // Process the results
      while ($row = $result->fetch_assoc()) {
          ?>
          <div class="container mt-3 mb-3">
            <div class="row align-items-center">
                <div class="col">
            <h1>View User 
                <a href="user_edit.php?id=<?php echo $row["userid"] ?>" type="button" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-pen-to-square"></i></a>
				<a href="user_delete.php?id=<?php echo $row["userid"] ?>" type="button" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-trash-can"></i></a>
                </h1></div>
                    <div class="col-auto">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="users.php">Users</a></li>
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
                        <div class="col"><strong>Username:</strong></div>
                        <div class="col"><span><?php echo $row['username']; ?></span></div>
                    </div>
                                        
                    <div class="row">
                        <div class="col"><strong>Full Name:</strong></div>
                        <div class="col"><span><?php echo $row['fullname']; ?></span></div>
                    </div>

                    <div class="row">
                        <div class="col"><strong>Email Address:</strong></div>
                        <div class="col"><span><?php echo $row['email']; ?></span></div>
                    </div>

                    <div class="row">
                        <div class="col"><strong>Last logged in:</strong></div>
                        <div class="col"><span><?php echo $row['loggedon'] ?></span></div>
                    </div>

                  </div>
                  <div class="col-md-6">
                      
                  </div>
              </div>
                <hr>
              <p class="text-center"><i>Created on <?php echo $row['createdon']; ?> by <?php echo $row['createdby']; ?>
                <br>Updated on <?php echo $row['updatedon']; ?> by <?php echo $row['updatedby']; ?></i></p>
                <hr>

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