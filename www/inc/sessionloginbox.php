<!DOCTYPE html>
<html lang="en">
  <head>
    <title>AssetCache - Please sign in...</title>
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="/css/bootstrap.min.css" rel="stylesheet" />
	<link href="/css/all.min.css" rel="stylesheet" />
	<link href="/css/assetcache.css" rel="stylesheet" />
</head>
<body>
<?php 
	if (isset($sess_query)) {
		// We are on the login check page
		$StartURL=$StartURL;
		$action="sessionlogincheck.php";
	} else {
		// We are on a real page
		$action="sessionlogincheck.php";
		$StartURL = strlen($_SERVER['QUERY_STRING']) ? basename($_SERVER['PHP_SELF'])."?".$_SERVER['QUERY_STRING'] : basename($_SERVER['PHP_SELF']);
	}
	
?>
<p>
	<div class="container">    
        <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
		
		<div class="card">
		  <div class="card-header text-bg-secondary"><h4>Sign in</h4></div>
		  <div class="card-body">
			<div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>         
			<form id="loginform" class="form-horizontal" role="form" method="post" action="<?php echo $action; ?>">
			<input type="hidden" name="StartURL" value="<?php echo $StartURL; ?>">
			
			<p>Please sign in to access this site. If you do not have an account, contact the site owner.</p>
						
				<div style="margin-bottom: 25px" class="input-group">
							<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
							<input id="username" type="text" class="form-control" name="username" value="" placeholder="&nbsp;username" autofocus>                                        
						</div>
					
				<div style="margin-bottom: 25px" class="input-group">
							<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
							<input id="password" type="password" class="form-control" name="password" placeholder="&nbsp;password">
						</div>                           
					<div style="margin-top:10px" class="form-group">
					
						<!-- Button -->

						<div class="col-sm-6 controls">
						  <button type="submit" class="btn btn-primary">Login</button>
						</div>
					</div> 
			</form>
			<?php 
				if (isset($error)) { ?>
				<p>
					<div class="alert alert-danger">
							<strong>Warning:</strong> <?php echo $error; ?> 
					</div>		
			<?php
				} ?>
		  </div>
		  <div class="card-footer">
			<span style="margin-top:10px; float:right; font-size: 90%;"><a href="sessionpasswordforgot.php">Forgot password?</a></span>
		  </div>
        </div>
	</div>
	
	






</body>
</html>

<?php
	exit;
?>