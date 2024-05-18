<?php

require("inc/dbconfig.php");
require_once('inc/sessionhandler.php');

$ip_address=$_SESSION['REMOTE_ADDR'];
$my_username=$_SESSION['USER_NAME'];

	unset($_SESSION['USER_ID']);
	unset($_SESSION['USER_NAME']);
	unset($_SESSION['USER_TABLE']);
	unset($_SESSION['USER_AUTH_DATE']);
  
  // Add record to security log //
	$querySQL3 = "INSERT INTO _logs (type, result, username, ipaddress) VALUES('Logout', 'Success', '$my_username', '$ip_address')"; 
	$insert_row = $mysqli->query($querySQL3); 
	if(!$insert_row){
		print 'Error : ('. $mysqli->errno .') '. $mysqli->error;
	}
   		header("Location: index.php");
		exit;
?>