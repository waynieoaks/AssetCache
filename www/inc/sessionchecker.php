<?php
include_once ("sessionhandler.php");

	// CHECK IF IP ADDRESS IS BANNED
	$sql = "SELECT COUNT(logid) AS Fails
				FROM _logs 
				WHERE ipaddress = '".$sess_ip."'
				AND result = 'Fail'
				AND timestamp >= '".$sess_date."'"; 	
	$result = $mysqli->query($sql) or die($mysqli->error.__LINE__);
	if($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			if ($row["Fails"]>=$sess_locknumber) {
				include_once ("sessionlockout.php");
				exit;
			}
		}
	}
	
	// CHECK IF USER IS AUTHENTICATED
		if(!isset($_SESSION['USER_ID']) || empty('USER_ID')){
		include_once ("sessionloginbox.php");
		exit;
	} else {
		$session_userid = $_SESSION['USER_ID'];
		$session_username = $_SESSION['USER_NAME'];
	}
?>