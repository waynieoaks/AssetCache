<!DOCTYPE html>
<html lang="en">
<head>
	<title>AssetCache - Password reset</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="/css/bootstrap.min.css" rel="stylesheet" />
	<link href="/css/all.min.css" rel="stylesheet" />
	<link href="/css/assetcache.css" rel="stylesheet" />
	<?php 
	require("inc/dbconfig.php");
	?>
</head>
<body><p>
	<div class="container">    
        <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">                    
            <div class="card" >
                    <div class="card-header text-bg-secondary"><h4>Reset password</h4></div>     

                    <div style="padding-top:30px" class="card-body">

                        <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>
                            
                        <form id="resetform" class="form-horizontal" role="form" method="post" autocomplete="off" action="sessionpasswordreset.php">
						
						<p>If you have an account and have forgotten your password, this page can send you a new one. For new accounts, contact the site owner. 
						<h5>Instructions:</h5>
						1. Enter your username or email
						<br>2. Click "Submit" and you will be sent a new password
						<br>3. Use your username and new password to log in
						<br>4. You can then select "Edit profile" to change your password</p>
                                    
                            <div style="margin-bottom: 25px" class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                <input id="username" type="text" class="form-control" name="username" value="" placeholder="&nbsp;username" autofocus>                                        
                            </div>
                 
							<div style="margin-top:10px" class="form-group">
								<!-- Button -->
									<div class="col-sm-12 controls">
									  <button type="submit" class="btn btn-primary">Submit</button>
										<div class="pull-right">
											
										</div>
									</div>
							</div> 
                        </form>     
                    </div>
					<div class="card-footer">
						<span style="margin-top:10px; float:right; font-size: 90%;"><a href="javascript:history.go(-1)">Go back</a></span>
					</div>
            </div>  
        </div>
		
	</div>
</body>
</html>
