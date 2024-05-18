<?php
$servername = "DATABASE_SERVER";
$username = "DATABASE_USERNAME";
$password = "DATABASE_PASSWORD";
$database="assetcache";

$sitename="AssetCache";

// Create connection
$mysqli = new mysqli($servername, $username, $password, $database);

// Check connection
	if (mysqli_connect_errno()) {
		printf("Database connection failed: %s\n", mysqli_connect_error());
		exit();
	}

	if(isset($_GET['id'])) {
		$recordid = $_GET['id'];
	} else {
		// If 'id' parameter is not set in the URL, set $recordid to null or any default value as needed.
		$recordid = null; // You can set it to null or any default value you prefer.
	}
	

?>
