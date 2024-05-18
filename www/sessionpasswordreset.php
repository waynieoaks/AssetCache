<?php
$ShowLogin="true";
require_once("inc/dbconfig.php");
$ipaddress=$_SERVER['REMOTE_ADDR'];

$num=0;
$errors=0;
$errormsg="";
$My_Email="";

// CHECK EMAIL ADDRESS IN DATABASE //
	if (isset($_POST["username"])) {
		$My_Username=$_POST["username"];
		//echo $My_Username;
	}
	
	// Attempt to reset account //
		
	$sql = "SELECT 
				userid, 
				username,
				email,
				fullname, 
				email 
				FROM users
				WHERE username = '$My_Username' OR email = '$My_Username'
				AND deletedon='0000-00-00'
				LIMIT 1";
	
	$result = $mysqli->query($sql) or die($mysqli->error.__LINE__);
	if($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$My_Userid=$row["userid"];
			$My_Name=$row["fullname"];
			$My_Username=$row["username"];
			$My_Email=$row["email"];
		}
	} else {
			//No record found 
			$errors=$errors+1;
			$errormsg=$errormsg."- The username you provided was not found<br>";
			$querySQL3 = "INSERT INTO _logs (type, result, username, ipaddress) VALUES('Reset', 'Fail', '$My_Username', '$ipaddress')"; 
				$insert_row = $mysqli->query($querySQL3); 
					if(!$insert_row){
						print 'Error : ('. $mysqli->errno .') '. $mysqli->error;
					}
	}
				
// CREATE A NEW PASSWORD //
if ($errors==0) {
	$vowels = 'aeuy!?$%&';
	$consonants = 'bdghjmnpqrstvz';
	$consonants .= 'BDGHJLMNPQRSTVWXZ';
	$vowels .= "AEUY";
	$consonants .= '23456789';

	$My_password = '';
	$alt = time() % 2;
	for ($i = 0; $i < 15; $i++) {
		if ($alt == 1) {
			$My_password .= $consonants[(rand() % strlen($consonants))];
			$alt = 0;
		} else {
			$My_password .= $vowels[(rand() % strlen($vowels))];
			$alt = 1;
		}
	}
// CONVERT TO BCRYPT //
		$My_sha1 = password_hash($My_password, PASSWORD_DEFAULT);
					
// Save the data //
		// UPDATE USERS RECORD //
		$querySQL2 = "UPDATE users SET password_sha1='".$My_sha1."' WHERE userid=".$My_Userid;
		$update_row= $mysqli->query($querySQL2);
			if(!$update_row){
				print 'Error : ('. $mysqli->errno .') '. $mysqli->error;
			}
		
// SEND PASSWORD TO EMAIL ADDRESS //

	require_once("inc/mailcon.php");
	//require($_SERVER["DOCUMENT_ROOT"].'/_shared/phpmailer_config.php');
	$mail->AddAddress($My_Email);
	//$mail->IsHTML(true); 

			$Subject = "[".$sitename."] Password reset";
			$BodyHead ="This is an automated email from ".$sitename.". 
 
You have indicated that you forgot your password and requested a reset. Your login details are as follows:";

			$BodyFoot = "You can now log into the system by going to http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."
 
Don't forget to change your password, by going to the 'Edit profile' page. 
 
If you feel you have received this email in error or are not expecting a password notification from us, please contact us IMMEDIATELY.";

	$Body = $BodyHead;
	$Body = $Body."

	Username: ".$My_Username."
	Password: ".$My_password."

";
	$Body = $Body.$BodyFoot;
	
	$mail->Subject = $Subject;
	$mail->Body = $Body;

	if(!$mail->Send()) {
		$errors=$errors+1;
		$errormsg=$errormsg."- There was a problem sending the confirmation email<br>";
		// Add record to security log //
			$querySQL3 = "INSERT INTO _logs (type, result, username, ipaddress) VALUES('Reset', 'Error (Email)', '$My_Username', '$ipaddress')"; 
			$insert_row = $mysqli->query($querySQL3); 
				if(!$insert_row){
					print 'Error : ('. $mysqli->errno .') '. $mysqli->error;
				}
	}
}

// HANDLE ERRORS HERE //
if (!$errors==0) { ?>
	<!DOCTYPE html>
	<html lang="en">
	<head>
		<title>AssetCache - password reset</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="/css/bootstrap.min.css" rel="stylesheet" />
		<link href="/css/all.min.css" rel="stylesheet" />
		<link href="/css/assetcache.css" rel="stylesheet" />
	</head>
	<body><p>
		<div class="container">    
			<div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">                    
				<div class="card">
					<div class="card-header text-bg-danger">
						<h4>Unable to reset password</h4>
					</div>     
					<div style="padding-top:30px" class="card-body" >
						We have been unable to reset your password due to the following errors:
						<br><bR>   <?php echo $errormsg; ?>   <br>
						Please <a href="sessionpasswordforgot.php">go back</a> and try again, or contact us for further support.
					</div>
				</div>  
			</div>
		</div>
	</body>
	</html>	
<?	exit;
}

// Add record to security log //
	$querySQL3 = "INSERT INTO _logs (type, result, username, ipaddress) VALUES('Reset', 'Success', '$My_Username', '$ipaddress')"; 
	$insert_row = $mysqli->query($querySQL3); 
	if(!$insert_row){
		print 'Error : ('. $mysqli->errno .') '. $mysqli->error;
	}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>AssetCache - Password reset</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="/css/bootstrap.min.css" rel="stylesheet" />
	<link href="/css/all.min.css" rel="stylesheet" />
	<link href="/css/assetcache.css" rel="stylesheet" />
</head>
<body><p>
	<div class="container">    
		<div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">                    
			<div class="card" >
				<div class="card-header text-bg-secondary">
					<h4>Password successfully set</h4>
				</div>     
				<div style="padding-top:30px" class="card-body" >
					Your new password has been successfully changed and has been sent to your email address. 
					<br><br>You can now log in with your new password <a href="index.php">here</a>
				</div>
			</div>  
		</div>
	</div>
</body>
</html>	