<?
 // Mail configuration file for sending emails, e.g. for password reset
 	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	use PHPMailer\PHPMailer\SMTP;

	require_once('phpmailer/Exception.php');
	require_once('phpmailer/PHPMailer.php');
	require_once('phpmailer/SMTP.php');
	
	$mail = new PHPMailer(true);
 
  //Server settings - Edit to your requirements
    $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'YOUR_MAIL_HOST';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'YOUR_USER_NAME';                //SMTP username
    $mail->Password   = 'YOUR_PASSWORD';                     //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                   //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
	$mail->AddReplyTo('YOUR_EMAIL');					// Reply to email address
	$mail->SetFrom('YOUR_EMAIL', $sitename); // Sent from email address

?>