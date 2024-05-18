<!DOCTYPE html>
<html lang="en">
  <head>
    <title>AssetCache - Home Inventory System</title>
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- CSS -->
	<link href="/css/bootstrap.min.css" rel="stylesheet">
	<link href="/css/all.min.css" rel="stylesheet" />
	<link href="/css/datatables.min.css" rel="stylesheet">
	<link href="/css/assetcache.css?v=1" rel="stylesheet">
	
	<!-- JS (more in foot.php) -->
	<script src="/js/bootstrap.bundle.min.js"></script>
	<script src="/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="/js/datatables.min.js" type="text/javascript"></script>
	<script src="/js/parsley.min.js" type="text/javascript"></script>
  </head>
  <body>  
	<?php 
		require_once'dbconfig.php';
		require_once('sessionchecker.php'); // *** PASSWORD PROTECT THIS PAGE ***
		
		//Get the Settings

		$sql = "SELECT setting_currency FROM settings WHERE idsettings=1";
			  
			  $result = $mysqli->query($sql);

				if ($result !== false && $result->num_rows > 0) {
					// Process the results
					while ($row = $result->fetch_assoc()) {


						$var_setting_currency = $row['setting_currency'];
					}
				}
		// Set the currency
		$sql = "SELECT currencyname, currencyvalue FROM currencies WHERE currencyname='".$var_setting_currency."'";
			  
			  $result = $mysqli->query($sql);

				if ($result !== false && $result->num_rows > 0) {
					// Process the results
					while ($row = $result->fetch_assoc()) {
						$var_currency = $row['currencyvalue'];
					}
				}	
	?>
  <div class="container">
  <nav class="navbar navbar-expand-lg bg-dark navbar-dark sticky-top">
	  <div class="container">
		<a class="navbar-brand" href="index.php"><i class="fa-solid fa-barcode"></i>&nbsp;AssetCache</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mynavbar">
		  <span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="mynavbar">
		  <ul class="navbar-nav me-auto">
		  <li class="nav-item"><a href="asset_add.php" class="btn btn-primary" type="button">Create</a></li>
			<li class="nav-item">
			  <a class="nav-link" href="assets.php"><i class="fa-solid fa-box"></i>&nbsp; Assets</a>
			</li>
			
			<li class="nav-item dropdown">
			  <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">More...</a>
			  <ul class="dropdown-menu">
				<li><a class="dropdown-item" href="locations.php"><i class="fa-solid fa-location-dot"></i>&nbsp;Locations</a></li>
				<li><a class="dropdown-item" href="labels.php"><i class="fa-solid fa-tag"></i>&nbsp;Labels</a></a></li>
				<li><a class="dropdown-item" href="tools.php"><i class="fa-solid fa-toolbox"></i>&nbsp;Tools</a></a></li>
				<li><hr class="dropdown-divider"></li>
				<li><a class="dropdown-item" href="profile.php"><i class="fa-solid fa-user"></i>&nbsp;Profile</a></a></li>
				<li><a class="dropdown-item" href="users.php"><i class="fa-solid fa-users"></i>&nbsp;Users</a></a></li>
				<li><a class="dropdown-item" href="settings.php"><i class="fa-solid fa-gear"></i>&nbsp;Settings</a></a></li>
			  </ul>
			</li>
			<li class="nav-item">
			  <a class="nav-link" href="sessionlogout.php"><i class="fa-solid fa-right-from-bracket"></i>&nbsp;Log out</a>
			</li>
		  </ul>
		  <form class="d-flex">
			<input class="form-control me-2" type="text" placeholder="Search">
			<button class="btn btn-primary" type="button">Search</button>
		  </form>
		</div>
	  </div>
  </nav>
