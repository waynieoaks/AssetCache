<?php
require_once("inc/dbconfig.php");
require_once("inc/sessionhandler.php");

	$username = $mysqli->real_escape_string($_POST['username']);
	$password = $mysqli->real_escape_string($_POST['password']);
	$StartURL = $mysqli->real_escape_string($_POST['StartURL']);
	$headerLocation="Location: ".$StartURL;
	$sess_ip=$_SESSION['REMOTE_ADDR'];

// CHECK IF USERNAME IS BANNED
	$sql = "SELECT COUNT(logid) AS Fails
		FROM _logs 
		WHERE username = '".$username."'
		AND result = 'Fail'
		AND timestamp >= '".$sess_date."'";
	
		$result = $mysqli->query($sql) or die($mysqli->error.__LINE__);

		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				if ($row["Fails"]>=$sess_locknumber) {
					include_once ("inc/sessionlockout.php");
					exit;
				}
			}
		}
		
	// CHECK IF USERNAME IN DATABASE
	$sess_query = "SELECT userid, username, password_sha1 FROM users WHERE username = '".$username."'";
	$sess_result = $mysqli->query($sess_query) or die($mysqli->error.__LINE__);
		if($sess_result->num_rows > 0) {
			while($sess_row = $sess_result->fetch_assoc()) {
				$db_userid=$sess_row["userid"];
				$db_username=$sess_row["username"];
				$dbhash=$sess_row["password_sha1"];
				$dbtable="users";
			}
		}			
		
		if (!isset($db_username)) {
		// USER NOT FOUND 
			// ADD FAIL TO LOG
			$insert_row = $mysqli->query("INSERT INTO _logs (type, result, username, ipaddress) VALUES('Login:[u]', 'Fail', '$username', '$sess_ip')"); 
				if(!$insert_row){
					$error = 'Error : ('. $mysqli->errno .') '. $mysqli->error;
				}
			// SET ERROR MESSAGE
				$error = "Incorrect username or password<br>";
				include_once("inc/sessionloginbox.php");
			exit;
		} else {
			$passed="false";
			// CHECK IF PASSWORD MATCHES HASH
			if (password_verify($password, $dbhash)) {
				$passed="true";
			} else {
				$passed="false";
				// CONVERT TO BCRYPT IF USER STILL ON SHA1
				$password_sha1=sha1($password);
				if($dbhash==$password_sha1) {
					$passed="true";
					$password_hashed = password_hash($password, PASSWORD_DEFAULT);
					$update_row = $mysqli->query("UPDATE $dbtable SET password_sha1='$password_hashed' WHERE userid=".$db_userid); 
						if(!$update_row){
							$error =  'Error : ('. $mysqli->errno .') '. $mysqli->error;
						}
				} else {
					$passed="false";
				}
			}
			if ($passed=="true") {
					$LoginDate=Date("Y-m-d");
					$LoginTime=Date("H:i:s");
					
					// UPDATE SESSION WITH USERNAME, ID
						$_SESSION['USER_ID']=$db_userid;
						$_SESSION['USER_NAME']=$db_username;
						$_SESSION['USER_TABLE']=$dbtable;
						$_SESSION['USER_AUTH_DATE']=$LoginDate." ".$LoginTime;
	
					// UPDATE USER RECORD TO SHOW LOGGED IN TODAY
						
						$update_row = $mysqli->query("UPDATE $dbtable SET Loggedon='$LoginDate' WHERE userid=".$db_userid); 
						if(!$update_row){
							$error =  'Error : ('. $mysqli->errno .') '. $mysqli->error;
						}
				// ADD SUCCESS TO LOG
						$insert_row = $mysqli->query("INSERT INTO _logs (type, result, username, ipaddress) VALUES('Login', 'Success', '$username', '$sess_ip')"); 
						if(!$insert_row){
							$error =  'Error : ('. $mysqli->errno .') '. $mysqli->error;
						}
					// REDIRECT TO LAST PAGE
					header($headerLocation);
					exit;
			} else {
						// ADD FAIL TO LOG
						$insert_row = $mysqli->query("INSERT INTO _logs (type, result, username, ipaddress) VALUES('Login:[p]', 'Fail', '$username', '$sess_ip')"); 
						if(!$insert_row){
							$error =  'Error : ('. $mysqli->errno .') '. $mysqli->error;
						}
					// SET ERROR MESSAGE AND SHOW LOGIN BOX AGAIN
							$error =  "Incorrect username or password<br>";
							include_once("inc/sessionloginbox.php");
						exit;
			}
		}
			
// REDIRECT AS A CATCH ALL [You should not reach here]
	header($headerLocation);
	exit;

?>
<p>You should not get here!</p>